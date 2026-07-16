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

namespace Borlabs\Cookie\Dto\Repository;

/**
 * The **PropertyMapDto** class is used as a typed object that is passed within the system.
 *
 * The object contains the information about the affiliation between the properties and their data source and is used
 * by a model repository in its **propertyMap()** method. The data source can be a table column or a model. The object
 * is used by the **RepositoryManager**.
 *
 * Example:
 * <code>
 * public static function propertyMap(): PropertyMapDto
 * {
 *     return new PropertyMapDto(
 *         [
 *             new PropertyMapItemDto('id', 'id'),
 *             new PropertyMapItemDto('firstName', 'first_name'),
 *             new PropertyMapItemDto('lastName', 'last_name'),
 *             new PropertyMapRelationItemDto(
 *                 'ownedCars', new AbstractRelationInfoDto(
 *                     ServiceLocationRepository::class, [new PropertyRelationDto('id', 'ownerId')]
 *                 )
 *             ),
 *         ]
 *     );
 * }
 * </code>
 *
 * @see \Borlabs\Cookie\Dto\Repository\PropertyMapDto::$map
 * @see \Borlabs\Cookie\Dto\Repository\PropertyMapItemDto Date source: table column
 * @see \Borlabs\Cookie\Dto\Repository\PropertyMapRelationItemDto Data source: model
 * @see \Borlabs\Cookie\Repository\AbstractRepository AbstractRepository
 */
final class PropertyMapDto
{
    /**
     * @see \Borlabs\Cookie\Dto\Repository\PropertyMapDto More information about the object.
     *
     * @var \Borlabs\Cookie\Dto\Repository\PropertyMapItemDto[]|\Borlabs\Cookie\Dto\Repository\PropertyMapRelationItemDto[] The array contains the objects with the affiliation info
     */
    public $map;

    /**
     * PropertyMapDto constructor.
     *
     * @see \Borlabs\Cookie\Dto\Repository\PropertyMapDto More information about the object.
     *
     * @param array<\Borlabs\Cookie\Dto\Repository\PropertyMapItemDto|\Borlabs\Cookie\Dto\Repository\PropertyMapRelationItemDto> $propertyMapItems The array contains the objects with the affiliation info
     */
    public function __construct(array $propertyMapItems)
    {
        $this->map = $propertyMapItems;
    }
}
