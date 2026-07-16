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

namespace Borlabs\Cookie\DtoList\News;

use Borlabs\Cookie\Dto\News\NewsDto;
use Borlabs\Cookie\DtoList\AbstractDtoList;

/**
 * @extends AbstractDtoList<NewsDto>
 */
final class NewsListDto extends AbstractDtoList
{
    public const DTO_CLASS = NewsDto::class;

    public const UNIQUE_PROPERTY = 'id';

    public function __construct(
        ?array $newsList = null
    ) {
        parent::__construct($newsList);
    }

    public static function __listFromJson(array $data)
    {
        $list = [];

        foreach ($data as $key => $newsData) {
            $news = new NewsDto(
                $newsData->id,
                $newsData->language,
                $newsData->title,
                $newsData->message,
                $newsData->stamp,
            );
            $list[$key] = $news;
        }

        return $list;
    }

    public static function __listToJson(array $data)
    {
        $list = [];

        foreach ($data as $key => $news) {
            $list[$key] = NewsDto::prepareForJson($news);
        }

        return $list;
    }
}
