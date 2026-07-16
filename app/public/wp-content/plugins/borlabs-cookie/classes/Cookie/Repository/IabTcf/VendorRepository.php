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

namespace Borlabs\Cookie\Repository\IabTcf;

use Borlabs\Cookie\Dto\Repository\PropertyMapDto;
use Borlabs\Cookie\Dto\Repository\PropertyMapItemDto;
use Borlabs\Cookie\Model\AbstractModel;
use Borlabs\Cookie\Model\IabTcf\VendorModel;
use Borlabs\Cookie\Repository\AbstractRepository;
use Borlabs\Cookie\Repository\RepositoryInterface;

/**
 * @extends AbstractRepository<VendorModel>
 */
class VendorRepository extends AbstractRepository implements RepositoryInterface
{
    public const MODEL = VendorModel::class;

    public const TABLE = 'borlabs_cookie_iab_tcf_vendors';

    public static function propertyMap(): PropertyMapDto
    {
        return new PropertyMapDto([
            new PropertyMapItemDto('id', 'id'),
            new PropertyMapItemDto('vendorId', 'vendor_id'),
            new PropertyMapItemDto('cookieMaxAgeSeconds', 'cookie_max_age_seconds'),
            new PropertyMapItemDto('dataDeclaration', 'data_declaration'),
            new PropertyMapItemDto('dataRetention', 'data_retention'),
            new PropertyMapItemDto('deviceStorageDisclosureUrl', 'device_storage_disclosure_url'),
            new PropertyMapItemDto('features', 'features'),
            new PropertyMapItemDto('legIntPurposes', 'leg_int_purposes'),
            new PropertyMapItemDto('name', 'name'),
            new PropertyMapItemDto('purposes', 'purposes'),
            new PropertyMapItemDto('specialFeatures', 'special_features'),
            new PropertyMapItemDto('specialPurposes', 'special_purposes'),
            new PropertyMapItemDto('urls', 'urls'),
            new PropertyMapItemDto('usesCookies', 'uses_cookies'),
            new PropertyMapItemDto('usesNonCookieAccess', 'uses_non_cookie_access'),
            new PropertyMapItemDto('status', 'status'),
        ]);
    }

    public function deactivateAll(): void
    {
        $allModels = $this->getAll();
        /** @var \Borlabs\Cookie\Model\IabTcf\VendorModel $vendor */
        foreach ($allModels as $vendor) {
            if ($vendor->status === true) {
                $vendor->status = false;
                $this->update($vendor);
            }
        }
    }

    /**
     * @return array<VendorModel>
     */
    public function getAll(): array
    {
        return $this->find(
            [],
            [
                'vendorId' => 'ASC',
            ],
        );
    }

    public function getAllActive(): array
    {
        return $this->find(['status' => true]);
    }

    public function getByVendorId(int $id): ?AbstractModel
    {
        return $this->find(['vendorId' => $id])[0] ?? null;
    }

    /**
     * @throws \Borlabs\Cookie\Exception\UnexpectedRepositoryOperationException
     */
    public function switchStatus(int $id): void
    {
        $model = $this->findByIdOrFail($id);
        $model->status = !$model->status;
        $this->update($model);
    }
}
