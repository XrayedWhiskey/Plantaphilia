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

namespace Borlabs\Cookie\System\Localization;

use Borlabs\Cookie\Dto\Localization\LocalizationAggregatedTagInformationDto;
use Borlabs\Cookie\DtoList\Localization\LocalizationAggregatedTagInformationDtoList;

class LocalizationAggregatedTagInformationDtoListCombinerService
{
    public function concatLists(
        LocalizationAggregatedTagInformationDtoList $resultList,
        LocalizationAggregatedTagInformationDtoList $concat
    ): void {
        foreach ($concat->list as $tag) {
            /**
             * @var LocalizationAggregatedTagInformationDto $existingDto
             */
            $existingDto = $resultList->get($tag);

            if ($existingDto === null) {
                $resultList->add($tag);
            } else {
                $existingDto->counter += $tag->counter;
                $existingDto->contents = array_unique(array_merge(
                    $existingDto->contents,
                    $tag->contents,
                ));
            }
        }
    }
}
