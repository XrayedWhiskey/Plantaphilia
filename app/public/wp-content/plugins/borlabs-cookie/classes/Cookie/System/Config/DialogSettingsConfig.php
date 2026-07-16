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

use Borlabs\Cookie\Dto\Config\DialogSettingsDto;
use Borlabs\Cookie\System\Language\Language;

/**
 * @extends AbstractConfigManagerWithLanguage<DialogSettingsDto>
 */
final class DialogSettingsConfig extends AbstractConfigManagerWithLanguage
{
    /**
     * Name of `config_name` where the configuration will be stored. The name is automatically extended with a language
     * code.
     */
    public const CONFIG_NAME = 'DialogSettingsConfig';

    /**
     * The property is set by {@see \Borlabs\Cookie\System\Config\AbstractConfigManagerWithLanguage}.
     */
    public static ?DialogSettingsDto $baseConfigDto = null;

    /**
     * Returns an {@see \Borlabs\Cookie\Dto\Config\DialogSettingsDto} object with all properties set to the default
     * values.
     */
    public function defaultConfig(): DialogSettingsDto
    {
        $defaultConfig = new DialogSettingsDto();
        $imagePath = BORLABS_COOKIE_PLUGIN_URL . '/assets/images';
        $defaultConfig->logo = $imagePath . $defaultConfig->logo;
        $defaultConfig->logoHd = $imagePath . $defaultConfig->logoHd;

        return $defaultConfig;
    }

    /**
     * This method returns the {@see \Borlabs\Cookie\Dto\Config\DialogSettingsDto} object with all properties for the language
     * specified when calling the {@see \Borlabs\Cookie\System\Config\DialogSettingsConfig::load()} method.
     */
    public function get(): DialogSettingsDto
    {
        $this->ensureConfigWasInitialized();

        return self::$baseConfigDto;
    }

    /**
     * Returns the {@see \Borlabs\Cookie\Dto\Config\DialogSettingsDto} object of the specified language.
     * If no configuration is found for the language, the default settings are used.
     */
    public function load(string $languageCode): DialogSettingsDto
    {
        return $this->_load($languageCode);
    }

    /**
     * Saves the configuration of the specified language.
     */
    public function save(DialogSettingsDto $config, string $languageCode): bool
    {
        return $this->_save($config, $languageCode);
    }
}
