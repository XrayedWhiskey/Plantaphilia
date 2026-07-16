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

namespace Borlabs\Cookie\System\WordPressGlobalFunctions;

use Borlabs\Cookie\System\Localization\LocalizationCollectorService;
use Borlabs\Cookie\System\Localization\LocalizationStringService;

use function _x;

class WordPressGlobalFunctionUnderscoreXService
{
    private LocalizationCollectorService $localizationCollectorService;

    private LocalizationStringService $localizationStringService;

    public function __construct(
        LocalizationStringService $localizationStringService,
        LocalizationCollectorService $localizationCollectorService
    ) {
        $this->localizationStringService = $localizationStringService;
        $this->localizationCollectorService = $localizationCollectorService;
    }

    public function call(string $text, string $context, string $domain = 'default'): string
    {
        $translation = _x($text, $context, $domain);
        $this->localizationCollectorService->collect($text, $context, $domain, $translation);

        return $this->localizationStringService->replaceTags($translation);
    }
}
