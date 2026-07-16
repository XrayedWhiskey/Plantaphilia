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

namespace Borlabs\Cookie\System\Script;

use Borlabs\Cookie\Adapter\WpFunction;
use Borlabs\Cookie\Repository\Service\ServiceRepository;
use Borlabs\Cookie\Repository\ServiceGroup\ServiceGroupRepository;
use Borlabs\Cookie\System\Language\Language;
use Borlabs\Cookie\System\Script\Traits\RepeatableSettingsTrait;

class FallbackCodeManager
{
    use RepeatableSettingsTrait;

    private Language $language;

    private ServiceGroupRepository $serviceGroupRepository;

    private ServiceRepository $serviceRepository;

    private WpFunction $wpFunction;

    public function __construct(
        Language $language,
        ServiceGroupRepository $serviceGroupRepository,
        ServiceRepository $serviceRepository,
        WpFunction $wpFunction
    ) {
        $this->language = $language;
        $this->serviceGroupRepository = $serviceGroupRepository;
        $this->serviceRepository = $serviceRepository;
        $this->wpFunction = $wpFunction;
    }

    public function getFallbackCodes(): string
    {
        $serviceGroups = $this->serviceGroupRepository->getAllActiveOfLanguage($this->language->getCurrentLanguageCode());
        $serviceGroupIds = array_column($serviceGroups, 'id');
        $services = $this->serviceRepository->getAllOfCurrentLanguage(false, true);
        $fallbackCodes = '';

        foreach ($services as $service) {
            if ($service->fallbackCode !== '' && in_array($service->serviceGroupId, $serviceGroupIds, true)) {
                $settings = array_column($service->settingsFields->list, 'value', 'key');

                if (isset($settings['disable-code-execution']) && $settings['disable-code-execution'] === '1') {
                    continue;
                }

                $repeatableSettings = $service->repeatableSettingsFields ? $this->transformRepeatableSettingsToArray($service->repeatableSettingsFields) : [];
                $searchAndReplace = [
                    'search' => array_map(
                        static fn ($value) => '{{ ' . $value . ' }}',
                        array_column($service->settingsFields->list ?? [], 'key'),
                    ),
                    'replace' => array_column($service->settingsFields->list ?? [], 'value'),
                ];
                $searchAndReplace['search'][] = '{{ key }}';
                $searchAndReplace['replace'][] = $service->key;

                foreach ($repeatableSettings as $key => $settings) {
                    $searchAndReplace['search'][] = '{{ ' . $key . ' }}';
                    $searchAndReplace['replace'][] = json_encode($settings);
                }

                $searchAndReplace = $this->wpFunction->applyFilter(
                    'borlabsCookie/scriptBuilder/service/modifyPlaceholders/' . $service->key,
                    $searchAndReplace,
                );
                $fallbackCode = $this->wpFunction->applyFilter(
                    'borlabsCookie/scriptBuilder/service/modifyFallbackCode/',
                    $service->fallbackCode,
                );
                $fallbackCode = $this->wpFunction->applyFilter(
                    'borlabsCookie/scriptBuilder/service/modifyFallbackCode/' . $service->key,
                    $fallbackCode,
                );
                $fallbackCodes .= str_replace($searchAndReplace['search'], $searchAndReplace['replace'], $fallbackCode);
            }
        }

        return $fallbackCodes;
    }
}
