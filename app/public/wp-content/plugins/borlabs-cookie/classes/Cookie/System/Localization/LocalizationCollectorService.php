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
use Borlabs\Cookie\DtoList\Localization\LocalizationCollectorEntryDtoList;
use Borlabs\Cookie\DtoList\Localization\LocalizedClassDtoList;
use Borlabs\Cookie\Localization\LocalizationInterface;
use LogicException;

class LocalizationCollectorService
{
    private array $collected = [];

    private bool $collectionMode = false;

    private LocalizedClassesService $localizationClassesService;

    public function __construct(
        LocalizedClassesService $localizationClassesService
    ) {
        $this->localizationClassesService = $localizationClassesService;
    }

    public function collect(string $text, string $context, string $domain, string $translation): void
    {
        if (!$this->collectionModeActive()) {
            return;
        }

        $this->collected[] = [
            'text' => $text,
            'context' => $context,
            'domain' => $domain,
            'translation' => $translation,
        ];
    }

    public function collectAllLocalizations(): LocalizationCollectorEntryDtoList
    {
        $return = [];

        $localizationClasses = new LocalizedClassDtoList();
        $localizationClasses->addList($this->localizationClassesService->getAllLocalizationClasses());
        $localizationClasses->addList($this->localizationClassesService->getAllLocalizedEnumClasses());

        foreach ($localizationClasses->list as $classNameOrObject) {
            $this->toggleCollectionMode();

            if ($classNameOrObject->instance !== null) {
                if ($classNameOrObject->instance instanceof LocalizationInterface) {
                    $classNameOrObject->instance->get();
                } else {
                    throw new LogicException('Unexpected object type: ' . get_class($classNameOrObject->instance));
                }
            } else {
                ($classNameOrObject->className)::localized();
            }

            $this->toggleCollectionMode();

            $collected = $this->getAndCleanCollectedBuffer();

            foreach ($collected as $collectedEntry) {
                $return[] = new LocalizationCollectorEntryDto(
                    $classNameOrObject->className,
                    $collectedEntry['text'],
                    $collectedEntry['context'],
                    $collectedEntry['domain'],
                    $collectedEntry['translation'],
                );
            }
        }

        return new LocalizationCollectorEntryDtoList($return);
    }

    public function collectionModeActive(): bool
    {
        return $this->collectionMode;
    }

    public function getAndCleanCollectedBuffer(): array
    {
        $return = $this->collected;
        $this->collected = [];

        return $return;
    }

    public function toggleCollectionMode(): void
    {
        $this->collectionMode = !$this->collectionMode;
    }
}
