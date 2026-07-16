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

namespace Borlabs\Cookie\Dto\CloudScan;

use Borlabs\Cookie\Dto\AbstractDto;
use Borlabs\Cookie\DtoList\CloudScan\SuggestionPagesDtoList;

class SuggestionDto extends AbstractDto
{
    public string $packageKey;

    public SuggestionPagesDtoList $pages;

    public function __construct(string $packageKey, SuggestionPagesDtoList $pages)
    {
        $this->packageKey = $packageKey;
        $this->pages = $pages;
    }
}
