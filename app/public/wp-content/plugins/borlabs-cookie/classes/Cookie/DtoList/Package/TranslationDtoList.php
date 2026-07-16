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

namespace Borlabs\Cookie\DtoList\Package;

use Borlabs\Cookie\Dto\Package\TranslationDto;
use Borlabs\Cookie\DtoList\AbstractDtoList;

/**
 * @extends AbstractDtoList<TranslationDto>
 */
final class TranslationDtoList extends AbstractDtoList
{
    public const DTO_CLASS = TranslationDto::class;

    public const UNIQUE_PROPERTY = 'language';

    public function __construct(
        ?array $translationList = null
    ) {
        parent::__construct($translationList);
    }

    public static function __listFromJson(array $data)
    {
        $list = [];

        foreach ($data as $key => $translationData) {
            $translation = new TranslationDto();

            if (!isset($translationData->language)) {
                continue;
            }

            $translation->description = $translationData->description;
            $translation->followUp = $translationData->followUp;
            $translation->language = $translationData->language;
            $translation->preparation = $translationData->preparation;
            $list[$key] = $translation;
        }

        return $list;
    }

    public static function __listToJson(array $data)
    {
        $list = [];

        foreach ($data as $key => $translation) {
            $list[$key] = TranslationDto::prepareForJson($translation);
        }

        return $list;
    }
}
