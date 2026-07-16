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
use Borlabs\Cookie\System\Language\Language;
use Borlabs\Cookie\System\Log\Log;
use Borlabs\Cookie\System\Option\Option;
use Borlabs\Cookie\System\Script\ScriptConfigBuilder;
use Borlabs\Cookie\System\Style\StyleBuilder;
use Borlabs\Cookie\System\ThirdPartyCacheClearer\ThirdPartyCacheClearerManager;

class Migration_3_3_4
{
    private Container $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function run()
    {
        $language = $this->container->get(Language::class);
        $option = $this->container->get(Option::class);
        $wpFunction = $this->container->get(WpFunction::class);
        $configuredLanguages = [];

        // In a multisite network, each site can have a different locale.
        $localeOption = $option->getThirdPartyOption('WPLANG', '');
        $defaultLanguageCode = $language->determineLanguageCodeLength(is_string($localeOption->value) && strlen($localeOption->value) >= 2 ? $localeOption->value : BORLABS_COOKIE_DEFAULT_LANGUAGE);
        $configuredLanguages[$defaultLanguageCode] = $defaultLanguageCode;

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
        }

        $this->container->get(ThirdPartyCacheClearerManager::class)->clearCache();
    }
}
