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

use Borlabs\Cookie\Dto\CloudScan\ExternalResourceExampleDto;
use Borlabs\Cookie\DtoList\AbstractDtoList;

/**
 * @extends AbstractDtoList<ExternalResourceExampleDto>
 */
final class ExternalResourceExampleDtoList extends AbstractDtoList
{
    public const DTO_CLASS = ExternalResourceExampleDto::class;

    public static function __listFromJson(array $data)
    {
        $list = [];

        foreach ($data as $key => $externalResourceExampleData) {
            $externalResourceExample = new ExternalResourceExampleDto(
                $externalResourceExampleData->pageId,
                $externalResourceExampleData->pageUrl,
                $externalResourceExampleData->resourceUrl,
            );
            $list[$key] = $externalResourceExample;
        }

        return $list;
    }

    public static function __listToJson(array $data)
    {
        $list = [];

        foreach ($data as $key => $externalResourceExamples) {
            $list[$key] = ExternalResourceExampleDto::prepareForJson($externalResourceExamples);
        }

        return $list;
    }
}
