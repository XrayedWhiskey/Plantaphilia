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

use Borlabs\Cookie\Container\Container;
use Borlabs\Cookie\Enum\System\AutomaticUpdateEnum;
use Borlabs\Cookie\System\Config\PluginConfig;
use Borlabs\Cookie\System\Log\Log;

class Migration_3_0_11
{
    private Container $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function run(): void
    {
        $pluginConfigManager = $this->container->get(PluginConfig::class);
        $pluginConfig = $pluginConfigManager->get();

        $this->container->get(Log::class)->info(
            'Update plugin config',
            [
                'old' => [
                    'automaticUpdate' => $pluginConfig->automaticUpdate->value,
                    'enableDebugLogging' => $pluginConfig->enableDebugLogging,
                ],
                'new' => [
                    'automaticUpdate' => AutomaticUpdateEnum::AUTO_UPDATE_ALL()->value,
                    'enableDebugLogging' => false,
                ],
            ],
        );

        $pluginConfig->automaticUpdate = AutomaticUpdateEnum::AUTO_UPDATE_ALL();
        $pluginConfig->enableDebugLogging = false;
        $pluginConfigManager->save($pluginConfig);
    }
}
