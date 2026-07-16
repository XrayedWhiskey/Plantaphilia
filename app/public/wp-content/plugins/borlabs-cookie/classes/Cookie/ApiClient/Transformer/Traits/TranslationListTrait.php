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

namespace Borlabs\Cookie\ApiClient\Transformer\Traits;

use Borlabs\Cookie\Support\Searcher;

trait TranslationListTrait
{
    public function getTranslation(array $translationList, string $languageCode): ?object
    {
        $translation = Searcher::findObject($translationList, 'language', $languageCode);

        if ($translation === null && $languageCode !== 'en') {
            // Fallback english
            $translation = Searcher::findObject($translationList, 'language', 'en');
        }

        return $translation;
    }
}
