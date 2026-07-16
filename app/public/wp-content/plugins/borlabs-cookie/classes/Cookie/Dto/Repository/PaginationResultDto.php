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
use Borlabs\Cookie\DtoList\Repository\PageDtoList;
use Borlabs\Cookie\Model\AbstractModel;

final class PaginationResultDto extends AbstractDto
{
    public int $currentPage;

    /**
     * @var AbstractModel[]
     */
    public array $data;

    public ?string $firstPageQueryParameter;

    public int $from;

    public int $lastPage;

    public ?string $lastPageQueryParameter = null;

    public ?string $nextPageQueryParameter = null;

    public PageDtoList $pages;

    public int $perPage;

    public ?string $previousPageQueryParameter = null;

    public int $to;

    public int $total;
}
