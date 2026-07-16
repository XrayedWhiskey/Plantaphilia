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

use Borlabs\Cookie\Dto\System\KeyValueDto;
use Borlabs\Cookie\DtoList\AbstractDtoList;

/**
 * The **KeyValueDtoList** class is used as a typed object that contains a list of
 * {@see \Borlabs\Cookie\Dto\System\KeyValueDto} objects.
 *
 * @see \Borlabs\Cookie\Dto\System\KeyValueDtoList::$list
 *
 * @extends AbstractDtoList<KeyValueDto>
 */
final class KeyValueDtoList extends AbstractDtoList
{
    public const DTO_CLASS = KeyValueDto::class;

    public const UNIQUE_PROPERTY = 'key';

    /**
     * @param \Borlabs\Cookie\Dto\System\KeyValueDto[] $keyValueList array of {@see \Borlabs\Cookie\Dto\System\KeyValueDto} objects
     */
    public function __construct(
        ?array $keyValueList = null
    ) {
        parent::__construct($keyValueList);
    }

    public static function __listFromJson(array $data)
    {
        $list = [];

        foreach ($data as $key => $value) {
            $list[$key] = KeyValueDto::fromJson($value);
        }

        return $list;
    }

    public static function __listToJson(array $data)
    {
        $list = [];

        foreach ($data as $key => $value) {
            $list[$key] = KeyValueDto::prepareForJson($value);
        }

        return $list;
    }
}
