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

class ScanRequestResponseDto extends AbstractDto
{
    public string $scanRequestId;

    public string $signedUrl;

    public function __construct(string $scanRequestId, string $signedUrl)
    {
        $this->scanRequestId = $scanRequestId;
        $this->signedUrl = $signedUrl;
    }
}
