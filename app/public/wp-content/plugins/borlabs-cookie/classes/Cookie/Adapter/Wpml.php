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

use Borlabs\Cookie\Dto\System\KeyValueDto;
use Borlabs\Cookie\DtoList\System\KeyValueDtoList;
use Borlabs\Cookie\System\Language\MultilanguageInterface;
use Borlabs\Cookie\System\Language\Traits\LanguageTrait;

/**
 * Class WPML.
 *
 * The **WPML** class is used as a strategy in the **MultilanguageContext** class.
 * The class acts as an adapter between the WPML API and the **Language** class.
 *
 * @see \Borlabs\Cookie\System\Language\Language
 * @see \Borlabs\Cookie\System\Language\MultilanguageContext
 * @see \Borlabs\Cookie\System\Language\MultilanguageInterface
 */
final class Wpml implements MultilanguageInterface
{
    use LanguageTrait;

    private WpFunction $wpFunction;

    public function __construct(WpFunction $wpFunction)
    {
        $this->wpFunction = $wpFunction;
    }

    /**
     * This method returns the current language code. If no current language code can be detected, the default language
     * code is used.
     * If no language code can be detected, this method returns `null`.
     */
    public function getCurrentLanguageCode(): ?string
    {
        $null = null;
        // During the setup of WPML, the filter can return `false`.
        $wpmlCurrentLanguage = $this->wpFunction->applyFilter('wpml_current_language', $null);

        return is_string($wpmlCurrentLanguage) ? $wpmlCurrentLanguage : null;
    }

    /**
     * This method returns the default language code, which MUST NOT be the current language code.
     * This method is used when no current language code can be detected or is *all*.
     * If no language code can be detected, this method returns `null`.
     */
    public function getDefaultLanguageCode(): ?string
    {
        $null = null;
        $defaultLanguage = $this->wpFunction->applyFilter('wpml_default_language', $null);

        return is_string($defaultLanguage) && $defaultLanguage !== 'all' ? $this->determineLanguageCodeLength($defaultLanguage)
            : null;
    }

    /**
     * This method returns a {@see \Borlabs\Cookie\DtoList\System\KeyValueDtoList} with the available languages. The `name`
     * contains the language code and the `value` contains the name of the language.
     */
    public function getLanguageList(): KeyValueDtoList
    {
        $list = new KeyValueDtoList();
        $languages = $this->getActiveLanguages();

        foreach ($languages as $languageCode => $languageData) {
            $list->add(
                new KeyValueDto(
                    $this->determineLanguageCodeLength($languageCode),
                    $languageData['translated_name'],
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

        // TODO: Needs testing in context of `determineLanguageCodeLength()`
        if (!empty($languages[$languageCode])) {
            return $languages[$languageCode]['native_name'];
        }

        return null;
    }

    /**
     * This method returns a {@see \Borlabs\Cookie\DtoList\System\KeyValueDtoList} where each item contains the
     * language code as the key and the URL for that language as the value.
     */
    public function getLanguageUrlList(): KeyValueDtoList
    {
        $list = new KeyValueDtoList();
        $languages = $this->getActiveLanguages();

        foreach ($languages as $languageCode => $languageData) {
            $list->add(
                new KeyValueDto(
                    $this->determineLanguageCodeLength($languageCode),
                    $languageData['url'],
                ),
            );
        }

        return $list;
    }

    /**
     * This method returns the current site URL for the active language.
     * If the active language cannot be determined, it falls back to the default site URL.
     *
     * @return string the URL of the current site in the active language
     */
    public function getSiteUrl(string $languageCode): string
    {
        $languages = $this->getActiveLanguages();
        $url = $this->wpFunction->getSiteUrl();

        foreach ($languages as $wpmlLanguageCode => $language) {
            if ($languageCode === $this->determineLanguageCodeLength($wpmlLanguageCode) && isset($language['url']) && is_string($language['url'])) {
                $url = $language['url'];

                break;
            }
        }

        return $url;
    }

    /**
     * This method returns `true` if the corresponding multi-language plugin is active.
     */
    public function isActive(): bool
    {
        return defined('ICL_LANGUAGE_CODE');
    }

    private function getActiveLanguages(): array
    {
        $null = null;
        $languages = $this->wpFunction->applyFilter('wpml_active_languages', $null);

        if (!is_array($languages)) {
            $languages = [];
        }

        return $languages;
    }
}
