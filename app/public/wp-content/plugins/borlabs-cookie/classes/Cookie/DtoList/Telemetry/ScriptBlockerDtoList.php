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

use Borlabs\Cookie\Dto\Telemetry\ScriptBlockerDto;
use Borlabs\Cookie\DtoList\AbstractDtoList;
use Borlabs\Cookie\DtoList\System\KeyValueDtoList;

/**
 * @extends AbstractDtoList<ScriptBlockerDto>
 */
final class ScriptBlockerDtoList extends AbstractDtoList
{
    public const DTO_CLASS = ScriptBlockerDto::class;

    public function __construct(
        ?array $scriptBlockerList = null
    ) {
        parent::__construct($scriptBlockerList);
    }

    public static function __listFromJson(array $data)
    {
        $list = [];

        foreach ($data as $key => $scriptBlockerData) {
            $scriptBlocker = new ScriptBlockerDto();
            $scriptBlocker->borlabsServicePackageKey = $scriptBlockerData->borlabsServicePackageKey;
            $scriptBlocker->handles = KeyValueDtoList::fromJson($scriptBlockerData->handles);
            $scriptBlocker->key = $scriptBlockerData->key;
            $scriptBlocker->name = $scriptBlockerData->name;
            $scriptBlocker->onExist = KeyValueDtoList::fromJson($scriptBlockerData->onExist);
            $scriptBlocker->phrases = KeyValueDtoList::fromJson($scriptBlockerData->phrases);
            $scriptBlocker->status = $scriptBlockerData->status;

            $list[$key] = $scriptBlocker;
        }

        return $list;
    }

    public static function __listToJson(array $data)
    {
        $list = [];

        foreach ($data as $key => $scriptBlockers) {
            $list[$key] = ScriptBlockerDto::prepareForJson($scriptBlockers);
        }

        return $list;
    }
}
