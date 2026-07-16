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

use Borlabs\Cookie\Dto\Telemetry\StyleBlockerDto;
use Borlabs\Cookie\DtoList\AbstractDtoList;
use Borlabs\Cookie\DtoList\System\KeyValueDtoList;

/**
 * @extends AbstractDtoList<StyleBlockerDto>
 */
final class StyleBlockerDtoList extends AbstractDtoList
{
    public const DTO_CLASS = StyleBlockerDto::class;

    public function __construct(
        ?array $styleBlockerList = null
    ) {
        parent::__construct($styleBlockerList);
    }

    public static function __listFromJson(array $data)
    {
        $list = [];

        foreach ($data as $key => $styleBlockerData) {
            $styleBlocker = new StyleBlockerDto();
            $styleBlocker->borlabsServicePackageKey = $styleBlockerData->borlabsServicePackageKey;
            $styleBlocker->handles = KeyValueDtoList::fromJson($styleBlockerData->handles);
            $styleBlocker->key = $styleBlockerData->key;
            $styleBlocker->name = $styleBlockerData->name;
            $styleBlocker->phrases = KeyValueDtoList::fromJson($styleBlockerData->phrases);
            $styleBlocker->status = $styleBlockerData->status;

            $list[$key] = $styleBlocker;
        }

        return $list;
    }

    public static function __listToJson(array $data)
    {
        $list = [];

        foreach ($data as $key => $scriptBlockers) {
            $list[$key] = StyleBlockerDto::prepareForJson($scriptBlockers);
        }

        return $list;
    }
}
