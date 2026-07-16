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

use Borlabs\Cookie\Dto\Config\ContentBlockerSettingsDto;
use Borlabs\Cookie\System\Language\Language;

/**
 * @extends AbstractConfigManagerWithLanguage<ContentBlockerSettingsDto>
 */
final class ContentBlockerSettingsConfig extends AbstractConfigManagerWithLanguage
{
    /**
     * Name of `config_name` where the configuration will be stored. The name is automatically extended with a language
     * code.
     */
    public const CONFIG_NAME = 'ContentBlockerConfig';

    /**
     * The property is set by {@see \Borlabs\Cookie\System\Config\AbstractConfigManagerWithLanguage}.
     */
    public static ?ContentBlockerSettingsDto $baseConfigDto = null;

    /**
     * Returns an {@see \Borlabs\Cookie\Dto\Config\ContentBlockerSettingsDto} object with all properties set to the default
     * values.
     */
    public function defaultConfig(): ContentBlockerSettingsDto
    {
        return new ContentBlockerSettingsDto();
    }

    /**
     * This method returns the {@see \Borlabs\Cookie\Dto\Config\ContentBlockerSettingsDto} object with all properties for the
     * language specified when calling the {@see \Borlabs\Cookie\System\Config\ContentBlockerConfig::load()} method.
     */
    public function get(): ContentBlockerSettingsDto
    {
        $this->ensureConfigWasInitialized();

        return self::$baseConfigDto;
    }

    /**
     * Returns the {@see \Borlabs\Cookie\Dto\Config\ContentBlockerSettingsDto} object of the specified language.
     * If no configuration is found for the language, the default settings are used.
     */
    public function load(string $languageCode): ContentBlockerSettingsDto
    {
        return $this->_load($languageCode);
    }

    /**
     * Saves the configuration of the specified language.
     */
    public function save(ContentBlockerSettingsDto $config, string $languageCode): bool
    {
        return $this->_save($config, $languageCode);
    }
}
