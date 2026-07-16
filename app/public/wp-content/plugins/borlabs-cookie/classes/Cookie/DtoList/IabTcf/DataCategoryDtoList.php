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

namespace Borlabs\Cookie\DtoList\IabTcf;

use Borlabs\Cookie\Dto\IabTcf\DataCategoryDto;
use Borlabs\Cookie\DtoList\AbstractDtoList;

/**
 * @extends AbstractDtoList<DataCategoryDto>
 */
final class DataCategoryDtoList extends AbstractDtoList
{
    public const DTO_CLASS = DataCategoryDto::class;

    public const UNIQUE_PROPERTY = 'id';

    public function __construct(
        ?array $dataCategoryList = null
    ) {
        parent::__construct($dataCategoryList);
    }

    public static function __listFromJson(array $data)
    {
        $list = [];

        foreach ($data as $key => $dataCategoryData) {
            $dataCategory = new DataCategoryDto(
                $dataCategoryData->id,
                $dataCategoryData->name,
                $dataCategoryData->description,
            );
            $list[$key] = $dataCategory;
        }

        return $list;
    }

    public static function __listToJson(array $data)
    {
        $list = [];

        foreach ($data as $key => $dataCategory) {
            $list[$key] = DataCategoryDto::prepareForJson($dataCategory);
        }

        return $list;
    }
}
