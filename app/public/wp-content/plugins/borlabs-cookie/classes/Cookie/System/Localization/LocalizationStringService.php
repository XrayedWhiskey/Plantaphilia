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
use Borlabs\Cookie\Dto\Localization\LocalizationExtractedTagDto;
use Borlabs\Cookie\DtoList\Localization\LocalizationAggregatedTagInformationDtoList;

class LocalizationStringService
{
    public string $replacementTag = '<span class="brlbs-cmpnt-term">%s</span>';

    private LocalizationAggregatedTagInformationDtoListCombinerService $localizationAggregatedTagInformationDtoListCombinerService;

    private LocalizationTagDefinitions $localizationTagDefinitions;

    public function __construct(
        LocalizationAggregatedTagInformationDtoListCombinerService $localizationAggregatedTagInformationDtoListCombinerService,
        LocalizationTagDefinitions $localizationTags
    ) {
        $this->localizationAggregatedTagInformationDtoListCombinerService = $localizationAggregatedTagInformationDtoListCombinerService;
        $this->localizationTagDefinitions = $localizationTags;
    }

    /**
     * @param array<string, array<LocalizationExtractedTagDto>> $collectedTags
     *
     * @return array<string, LocalizationAggregatedTagInformationDtoList>
     */
    public function aggregateTags(array $collectedTags): array
    {
        $result = [];

        foreach ($this->localizationTagDefinitions->getTagIterator() as $tagDefinition) {
            $result[$tagDefinition->propertyName] = new LocalizationAggregatedTagInformationDtoList();
        }

        foreach ($this->localizationTagDefinitions->getTagIterator() as $tagDefinition) {
            /**
             * @var array<LocalizationExtractedTagDto> $currentTags
             */
            $currentTags = $collectedTags[$tagDefinition->propertyName];
            $aggregatedTags = new LocalizationAggregatedTagInformationDtoList();

            foreach ($currentTags as $tag) {
                $newDto = new LocalizationAggregatedTagInformationDto(
                    $tag->id,
                    [$tag->content],
                    1,
                );

                $this->localizationAggregatedTagInformationDtoListCombinerService->concatLists(
                    $aggregatedTags,
                    new LocalizationAggregatedTagInformationDtoList([$newDto]),
                );
            }
            $result[$tagDefinition->propertyName] = $aggregatedTags;
        }

        return $result;
    }

    /**
     * @return array<string, array<LocalizationExtractedTagDto>>
     */
    public function extractTags(string $localizationString): array
    {
        $foundTags = [];

        foreach ($this->localizationTagDefinitions->getTagIterator() as $tagDefinition) {
            $foundTags[$tagDefinition->propertyName] = [];
        }

        foreach ($this->localizationTagDefinitions->getTagIterator() as $tagDefinition) {
            $matches = [];
            preg_match_all(
                $tagDefinition->regex,
                $localizationString,
                $matches,
            );
            $list = [];

            foreach ($matches[1] as $key => $id) {
                $list[] = new LocalizationExtractedTagDto(
                    $id,
                    $matches[2][$key],
                );
            }

            $foundTags[$tagDefinition->propertyName] = $list;
        }

        return $foundTags;
    }

    public function replaceTags(string $localizationString): string
    {
        $returnLocalizationString = $localizationString;

        foreach ($this->localizationTagDefinitions->getTagIterator() as $tagDefinition) {
            $returnLocalizationString = preg_replace_callback(
                $tagDefinition->regex,
                function (array $match): string {
                    return sprintf($this->replacementTag, $match[2]);
                },
                $returnLocalizationString,
            );
        }

        return $returnLocalizationString;
    }
}
