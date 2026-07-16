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
use Borlabs\Cookie\DtoList\CloudScan\CookieExampleDtoList;

class CookieDto extends AbstractDto
{
    public CookieExampleDtoList $examples;

    public string $hostname;

    public ?int $lifetime = null;

    public string $name;

    public ?string $packageKey;

    public string $path;

    public function __construct(
        string $name,
        string $hostname,
        string $path,
        CookieExampleDtoList $examples,
        ?int $lifetime = null,
        ?string $packageKey = null
    ) {
        $this->examples = $examples;
        $this->hostname = $hostname;
        $this->lifetime = $lifetime;
        $this->name = $name;
        $this->packageKey = $packageKey;
        $this->path = $path;
    }
}
