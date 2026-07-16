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

namespace Borlabs\Cookie\ApiClient\Transformer;

use Borlabs\Cookie\DtoList\News\NewsListDto;

final class NewsListTransformer
{
    private NewsTransformer $newsTransformer;

    public function __construct(NewsTransformer $newsTransformer)
    {
        $this->newsTransformer = $newsTransformer;
    }

    public function toDto(object $newsList): NewsListDto
    {
        return new NewsListDto(
            array_map(
                fn ($news) => $this->newsTransformer->toDto($news),
                (array) $newsList,
            ),
        );
    }
}
