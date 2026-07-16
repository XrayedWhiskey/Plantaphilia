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
 * The **PropertyMapItemDto** class is used as a typed object that is passed within the system.
 *
 * The object contains the information about the affiliation between a property and its data source, a table column. It
 * is used within the **PropertyMapDto** object.
 *
 * @see \Borlabs\Cookie\Dto\Repository\PropertyMapItemDto::$reference
 * @see \Borlabs\Cookie\Dto\Repository\PropertyMapItemDto::$target
 * @see \Borlabs\Cookie\Dto\Repository\PropertyMapDto PropertyMapDto
 */
final class PropertyMapItemDto
{
    /**
     * @var string Name of the property in the model
     */
    public string $reference;

    /**
     * @var string Table column which is affiliated with the property in
     *             {@see \Borlabs\Cookie\Dto\Repository\PropertyMapItemDto::$reference}
     */
    public string $target;

    /**
     * @param string $propertyName name of the property in the model
     * @param string $columnName   table column which is affiliated with the property in
     *                             {@see \Borlabs\Cookie\Dto\Repository\PropertyMapItemDto::$reference}
     */
    public function __construct(string $propertyName, string $columnName)
    {
        $this->reference = $propertyName;
        $this->target = $columnName;
    }
}
