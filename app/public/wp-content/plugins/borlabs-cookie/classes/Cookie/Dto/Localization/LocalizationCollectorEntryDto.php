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

namespace Borlabs\Cookie\Dto\Localization;

use Borlabs\Cookie\Dto\AbstractDto;

class LocalizationCollectorEntryDto extends AbstractDto
{
    public string $context;

    public string $domain;

    /**
     * @var class-string
     */
    public string $localizationClassName;

    public string $text;

    public string $translation;

    /**
     * @param class-string $localizationClassName
     */
    public function __construct(
        string $localizationClassName,
        string $text,
        string $context,
        string $domain,
        string $translation
    ) {
        $this->localizationClassName = $localizationClassName;
        $this->text = $text;
        $this->context = $context;
        $this->domain = $domain;
        $this->translation = $translation;
    }
}
