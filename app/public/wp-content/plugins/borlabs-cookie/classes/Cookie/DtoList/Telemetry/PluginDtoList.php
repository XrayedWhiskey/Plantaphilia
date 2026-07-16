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

use Borlabs\Cookie\Dto\Telemetry\PluginDto;
use Borlabs\Cookie\DtoList\AbstractDtoList;

/**
 * @extends AbstractDtoList<PluginDto>
 */
final class PluginDtoList extends AbstractDtoList
{
    public const DTO_CLASS = PluginDto::class;

    public function __construct(
        ?array $pluginList = null
    ) {
        parent::__construct($pluginList);
    }

    public static function __listFromJson(array $data)
    {
        $list = [];

        foreach ($data as $key => $pluginData) {
            $plugin = new PluginDto();
            $plugin->author = $pluginData->autor;
            $plugin->isEnabled = $pluginData->enabled;
            $plugin->name = $pluginData->name;
            $plugin->pluginUrl = $pluginData->pluginUrl;
            $plugin->slug = $pluginData->slug;
            $plugin->textDomain = $pluginData->textDomain;
            $plugin->version = $pluginData->version;

            $list[$key] = $plugin;
        }

        return $list;
    }

    public static function __listToJson(array $data)
    {
        $list = [];

        foreach ($data as $key => $plugins) {
            $list[$key] = PluginDto::prepareForJson($plugins);
        }

        return $list;
    }
}
