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

namespace Borlabs\CookieApi\PhpApi\Component;

use Borlabs\Cookie\System\Language\Language;

final class LanguagePhpApi
{
    private Language $language;

    public function __construct(Language $language)
    {
        $this->language = $language;
    }

    /**
     * Returns the language code of the current language.
     */
    public function getCurrentLanguageCode(): string
    {
        return $this->language->getCurrentLanguageCode();
    }
}
