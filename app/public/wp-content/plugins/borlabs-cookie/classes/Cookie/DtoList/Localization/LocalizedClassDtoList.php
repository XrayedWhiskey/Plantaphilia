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

namespace Borlabs\Cookie\DtoList\Localization;

use Borlabs\Cookie\Dto\Localization\LocalizedClassDto;
use Borlabs\Cookie\DtoList\AbstractDtoList;

/**
 * @extends AbstractDtoList<LocalizedClassDto>
 */
final class LocalizedClassDtoList extends AbstractDtoList
{
    public const DTO_CLASS = LocalizedClassDto::class;

    public function __construct(
        ?array $localizationCollectorEntryList = null
    ) {
        parent::__construct($localizationCollectorEntryList);
    }

    public static function __listFromJson(array $data)
    {
        $list = [];

        foreach ($data as $key => $value) {
            $entry = new LocalizedClassDto(
                $value->className,
                $value->instance,
            );
            $list[$key] = $entry;
        }

        return $list;
    }

    public static function __listToJson(array $data)
    {
        $list = [];

        foreach ($data as $key => $value) {
            $list[$key] = LocalizedClassDto::prepareForJson($value);
        }

        return $list;
    }
}
