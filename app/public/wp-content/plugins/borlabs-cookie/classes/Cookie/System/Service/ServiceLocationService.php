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

namespace Borlabs\Cookie\System\Service;

use Borlabs\Cookie\DtoList\System\KeyValueDtoList;
use Borlabs\Cookie\Model\Service\ServiceLocationModel;
use Borlabs\Cookie\Model\Service\ServiceModel;
use Borlabs\Cookie\Repository\Service\ServiceLocationRepository;
use Borlabs\Cookie\Repository\Service\ServiceRepository;
use Borlabs\Cookie\Support\Searcher;
use Borlabs\Cookie\Validator\Service\ServiceLocationValidator;

class ServiceLocationService
{
    private ServiceLocationRepository $serviceLocationRepository;

    private ServiceLocationValidator $serviceLocationValidator;

    private ServiceRepository $serviceRepository;

    public function __construct(
        ServiceLocationRepository $serviceLocationRepository,
        ServiceLocationValidator $serviceLocationValidator,
        ServiceRepository $serviceRepository
    ) {
        $this->serviceLocationRepository = $serviceLocationRepository;
        $this->serviceLocationValidator = $serviceLocationValidator;
        $this->serviceRepository = $serviceRepository;
    }

    public function deleteAll(ServiceModel $serviceModel): void
    {
        if (!isset($serviceModel->serviceLocations)) {
            return;
        }

        foreach ($serviceModel->serviceLocations as $serviceLocation) {
            $this->serviceLocationRepository->delete($serviceLocation);
        }
    }

    public function handleAdditionalLanguages(
        array $postData,
        array $configurationLanguages,
        array $translationLanguages,
        KeyValueDtoList $servicePerLanguageList
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
            $serviceId = Searcher::findObject($servicePerLanguageList->list, 'key', $languageCode)->value ?? null;
            $service = $this->serviceRepository->findById((int) $serviceId, ['serviceLocations']);

            if (!isset($service)) {
                continue;
            }

            $this->save(
                $service,
                $postData,
            );
        }
    }

    public function save(ServiceModel $serviceModel, array $postData): void
    {
        $this->deleteAll($serviceModel);

        foreach ($postData as $newServiceLocationData) {
            if (!$this->serviceLocationValidator->isValid($newServiceLocationData)) {
                continue;
            }

            $newModel = new ServiceLocationModel();
            $newModel->serviceId = $serviceModel->id;
            $newModel->hostname = $newServiceLocationData['hostname'];
            $newModel->path = $newServiceLocationData['path'];
            $this->serviceLocationRepository->insert($newModel);
        }
    }
}
