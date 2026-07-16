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

use Borlabs\Cookie\Dto\LocalScanner\UnmatchedTagDto;
use Borlabs\Cookie\DtoList\AbstractDtoList;

/**
 * @extends AbstractDtoList<UnmatchedTagDto>
 */
final class UnmatchedTagDtoList extends AbstractDtoList
{
    public const DTO_CLASS = UnmatchedTagDto::class;

    public const UNIQUE_PROPERTY = 'tag';

    public function __construct(
        ?array $unmatchedTagList = null
    ) {
        parent::__construct($unmatchedTagList);
    }

    public static function __listFromJson(array $data)
    {
        $list = [];

        foreach ($data as $key => $unmatchedTagData) {
            $unmatchedTag = new UnmatchedTagDto(
                $unmatchedTagData->type,
                $unmatchedTagData->tag,
            );
            $list[$key] = $unmatchedTag;
        }

        return $list;
    }

    public static function __listToJson(array $data)
    {
        $list = [];

        foreach ($data as $key => $unmatchedTag) {
            $list[$key] = UnmatchedTagDto::prepareForJson($unmatchedTag);
        }

        return $list;
    }
}
