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

namespace Borlabs\Cookie\Repository\CompatibilityPatch;

use Borlabs\Cookie\Dto\Repository\BelongsToRelationDto;
use Borlabs\Cookie\Dto\Repository\PropertyMapDto;
use Borlabs\Cookie\Dto\Repository\PropertyMapItemDto;
use Borlabs\Cookie\Dto\Repository\PropertyMapRelationItemDto;
use Borlabs\Cookie\Model\CompatibilityPatch\CompatibilityPatchModel;
use Borlabs\Cookie\Repository\AbstractRepository;
use Borlabs\Cookie\Repository\Package\PackageRepository;
use Borlabs\Cookie\Repository\RepositoryInterface;

/**
 * @extends AbstractRepository<CompatibilityPatchModel>
 */
final class CompatibilityPatchRepository extends AbstractRepository implements RepositoryInterface
{
    public const MODEL = CompatibilityPatchModel::class;

    public const TABLE = 'borlabs_cookie_compatibility_patches';

    public static function propertyMap(): PropertyMapDto
    {
        return new PropertyMapDto([
            new PropertyMapItemDto('id', 'id'),
            new PropertyMapItemDto('borlabsServicePackageKey', 'borlabs_service_package_key'),
            new PropertyMapItemDto('key', 'key'),
            new PropertyMapItemDto('fileName', 'file_name'),
            new PropertyMapItemDto('hash', 'hash'),
            new PropertyMapRelationItemDto(
                'package',
                new BelongsToRelationDto(
                    PackageRepository::class,
                    'borlabsServicePackageKey',
                    'borlabsServicePackageKey',
                ),
            ),
        ]);
    }

    public function getAll(): array
    {
        return $this->find([], [
            'key' => 'ASC',
        ]);
    }

    public function getByKey(string $key): ?CompatibilityPatchModel
    {
        $data = $this->find(
            [
                'key' => $key,
            ],
        );

        if (isset($data[0]->id) === false) {
            return null;
        }

        return $data[0];
    }
}
