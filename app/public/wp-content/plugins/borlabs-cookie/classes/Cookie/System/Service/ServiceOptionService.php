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

use Borlabs\Cookie\Dto\System\KeyValueDto;
use Borlabs\Cookie\DtoList\System\KeyValueDtoList;
use Borlabs\Cookie\Enum\Service\ServiceOptionEnum;
use Borlabs\Cookie\Model\Service\ServiceModel;
use Borlabs\Cookie\Model\Service\ServiceOptionModel;
use Borlabs\Cookie\Repository\Service\ServiceOptionRepository;
use Borlabs\Cookie\Repository\Service\ServiceRepository;
use Borlabs\Cookie\Support\Searcher;
use Borlabs\Cookie\System\Language\Language;
use Borlabs\Cookie\System\Translator\TranslatorService;
use Borlabs\Cookie\Validator\Service\ServiceOptionValidator;

class ServiceOptionService
{
    private Language $language;

    private ServiceOptionRepository $serviceOptionRepository;

    private ServiceOptionValidator $serviceOptionValidator;

    private ServiceRepository $serviceRepository;

    private TranslatorService $translatorService;

    public function __construct(
        Language $language,
        ServiceOptionRepository $serviceOptionRepository,
        ServiceOptionValidator $serviceOptionValidator,
        ServiceRepository $serviceRepository,
        TranslatorService $translatorService
    ) {
        $this->language = $language;
        $this->serviceOptionRepository = $serviceOptionRepository;
        $this->serviceOptionValidator = $serviceOptionValidator;
        $this->serviceRepository = $serviceRepository;
        $this->translatorService = $translatorService;
    }

    public function deleteAll(ServiceModel $serviceModel): void
    {
        if (!isset($serviceModel->serviceOptions)) {
            return;
        }

        foreach ($serviceModel->serviceOptions as $serviceOption) {
            $this->serviceOptionRepository->delete($serviceOption);
        }
    }

    public function handleAdditionalLanguages(
        array $postData,
        array $configurationLanguages,
        array $translationLanguages,
        KeyValueDtoList $servicePerLanguageList
    ): void {
        $sourceTexts = new KeyValueDtoList();

        foreach ($postData as $index => $optionData) {
            $sourceTexts->add(new KeyValueDto($index . '_description', $optionData['description']));
        }

        $translations = $this->translatorService->translate(
            $this->language->getSelectedLanguageCode(),
            count($translationLanguages) ? $translationLanguages : $configurationLanguages,
            $sourceTexts,
        );

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
            $service = $this->serviceRepository->findById((int) $serviceId, ['serviceOptions']);

            if (!isset($service)) {
                continue;
            }

            foreach ($postData as $index => $optionData) {
                $translation = isset($translations->list) ? Searcher::findObject($translations->list, 'language', $languageCode) : null;
                $description = $optionData['description'];

                if (
                    isset($translation->translations->list)
                    && is_array($translation->translations->list)
                    && count($translation->translations->list)
                ) {
                    $description = array_column($translation->translations->list, 'value', 'key')[$index . '_description'] ?? $description;
                }

                $postData[$index]['description'] = $description;
            }

            $this->save($service, $postData);
        }
    }

    public function save(ServiceModel $serviceModel, array $postData): void
    {
        $this->deleteAll($serviceModel);

        foreach ($postData as $newServiceOptionData) {
            if (!$this->serviceOptionValidator->isValid($newServiceOptionData)) {
                continue;
            }

            $newModel = new ServiceOptionModel();
            $newModel->serviceId = $serviceModel->id;
            $newModel->description = $newServiceOptionData['description'];
            $newModel->type = ServiceOptionEnum::fromValue($newServiceOptionData['type']);
            $this->serviceOptionRepository->insert($newModel);
        }
    }
}
