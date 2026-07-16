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

abstract class AbstractRelationJoinInfoDto extends AbstractRelationInfoDto
{
    public string $joinProperty;

    public string $referencedJoinProperty;

    public function __construct(
        string $repository,
        string $joinProperty,
        string $referencedJoinProperty
    ) {
        parent::__construct($repository);
        $this->joinProperty = $joinProperty;
        $this->referencedJoinProperty = $referencedJoinProperty;
    }
}
