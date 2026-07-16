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

namespace Borlabs\Cookie\Repository\CloudScan;

use Borlabs\Cookie\Dto\Repository\BelongsToRelationDto;
use Borlabs\Cookie\Dto\Repository\PropertyMapDto;
use Borlabs\Cookie\Dto\Repository\PropertyMapItemDto;
use Borlabs\Cookie\Dto\Repository\PropertyMapRelationItemDto;
use Borlabs\Cookie\Model\CloudScan\CloudScanModel;
use Borlabs\Cookie\Model\CloudScan\CloudScanSuggestionModel;
use Borlabs\Cookie\Repository\AbstractRepositoryWithLanguage;
use Borlabs\Cookie\Repository\RepositoryInterface;

/**
 * @extends AbstractRepositoryWithLanguage<CloudScanSuggestionModel>
 */
final class CloudScanSuggestionRepository extends AbstractRepositoryWithLanguage implements RepositoryInterface
{
    public const MODEL = CloudScanSuggestionModel::class;

    public const TABLE = 'borlabs_cookie_cloud_scan_suggests';

    protected const UNDELETABLE = false;

    public static function propertyMap(): PropertyMapDto
    {
        return new PropertyMapDto([
            new PropertyMapItemDto('id', 'id'),
            new PropertyMapItemDto('cloudScanId', 'cloud_scan_id'),
            new PropertyMapItemDto('borlabsServicePackageKey', 'borlabs_service_package_key'),
            new PropertyMapItemDto('pages', 'pages'),
            new PropertyMapRelationItemDto(
                'cloudScan',
                new BelongsToRelationDto(
                    CloudScanRepository::class,
                    'cloudScanId',
                    'id',
                    'suggestions',
                ),
            ),
        ]);
    }

    /**
     * @return array<CloudScanSuggestionModel>
     */
    public function getByCloudScan(CloudScanModel $cloudScanModel): array
    {
        return $this->getByCloudScanId($cloudScanModel->id);
    }

    public function getByCloudScanId(int $id): array
    {
        return $this->find([
            'cloudScanId' => $id,
        ]);
    }
}
