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
use Borlabs\Cookie\DtoList\CloudScan\ExternalResourceExampleDtoList;

class ExternalResourceDto extends AbstractDto
{
    public ExternalResourceExampleDtoList $examples;

    public string $hostname;

    public ?string $packageKey;

    public function __construct(
        string $hostname,
        ExternalResourceExampleDtoList $examples,
        ?string $packageKey = null
    ) {
        $this->examples = $examples;
        $this->hostname = $hostname;
        $this->packageKey = $packageKey;
    }
}
