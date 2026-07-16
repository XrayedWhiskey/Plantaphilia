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

namespace Borlabs\Cookie\Dto\Localization;

use Borlabs\Cookie\Dto\AbstractDto;
use Borlabs\Cookie\DtoList\Localization\LocalizationAggregatedTagInformationDtoList;

class LocalizationConsistencyCheckerResultDto extends AbstractDto
{
    /**
     * @var array<string, array<LocalizationTagDifferenceDto>>
     */
    public array $differencesInLocalization;

    /**
     * @var array<string, array<LocalizationTagDifferenceDto>>
     */
    public array $differencesPerTag;

    /**
     * @var array<string, LocalizationAggregatedTagInformationDtoList>
     */
    public array $informationPerTag;

    /**
     * @param array<string, LocalizationAggregatedTagInformationDtoList> $informationPerTag
     * @param array<string, array<LocalizationTagDifferenceDto>>         $differencesPerTag
     */
    public function __construct(
        array $informationPerTag,
        array $differencesPerTag,
        array $differencesInLocalization
    ) {
        $this->informationPerTag = $informationPerTag;
        $this->differencesPerTag = $differencesPerTag;
        $this->differencesInLocalization = $differencesInLocalization;
    }
}
