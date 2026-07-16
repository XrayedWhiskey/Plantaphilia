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

use Borlabs\Cookie\Dto\System\SettingsFieldDto;
use Borlabs\Cookie\Dto\System\SettingsFieldTranslationDto;
use Borlabs\Cookie\DtoList\AbstractDtoList;
use Borlabs\Cookie\Enum\System\SettingsFieldDataTypeEnum;
use Borlabs\Cookie\Enum\System\SettingsFieldVisibilityEnum;
use Borlabs\Cookie\Enum\System\ValidatorEnum;

/**
 * @extends AbstractDtoList<SettingsFieldDto>
 */
final class SettingsFieldDtoList extends AbstractDtoList
{
    public const DTO_CLASS = SettingsFieldDto::class;

    public const UNIQUE_PROPERTY = 'key';

    public function __construct(
        ?array $settingsFieldList = null
    ) {
        parent::__construct($settingsFieldList);
    }

    public static function __listFromJson(array $data)
    {
        $list = [];

        foreach ($data as $key => $settingsField) {
            $list[$key] = new SettingsFieldDto(
                $settingsField->key,
                SettingsFieldDataTypeEnum::fromValue($settingsField->dataType),
                SettingsFieldTranslationDto::fromJson($settingsField->translation),
                ValidatorEnum::fromValue($settingsField->validator),
                SettingsFieldVisibilityEnum::fromValue($settingsField->visibility),
                $settingsField->defaultValue,
                $settingsField->formFieldCollectionName,
                $settingsField->isRequired,
                $settingsField->position,
                $settingsField->validationRegex,
                $settingsField->value,
                KeyValueDtoList::fromJson($settingsField->values),
            );
        }

        return $list;
    }

    public static function __listToJson(array $data)
    {
        $list = [];

        foreach ($data as $key => $settingsField) {
            $list[$key] = SettingsFieldDto::prepareForJson($settingsField);
        }

        return $list;
    }
}
