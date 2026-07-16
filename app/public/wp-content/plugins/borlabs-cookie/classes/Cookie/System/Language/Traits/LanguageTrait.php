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

namespace Borlabs\Cookie\System\Language\Traits;

trait LanguageTrait
{
    public function determineLanguageCodeLength(string $languageCode): string
    {
        return $this->ignoreISO639_1()
            ? $languageCode
            : substr(
                $languageCode,
                0,
                2,
            );
    }

    /**
     * This method helps other methods to decide whether the language code can be reduced to two characters.
     * If the constant 'BORLABS_COOKIE_IGNORE_ISO_639_1' is not defined, each language code must be reduced to two
     * characters: en_US > en.
     */
    private function ignoreISO639_1(): bool
    {
        return defined('BORLABS_COOKIE_IGNORE_ISO_639_1') && constant('BORLABS_COOKIE_IGNORE_ISO_639_1') === true;
    }
}
