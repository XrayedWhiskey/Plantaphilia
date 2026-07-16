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

use Borlabs\Cookie\Dto\Telemetry\LegalLinksPerLanguageListItemDto;
use Borlabs\Cookie\DtoList\AbstractDtoList;

/**
 * @extends AbstractDtoList<LegalLinksPerLanguageListItemDto>
 */
final class LegalLinksPerLanguageDtoList extends AbstractDtoList
{
    public const DTO_CLASS = LegalLinksPerLanguageListItemDto::class;

    public const UNIQUE_PROPERTY = 'language';

    public function __construct(
        ?array $legalLinksPerLanguageList = null
    ) {
        parent::__construct($legalLinksPerLanguageList);
    }

    public static function __listFromJson(array $data)
    {
        $list = [];

        foreach ($data as $key => $legalLinksPerLanguageListData) {
            $legalLinksPerLanguageListItem = new LegalLinksPerLanguageListItemDto(
                $legalLinksPerLanguageListData->language,
                LegalLinkDtoList::fromJson($legalLinksPerLanguageListData->legalLinks),
            );
            $list[$key] = $legalLinksPerLanguageListItem;
        }

        return $list;
    }

    public static function __listToJson(array $data)
    {
        $list = [];

        foreach ($data as $key => $legalLinksPerLanguageListItem) {
            $list[$key] = LegalLinksPerLanguageListItemDto::prepareForJson($legalLinksPerLanguageListItem);
        }

        return $list;
    }
}
