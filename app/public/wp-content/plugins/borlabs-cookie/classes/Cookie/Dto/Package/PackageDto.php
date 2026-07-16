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

namespace Borlabs\Cookie\Dto\Package;

use Borlabs\Cookie\Dto\AbstractDto;
use Borlabs\Cookie\Model\Package\PackageModel;

class PackageDto extends AbstractDto
{
    public string $key;

    public PackageModel $packageModel;

    public function __construct(
        string $key,
        PackageModel $packageModel
    ) {
        $this->key = $key;
        $this->packageModel = $packageModel;
    }
}
