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

namespace Borlabs\Cookie\DtoList\Telemetry;

use Borlabs\Cookie\Dto\Telemetry\ThemeDto;
use Borlabs\Cookie\DtoList\AbstractDtoList;

/**
 * @extends AbstractDtoList<ThemeDto>
 */
final class ThemeDtoList extends AbstractDtoList
{
    public const DTO_CLASS = ThemeDto::class;

    public function __construct(
        ?array $themeList = null
    ) {
        parent::__construct($themeList);
    }

    public static function __listFromJson(array $data)
    {
        $list = [];

        foreach ($data as $key => $themeData) {
            $theme = new ThemeDto();
            $theme->author = $themeData->author;
            $theme->isChildtheme = $themeData->isChildtheme;
            $theme->isEnabled = $themeData->enabled;
            $theme->name = $themeData->name;
            $theme->template = $themeData->template;
            $theme->textDomain = $themeData->textDomain;
            $theme->themeUrl = $themeData->themeUrl;
            $theme->version = $themeData->version;

            $list[$key] = $theme;
        }

        return $list;
    }

    public static function __listToJson(array $data)
    {
        $list = [];

        foreach ($data as $key => $themes) {
            $list[$key] = ThemeDto::prepareForJson($themes);
        }

        return $list;
    }
}
