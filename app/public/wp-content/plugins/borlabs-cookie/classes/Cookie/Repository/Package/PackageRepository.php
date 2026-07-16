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

namespace Borlabs\Cookie\Repository\Package;

use Borlabs\Cookie\Dto\Repository\HasManyRelationDto;
use Borlabs\Cookie\Dto\Repository\PropertyMapDto;
use Borlabs\Cookie\Dto\Repository\PropertyMapItemDto;
use Borlabs\Cookie\Dto\Repository\PropertyMapRelationItemDto;
use Borlabs\Cookie\Enum\Package\PackageTypeEnum;
use Borlabs\Cookie\Model\Package\PackageModel;
use Borlabs\Cookie\Repository\AbstractRepository;
use Borlabs\Cookie\Repository\CompatibilityPatch\CompatibilityPatchRepository;
use Borlabs\Cookie\Repository\ContentBlocker\ContentBlockerRepository;
use Borlabs\Cookie\Repository\Expression\BinaryOperatorExpression;
use Borlabs\Cookie\Repository\Expression\LiteralExpression;
use Borlabs\Cookie\Repository\Expression\ModelFieldNameExpression;
use Borlabs\Cookie\Repository\Expression\NullExpression;
use Borlabs\Cookie\Repository\Provider\ProviderRepository;
use Borlabs\Cookie\Repository\RepositoryInterface;
use Borlabs\Cookie\Repository\ScriptBlocker\ScriptBlockerRepository;
use Borlabs\Cookie\Repository\Service\ServiceRepository;
use Borlabs\Cookie\Repository\StyleBlocker\StyleBlockerRepository;

/**
 * @extends AbstractRepository<PackageModel>
 */
final class PackageRepository extends AbstractRepository implements RepositoryInterface
{
    public const MODEL = PackageModel::class;

    public const TABLE = 'borlabs_cookie_packages';

    public static function propertyMap(): PropertyMapDto
    {
        return new PropertyMapDto([
            new PropertyMapItemDto('id', 'id'),
            new PropertyMapItemDto('borlabsServicePackageKey', 'borlabs_service_package_key'),
            new PropertyMapItemDto('borlabsServicePackageSuccessorKey', 'borlabs_service_package_successor_key'),
            new PropertyMapItemDto('borlabsServicePackageVersion', 'borlabs_service_package_version'),
            new PropertyMapItemDto('borlabsServiceUpdatedAt', 'borlabs_service_updated_at'),
            new PropertyMapItemDto('autoUpdateEnabled', 'auto_update_enabled'),
            new PropertyMapItemDto('autoUpdateOverwriteCode', 'auto_update_overwrite_code'),
            new PropertyMapItemDto('autoUpdateOverwriteTranslation', 'auto_update_overwrite_translation'),
            new PropertyMapItemDto('components', 'components'),
            new PropertyMapItemDto('installedAt', 'installed_at'),
            new PropertyMapItemDto('isDeprecated', 'is_deprecated'),
            new PropertyMapItemDto('isFeatured', 'is_featured'),
            new PropertyMapItemDto('name', 'name'),
            new PropertyMapItemDto('requiredBorlabsCookieVersion', 'required_borlabs_cookie_version'),
            new PropertyMapItemDto('thumbnail', 'thumbnail'),
            new PropertyMapItemDto('translations', 'translations'),
            new PropertyMapItemDto('type', 'type'),
            new PropertyMapItemDto('updatedAt', 'updated_at'),
            new PropertyMapItemDto('version', 'version'),
            new PropertyMapRelationItemDto(
                'contentBlockers',
                new HasManyRelationDto(
                    ContentBlockerRepository::class,
                    'borlabsServicePackageKey',
                    'borlabsServicePackageKey',
                    'package',
                ),
            ),
            new PropertyMapRelationItemDto(
                'scriptBlockers',
                new HasManyRelationDto(
                    ScriptBlockerRepository::class,
                    'borlabsServicePackageKey',
                    'borlabsServicePackageKey',
                    'package',
                ),
            ),
            new PropertyMapRelationItemDto(
                'services',
                new HasManyRelationDto(
                    ServiceRepository::class,
                    'borlabsServicePackageKey',
                    'borlabsServicePackageKey',
                    'package',
                ),
            ),
            new PropertyMapRelationItemDto(
                'styleBlockers',
                new HasManyRelationDto(
                    StyleBlockerRepository::class,
                    'borlabsServicePackageKey',
                    'borlabsServicePackageKey',
                    'package',
                ),
            ),
            new PropertyMapRelationItemDto(
                'compatibilityPatches',
                new HasManyRelationDto(
                    CompatibilityPatchRepository::class,
                    'borlabsServicePackageKey',
                    'borlabsServicePackageKey',
                    'package',
                ),
            ),
            new PropertyMapRelationItemDto(
                'providers',
                new HasManyRelationDto(
                    ProviderRepository::class,
                    'borlabsServicePackageKey',
                    'borlabsServicePackageKey',
                    'package',
                ),
            ),
        ]);
    }

    /**
     * @return array<PackageModel>
     */
    public function getAll(?string $type = null): array
    {
        $where = [];

        if ($type !== null && PackageTypeEnum::hasValue($type)) {
            $where['type'] = $type;
        }

        return $this->find($where, [
            'name' => 'ASC',
        ]);
    }

    public function getByPackageKey(string $borlabsServicePackageKey, array $withRelations = []): ?PackageModel
    {
        $data = $this->find(
            [
                'borlabsServicePackageKey' => $borlabsServicePackageKey,
            ],
            [],
            [],
            $withRelations,
        );

        if (isset($data[0]->id) === false) {
            return null;
        }

        return $data[0];
    }

    /**
     * @return array<PackageModel>
     */
    public function getInstalledPackages(): array
    {
        return $this->find([
            new BinaryOperatorExpression(
                new ModelFieldNameExpression('installedAt'),
                'IS NOT',
                new NullExpression(),
            ),
        ]);
    }

    /**
     * @return array<PackageModel>
     */
    public function getNotInstalledPackages(): array
    {
        return $this->find(
            [
                'installedAt' => null,
            ],
            [
                'name' => 'ASC',
            ],
        );
    }

    /**
     * @param null|bool $autoUpdateEnabled optional; Default: `null`;
     *                                     `true`: Only return packages with auto update enabled,
     *                                     `false`: Only return packages with auto update disabled,
     *                                     `null`: Return all packages
     *
     * @return array<PackageModel>
     */
    public function getUpdatablePackages(?bool $autoUpdateEnabled = null): array
    {
        $where = [
            new BinaryOperatorExpression(
                new ModelFieldNameExpression('borlabsServicePackageVersion'),
                '!=',
                new ModelFieldNameExpression('version'),
            ),
            new BinaryOperatorExpression(
                new ModelFieldNameExpression('installedAt'),
                'IS NOT',
                new NullExpression(),
            ),
        ];

        if (isset($autoUpdateEnabled)) {
            $where[] =
                new BinaryOperatorExpression(
                    new ModelFieldNameExpression('autoUpdateEnabled'),
                    '=',
                    new LiteralExpression($autoUpdateEnabled),
                );
        }

        return $this->find($where);
    }

    public function removeNotInstalledPackages(): bool
    {
        $packages = $this->find(['installedAt' => null]);

        foreach ($packages as $package) {
            $this->delete($package);
        }

        return true;
    }
}
