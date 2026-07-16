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
use Borlabs\Cookie\Enum\CloudScan\CloudScanStatusEnum;

class ScanResultDto extends AbstractDto
{
    public int $failedPagesCount;

    public int $finishedPagesCount;

    public int $scanningPagesCount;

    public CloudScanStatusEnum $status;

    public function __construct(CloudScanStatusEnum $status, int $failedPagesCount, int $finishedPagesCount, int $scanningPagesCount)
    {
        $this->status = $status;
        $this->failedPagesCount = $failedPagesCount;
        $this->finishedPagesCount = $finishedPagesCount;
        $this->scanningPagesCount = $scanningPagesCount;
    }
}
