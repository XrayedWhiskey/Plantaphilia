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

use Borlabs\Cookie\Dto\LocalScanner\MatchedHandleDto;
use Borlabs\Cookie\DtoList\AbstractDtoList;

/**
 * @extends AbstractDtoList<MatchedHandleDto>
 */
final class MatchedHandleDtoList extends AbstractDtoList
{
    public const DTO_CLASS = MatchedHandleDto::class;

    public const UNIQUE_PROPERTY = 'handle';

    public function __construct(
        ?array $matchedHandleList = null
    ) {
        parent::__construct($matchedHandleList);
    }

    public static function __listFromJson(array $data)
    {
        $list = [];

        foreach ($data as $key => $matchedHandleData) {
            $matchedHandle = new MatchedHandleDto(
                $matchedHandleData->type,
                $matchedHandleData->handle,
                $matchedHandleData->phrase,
                $matchedHandleData->url,
            );
            $list[$key] = $matchedHandle;
        }

        return $list;
    }

    public static function __listToJson(array $data)
    {
        $list = [];

        foreach ($data as $key => $matchedHandle) {
            $list[$key] = MatchedHandleDto::prepareForJson($matchedHandle);
        }

        return $list;
    }
}
