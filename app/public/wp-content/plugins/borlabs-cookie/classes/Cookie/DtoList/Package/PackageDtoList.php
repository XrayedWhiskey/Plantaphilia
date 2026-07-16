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

namespace Borlabs\Cookie\DtoList\Package;

use Borlabs\Cookie\Dto\Package\PackageDto;
use Borlabs\Cookie\DtoList\AbstractDtoList;

/**
 * @extends AbstractDtoList<PackageDto>
 */
final class PackageDtoList extends AbstractDtoList
{
    public const DTO_CLASS = PackageDto::class;

    public function __construct(?array $installationStatusList)
    {
        parent::__construct($installationStatusList);
    }

    public static function __listFromJson(array $data)
    {
        $list = [];

        foreach ($data as $key => $packageData) {
            $package = new PackageDto(
                $packageData->key,
                $packageData->packageModel,
            );
            $list[$key] = $package;
        }

        return $list;
    }

    public static function __listToJson(array $data)
    {
        $list = [];

        foreach ($data as $key => $packages) {
            $list[$key] = PackageDto::prepareForJson($packages);
        }

        return $list;
    }
}
