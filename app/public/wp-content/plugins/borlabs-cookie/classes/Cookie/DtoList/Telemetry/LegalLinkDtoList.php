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

namespace Borlabs\Cookie\DtoList\Telemetry;

use Borlabs\Cookie\Dto\Telemetry\LegalLinkDto;
use Borlabs\Cookie\DtoList\AbstractDtoList;
use Borlabs\Cookie\Enum\Telemetry\LinkTypeEnum;

/**
 * @extends AbstractDtoList<LegalLinkDto>
 */
final class LegalLinkDtoList extends AbstractDtoList
{
    public const DTO_CLASS = LegalLinkDto::class;

    public function __construct(
        ?array $legalLinkList = null
    ) {
        parent::__construct($legalLinkList);
    }

    public static function __listFromJson(array $data)
    {
        $list = [];

        foreach ($data as $key => $legalLinkData) {
            $legalLink = new LegalLinkDto(
                LinkTypeEnum::fromValue($legalLinkData->linkType),
                $legalLinkData->url,
            );

            $list[$key] = $legalLink;
        }

        return $list;
    }

    public static function __listToJson(array $data)
    {
        $list = [];

        foreach ($data as $key => $legalLinks) {
            $list[$key] = LegalLinkDto::prepareForJson($legalLinks);
        }

        return $list;
    }
}
