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

use Borlabs\Cookie\Dto\CloudScan\CookieExampleDto;
use Borlabs\Cookie\DtoList\AbstractDtoList;

/**
 * @extends AbstractDtoList<CookieExampleDto>
 */
final class CookieExampleDtoList extends AbstractDtoList
{
    public const DTO_CLASS = CookieExampleDto::class;

    public static function __listFromJson(array $data)
    {
        $list = [];

        foreach ($data as $key => $cookieExampleData) {
            $cookieExample = new CookieExampleDto(
                $cookieExampleData->pageId,
                $cookieExampleData->pageUrl,
            );
            $list[$key] = $cookieExample;
        }

        return $list;
    }

    public static function __listToJson(array $data)
    {
        $list = [];

        foreach ($data as $key => $cookieExamples) {
            $list[$key] = CookieExampleDto::prepareForJson($cookieExamples);
        }

        return $list;
    }
}
