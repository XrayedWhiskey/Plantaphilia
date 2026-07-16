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

use Borlabs\Cookie\Dto\Config\ContentBlockerStyleDto;
use Borlabs\Cookie\System\Language\Language;

/**
 * @extends AbstractConfigManagerWithLanguage<ContentBlockerStyleDto>
 */
final class ContentBlockerStyleConfig extends AbstractConfigManagerWithLanguage
{
    /**
     * Name of `config_name` where the configuration will be stored. The name is automatically extended with a language
     * code.
     */
    public const CONFIG_NAME = 'ContentBlockerStyleConfig';

    /**
     * The property is set by {@see \Borlabs\Cookie\System\Config\AbstractConfigManagerWithLanguage}.
     */
    public static ?ContentBlockerStyleDto $baseConfigDto = null;

    /**
     * Returns an {@see \Borlabs\Cookie\Dto\Config\ContentBlockerStyleDto} object with all properties set to the default
     * values.
     */
    public function defaultConfig(): ContentBlockerStyleDto
    {
        return new ContentBlockerStyleDto();
    }

    /**
     * This method returns the {@see \Borlabs\Cookie\Dto\Config\ContentBlockerStyleDto} object with all properties for the language
     * specified when calling the {@see \Borlabs\Cookie\System\Config\DialogStyleConfig::load()} method.
     */
    public function get(): ContentBlockerStyleDto
    {
        $this->ensureConfigWasInitialized();

        return self::$baseConfigDto;
    }

    /**
     * Returns the {@see \Borlabs\Cookie\Dto\Config\ContentBlockerStyleDto} object of the specified language.
     * If no configuration is found for the language, the default settings are used.
     */
    public function load(string $languageCode): ContentBlockerStyleDto
    {
        return $this->_load($languageCode);
    }

    /**
     * Saves the configuration of the specified language.
     */
    public function save(ContentBlockerStyleDto $config, string $languageCode): bool
    {
        return $this->_save($config, $languageCode);
    }
}
