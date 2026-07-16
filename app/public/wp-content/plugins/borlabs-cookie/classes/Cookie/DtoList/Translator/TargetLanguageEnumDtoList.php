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

namespace Borlabs\Cookie\DtoList\Translator;

use Borlabs\Cookie\Dto\Translator\TargetLanguageDto;
use Borlabs\Cookie\DtoList\AbstractDtoList;
use Borlabs\Cookie\Enum\Translator\TargetLanguageEnum;

/**
 * @extends AbstractDtoList<TargetLanguageDto>
 */
final class TargetLanguageEnumDtoList extends AbstractDtoList
{
    public const DTO_CLASS = TargetLanguageDto::class;

    public function __construct(
        ?array $targetLanguageDtoList = null
    ) {
        parent::__construct($targetLanguageDtoList);
    }

    public static function __listFromJson(array $data)
    {
        $list = [];

        foreach ($data as $key => $targetLanguageEnumValue) {
            $list[$key] = new TargetLanguageDto(TargetLanguageEnum::fromValue($targetLanguageEnumValue));
        }

        return $list;
    }

    public static function __listToJson(array $data)
    {
        $list = [];

        foreach ($data as $key => $targetLanguageDto) {
            $list[$key] = $targetLanguageDto->targetLanguageEnum->__toString();
        }

        return $list;
    }
}
