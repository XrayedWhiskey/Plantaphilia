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

namespace Borlabs\Cookie\DtoList\CompatibilityPatch;

use Borlabs\Cookie\Dto\CompatibilityPatch\CompatibilityPatchDetailsDto;
use Borlabs\Cookie\DtoList\AbstractDtoList;

/**
 * @extends AbstractDtoList<CompatibilityPatchDetailsDto>
 */
final class CompatibilityPatchDetailsDtoList extends AbstractDtoList
{
    public const DTO_CLASS = CompatibilityPatchDetailsDto::class;

    public function __construct(
        ?array $compatibilityPatchDetailsList = null
    ) {
        parent::__construct($compatibilityPatchDetailsList);
    }

    public static function __listFromJson(array $data)
    {
        $list = [];

        foreach ($data as $key => $compatibilityPatchDetailsData) {
            $compatibilityPatchDetails = new CompatibilityPatchDetailsDto(
                $compatibilityPatchDetailsData->compatibilityPatch,
                $compatibilityPatchDetailsData->file,
                $compatibilityPatchDetailsData->package,
                $compatibilityPatchDetailsData->validationStatus,
            );
            $list[$key] = $compatibilityPatchDetails;
        }

        return $list;
    }

    public static function __listToJson(array $data)
    {
        $list = [];

        foreach ($data as $key => $compatibilityPatchDetails) {
            $list[$key] = CompatibilityPatchDetailsDto::prepareForJson($compatibilityPatchDetails);
        }

        return $list;
    }
}
