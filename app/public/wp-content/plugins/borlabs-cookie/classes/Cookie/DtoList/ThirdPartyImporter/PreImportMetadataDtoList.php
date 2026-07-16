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

namespace Borlabs\Cookie\DtoList\ThirdPartyImporter;

use Borlabs\Cookie\Dto\ThirdPartyImporter\PreImportMetadataDto;
use Borlabs\Cookie\DtoList\AbstractDtoList;

/**
 * @extends AbstractDtoList<PreImportMetadataDto>
 */
final class PreImportMetadataDtoList extends AbstractDtoList
{
    public const DTO_CLASS = PreImportMetadataDto::class;

    public function __construct(?array $preImportMetadataList)
    {
        parent::__construct($preImportMetadataList);
    }

    public static function __listFromJson(array $data)
    {
        $list = [];

        foreach ($data as $key => $preImportMetadataData) {
            $preImportMetadata = new PreImportMetadataDto(
                $preImportMetadataData->componentType,
                $preImportMetadataData->name,
                $preImportMetadataData->legacyKey,
                $preImportMetadataData->newKey,
                $preImportMetadataData->language,
            );
            $list[$key] = $preImportMetadata;
        }

        return $list;
    }

    public static function __listToJson(array $data)
    {
        $list = [];

        foreach ($data as $key => $preImportMetadataData) {
            $list[$key] = PreImportMetadataDto::prepareForJson($preImportMetadataData);
        }

        return $list;
    }
}
