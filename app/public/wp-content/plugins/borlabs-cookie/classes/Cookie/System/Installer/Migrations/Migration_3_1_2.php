<?php
/*
 *  Copyright (c) 2026 Borlabs GmbH. All rights reserved.
 *  This file may not be redistributed in whole or significant part.
 *  Content of this file is protected by international copyright laws.
 *
 *  ----------------- Borlabs Cookie IS NOT FREE SOFTWARE -----------------
 *
 *  @copyright Borlabs GmbH, https://borlabs.io
 */

declare(strict_types=1);

namespace Borlabs\Cookie\System\Installer\Migrations;

use Borlabs\Cookie\Adapter\WpFunction;
use Borlabs\Cookie\Container\Container;
use Borlabs\Cookie\Model\Service\ServiceModel;
use Borlabs\Cookie\Repository\Service\ServiceRepository;
use Borlabs\Cookie\System\Language\Language;
use Borlabs\Cookie\System\Log\Log;
use Borlabs\Cookie\System\Script\ScriptConfigBuilder;
use Borlabs\Cookie\System\Style\StyleBuilder;
use Borlabs\Cookie\System\ThirdPartyCacheClearer\ThirdPartyCacheClearerManager;

class Migration_3_1_2
{
    private Container $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function run()
    {
        $language = $this->container->get(Language::class);
        $wpFunction = $this->container->get(WpFunction::class);
        $this->fixGoogleTagManagerSettings();

        // Update JavaScript configuration
        $status = $this->container->get(ScriptConfigBuilder::class)->updateJavaScriptConfigFileAndIncrementConfigVersion(
            $language->getSelectedLanguageCode(),
        );

        $this->container->get(Log::class)->info(
            'JavaScript config file updated: {{ status }}',
            [
                'language' => $language->getSelectedLanguageCode(),
                'status' => $status ? 'Yes' : 'No',
            ],
        );

        // Update CSS file
        $status = $this->container->get(StyleBuilder::class)->updateCssFileAndIncrementStyleVersion(
            $wpFunction->getCurrentBlogId(),
            $language->getSelectedLanguageCode(),
        );

        $this->container->get(Log::class)->info(
            'CSS file updated: {{ status }}',
            [
                'blogId' => $wpFunction->getCurrentBlogId(),
                'language' => $language->getSelectedLanguageCode(),
                'status' => $status ? 'Yes' : 'No',
            ],
        );

        $this->container->get(ThirdPartyCacheClearerManager::class)->clearCache();
    }

    private function fixGoogleTagManagerSettings()
    {
        $log = $this->container->get(Log::class);
        $serviceRepository = $this->container->get(ServiceRepository::class);

        /**
         * @var ServiceModel[] $googleTagManagerService
         */
        $googleTagManagerService = $serviceRepository->find([
            'borlabsServicePackageKey' => 'google-tag-manager',
        ]);

        if (!is_array($googleTagManagerService)) {
            return;
        }

        $settingsFieldsKeyToServiceGroupKeyMap = [
            'google-tag-manager-cm-ad-personalization-service-group' => 'marketing',
            'google-tag-manager-cm-ad-storage-service-group' => 'marketing',
            'google-tag-manager-cm-ad-user-data-service-group' => 'marketing',
            'google-tag-manager-cm-analytics-storage-service-group' => 'statistics',
            'google-tag-manager-cm-functionality-storage-service-group' => 'statistics',
            'google-tag-manager-cm-personalization-storage-service-group' => 'marketing',
            'google-tag-manager-cm-security-storage-service-group' => 'statistics',
        ];

        foreach ($googleTagManagerService as $service) {
            foreach ($service->settingsFields->list ?? [] as &$field) {
                if (!isset($settingsFieldsKeyToServiceGroupKeyMap[$field->key])) {
                    continue;
                }

                $serviceGroupKey = $settingsFieldsKeyToServiceGroupKeyMap[$field->key];
                $log->info(
                    'Reset "Google Tag Manager" setting: {{ field }}: {{ valueOld }} > {{ valueNew }}',
                    [
                        'field' => $field->translation->field,
                        'valueNew' => $serviceGroupKey,
                        'valueOld' => $field->value,
                    ],
                );
                $field->value = $serviceGroupKey;
            }

            $serviceRepository->update($service);
        }
    }
}
