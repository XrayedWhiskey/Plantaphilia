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

namespace Borlabs\Cookie\System\Language;

use Borlabs\Cookie\DtoList\System\KeyValueDtoList;

/**
 * Interface MultilanguageInterface.
 *
 * Each multilanguage strategy MUST implement the **MultilanguageInterface** to work with the **MultilanguageContext**.
 *
 * @see \Borlabs\Cookie\System\Language\MultilanguageInterface::getCurrentLanguageCode()
 * @see \Borlabs\Cookie\System\Language\MultilanguageInterface::getLanguageList()
 * @see \Borlabs\Cookie\System\Language\MultilanguageInterface::getLanguageName()
 * @see \Borlabs\Cookie\System\Language\MultilanguageInterface::getLanguageUrlList()
 * @see \Borlabs\Cookie\System\Language\MultilanguageInterface::getSiteUrl()
 * @see \Borlabs\Cookie\System\Language\MultilanguageInterface::isActive()
 */
interface MultilanguageInterface
{
    /**
     * This method should return the current language code. If no current language code can be detected, the default
     * language code should be returned. If both is empty, this method should return `null`.
     */
    public function getCurrentLanguageCode(): ?string;

    /**
     * This method must return a {@see \Borlabs\Cookie\DtoList\System\KeyValueDtoList} object with the available languages.
     * The `key` contains the language code and the `value` contains the name of the language.
     */
    public function getLanguageList(): KeyValueDtoList;

    /**
     * This method should return the name of the passed language code. If no name is available, this method should
     * return
     * `null`.
     */
    public function getLanguageName(string $languageCode): ?string;

    /**
     * This method must return a {@see \Borlabs\Cookie\DtoList\System\KeyValueDtoList} object with the available
     * languages URLs. The `key` contains the language code and the `value` contains the URL for the corresponding
     * language.
     */
    public function getLanguageUrlList(): KeyValueDtoList;

    /**
     * This method should return the website URL of the passed language code.
     *
     * @return string the URL of the current site
     */
    public function getSiteUrl(string $languageCode): string;

    /**
     * This method sould return, if the strategy can be used, for example: the multilanguage plugin of the strategy is
     * active.
     */
    public function isActive(): bool;
}
