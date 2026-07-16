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
use Borlabs\Cookie\Enum\Package\PackageTypeEnum;

class PackageSuggestionDto extends AbstractDto
{
    public int $id;

    public string $name;

    public PackageTypeEnum $type;

    public function __construct(int $id, string $name, PackageTypeEnum $type)
    {
        $this->id = $id;
        $this->name = $name;
        $this->type = $type;
    }
}
