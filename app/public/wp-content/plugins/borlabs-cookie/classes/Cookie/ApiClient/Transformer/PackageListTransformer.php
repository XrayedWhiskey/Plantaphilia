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

namespace Borlabs\Cookie\ApiClient\Transformer;

use Borlabs\Cookie\Dto\Package\PackageDto;
use Borlabs\Cookie\DtoList\Package\PackageDtoList;

final class PackageListTransformer
{
    private PackageTransformer $packageTransformer;

    public function __construct(PackageTransformer $packageTransformer)
    {
        $this->packageTransformer = $packageTransformer;
    }

    public function toDto(object $packages): PackageDtoList
    {
        $packageList = new PackageDtoList([]);

        foreach ($packages as $package) {
            $packageList->add(
                new PackageDto(
                    $package->key,
                    $this->packageTransformer->toModel($package),
                ),
            );
        }

        return $packageList;
    }
}
