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

use Borlabs\Cookie\DtoList\AbstractDtoList;

/**
 * @extends AbstractDtoList<SettingsFieldDtoList>
 */
final class SettingsFieldDtoListList extends AbstractDtoList
{
    public const DTO_CLASS = SettingsFieldDtoList::class;

    public function __construct(
        ?array $settingsFieldListList = null
    ) {
        parent::__construct($settingsFieldListList);
    }

    public static function __listFromJson(array $data)
    {
        $list = [];

        foreach ($data as $key => $settingsFieldList) {
            $list[$key] = SettingsFieldDtoList::fromJson($settingsFieldList);
        }

        return $list;
    }

    public static function __listToJson(array $data)
    {
        $list = [];

        foreach ($data as $key => $settingsFieldList) {
            $list[$key] = SettingsFieldDtoList::prepareForJson($settingsFieldList);
        }

        return $list;
    }
}
