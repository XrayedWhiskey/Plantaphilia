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

namespace Borlabs\Cookie\DtoList\CloudScan;

use Borlabs\Cookie\Dto\CloudScan\SuggestionPageDto;
use Borlabs\Cookie\DtoList\AbstractDtoList;

/**
 * @extends AbstractDtoList<SuggestionPageDto>
 */
final class SuggestionPagesDtoList extends AbstractDtoList
{
    public const DTO_CLASS = SuggestionPageDto::class;

    public static function __listFromJson(array $data)
    {
        $list = [];

        foreach ($data as $key => $suggestionPageData) {
            $suggestionPage = new SuggestionPageDto(
                $suggestionPageData->url,
            );
            $list[$key] = $suggestionPage;
        }

        return $list;
    }

    public static function __listToJson(array $data)
    {
        $list = [];

        foreach ($data as $key => $suggestionPages) {
            $list[$key] = SuggestionPageDto::prepareForJson($suggestionPages);
        }

        return $list;
    }
}
