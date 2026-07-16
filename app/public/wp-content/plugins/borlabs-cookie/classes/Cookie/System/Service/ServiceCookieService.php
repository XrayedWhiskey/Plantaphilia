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
use Borlabs\Cookie\DtoList\Translator\LanguageSpecificKeyValueDtoList;
use Borlabs\Cookie\Enum\Service\CookiePurposeEnum;
use Borlabs\Cookie\Enum\Service\CookieTypeEnum;
use Borlabs\Cookie\Model\Service\ServiceCookieModel;
use Borlabs\Cookie\Model\Service\ServiceModel;
use Borlabs\Cookie\Repository\Service\ServiceCookieRepository;
use Borlabs\Cookie\Repository\Service\ServiceRepository;
use Borlabs\Cookie\Support\Searcher;
use Borlabs\Cookie\System\Language\Language;
use Borlabs\Cookie\System\Translator\TranslatorService;
use Borlabs\Cookie\Validator\Service\ServiceCookieValidator;

class ServiceCookieService
{
    private Language $language;

    private ServiceCookieRepository $serviceCookieRepository;

    private ServiceCookieValidator $serviceCookieValidator;

    private ServiceRepository $serviceRepository;

    private TranslatorService $translatorService;

    public function __construct(
        Language $language,
        ServiceCookieRepository $serviceCookieRepository,
        ServiceCookieValidator $serviceCookieValidator,
        ServiceRepository $serviceRepository,
        TranslatorService $translatorService
    ) {
        $this->language = $language;
        $this->serviceCookieRepository = $serviceCookieRepository;
        $this->serviceCookieValidator = $serviceCookieValidator;
        $this->serviceRepository = $serviceRepository;
        $this->translatorService = $translatorService;
    }

    public function deleteAll(ServiceModel $serviceModel): void
    {
        if (!isset($serviceModel->serviceCookies)) {
            return;
        }

        foreach ($serviceModel->serviceCookies as $serviceCookie) {
            $this->serviceCookieRepository->delete($serviceCookie);
        }
    }

    public function handleAdditionalLanguages(
        array $postData,
        array $configurationLanguages,
        array $translationLanguages,
        KeyValueDtoList $servicePerLanguageList
    ): void {
        $sourceTexts = new KeyValueDtoList();

        foreach ($postData as $index => $cookieData) {
            $sourceTexts->add(new KeyValueDto($index . '_description', $cookieData['description']));
            $sourceTexts->add(new KeyValueDto($index . '_lifetime', $cookieData['lifetime']));
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
            $service = $this->serviceRepository->findById((int) $serviceId, ['serviceCookies']);

            if (!isset($service)) {
                continue;
            }

            $preparedPostData = $this->preparePostDataForLanguage(
                $service,
                $postData,
                $languageCode,
                in_array($languageCode, $configurationLanguages, true),
                in_array($languageCode, $translationLanguages, true),
                $translations,
            );

            $this->save($service, $preparedPostData);
        }
    }

    public function save(ServiceModel $serviceModel, array $postData): void
    {
        $this->deleteAll($serviceModel);

        foreach ($postData as $newServiceCookieData) {
            if (!$this->serviceCookieValidator->isValid($newServiceCookieData)) {
                continue;
            }

            $newModel = new ServiceCookieModel();
            $newModel->serviceId = $serviceModel->id;
            $newModel->description = $newServiceCookieData['description'];
            $newModel->hostname = $newServiceCookieData['hostname'];
            $newModel->lifetime = $newServiceCookieData['lifetime'];
            $newModel->name = $newServiceCookieData['name'];
            $newModel->path = $newServiceCookieData['path'];
            $newModel->purpose = CookiePurposeEnum::fromValue($newServiceCookieData['purpose']);
            $newModel->type = CookieTypeEnum::fromValue($newServiceCookieData['type']);
            $this->serviceCookieRepository->insert($newModel);
        }
    }

    private function preparePostDataForLanguage(
        ServiceModel $service,
        array $postData,
        string $languageCode,
        bool $overwriteConfigurationOption = true,
        bool $overwriteTranslationOption = true,
        ?LanguageSpecificKeyValueDtoList $translations = null
    ): array {
        foreach ($postData as $index => $cookieData) {
            // Check if a Service Cookie exists
            $serviceCookie = null;

            foreach ($service->serviceCookies as $serviceCookieModel) {
                if ($serviceCookieModel->name === $cookieData['name']
                    && $serviceCookieModel->hostname === $cookieData['hostname']
                    && $serviceCookieModel->path === $cookieData['path']
                ) {
                    $serviceCookie = $serviceCookieModel;

                    break;
                }
            }

            $postData[$index]['id'] = '-1';

            if ($serviceCookie !== null) {
                $postData[$index]['id'] = (string) $serviceCookie->id;
            }

            // Keep configuration if the option is set to false
            if ($overwriteConfigurationOption === false && $serviceCookie !== null) {
                $postData[$index]['hostname'] = (string) $serviceCookie->hostname;
                $postData[$index]['name'] = (string) $serviceCookie->name;
                $postData[$index]['path'] = (string) $serviceCookie->path;
                $postData[$index]['purpose'] = (string) $serviceCookie->purpose;
                $postData[$index]['type'] = (string) $serviceCookie->type;
            }

            // Set translations if the option is set to true or the provider is being created.
            $description = $postData[$index]['description'];
            $lifetime = $postData[$index]['lifetime'];

            if ($overwriteTranslationOption || $postData[$index]['id'] === '-1') {
                $translation = isset($translations->list) ? Searcher::findObject($translations->list, 'language', $languageCode) : null;

                if (
                    isset($translation->translations->list)
                    && is_array($translation->translations->list)
                    && count($translation->translations->list)
                ) {
                    $description = array_column($translation->translations->list, 'value', 'key')[$index . '_description'] ?? $description;
                    $lifetime = array_column($translation->translations->list, 'value', 'key')[$index . '_lifetime'] ?? $lifetime;
                }
            } else {
                $description = $serviceCookie->description;
                $lifetime = $serviceCookie->lifetime;
            }

            $postData[$index]['description'] = $description;
            $postData[$index]['lifetime'] = $lifetime;
        }

        return $postData;
    }
}
