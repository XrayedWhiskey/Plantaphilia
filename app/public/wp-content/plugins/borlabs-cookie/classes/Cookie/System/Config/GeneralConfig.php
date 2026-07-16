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

use Borlabs\Cookie\Dto\Config\GeneralDto;
use Borlabs\Cookie\Enum\Cookie\SameSiteEnum;
use Borlabs\Cookie\System\Language\Language;
use Borlabs\Cookie\System\Option\Option;

/**
 * @extends AbstractConfigManagerWithLanguage<GeneralDto>
 */
final class GeneralConfig extends AbstractConfigManagerWithLanguage
{
    /**
     * Name of `config_name` where the configuration will be stored. The name is automatically extended with a language
     * code.
     */
    public const CONFIG_NAME = 'GeneralConfig';

    /**
     * The property is set by {@see \Borlabs\Cookie\System\Config\AbstractConfigManagerWithLanguage}.
     */
    public static ?GeneralDto $baseConfigDto = null;

    private Language $language;

    public function __construct(
        Language $language,
        Option $option
    ) {
        parent::__construct($language, $option);

        $this->language = $language;
    }

    /**
     * Returns an {@see \Borlabs\Cookie\Dto\Config\GeneralDto} object with all properties set to the default
     * values.
     */
    public function defaultConfig(): GeneralDto
    {
        $homeUrlInfo = parse_url($this->language->getHomeUrlForSelectedLanguageCode());
        $defaultConfig = new GeneralDto();
        $defaultConfig->cookieDomain = $homeUrlInfo['host'];
        /*
         * We no longer set `$defaultConfig->cookiePath`, as in most cases, the result does not align with user expectations.
         * The majority of customers with a value in `$homeUrlInfo['path']` are using a multilanguage plugin that adds the language code as a folder.
         * This would require users to give consent separately for each language, which is not desired.
         * Therefore, using `/` as the cookie path is the best option and should help reduce support requests.
         */
        $defaultConfig->cookieSameSite = SameSiteEnum::LAX();
        $pluginUrlInfo = parse_url(
            rtrim(BORLABS_COOKIE_PLUGIN_URL, '/'),
        );
        $localizedSiteUrlInfo = parse_url($this->language->getSiteUrlForSelectedLanguageCode());
        $defaultConfig->pluginUrl = $localizedSiteUrlInfo['scheme'] . '://'
            . $localizedSiteUrlInfo['host']
            . (isset($localizedSiteUrlInfo['port']) ? ':' . $localizedSiteUrlInfo['port'] : '')
            . $pluginUrlInfo['path'];

        return $defaultConfig;
    }

    /**
     * This method returns the {@see \Borlabs\Cookie\Dto\Config\GeneralDto} object with all properties for the
     * language specified when calling the {@see \Borlabs\Cookie\System\Config\GeneralConfig::load()} method.
     */
    public function get(): GeneralDto
    {
        $this->ensureConfigWasInitialized();

        return self::$baseConfigDto;
    }

    /**
     * Returns the {@see \Borlabs\Cookie\Dto\Config\GeneralDto} object of the specified language.
     * If no configuration is found for the language, the default settings are used.
     */
    public function load(string $languageCode): GeneralDto
    {
        return $this->_load($languageCode);
    }

    /**
     * Saves the configuration of the specified language.
     */
    public function save(GeneralDto $config, string $languageCode): bool
    {
        return $this->_save($config, $languageCode);
    }
}
