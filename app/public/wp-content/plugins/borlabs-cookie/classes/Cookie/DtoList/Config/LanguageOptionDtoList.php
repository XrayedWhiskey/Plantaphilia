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

namespace Borlabs\Cookie\DtoList\Config;

use Borlabs\Cookie\Dto\Config\LanguageOptionDto;
use Borlabs\Cookie\DtoList\AbstractDtoList;

final class LanguageOptionDtoList extends AbstractDtoList
{
    public const DTO_CLASS = LanguageOptionDto::class;

    public const UNIQUE_PROPERTY = 'code';

    /**
     * @var \Borlabs\Cookie\Dto\Config\LanguageOptionDto[]
     */
    public array $list = [];

    /**
     * @param \Borlabs\Cookie\Dto\Config\LanguageOptionDto[] $languageOptionList array of {@see \Borlabs\Cookie\Dto\Config\LanguageOptionDto} objects
     */
    public function __construct(
        ?array $languageOptionList = null
    ) {
        parent::__construct($languageOptionList);
    }

    public static function __listFromJson(array $data)
    {
        $list = [];

        foreach ($data as $key => $value) {
            $list[$key] = LanguageOptionDto::fromJson($value);
        }

        return $list;
    }

    public static function __listToJson(array $data)
    {
        $list = [];

        foreach ($data as $key => $value) {
            $list[$key] = LanguageOptionDto::prepareForJson($value);
        }

        return $list;
    }
}
