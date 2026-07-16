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
use Borlabs\Cookie\Localization\DefaultLocalizationStrings;
use Borlabs\Cookie\ScheduleEvent\ScannerPageSelectionKeywordDatabaseUpdateEvent;
use Borlabs\Cookie\System\Config\GeneralConfig;
use Borlabs\Cookie\System\Config\LibraryConfig;
use Borlabs\Cookie\System\Config\PluginConfig;
use Borlabs\Cookie\System\Config\Traits\PackageAutoUpdateTimeHelperTrait;
use Borlabs\Cookie\System\Language\Language;
use Borlabs\Cookie\System\Log\Log;
use Borlabs\Cookie\System\Option\Option;
use Borlabs\Cookie\System\Script\ScriptConfigBuilder;
use Borlabs\Cookie\System\Script\UpdateJavaScriptConfigFileJobService;
use Borlabs\Cookie\System\Style\StyleBuilder;
use Borlabs\Cookie\System\ThirdPartyCacheClearer\ThirdPartyCacheClearerManager;

class Migration_3_4_0
{
    use PackageAutoUpdateTimeHelperTrait;

    private Container $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function run()
    {
        $generalConfig = $this->container->get(GeneralConfig::class);
        $language = $this->container->get(Language::class);
        $libraryConfig = $this->container->get(LibraryConfig::class);
        $option = $this->container->get(Option::class);
        $pluginConfig = $this->container->get(PluginConfig::class);
        $scannerPageSelectionKeywordDatabaseUpdateEvent = $this->container->get(ScannerPageSelectionKeywordDatabaseUpdateEvent::class);
        $updateJavaScriptConfigFileJobService = $this->container->get(UpdateJavaScriptConfigFileJobService::class);
        $wpFunction = $this->container->get(WpFunction::class);
        $configuredLanguages = [];

        // Unregister the event
        $scannerPageSelectionKeywordDatabaseUpdateEvent->deregister();

        // In a multisite network, each site can have a different locale.
        $localeOption = $option->getThirdPartyOption('WPLANG', '');
        $defaultLanguageCode = $language->determineLanguageCodeLength(is_string($localeOption->value) && strlen($localeOption->value) >= 2 ? $localeOption->value : BORLABS_COOKIE_DEFAULT_LANGUAGE);
        $configuredLanguages[$defaultLanguageCode] = $defaultLanguageCode;

        // Migrate PluginDto settings to LibraryDto settings
        $libraryConfigDto = $libraryConfig->get();
        $pluginConfigDto = $pluginConfig->get();
        $libraryConfigDto->enableEmailNotificationsForUpdatablePackagesWithAutoUpdateDisabled = $pluginConfigDto->enableEmailNotificationsForUpdatablePackagesWithAutoUpdateDisabled;
        $libraryConfigDto->enableEmailNotificationsForUpdatablePackagesWithAutoUpdateEnabled = $pluginConfigDto->enableEmailNotificationsForUpdatablePackagesWithAutoUpdateEnabled;
        $libraryConfigDto->packageAutoUpdateEmailAddresses = $pluginConfigDto->packageAutoUpdateEmailAddresses;
        $libraryConfigDto->packageAutoUpdateInterval = $pluginConfigDto->packageAutoUpdateInterval;
        $libraryConfigDto->packageAutoUpdateTime = $pluginConfigDto->packageAutoUpdateTime;
        $libraryConfigDto->packageAutoUpdateTimeSpan = $pluginConfigDto->packageAutoUpdateTimeSpan;

        $statusLibraryConfig = $libraryConfig->save($libraryConfigDto);
        $this->container->get(Log::class)->info(
            'Library config updated: {{ status }}',
            [
                'status' => $statusLibraryConfig ? 'Yes' : 'No',
            ],
        );

        // Migrate GeneralDto settings to PluginDto settings
        $metaBoxes = [];
        $clearThirdPartyCacheFlag = false;
        $allGeneralConfigs = $generalConfig->getAllConfigs();

        foreach ($allGeneralConfigs as $optionDto) {
            if (!isset($optionDto->language) || !$generalConfig->hasConfig($optionDto->language)) {
                continue;
            }

            $generalConfigDto = $generalConfig->load($optionDto->language);

            if ($generalConfigDto->clearThirdPartyCache === true) {
                $clearThirdPartyCacheFlag = true;
            }

            foreach ($generalConfigDto->metaBox as $postType => $true) {
                $metaBoxes[$postType] = true;
            }
        }

        $pluginConfigDto->clearThirdPartyCache = $clearThirdPartyCacheFlag;
        $pluginConfigDto->metaBox = $metaBoxes;

        $statusPluginConfig = $pluginConfig->save($pluginConfigDto);
        $this->container->get(Log::class)->info(
            'Plugin config updated: {{ status }}',
            [
                'status' => $statusPluginConfig ? 'Yes' : 'No',
            ],
        );

        // Retrieve all available languages when a multilingual plugin is active
        $availableLanguages = $language->getLanguageList();

        foreach ($availableLanguages->list as $languageData) {
            $configuredLanguages[$languageData->key] = $languageData->key;
        }

        // Update JavaScript configuration
        foreach ($configuredLanguages as $languageCode) {
            $status = $this->container->get(ScriptConfigBuilder::class)->updateJavaScriptConfigFileAndIncrementConfigVersion(
                $languageCode,
            );

            $this->container->get(Log::class)->info(
                'JavaScript config ({{ language }}) file updated: {{ status }}',
                [
                    'language' => $languageCode,
                    'status' => $status ? 'Yes' : 'No',
                ],
            );

            // Update CSS file
            $status = $this->container->get(StyleBuilder::class)->updateCssFileAndIncrementStyleVersion(
                $wpFunction->getCurrentBlogId(),
                $languageCode,
            );

            $this->container->get(Log::class)->info(
                'CSS file ({{ language }}) updated: {{ status }}',
                [
                    'blogId' => $wpFunction->getCurrentBlogId(),
                    'language' => $languageCode,
                    'status' => $status ? 'Yes' : 'No',
                ],
            );

            $updateJavaScriptConfigFileJobService->updateJob($languageCode);
        }

        // Prior to version 3.3.12, the DefaultLocalizationStrings class is already loaded, so the localization strings cannot be updated during the upgrade process.
        $this->container->get(ThirdPartyCacheClearerManager::class)->clearCache();
    }
}
