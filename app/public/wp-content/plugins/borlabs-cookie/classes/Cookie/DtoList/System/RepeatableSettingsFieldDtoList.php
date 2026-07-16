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

namespace Borlabs\Cookie\DtoList\System;

use Borlabs\Cookie\Dto\System\RepeatableSettingsFieldDto;
use Borlabs\Cookie\Dto\System\RepeatableSettingsFieldTranslationDto;
use Borlabs\Cookie\DtoList\AbstractDtoList;

/**
 * @extends AbstractDtoList<RepeatableSettingsFieldDto>
 */
final class RepeatableSettingsFieldDtoList extends AbstractDtoList
{
    public const DTO_CLASS = RepeatableSettingsFieldDto::class;

    public const UNIQUE_PROPERTY = 'key';

    public function __construct(
        ?array $repeatableSettingsFieldList = null
    ) {
        parent::__construct($repeatableSettingsFieldList);
    }

    public static function __listFromJson(array $data)
    {
        $list = [];

        foreach ($data as $key => $repeatableSettingsField) {
            $list[$key] = new RepeatableSettingsFieldDto(
                $repeatableSettingsField->key,
                SettingsFieldDtoList::fromJson($repeatableSettingsField->settingsFieldsDefinition),
                SettingsFieldDtoListList::fromJson($repeatableSettingsField->settingsFieldsListList),
                RepeatableSettingsFieldTranslationDto::fromJson($repeatableSettingsField->translation),
                $repeatableSettingsField->position,
            );
        }

        return $list;
    }

    public static function __listToJson(array $data)
    {
        $list = [];

        foreach ($data as $key => $repeatableSettingsField) {
            $list[$key] = RepeatableSettingsFieldDto::prepareForJson($repeatableSettingsField);
        }

        return $list;
    }
}
