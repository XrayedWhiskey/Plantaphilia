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

namespace Borlabs\Cookie\Dto\SetupAssistant;

use Borlabs\Cookie\Dto\AbstractDto;
use Borlabs\Cookie\Enum\PageSelection\KeywordTypeEnum;

final class LanguageSpecificPageUrlByKeywordTypeDto extends AbstractDto
{
    public KeywordTypeEnum $keywordType;

    public string $language;

    public ?string $url = null;

    public function __construct(KeywordTypeEnum $keywordType, string $languageCode, ?string $url = null)
    {
        $this->keywordType = $keywordType;
        $this->language = $languageCode;
        $this->url = $url;
    }
}
