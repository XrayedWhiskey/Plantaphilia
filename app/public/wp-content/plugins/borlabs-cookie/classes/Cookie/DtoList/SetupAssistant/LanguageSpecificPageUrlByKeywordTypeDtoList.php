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

namespace Borlabs\Cookie\DtoList\SetupAssistant;

use Borlabs\Cookie\Dto\SetupAssistant\LanguageSpecificPageUrlByKeywordTypeDto;
use Borlabs\Cookie\DtoList\AbstractDtoList;
use Borlabs\Cookie\Enum\PageSelection\KeywordTypeEnum;

/**
 * @extends AbstractDtoList<LanguageSpecificPageUrlByKeywordTypeDto>
 */
final class LanguageSpecificPageUrlByKeywordTypeDtoList extends AbstractDtoList
{
    public const DTO_CLASS = LanguageSpecificPageUrlByKeywordTypeDto::class;

    public function __construct(
        ?array $languageSpecificPageUrlByKeywordTypeList = null
    ) {
        parent::__construct($languageSpecificPageUrlByKeywordTypeList);
    }

    public static function __listFromJson(array $data)
    {
        $list = [];

        foreach ($data as $key => $languageSpecificPageUrlByKeywordTypeData) {
            $entry = new LanguageSpecificPageUrlByKeywordTypeDto(
                KeywordTypeEnum::fromValue($languageSpecificPageUrlByKeywordTypeData->keywordType),
                $languageSpecificPageUrlByKeywordTypeData->language,
                $languageSpecificPageUrlByKeywordTypeData->url,
            );
            $list[$key] = $entry;
        }

        return $list;
    }

    public static function __listToJson(array $data)
    {
        $list = [];

        foreach ($data as $key => $languageSpecificPageUrlByKeywordType) {
            $list[$key] = LanguageSpecificPageUrlByKeywordTypeDto::prepareForJson($languageSpecificPageUrlByKeywordType);
        }

        return $list;
    }
}
