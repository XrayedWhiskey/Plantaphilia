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
use Borlabs\Cookie\System\Config\PluginConfig;
use Borlabs\Cookie\System\Language\Language;
use Borlabs\Cookie\System\Log\Log;
use Borlabs\Cookie\System\Script\ScriptConfigBuilder;
use Borlabs\Cookie\System\Style\StyleBuilder;
use Borlabs\Cookie\System\ThirdPartyCacheClearer\ThirdPartyCacheClearerManager;

class Migration_3_2_3
{
    private Container $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function run()
    {
        $language = $this->container->get(Language::class);
        $pluginConfig = $this->container->get(PluginConfig::class);
        $wpFunction = $this->container->get(WpFunction::class);

        // Update plugin config
        $pluginConfigDto = $pluginConfig->get();
        $pluginConfigDto->enableDebugConsole = false;
        $status = $pluginConfig->save($pluginConfigDto);

        $this->container->get(Log::class)->info(
            'Plugin config updated: {{ status }}',
            [
                'status' => $status ? 'Yes' : 'No',
            ],
        );

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
}
