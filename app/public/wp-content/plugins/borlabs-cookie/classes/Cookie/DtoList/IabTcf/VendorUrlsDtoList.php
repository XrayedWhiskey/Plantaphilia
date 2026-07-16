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

use Borlabs\Cookie\Dto\IabTcf\VendorUrlsDto;
use Borlabs\Cookie\DtoList\AbstractDtoList;

/**
 * @extends AbstractDtoList<VendorUrlsDto>
 */
final class VendorUrlsDtoList extends AbstractDtoList
{
    public const DTO_CLASS = VendorUrlsDto::class;

    public const UNIQUE_PROPERTY = 'language';

    public function __construct(
        ?array $vendorUrlsList = null
    ) {
        parent::__construct($vendorUrlsList);
    }

    public static function __listFromJson(array $data)
    {
        $list = [];

        foreach ($data as $key => $vendorUrlsData) {
            $vendorUrls = new VendorUrlsDto(
                $vendorUrlsData->language,
                $vendorUrlsData->legitimateInterestClaim,
                $vendorUrlsData->privacy,
            );
            $list[$key] = $vendorUrls;
        }

        return $list;
    }

    public static function __listToJson(array $data)
    {
        $list = [];

        foreach ($data as $key => $vendorUrls) {
            $list[$key] = VendorUrlsDto::prepareForJson($vendorUrls);
        }

        return $list;
    }
}
