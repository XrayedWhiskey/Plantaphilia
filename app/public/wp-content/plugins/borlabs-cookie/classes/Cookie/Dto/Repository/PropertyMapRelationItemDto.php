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
 * The **PropertyMapRelationItemDto** class is used as a typed object that is passed within the system.
 *
 * The object contains the information about the affiliation between a property and its data source, a model.
 * It is used within the **PropertyMapDto** object.
 *
 * @see \Borlabs\Cookie\Dto\Repository\PropertyMapRelationItemDto::$reference
 * @see \Borlabs\Cookie\Dto\Repository\PropertyMapRelationItemDto::$relationInfo
 * @see \Borlabs\Cookie\Dto\Repository\PropertyMapDto PropertyMapDto
 */
final class PropertyMapRelationItemDto
{
    /**
     * @var string Name of the property in the model
     */
    public string $reference;

    /**
     * @var \Borlabs\Cookie\Dto\Repository\AbstractRelationInfoDto see {@see \Borlabs\Cookie\Dto\Repository\AbstractRelationInfoDto}
     */
    public AbstractRelationInfoDto $relationInfo;

    /**
     * @param string                                                 $propertyName name of the property in the model
     * @param \Borlabs\Cookie\Dto\Repository\AbstractRelationInfoDto $relationInfo see
     *                                                                             {@see \Borlabs\Cookie\Dto\Repository\AbstractRelationInfoDto}
     */
    public function __construct(
        string $propertyName,
        AbstractRelationInfoDto $relationInfo
    ) {
        $this->reference = $propertyName;
        $this->relationInfo = $relationInfo;
    }
}
