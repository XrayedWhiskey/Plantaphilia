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

class BelongsToRelationDto extends AbstractRelationJoinInfoDto
{
    /*
     * Can be unidirectional
     * It is always the owning side
     * Optional `inversedBy` if it is bidirectional
     */

    public ?string $inversedBy = null;

    public function __construct(
        string $repository,
        string $joinProperty,
        string $referencedJoinProperty = 'id',
        ?string $inversedBy = null
    ) {
        $args = func_get_args();
        parent::__construct(...$args);
        $this->inversedBy = $inversedBy;
    }
}
