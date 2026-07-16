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

use Borlabs\Cookie\Dto\CloudScan\PackageSuggestionDto;
use Borlabs\Cookie\DtoList\AbstractDtoList;

/**
 * @extends AbstractDtoList<PackageSuggestionDto>
 */
final class PackageSuggestionDtoList extends AbstractDtoList
{
    public const DTO_CLASS = PackageSuggestionDto::class;

    public function __construct(
        ?array $suggestionList = null
    ) {
        parent::__construct($suggestionList);
    }

    public static function __listFromJson(array $data)
    {
        $list = [];

        foreach ($data as $key => $packageSuggestionData) {
            $suggestion = new PackageSuggestionDto(
                $packageSuggestionData->id,
                $packageSuggestionData->name,
                $packageSuggestionData->type,
            );
            $list[$key] = $suggestion;
        }

        return $list;
    }

    public static function __listToJson(array $data)
    {
        $list = [];

        foreach ($data as $key => $packageSuggestions) {
            $list[$key] = PackageSuggestionDto::prepareForJson($packageSuggestions);
        }

        return $list;
    }
}
