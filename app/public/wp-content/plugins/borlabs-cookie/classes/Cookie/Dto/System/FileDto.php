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

namespace Borlabs\Cookie\Dto\System;

use Borlabs\Cookie\Dto\AbstractDto;

class FileDto extends AbstractDto
{
    public string $fileName;

    public string $fullPath;

    public ?string $hash = null;

    public string $path;

    public ?string $url = null;

    public function __construct(
        string $fileName,
        string $path,
        ?string $hash = null,
        ?string $url = null
    ) {
        $this->fileName = $fileName;
        $this->fullPath = $path . '/' . $fileName;
        $this->hash = $hash;
        $this->path = $path;
        $this->url = $url;
    }
}
