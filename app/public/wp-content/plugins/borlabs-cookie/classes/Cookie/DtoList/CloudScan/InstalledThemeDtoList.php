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

use Borlabs\Cookie\Dto\CloudScan\InstalledThemeDto;
use Borlabs\Cookie\DtoList\AbstractDtoList;

/**
 * @extends AbstractDtoList<InstalledThemeDto>
 */
final class InstalledThemeDtoList extends AbstractDtoList
{
    public const DTO_CLASS = InstalledThemeDto::class;

    public function __construct(
        ?array $installedThemeList = null
    ) {
        parent::__construct($installedThemeList);
    }

    public static function __listFromJson(array $data)
    {
        $list = [];

        foreach ($data as $key => $installedThemeData) {
            $installedTheme = new InstalledThemeDto(
                $installedThemeData->slug,
                $installedThemeData->textdomain,
            );
            $list[$key] = $installedTheme;
        }

        return $list;
    }

    public static function __listToJson(array $data)
    {
        $list = [];

        foreach ($data as $key => $installedThemes) {
            $list[$key] = InstalledThemeDto::prepareForJson($installedThemes);
        }

        return $list;
    }
}
