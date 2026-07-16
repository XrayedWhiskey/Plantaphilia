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

namespace Borlabs\Cookie\DtoList\LocalScanner;

use Borlabs\Cookie\Dto\LocalScanner\UnmatchedHandleDto;
use Borlabs\Cookie\DtoList\AbstractDtoList;

/**
 * @extends AbstractDtoList<UnmatchedHandleDto>
 */
final class UnmatchedHandleDtoList extends AbstractDtoList
{
    public const DTO_CLASS = UnmatchedHandleDto::class;

    public const UNIQUE_PROPERTY = 'handle';

    public function __construct(
        ?array $unmatchedHandleList = null
    ) {
        parent::__construct($unmatchedHandleList);
    }

    public static function __listFromJson(array $data)
    {
        $list = [];

        foreach ($data as $key => $unmatchedHandleData) {
            $unmatchedHandle = new UnmatchedHandleDto(
                $unmatchedHandleData->type,
                $unmatchedHandleData->handle,
                $unmatchedHandleData->url,
            );
            $list[$key] = $unmatchedHandle;
        }

        return $list;
    }

    public static function __listToJson(array $data)
    {
        $list = [];

        foreach ($data as $key => $unmatchedHandle) {
            $list[$key] = UnmatchedHandleDto::prepareForJson($unmatchedHandle);
        }

        return $list;
    }
}
