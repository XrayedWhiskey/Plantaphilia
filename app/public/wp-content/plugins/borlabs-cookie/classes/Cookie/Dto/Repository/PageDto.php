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

use Borlabs\Cookie\Dto\AbstractDto;

final class PageDto extends AbstractDto
{
    public bool $isCurrent = false;

    public int $page;

    public string $queryParameter;

    public function __construct(int $page, string $queryParameter, bool $isCurrent = false)
    {
        $this->isCurrent = $isCurrent;
        $this->page = $page;
        $this->queryParameter = $queryParameter;
    }
}
