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

use Borlabs\Cookie\Dto\IabTcf\ConsentParameterDto;
use Borlabs\Cookie\DtoList\AbstractDtoList;

/**
 * @extends AbstractDtoList<ConsentParameterDto>
 */
final class ConsentParameterDtoList extends AbstractDtoList
{
    public const DTO_CLASS = ConsentParameterDto::class;

    public const UNIQUE_PROPERTY = 'id';

    public function __construct(
        ?array $purposeList = null
    ) {
        parent::__construct($purposeList);
    }

    public static function __listFromJson(array $data)
    {
        $list = [];

        foreach ($data as $key => $purposeData) {
            $purpose = new ConsentParameterDto(
                $purposeData->id,
                $purposeData->name,
                $purposeData->description,
                $purposeData->illustrations,
            );
            $list[$key] = $purpose;
        }

        return $list;
    }

    public static function __listToJson(array $data)
    {
        $list = [];

        foreach ($data as $key => $purpose) {
            $list[$key] = ConsentParameterDto::prepareForJson($purpose);
        }

        return $list;
    }
}
