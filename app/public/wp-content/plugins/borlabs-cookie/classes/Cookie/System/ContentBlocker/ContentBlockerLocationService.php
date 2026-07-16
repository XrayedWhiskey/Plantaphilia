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

namespace Borlabs\Cookie\System\ContentBlocker;

use Borlabs\Cookie\DtoList\System\KeyValueDtoList;
use Borlabs\Cookie\Model\ContentBlocker\ContentBlockerLocationModel;
use Borlabs\Cookie\Model\ContentBlocker\ContentBlockerModel;
use Borlabs\Cookie\Repository\ContentBlocker\ContentBlockerLocationRepository;
use Borlabs\Cookie\Repository\ContentBlocker\ContentBlockerRepository;
use Borlabs\Cookie\Support\Searcher;
use Borlabs\Cookie\Validator\ContentBlocker\ContentBlockerLocationValidator;

class ContentBlockerLocationService
{
    private ContentBlockerLocationRepository $contentBlockerLocationRepository;

    private ContentBlockerLocationValidator $contentBlockerLocationValidator;

    private ContentBlockerRepository $contentBlockerRepository;

    public function __construct(
        ContentBlockerLocationRepository $contentBlockerLocationRepository,
        ContentBlockerLocationValidator $contentBlockerLocationValidator,
        ContentBlockerRepository $contentBlockerRepository
    ) {
        $this->contentBlockerLocationRepository = $contentBlockerLocationRepository;
        $this->contentBlockerLocationValidator = $contentBlockerLocationValidator;
        $this->contentBlockerRepository = $contentBlockerRepository;
    }

    public function deleteAll(ContentBlockerModel $contentBlockerModel): void
    {
        if (!isset($contentBlockerModel->contentBlockerLocations)) {
            return;
        }

        foreach ($contentBlockerModel->contentBlockerLocations as $contentBlockerLocation) {
            $this->contentBlockerLocationRepository->delete($contentBlockerLocation);
        }
    }

    public function handleAdditionalLanguages(
        array $postData,
        array $configurationLanguages,
        array $translationLanguages,
        KeyValueDtoList $contentBlockerPerLanguageList
    ): void {
        /**
         * @var array $languages
         *
         * Example
         * <code>
         * [
         *     0 => 'de',
         *     1 => 'en',
         *     2 => 'it',
         * ]
         * </code>
         */
        $languages = array_unique(
            array_merge($configurationLanguages, $translationLanguages),
        );

        foreach ($languages as $languageCode) {
            $contentBlockerId = Searcher::findObject($contentBlockerPerLanguageList->list, 'key', $languageCode)->value ?? null;
            $contentBlocker = $this->contentBlockerRepository->findById((int) $contentBlockerId, ['contentBlockerLocations']);

            if (!isset($contentBlocker)) {
                continue;
            }

            $this->save(
                $contentBlocker,
                $postData,
            );
        }
    }

    public function save(ContentBlockerModel $contentBlockerModel, array $postData): void
    {
        if (isset($contentBlockerModel->contentBlockerLocations)) {
            foreach ($contentBlockerModel->contentBlockerLocations as $contentBlockerLocation) {
                $this->contentBlockerLocationRepository->delete($contentBlockerLocation);
            }
        }

        foreach ($postData as $newContentBlockerLocationData) {
            if (!$this->contentBlockerLocationValidator->isValid($newContentBlockerLocationData)) {
                continue;
            }

            $newModel = new ContentBlockerLocationModel();
            $newModel->contentBlockerId = $contentBlockerModel->id;
            $newModel->hostname = $newContentBlockerLocationData['hostname'];
            $newModel->path = $newContentBlockerLocationData['path'];
            $this->contentBlockerLocationRepository->insert($newModel);
        }
    }
}
