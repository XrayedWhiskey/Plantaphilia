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
use Borlabs\Cookie\Support\Searcher;
use Borlabs\Cookie\System\Language\Language;
use Borlabs\Cookie\System\Language\MultilanguageInterface;
use Borlabs\Cookie\System\Language\Traits\LanguageTrait;
use LogicException;

/**
 * Class TranslatePress.
 *
 * The **TranslatePress** class is used as a strategy in the **MultilanguageContext** class.
 * The class acts as an adapter between the TranslatePress API and the **Language** class.
 *
 * @see \Borlabs\Cookie\System\Language\Language
 * @see \Borlabs\Cookie\System\Language\MultilanguageContext
 * @see \Borlabs\Cookie\System\Language\MultilanguageInterface
 */
final class TranslatePress implements MultilanguageInterface
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
        global $TRP_LANGUAGE;

        $currentLanguage = $TRP_LANGUAGE;

        if (is_string($currentLanguage)) {
            return $currentLanguage;
        }

        return $this->getDefaultLanguageCode();
    }

    /**
     * This method returns the default language code, which MUST NOT be the current language code.
     * This method is used when no current language code can be detected or is *all*.
     * If no language code can be detected, this method returns `null`.
     */
    public function getDefaultLanguageCode(): ?string
    {
        $defaultLanguage = $this->wpFunction->getLocale();

        if (empty($defaultLanguage) || strlen($defaultLanguage) <= 1) {
            $defaultLanguage = BORLABS_COOKIE_DEFAULT_LANGUAGE;
        }

        return $this->determineLanguageCodeLength($defaultLanguage);
    }

    /**
     * This method returns a {@see \Borlabs\Cookie\DtoList\System\KeyValueDtoList} with the available languages. The `name`
     * contains the language code and the `value` contains the name of the language.
     */
    public function getLanguageList(): KeyValueDtoList
    {
        if (!function_exists('trp_get_languages')) {
            throw new LogicException('A required third-party function does not exist.', E_USER_ERROR);
        }

        return new KeyValueDtoList(
            array_map(
                fn ($languageCode, $languageName) => new KeyValueDto(
                    $this->determineLanguageCodeLength($languageCode),
                    $languageName,
                ),
                array_flip(trp_get_languages(null)),
                trp_get_languages(null),
            ),
        );
    }

    /**
     * This method returns the name of the passed language code.
     * If no language name can be found, this method returns `null`.
     */
    public function getLanguageName(string $languageCode): ?string
    {
        return Searcher::findObject($this->getLanguageList()->list, 'key', $languageCode)->value ?? null;
    }

    /**
     * This method returns a {@see \Borlabs\Cookie\DtoList\System\KeyValueDtoList} where each item contains the
     * language code as the key and the URL for that language as the value.
     */
    public function getLanguageUrlList(): KeyValueDtoList
    {
        $list = new KeyValueDtoList();
        $siteUrl = $this->wpFunction->getSiteUrl();
        $languages = $this->getLanguageList();

        foreach ($languages->list as $languageData) {
            $list->add(
                new KeyValueDto(
                    $languageData->key,
                    $siteUrl,
                ),
            );
        }

        return $list;
    }

    /**
     * TranslatePress does not support domain mapping, so the site URL is always returned regardless of the language code provided.
     */
    public function getSiteUrl(string $languageCode): string
    {
        return $this->wpFunction->getSiteUrl();
    }

    /**
     * This method returns `true` if the corresponding multi-language plugin is active.
     */
    public function isActive(): bool
    {
        return function_exists('trp_enable_translatepress') && trp_enable_translatepress();
    }
}
