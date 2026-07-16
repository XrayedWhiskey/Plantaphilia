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

namespace Borlabs\Cookie\Adapter;

use Borlabs\Cookie\Container\Container;
use Borlabs\Cookie\Dto\System\KeyValueDto;
use Borlabs\Cookie\DtoList\System\KeyValueDtoList;
use Borlabs\Cookie\System\Config\DialogLocalization;
use Borlabs\Cookie\System\Config\DialogSettingsConfig;
use Borlabs\Cookie\System\Config\GeneralConfig;
use Borlabs\Cookie\System\Config\IabTcfConfig;
use Borlabs\Cookie\System\ContentBlocker\ContentBlockerManager;
use Borlabs\Cookie\System\Language\Language;
use Borlabs\Cookie\System\Language\MultilanguageInterface;
use Borlabs\Cookie\System\Language\Traits\LanguageTrait;
use Borlabs\Cookie\System\Option\Option;
use LogicException;

/**
 * Class Polylang.
 *
 * The **Polylang** class is used as a strategy in the **MultilanguageContext** class.
 * The class acts as an adapter between the Polylang API and the **Language** class.
 *
 * @see \Borlabs\Cookie\System\Language\Language
 * @see \Borlabs\Cookie\System\Language\MultilanguageContext
 * @see \Borlabs\Cookie\System\Language\MultilanguageInterface
 */
final class Polylang implements MultilanguageInterface
{
    use LanguageTrait;

    private Container $container;

    private Option $option;

    private WpFunction $wpFunction;

    public function __construct(
        Container $container,
        Option $option,
        WpFunction $wpFunction
    ) {
        $this->container = $container;
        $this->option = $option;
        $this->wpFunction = $wpFunction;
    }

    /**
     * This method returns the current language code. If no current language code can be detected, the default language
     * code is used.
     * If no language code can be detected, this method returns `null`.
     */
    public function getCurrentLanguageCode(): ?string
    {
        // pll_current_language is not available when wp_doing_ajax() is active
        if (!function_exists('pll_current_language')) {
            return null;
        }

        $currentLanguage = pll_current_language();

        if (is_string($currentLanguage) && $currentLanguage !== 'all') {
            return $currentLanguage;
        }

        // Fallback: Add action to reload AbstractConfigManagerWithLanguage later. Necessary when the content defines the language
        if (is_admin() === false) {
            add_action('pll_language_defined', [$this, 'polylangLanguageDefined']);
        }

        // If currentLanguage is still empty, we have to get the default language
        $currentLanguage = function_exists('pll_default_language') ? pll_default_language() : null;

        return is_string($currentLanguage) && $currentLanguage !== 'all' ? $currentLanguage
            : $this->getDefaultLanguageCode();
    }

    /**
     * This method returns the default language code, which MUST NOT be the current language code.
     * This method is used when no current language code can be detected or is *all*.
     * If no language code can be detected, this method returns `null`.
     */
    public function getDefaultLanguageCode(): ?string
    {
        if (function_exists('pll_default_language')) {
            $languageCode = pll_default_language();

            return is_string($languageCode) ? $this->determineLanguageCodeLength($languageCode) : null;
        }

        return null;
    }

    /**
     * This method returns a {@see \Borlabs\Cookie\DtoList\System\KeyValueDtoList} with the available languages. The `name`
     * contains the language code and the `value` contains the name of the language.
     */
    public function getLanguageList(): KeyValueDtoList
    {
        if (!function_exists('pll_languages_list')) {
            throw new LogicException('A required third-party function does not exist.', E_USER_ERROR);
        }

        $list = new KeyValueDtoList();
        $languages = array_combine(pll_languages_list(['fields' => 'slug']), pll_languages_list(['fields' => 'name']));

        foreach ($languages as $languageCode => $languageName) {
            $list->add(
                new KeyValueDto(
                    $this->determineLanguageCodeLength($languageCode),
                    $languageName,
                ),
            );
        }

        return $list;
    }

    /**
     * This method returns the name of the passed language code.
     * If no language name can be found, this method returns `null`.
     */
    public function getLanguageName(string $languageCode): ?string
    {
        $null = null;
        $languages = $this->wpFunction->applyFilter('wpml_active_languages', $null, []);

        if (!empty($languages[$languageCode])) {
            return $languages[$languageCode]['native_name'];
        }

        return null;
    }

    public function getLanguageUrlList(): KeyValueDtoList
    {
        $list = new KeyValueDtoList();

        $polylangSettings = $this->option->getThirdPartyOption('polylang');

        if ($polylangSettings->value === false) {
            return $list;
        }

        if ((int) $polylangSettings->value['force_lang'] !== 3 || count($polylangSettings->value['domains']) === 0) {
            return $list;
        }

        foreach ($polylangSettings->value['domains'] as $languageCode => $languageUrl) {
            $list->add(new KeyValueDto(
                $this->determineLanguageCodeLength($languageCode),
                $languageUrl,
            ));
        }

        return $list;
    }

    public function getSiteUrl(string $languageCode): string
    {
        $siteUrl = $this->wpFunction->getSiteUrl();
        $list = $this->getLanguageUrlList();

        foreach ($list->list as $languageUrlData) {
            if ($languageCode === $languageUrlData->key) {
                return $languageUrlData->value;
            }
        }

        return $siteUrl;
    }

    /**
     * This method returns `true` if the corresponding multi-language plugin is active.
     */
    public function isActive(): bool
    {
        return defined('POLYLANG_VERSION') && $this->isPluginDeactivationRequested() === false;
    }

    /**
     * This method is used to reinitialize the **AbstractConfigManagerWithLanguage** at a later time.
     * This is necessary if Polylang is configured to detect the language used based on the content loaded.
     *
     * @see \Borlabs\Cookie\Adapter\Polylang::getCurrentLanguageCode()
     */
    public function polylangLanguageDefined(string $languageCode): void
    {
        // Load config with new language code
        $this->container->get(DialogSettingsConfig::class)->init($languageCode);
        $this->container->get(GeneralConfig::class)->init($languageCode);
        $this->container->get(DialogLocalization::class)->init($languageCode);
        $this->container->get(IabTcfConfig::class)->init($languageCode);

        // Load Content Blocker settings with new language code
        $this->container->get(ContentBlockerManager::class)->init();
    }

    /**
     * When Polylang is disabled, it stops its initialization process.
     * This means that not all functions are available and would cause an error because the Borlabs Cookie
     * tries to determine the language in the background.
     */
    private function isPluginDeactivationRequested(): bool
    {
        return defined('POLYLANG_BASENAME')
            && isset($_GET['action'], $_GET['plugin'])
            && $_GET['action'] === 'deactivate'
            && $_GET['plugin'] === constant('POLYLANG_BASENAME');
    }
}
