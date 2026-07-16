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

use Borlabs\Cookie\Dto\Telemetry\ContentBlockerDto;
use Borlabs\Cookie\DtoList\AbstractDtoList;

/**
 * @extends AbstractDtoList<ContentBlockerDto>
 */
final class ContentBlockerDtoList extends AbstractDtoList
{
    public const DTO_CLASS = ContentBlockerDto::class;

    public function __construct(
        ?array $contentBlockerList = null
    ) {
        parent::__construct($contentBlockerList);
    }

    public static function __listFromJson(array $data)
    {
        $list = [];

        foreach ($data as $key => $contentBlockerData) {
            $contentBlocker = new ContentBlockerDto();
            $contentBlocker->borlabsServicePackageKey = $contentBlockerData->borlabsServicePackageKey;
            $contentBlocker->key = $contentBlockerData->key;
            $contentBlocker->name = $contentBlockerData->name;
            $contentBlocker->status = $contentBlockerData->status;

            $list[$key] = $contentBlocker;
        }

        return $list;
    }

    public static function __listToJson(array $data)
    {
        $list = [];

        foreach ($data as $key => $contentBlockers) {
            $list[$key] = ContentBlockerDto::prepareForJson($contentBlockers);
        }

        return $list;
    }
}
