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

use Borlabs\Cookie\Dto\CloudScan\InstalledPluginDto;
use Borlabs\Cookie\DtoList\AbstractDtoList;

/**
 * @extends AbstractDtoList<InstalledPluginDto>
 */
final class InstalledPluginDtoList extends AbstractDtoList
{
    public const DTO_CLASS = InstalledPluginDto::class;

    public function __construct(
        ?array $installedPluginList = null
    ) {
        parent::__construct($installedPluginList);
    }

    public static function __listFromJson(array $data)
    {
        $list = [];

        foreach ($data as $key => $installedPluginData) {
            $installedPlugin = new InstalledPluginDto(
                $installedPluginData->slug,
                $installedPluginData->textdomain,
            );
            $list[$key] = $installedPlugin;
        }

        return $list;
    }

    public static function __listToJson(array $data)
    {
        $list = [];

        foreach ($data as $key => $installedPlugins) {
            $list[$key] = InstalledPluginDto::prepareForJson($installedPlugins);
        }

        return $list;
    }
}
