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

namespace Borlabs\Cookie\DtoList\Translator;

use Borlabs\Cookie\Dto\Translator\LanguageSpecificKeyValueListItemDto;
use Borlabs\Cookie\DtoList\AbstractDtoList;
use Borlabs\Cookie\DtoList\System\KeyValueDtoList;

/**
 * @extends AbstractDtoList<LanguageSpecificKeyValueListItemDto>
 */
final class LanguageSpecificKeyValueDtoList extends AbstractDtoList
{
    public const DTO_CLASS = LanguageSpecificKeyValueListItemDto::class;

    public const UNIQUE_PROPERTY = 'language';

    public function __construct(
        ?array $languageSpecificKeyValueList = null
    ) {
        parent::__construct($languageSpecificKeyValueList);
    }

    public static function __listFromJson(array $data)
    {
        $list = [];

        foreach ($data as $key => $languageSpecificKeyValueListListData) {
            $languageSpecificKeyValueListItem = new LanguageSpecificKeyValueListItemDto(
                $languageSpecificKeyValueListListData->language,
                KeyValueDtoList::fromJson($languageSpecificKeyValueListListData->translations),
            );
            $list[$key] = $languageSpecificKeyValueListItem;
        }

        return $list;
    }

    public static function __listToJson(array $data)
    {
        $list = [];

        foreach ($data as $key => $languageSpecificKeyValueListItem) {
            $list[$key] = LanguageSpecificKeyValueListItemDto::prepareForJson($languageSpecificKeyValueListItem);
        }

        return $list;
    }
}
