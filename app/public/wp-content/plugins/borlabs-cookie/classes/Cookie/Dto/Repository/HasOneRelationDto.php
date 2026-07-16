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

class HasOneRelationDto extends AbstractRelationInfoDto
{
    public string $mappedBy;

    public function __construct(
        string $repository,
        string $mappedBy
    ) {
        parent::__construct($repository);
        $this->mappedBy = $mappedBy;
    }
}
