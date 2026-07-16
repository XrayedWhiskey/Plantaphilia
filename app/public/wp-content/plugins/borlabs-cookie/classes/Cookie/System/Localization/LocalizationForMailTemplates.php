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

namespace Borlabs\Cookie\System\Localization;

class LocalizationForMailTemplates
{
    private LocalizationStringService $localizationStringService;

    public function __construct(
        LocalizationStringService $localizationStringService
    ) {
        $this->localizationStringService = $localizationStringService;
    }

    /**
     * Replaces localization tags <translation-key id="key">content</translation-key> with a custom HTML tag for emails.
     *
     * @return mixed
     */
    public function replaceLocalizationTags(callable $fn)
    {
        $original = $this->localizationStringService->replacementTag;
        $this->localizationStringService->replacementTag = '<span style="font-weight:bold;">%s</span>';
        $returnValue = $fn();
        $this->localizationStringService->replacementTag = $original;

        return $returnValue;
    }
}
