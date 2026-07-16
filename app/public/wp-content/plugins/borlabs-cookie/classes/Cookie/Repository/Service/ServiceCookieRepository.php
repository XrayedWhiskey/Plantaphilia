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

namespace Borlabs\Cookie\Repository\Service;

use Borlabs\Cookie\Dto\Repository\BelongsToRelationDto;
use Borlabs\Cookie\Dto\Repository\PropertyMapDto;
use Borlabs\Cookie\Dto\Repository\PropertyMapItemDto;
use Borlabs\Cookie\Dto\Repository\PropertyMapRelationItemDto;
use Borlabs\Cookie\Model\Service\ServiceCookieModel;
use Borlabs\Cookie\Repository\AbstractRepository;
use Borlabs\Cookie\Repository\RepositoryInterface;

/**
 * @extends AbstractRepository<ServiceCookieModel>
 */
final class ServiceCookieRepository extends AbstractRepository implements RepositoryInterface
{
    public const MODEL = ServiceCookieModel::class;

    public const TABLE = 'borlabs_cookie_service_cookies';

    public static function propertyMap(): PropertyMapDto
    {
        return new PropertyMapDto([
            new PropertyMapItemDto('id', 'id'),
            new PropertyMapItemDto('serviceId', 'service_id'),
            new PropertyMapItemDto('description', 'description'),
            new PropertyMapItemDto('hostname', 'hostname'),
            new PropertyMapItemDto('lifetime', 'lifetime'),
            new PropertyMapItemDto('name', 'name'),
            new PropertyMapItemDto('path', 'path'),
            new PropertyMapItemDto('purpose', 'purpose'),
            new PropertyMapItemDto('type', 'type'),
            new PropertyMapRelationItemDto(
                'service',
                new BelongsToRelationDto(
                    ServiceRepository::class,
                    'serviceId',
                    'id',
                    'serviceCookies',
                ),
            ),
        ]);
    }
}
