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

namespace Borlabs\Cookie\Dto\LocalScanner;

use Borlabs\Cookie\Dto\AbstractDto;
use Borlabs\Cookie\DtoList\LocalScanner\MatchedHandleDtoList;
use Borlabs\Cookie\DtoList\LocalScanner\MatchedTagDtoList;
use Borlabs\Cookie\DtoList\LocalScanner\UnmatchedHandleDtoList;
use Borlabs\Cookie\DtoList\LocalScanner\UnmatchedTagDtoList;

class ScanResultDto extends AbstractDto
{
    public MatchedHandleDtoList $matchedHandles;

    public MatchedTagDtoList $matchedTags;

    public UnmatchedHandleDtoList $unmatchedHandles;

    public UnmatchedTagDtoList $unmatchedTags;

    public function __construct()
    {
        $this->matchedHandles = new MatchedHandleDtoList();
        $this->matchedTags = new MatchedTagDtoList();
        $this->unmatchedHandles = new UnmatchedHandleDtoList();
        $this->unmatchedTags = new UnmatchedTagDtoList();
    }
}
