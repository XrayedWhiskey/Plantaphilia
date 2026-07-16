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

namespace Borlabs\Cookie\DtoList\LocalScanner;

use Borlabs\Cookie\Dto\LocalScanner\MatchedTagDto;
use Borlabs\Cookie\DtoList\AbstractDtoList;

/**
 * @extends AbstractDtoList<MatchedTagDto>
 */
final class MatchedTagDtoList extends AbstractDtoList
{
    public const DTO_CLASS = MatchedTagDto::class;

    public const UNIQUE_PROPERTY = 'tag';

    public function __construct(
        ?array $matchedTagList = null
    ) {
        parent::__construct($matchedTagList);
    }

    public static function __listFromJson(array $data)
    {
        $list = [];

        foreach ($data as $key => $matchedTagData) {
            $matchedTag = new MatchedTagDto(
                $matchedTagData->type,
                $matchedTagData->phrase,
                $matchedTagData->tag,
            );
            $list[$key] = $matchedTag;
        }

        return $list;
    }

    public static function __listToJson(array $data)
    {
        $list = [];

        foreach ($data as $key => $matchedTag) {
            $list[$key] = MatchedTagDto::prepareForJson($matchedTag);
        }

        return $list;
    }
}
