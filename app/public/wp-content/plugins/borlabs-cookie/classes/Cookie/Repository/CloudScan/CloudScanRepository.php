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

use Borlabs\Cookie\Dto\Repository\HasManyRelationDto;
use Borlabs\Cookie\Dto\Repository\PropertyMapDto;
use Borlabs\Cookie\Dto\Repository\PropertyMapItemDto;
use Borlabs\Cookie\Dto\Repository\PropertyMapRelationItemDto;
use Borlabs\Cookie\Enum\CloudScan\CloudScanStatusEnum;
use Borlabs\Cookie\Model\CloudScan\CloudScanModel;
use Borlabs\Cookie\Repository\AbstractRepositoryWithLanguage;
use Borlabs\Cookie\Repository\RepositoryInterface;

/**
 * @extends AbstractRepositoryWithLanguage<CloudScanModel>
 */
final class CloudScanRepository extends AbstractRepositoryWithLanguage implements RepositoryInterface
{
    public const MODEL = CloudScanModel::class;

    public const TABLE = 'borlabs_cookie_cloud_scans';

    protected const UNDELETABLE = false;

    public static function propertyMap(): PropertyMapDto
    {
        return new PropertyMapDto([
            new PropertyMapItemDto('id', 'id'),
            new PropertyMapItemDto('createdAt', 'created_at'),
            new PropertyMapItemDto('externalId', 'external_id'),
            new PropertyMapItemDto('pages', 'pages'),
            new PropertyMapItemDto('status', 'status'),
            new PropertyMapItemDto('type', 'type'),
            new PropertyMapRelationItemDto(
                'cookies',
                new HasManyRelationDto(
                    CloudScanCookieRepository::class,
                    'id',
                    'cloudScanId',
                    'cloudScan',
                ),
            ),
            new PropertyMapRelationItemDto(
                'externalResources',
                new HasManyRelationDto(
                    CloudScanExternalResourceRepository::class,
                    'id',
                    'cloudScanId',
                    'cloudScan',
                ),
            ),
            new PropertyMapRelationItemDto(
                'suggestions',
                new HasManyRelationDto(
                    CloudScanSuggestionRepository::class,
                    'id',
                    'cloudScanId',
                    'cloudScan',
                ),
            ),
        ]);
    }

    /**
     * @return array<CloudScanModel>
     */
    public function getAll(): array
    {
        return $this->find(
            [],
            [
                'createdAt' => 'DESC',
            ],
        );
    }

    /**
     * @return array<CloudScanModel>
     */
    public function getAllOfStatus(CloudScanStatusEnum $status): array
    {
        return $this->find(
            [
                'status' => $status->value,
            ],
            [
                'createdAt' => 'DESC',
            ],
        );
    }
}
