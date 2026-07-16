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

namespace Borlabs\Cookie\DtoList;

/**
 * @template TDtoListType of \Borlabs\Cookie\Dto\AbstractDto
 */
interface DtoListInterface
{
    /**
     * @return array<TDtoListType>
     */
    public static function __listFromJson(array $data);

    /**
     * @return array<TDtoListType>
     */
    public static function __listToJson(array $data);
}
