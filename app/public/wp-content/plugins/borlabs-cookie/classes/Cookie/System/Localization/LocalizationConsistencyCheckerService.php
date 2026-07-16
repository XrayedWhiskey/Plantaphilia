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

use Borlabs\Cookie\Dto\Localization\LocalizationCollectorEntryDto;
use Borlabs\Cookie\Dto\Localization\LocalizationConsistencyCheckerResultDto;
use Borlabs\Cookie\Dto\Localization\LocalizationTagDifferenceDto;
use Borlabs\Cookie\Dto\Localization\LocalizationTagDifferenceInLocalizationDto;
use Borlabs\Cookie\DtoList\Localization\LocalizationAggregatedTagInformationDtoList;
use Borlabs\Cookie\DtoList\Localization\LocalizationCollectorEntryDtoList;
use InvalidArgumentException;

class LocalizationConsistencyCheckerService
{
    private LocalizationAggregatedTagInformationDtoListCombinerService $localizationAggregatedTagInformationDtoListCombinerService;

    private LocalizationCollectorService $localizationCollectorService;

    private LocalizationStringService $localizationStringService;

    private LocalizationTagDefinitions $localizationTagDefinitions;

    public function __construct(
        LocalizationCollectorService $localizationCollectorService,
        LocalizationStringService $localizationStringService,
        LocalizationAggregatedTagInformationDtoListCombinerService $localizationAggregatedTagInformationDtoListCombinerService,
        LocalizationTagDefinitions $localizationTagDefinitions
    ) {
        $this->localizationCollectorService = $localizationCollectorService;
        $this->localizationStringService = $localizationStringService;
        $this->localizationAggregatedTagInformationDtoListCombinerService = $localizationAggregatedTagInformationDtoListCombinerService;
        $this->localizationTagDefinitions = $localizationTagDefinitions;
    }

    public function getInformation(): LocalizationConsistencyCheckerResultDto
    {
        return $this->validateSet($this->localizationCollectorService->collectAllLocalizations());
    }

    public function validateSet(LocalizationCollectorEntryDtoList $collected): LocalizationConsistencyCheckerResultDto
    {
        /**
         * @var array<string, array<string, array{content: string, className: class-string}>> $encounteredTagContents
         */
        $encounteredTagContents = [];

        /**
         * @var array<string, array<string, array{content: string, className: class-string}>> $encounteredTagContentsLocalization
         */
        $encounteredTagContentsLocalization = [];

        /**
         * @var array<string, array<LocalizationTagDifferenceDto>> $differences
         */
        $differences = [];

        /**
         * @var array<string, array<LocalizationTagDifferenceInLocalizationDto>> $differencesInLocalization
         */
        $differencesInLocalization = [];

        $aggregatedTagsOriginalAll = [];

        foreach ($this->localizationTagDefinitions->getTagIterator() as $tagDefinition) {
            $encounteredTagContents[$tagDefinition->propertyName] = [];
            $differences[$tagDefinition->propertyName] = [];
            $differencesInLocalization[$tagDefinition->propertyName] = [];
            $aggregatedTagsOriginalAll[$tagDefinition->propertyName] = new LocalizationAggregatedTagInformationDtoList();
        }

        foreach ($collected->list as $collectorEntryDto) {
            if (!$collectorEntryDto instanceof LocalizationCollectorEntryDto) {
                throw new InvalidArgumentException('Unexpected type: ' . get_class($collectorEntryDto));
            }

            $extractedTagsOriginal = $this->localizationStringService->extractTags($collectorEntryDto->text);
            $extractedTagsLocalization = $this->localizationStringService->extractTags($collectorEntryDto->translation);

            $aggregatedTagsOriginal = $this->localizationStringService->aggregateTags($extractedTagsOriginal);
            $aggregatedTagsLocalization = $this->localizationStringService->aggregateTags($extractedTagsLocalization);

            foreach ($this->localizationTagDefinitions->getTagIterator() as $tagDefinition) {
                $this->localizationAggregatedTagInformationDtoListCombinerService->concatLists(
                    $aggregatedTagsOriginalAll[$tagDefinition->propertyName],
                    $aggregatedTagsOriginal[$tagDefinition->propertyName],
                );
            }

            foreach ($this->localizationTagDefinitions->getTagIterator() as $tagDefinition) {
                $encounteredTagContent = &$encounteredTagContents[$tagDefinition->propertyName];

                foreach ($extractedTagsOriginal[$tagDefinition->propertyName] as $extractedTag) {
                    if (!isset($encounteredTagContent[$extractedTag->id])) {
                        $encounteredTagContent[$extractedTag->id] = [
                            'className' => $collectorEntryDto->localizationClassName,
                            'content' => $extractedTag->content,
                        ];
                    } elseif ($encounteredTagContent[$extractedTag->id]['content'] !== $extractedTag->content) {
                        $differences[$tagDefinition->propertyName][] = new LocalizationTagDifferenceDto(
                            $extractedTag->id,
                            $encounteredTagContent[$extractedTag->id]['className'],
                            $encounteredTagContent[$extractedTag->id]['content'],
                            $collectorEntryDto->localizationClassName,
                            $extractedTag->content,
                        );
                    }
                }
            }

            foreach ($this->localizationTagDefinitions->getTagIterator() as $tagDefinition) {
                $encounteredTagContentLocalization = &$encounteredTagContentsLocalization[$tagDefinition->propertyName];

                foreach ($extractedTagsLocalization[$tagDefinition->propertyName] as $extractedTag) {
                    if (!isset($encounteredTagContentLocalization[$extractedTag->id])) {
                        $encounteredTagContentLocalization[$extractedTag->id] = [
                            'context' => $collectorEntryDto->context,
                            'text' => $collectorEntryDto->translation,
                            'content' => $extractedTag->content,
                        ];
                    } elseif ($encounteredTagContentLocalization[$extractedTag->id]['content'] !== $extractedTag->content) {
                        $differencesInLocalization[$tagDefinition->propertyName][] = new LocalizationTagDifferenceInLocalizationDto(
                            $extractedTag->id,
                            $encounteredTagContentLocalization[$extractedTag->id]['context'],
                            $encounteredTagContentLocalization[$extractedTag->id]['text'],
                            $encounteredTagContentLocalization[$extractedTag->id]['content'],
                            $collectorEntryDto->context,
                            $collectorEntryDto->translation,
                            $extractedTag->content,
                        );
                    }
                }
            }
        }

        foreach ($this->localizationTagDefinitions->getTagIterator() as $tagDefinition) {
            $aggregatedTagsOriginalAll[$tagDefinition->propertyName]->sortListByPropertyNaturally('id');
        }

        return new LocalizationConsistencyCheckerResultDto(
            $aggregatedTagsOriginalAll,
            $differences,
            $differencesInLocalization,
        );
    }
}
