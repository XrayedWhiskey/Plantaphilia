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
use Borlabs\Cookie\Enum\CloudScan\PageFailureTypeEnum;
use Borlabs\Cookie\Enum\CloudScan\PageStatusEnum;

class PageDto extends AbstractDto
{
    public ?PageFailureTypeEnum $failureType = null;

    public PageStatusEnum $status;

    public string $url;

    public function __construct(
        string $url,
        PageStatusEnum $status,
        ?PageFailureTypeEnum $failureType
    ) {
        $this->failureType = $failureType;
        $this->status = $status;
        $this->url = $url;
    }
}
