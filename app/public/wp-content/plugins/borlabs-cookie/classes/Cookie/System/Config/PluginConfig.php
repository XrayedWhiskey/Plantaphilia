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

namespace Borlabs\Cookie\System\Config;

use Borlabs\Cookie\Dto\Config\PluginDto;
use Borlabs\Cookie\Enum\Library\AutoUpdateIntervalEnum;
use Borlabs\Cookie\Enum\System\AutomaticUpdateEnum;
use Borlabs\Cookie\Enum\System\DisplayModeSettingsEnum;
use Borlabs\Cookie\Enum\System\WordPressAdminSidebarMenuModeEnum;

/**
 * @extends AbstractConfigManager<PluginDto>
 *
 * @template TModel of PluginDto
 */
final class PluginConfig extends AbstractConfigManager
{
    /**
     * Name of `config_name` where the configuration will be stored.
     */
    public const CONFIG_NAME = 'PluginConfig';

    /**
     * The property is set by {@see \Borlabs\Cookie\System\Config\AbstractConfigManager}.
     */
    public static ?PluginDto $baseConfigDto = null;

    /**
     * Returns an {@see \Borlabs\Cookie\Dto\Config\PluginDto} object with all properties set to the default
     * values.
     */
    public function defaultConfig(): PluginDto
    {
        $defaultConfig = new PluginDto();
        $defaultConfig->automaticUpdate = AutomaticUpdateEnum::AUTO_UPDATE_ALL();
        $defaultConfig->packageAutoUpdateEmailAddresses = [
            $this->option->getThirdPartyOption('admin_email', '')->value,
        ];
        $defaultConfig->packageAutoUpdateInterval = AutoUpdateIntervalEnum::AFTER_24_HOURS();
        $defaultConfig->displayModeSettings = DisplayModeSettingsEnum::STANDARD();
        $defaultConfig->wordPressAdminSidebarMenuMode = WordPressAdminSidebarMenuModeEnum::STANDARD();

        return $defaultConfig;
    }

    /**
     * This method returns the {@see \Borlabs\Cookie\Dto\Config\PluginDto} object with all properties
     * when calling the {@see \Borlabs\Cookie\System\Config\PluginDto::load()} method.
     */
    public function get(): PluginDto
    {
        $this->ensureConfigWasInitialized();

        return self::$baseConfigDto;
    }

    /**
     * Returns the {@see \Borlabs\Cookie\Dto\Config\PluginDto} object.
     * If no configuration is found, the default settings are used.
     */
    public function load(): PluginDto
    {
        return $this->_load();
    }

    /**
     * Saves the configuration.
     */
    public function save(PluginDto $config): bool
    {
        return $this->_save($config);
    }
}
