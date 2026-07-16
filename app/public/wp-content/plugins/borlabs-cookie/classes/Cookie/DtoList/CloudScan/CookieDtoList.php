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

use Borlabs\Cookie\Dto\CloudScan\CookieDto;
use Borlabs\Cookie\DtoList\AbstractDtoList;

/**
 * @extends AbstractDtoList<CookieDto>
 */
final class CookieDtoList extends AbstractDtoList
{
    public const DTO_CLASS = CookieDto::class;

    /**
     * @var array<\Borlabs\Cookie\Dto\CloudScan\CookieDto>
     */
    public array $list = [];

    public function __construct(
        ?array $cookieList = null
    ) {
        parent::__construct($cookieList);
    }

    public static function __listFromJson(array $data)
    {
        $list = [];

        foreach ($data as $key => $cookieData) {
            $cookie = new CookieDto(
                $cookieData->name,
                $cookieData->hostname,
                $cookieData->path,
                $cookieData->examples,
                $cookieData->lifetime,
                $cookieData->packageKey,
            );
            $list[$key] = $cookie;
        }

        return $list;
    }

    public static function __listToJson(array $data)
    {
        $list = [];

        foreach ($data as $key => $cookies) {
            $list[$key] = CookieDto::prepareForJson($cookies);
        }

        return $list;
    }
}
