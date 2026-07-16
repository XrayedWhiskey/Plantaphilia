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

namespace Borlabs\Cookie\DtoList\Telemetry;

use Borlabs\Cookie\Dto\Telemetry\PackageDto;
use Borlabs\Cookie\DtoList\AbstractDtoList;

class PackageDtoList extends AbstractDtoList
{
    public const DTO_CLASS = PackageDto::class;

    /**
     * @var array<\Borlabs\Cookie\Dto\Telemetry\PackageDto>
     */
    public array $list = [];

    public function __construct(
        ?array $packageList = null
    ) {
        parent::__construct($packageList);
    }

    public static function __listFromJson(array $data)
    {
        $list = [];

        foreach ($data as $key => $packageData) {
            $package = new PackageDto();
            $package->key = $packageData->key;
            $package->name = $packageData->name;
            $package->version = $packageData->version;

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
