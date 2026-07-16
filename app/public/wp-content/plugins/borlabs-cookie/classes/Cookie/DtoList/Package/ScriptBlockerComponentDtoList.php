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

use Borlabs\Cookie\Dto\Package\ScriptBlockerComponentDto;
use Borlabs\Cookie\DtoList\AbstractDtoList;

/**
 * @extends AbstractDtoList<ScriptBlockerComponentDto>
 */
final class ScriptBlockerComponentDtoList extends AbstractDtoList
{
    public const DTO_CLASS = ScriptBlockerComponentDto::class;

    public const UNIQUE_PROPERTY = 'key';

    public function __construct(
        ?array $componentList = null
    ) {
        parent::__construct($componentList);
    }

    public static function __listFromJson(array $data)
    {
        $list = [];

        foreach ($data as $key => $componentData) {
            $scriptBlockerComponent = new ScriptBlockerComponentDto(
                $componentData->key,
                $componentData->name,
            );
            $list[$key] = $scriptBlockerComponent;
        }

        return $list;
    }

    public static function __listToJson(array $data)
    {
        $list = [];

        foreach ($data as $key => $scriptBlockerComponent) {
            $list[$key] = ScriptBlockerComponentDto::prepareForJson($scriptBlockerComponent);
        }

        return $list;
    }
}
