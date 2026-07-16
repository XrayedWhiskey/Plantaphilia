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
use Borlabs\Cookie\DtoList\CloudScan\CookieDtoList;
use Borlabs\Cookie\DtoList\CloudScan\ExternalResourceDtoList;
use Borlabs\Cookie\DtoList\CloudScan\PageDtoList;
use Borlabs\Cookie\DtoList\CloudScan\SuggestionDtoList;
use Borlabs\Cookie\Enum\CloudScan\CloudScanStatusEnum;
use Borlabs\Cookie\Enum\CloudScan\CloudScanTypeEnum;
use DateTime;

class ScanResponseDto extends AbstractDto
{
    public ?CookieDtoList $cookies = null;

    public ?ExternalResourceDtoList $externalResources = null;

    public ?DateTime $finishedAt = null;

    public string $id;

    public PageDtoList $pages;

    public CloudScanStatusEnum $status;

    public ?SuggestionDtoList $suggestions = null;

    public CloudScanTypeEnum $type;
}
