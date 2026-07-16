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

use Borlabs\Cookie\Dto\CloudScan\SuggestionDto;
use Borlabs\Cookie\DtoList\AbstractDtoList;

/**
 * @extends AbstractDtoList<SuggestionDto>
 */
final class SuggestionDtoList extends AbstractDtoList
{
    public const DTO_CLASS = SuggestionDto::class;

    public function __construct(
        ?array $suggestionList = null
    ) {
        parent::__construct($suggestionList);
    }

    public static function __listFromJson(array $data)
    {
        $list = [];

        foreach ($data as $key => $suggestionData) {
            $suggestion = new SuggestionDto(
                $suggestionData->packageKey,
                $suggestionData->pages,
            );
            $list[$key] = $suggestion;
        }

        return $list;
    }

    public static function __listToJson(array $data)
    {
        $list = [];

        foreach ($data as $key => $suggestions) {
            $list[$key] = SuggestionDto::prepareForJson($suggestions);
        }

        return $list;
    }
}
