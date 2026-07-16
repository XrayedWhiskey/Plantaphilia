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

namespace Borlabs\Cookie\DtoList\Repository;

use Borlabs\Cookie\Dto\Repository\PageDto;
use Borlabs\Cookie\DtoList\AbstractDtoList;

/**
 * @extends AbstractDtoList<PageDto>
 */
final class PageDtoList extends AbstractDtoList
{
    public const DTO_CLASS = PageDto::class;

    public const UNIQUE_PROPERTY = 'page';

    public function __construct(
        ?array $pageList = null
    ) {
        parent::__construct($pageList);
    }

    public static function __listFromJson(array $data)
    {
        $list = [];

        foreach ($data as $key => $pageData) {
            $page = new PageDto(
                $pageData->page,
                $pageData->queryParameter,
                $pageData->isCurrent,
            );
            $list[$key] = $page;
        }

        return $list;
    }

    public static function __listToJson(array $data)
    {
        $list = [];

        foreach ($data as $key => $page) {
            $list[$key] = PageDto::prepareForJson($page);
        }

        return $list;
    }
}
