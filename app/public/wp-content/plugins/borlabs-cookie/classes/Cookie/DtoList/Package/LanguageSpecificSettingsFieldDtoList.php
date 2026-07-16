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

namespace Borlabs\Cookie\DtoList\Package;

use Borlabs\Cookie\Dto\Package\LanguageSpecificSettingsFieldListItemDto;
use Borlabs\Cookie\DtoList\AbstractDtoList;
use Borlabs\Cookie\DtoList\System\SettingsFieldDtoList;

/**
 * @extends AbstractDtoList<LanguageSpecificSettingsFieldListItemDto>
 */
final class LanguageSpecificSettingsFieldDtoList extends AbstractDtoList
{
    public const DTO_CLASS = LanguageSpecificSettingsFieldListItemDto::class;

    public const UNIQUE_PROPERTY = 'language';

    public function __construct(
        ?array $languageSpecificSettingsFieldList = null
    ) {
        parent::__construct($languageSpecificSettingsFieldList);
    }

    public static function __listFromJson(array $data)
    {
        $list = [];

        foreach ($data as $key => $languageSpecificSettingsFieldListData) {
            $languageSpecificSettingsFieldListItem = new LanguageSpecificSettingsFieldListItemDto(
                $languageSpecificSettingsFieldListData->language,
                SettingsFieldDtoList::fromJson($languageSpecificSettingsFieldListData->settingsFields),
            );
            $list[$key] = $languageSpecificSettingsFieldListItem;
        }

        return $list;
    }

    public static function __listToJson(array $data)
    {
        $list = [];

        foreach ($data as $key => $languageSpecificSettingsFieldListItem) {
            $list[$key] = LanguageSpecificSettingsFieldListItemDto::prepareForJson($languageSpecificSettingsFieldListItem);
        }

        return $list;
    }
}
