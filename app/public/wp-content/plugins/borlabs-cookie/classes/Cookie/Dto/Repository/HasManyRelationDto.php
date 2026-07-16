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

class HasManyRelationDto extends AbstractRelationJoinInfoDto
{
    /*
     * Must be bidirectional
     * Always `mappedBy` because it is bidirectional
     */

    public string $mappedBy;

    public function __construct(
        string $repository,
        string $joinProperty,
        string $referencedJoinProperty,
        string $mappedBy
    ) {
        $args = func_get_args();
        parent::__construct(...$args);
        $this->mappedBy = $mappedBy;
    }
}
