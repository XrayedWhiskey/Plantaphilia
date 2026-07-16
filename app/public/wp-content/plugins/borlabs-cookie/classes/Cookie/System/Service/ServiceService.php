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

use Borlabs\Cookie\Adapter\WpFunction;
use Borlabs\Cookie\Dto\System\KeyValueDto;
use Borlabs\Cookie\DtoList\System\KeyValueDtoList;
use Borlabs\Cookie\DtoList\System\SettingsFieldDtoList;
use Borlabs\Cookie\DtoList\Translator\LanguageSpecificKeyValueDtoList;
use Borlabs\Cookie\Model\Service\ServiceModel;
use Borlabs\Cookie\Repository\Provider\ProviderRepository;
use Borlabs\Cookie\Repository\Service\ServiceCookieRepository;
use Borlabs\Cookie\Repository\Service\ServiceLocationRepository;
use Borlabs\Cookie\Repository\Service\ServiceOptionRepository;
use Borlabs\Cookie\Repository\Service\ServiceRepository;
use Borlabs\Cookie\Repository\ServiceGroup\ServiceGroupRepository;
use Borlabs\Cookie\Support\Searcher;
use Borlabs\Cookie\Support\Transformer;
use Borlabs\Cookie\System\Installer\Service\ServiceDefaultEntries;
use Borlabs\Cookie\System\Language\Language;
use Borlabs\Cookie\System\Package\Traits\SettingsFieldListTrait;
use Borlabs\Cookie\System\Provider\ProviderService;
use Borlabs\Cookie\System\Script\ScriptConfigBuilder;
use Borlabs\Cookie\System\ServiceGroup\ServiceGroupService;
use Borlabs\Cookie\System\Translator\TranslatorService;
use Borlabs\Cookie\Validator\Service\ServiceValidator;

class ServiceService
{
    use SettingsFieldListTrait;

    private Language $language;

    private ProviderRepository $providerRepository;

    private ProviderService $providerService;

    private ScriptConfigBuilder $scriptConfigBuilder;

    private ServiceCookieRepository $serviceCookieRepository;

    private ServiceCookieService $serviceCookieService;

    private ServiceDefaultEntries $serviceDefaultEntries;

    private ServiceDefaultSettingsFieldManager $serviceDefaultSettingsFieldManager;

    private ServiceGroupRepository $serviceGroupRepository;

    private ServiceGroupService $serviceGroupService;

    private ServiceLocationRepository $serviceLocationRepository;

    private ServiceLocationService $serviceLocationService;

    private ServiceOptionRepository $serviceOptionRepository;

    private ServiceOptionService $serviceOptionService;

    private ServiceRepository $serviceRepository;

    private ServiceValidator $serviceValidator;

    private TranslatorService $translatorService;

    private WpFunction $wpFunction;

    public function __construct(
        Language $language,
        ProviderRepository $providerRepository,
        ProviderService $providerService,
        ScriptConfigBuilder $scriptConfigBuilder,
        ServiceCookieRepository $serviceCookieRepository,
        ServiceCookieService $serviceCookieService,
        ServiceDefaultEntries $serviceDefaultEntries,
        ServiceDefaultSettingsFieldManager $serviceDefaultSettingsFieldManager,
        ServiceGroupRepository $serviceGroupRepository,
        ServiceGroupService $serviceGroupService,
        ServiceLocationRepository $serviceLocationRepository,
        ServiceLocationService $serviceLocationService,
        ServiceOptionRepository $serviceOptionRepository,
        ServiceOptionService $serviceOptionService,
        ServiceRepository $serviceRepository,
        ServiceValidator $serviceValidator,
        TranslatorService $translatorService,
        WpFunction $wpFunction
    ) {
        $this->language = $language;
        $this->providerRepository = $providerRepository;
        $this->providerService = $providerService;
        $this->scriptConfigBuilder = $scriptConfigBuilder;
        $this->serviceCookieRepository = $serviceCookieRepository;
        $this->serviceCookieService = $serviceCookieService;
        $this->serviceDefaultEntries = $serviceDefaultEntries;
        $this->serviceDefaultSettingsFieldManager = $serviceDefaultSettingsFieldManager;
        $this->serviceGroupRepository = $serviceGroupRepository;
        $this->serviceGroupService = $serviceGroupService;
        $this->serviceLocationRepository = $serviceLocationRepository;
        $this->serviceLocationService = $serviceLocationService;
        $this->serviceOptionRepository = $serviceOptionRepository;
        $this->serviceOptionService = $serviceOptionService;
        $this->serviceRepository = $serviceRepository;
        $this->serviceValidator = $serviceValidator;
        $this->translatorService = $translatorService;
        $this->wpFunction = $wpFunction;
    }

    public function createOrUpdateServicePerLanguage(
        int $originalServiceId,
        array $postData,
        array $configurationLanguages,
        array $translationLanguages
    ): KeyValueDtoList {
        // Get model with relations
        $originalService = $this->serviceRepository->findById($originalServiceId, ['serviceCookies', 'serviceLocations', 'serviceOptions']);
        $servicePerLanguageList = new KeyValueDtoList();

        $provider = $this->providerRepository->findById($originalService->providerId);
        $providerPerLanguageList = $this->providerService->getOrCreateProviderPerLanguage(
            $provider->id,
            $configurationLanguages,
            $translationLanguages,
        );
        $serviceGroup = $this->serviceGroupRepository->findById($originalService->serviceGroupId);
        $serviceGroupPerLanguageList = $this->serviceGroupService->getOrCreateServiceGroupsPerLanguage(
            $serviceGroup->id,
            $configurationLanguages,
            $translationLanguages,
        );

        if (count($postData) === 0) {
            // Get data from original service
            $postData = [
                'borlabsServicePackageKey' => $originalService->borlabsServicePackageKey ?? null,
                'description' => $originalService->description,
                'fallbackCode' => $originalService->fallbackCode,
                'name' => $originalService->name,
                'optInCode' => $originalService->optInCode,
                'optOutCode' => $originalService->optOutCode,
                'position' => (string) $originalService->position,
                'providerId' => (string) $originalService->providerId,
                'serviceGroupId' => (string) $originalService->serviceGroupId,
                'status' => (string) $originalService->status,
                'settingsFields' => array_map(
                    static fn ($settingsField) => [
                        $settingsField->formFieldCollectionName => [
                            $settingsField->key => $settingsField->value,
                        ],
                    ],
                    $originalService->settingsFields->list ?? [],
                ),
            ];
        }

        $servicePerLanguage = $this->handleAdditionalLanguages(
            $originalServiceId,
            $postData,
            $configurationLanguages,
            $translationLanguages,
            $providerPerLanguageList,
            $serviceGroupPerLanguageList,
        );

        foreach ($servicePerLanguage->list as $languageServiceId) {
            $servicePerLanguageList->add(new KeyValueDto($languageServiceId->key, (string) $languageServiceId->value));
        }

        $this->serviceCookieService->handleAdditionalLanguages(
            array_map(static fn ($cookieData) => (array) $cookieData, Transformer::arrayValuesToString($originalService->serviceCookies ?? (object) [])),
            $configurationLanguages,
            $translationLanguages,
            $servicePerLanguageList,
        );

        $this->serviceLocationService->handleAdditionalLanguages(
            array_map(static fn ($locationData) => (array) $locationData, Transformer::arrayValuesToString($originalService->serviceLocations) ?? (object) []),
            $configurationLanguages,
            $translationLanguages,
            $servicePerLanguageList,
        );

        $this->serviceOptionService->handleAdditionalLanguages(
            array_map(static fn ($optionData) => (array) $optionData, Transformer::arrayValuesToString($originalService->serviceOptions) ?? (object) []),
            $configurationLanguages,
            $translationLanguages,
            $servicePerLanguageList,
        );

        $languages = array_unique(
            array_merge($configurationLanguages, $translationLanguages),
        );

        foreach ($languages as $languageCode) {
            $this->scriptConfigBuilder->updateJavaScriptConfigFileAndIncrementConfigVersion($languageCode);
        }

        return $servicePerLanguageList;
    }

    public function handleAdditionalLanguages(
        int $originalServiceId,
        array $postData,
        array $configurationLanguages,
        array $translationLanguages,
        KeyValueDtoList $providerPerLanguageList,
        KeyValueDtoList $serviceGroupPerLanguageList
    ): KeyValueDtoList {
        $translations = $this->translatorService->translate(
            $this->language->getSelectedLanguageCode(),
            count($translationLanguages) ? $translationLanguages : $configurationLanguages,
            new KeyValueDtoList([
                new KeyValueDto('description', $postData['description']),
            ]),
        );

        $originalService = $this->serviceRepository->findById($originalServiceId);
        $servicePerLanguageList = new KeyValueDtoList();

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
            $preparedPostData = $this->preparePostDataForLanguage(
                $originalService,
                $postData,
                $languageCode,
                in_array($languageCode, $configurationLanguages, true),
                in_array($languageCode, $translationLanguages, true),
                $translations,
            );

            $providerId = Searcher::findObject($providerPerLanguageList->list, 'key', $languageCode)->value ?? null;
            $serviceGroupId = Searcher::findObject($serviceGroupPerLanguageList->list, 'key', $languageCode)->value ?? null;

            if (!isset($providerId, $serviceGroupId)) {
                continue;
            }

            $preparedPostData['providerId'] = (string) $providerId;
            $preparedPostData['serviceGroupId'] = (string) $serviceGroupId;

            $serviceId = $this->save(
                (int) $preparedPostData['id'],
                $languageCode,
                $preparedPostData,
                $originalServiceId,
            );

            if ($serviceId !== null) {
                $servicePerLanguageList->add(new KeyValueDto($languageCode, $serviceId));
            }
        }

        return $servicePerLanguageList;
    }

    public function reset(): bool
    {
        $this->language->runInLanguageContext(
            $this->language->getSelectedLanguageCode(),
            function () {
                $serviceDefaultEntries = $this->serviceDefaultEntries->getDefaultEntries(
                    $this->language->getSelectedLanguageCode(),
                );

                foreach ($serviceDefaultEntries as $defaultServiceModel) {
                    $serviceModel = $this->serviceRepository->getByKey(
                        $defaultServiceModel->key,
                        null,
                        true,
                    );

                    if ($serviceModel) {
                        $defaultServiceModel->id = $serviceModel->id;
                        $defaultServiceModel->serviceGroupId = $defaultServiceModel->serviceGroup->id;
                        $this->serviceRepository->update($defaultServiceModel);
                    } else {
                        $serviceModel = $this->serviceRepository->insert($defaultServiceModel);
                    }

                    // Delete service cookies
                    if (isset($serviceModel->serviceCookies)) {
                        foreach ($serviceModel->serviceCookies as $serviceCookie) {
                            $this->serviceCookieRepository->delete($serviceCookie);
                        }
                    }

                    // Delete service locations
                    if (isset($serviceModel->serviceLocations)) {
                        foreach ($serviceModel->serviceLocations as $serviceLocation) {
                            $this->serviceLocationRepository->delete($serviceLocation);
                        }
                    }

                    // Delete service options
                    if (isset($serviceModel->serviceOptions)) {
                        foreach ($serviceModel->serviceOptions as $serviceOption) {
                            $this->serviceOptionRepository->delete($serviceOption);
                        }
                    }

                    // Add service cookies
                    if (isset($defaultServiceModel->serviceCookies)) {
                        foreach ($defaultServiceModel->serviceCookies as $serviceCookie) {
                            $serviceCookie->serviceId = $serviceModel->id;
                            $this->serviceCookieRepository->insert($serviceCookie);
                        }
                    }
                }
            },
        );

        $this->scriptConfigBuilder->updateJavaScriptConfigFileAndIncrementConfigVersion(
            $this->language->getSelectedLanguageCode(),
        );

        return true;
    }

    public function save(int $id, string $languageCode, array $postData, ?int $sourceId = null): ?int
    {
        if (!$this->serviceValidator->isValid($postData, $languageCode)) {
            return null;
        }

        $postData = $this->wpFunction->applyFilter('borlabsCookie/service/modifyPostDataBeforeSaving', $postData);

        if ($id !== -1) {
            $existingModel = $this->serviceRepository->findById($id);
        } elseif ($sourceId !== null) {
            $sourceModel = $this->serviceRepository->findById($sourceId);
        }

        $repeatableSettingsFields = $existingModel->repeatableSettingsFields ?? $sourceModel->repeatableSettingsFields ?? null;
        $settingsFields = $existingModel->settingsFields ?? $sourceModel->settingsFields ?? new SettingsFieldDtoList();
        $defaultSettingsFields = $this->serviceDefaultSettingsFieldManager->get($languageCode);

        foreach ($defaultSettingsFields->list as $defaultSettingsField) {
            $settingsFields->add($defaultSettingsField, true);
        }

        $newModel = new ServiceModel();
        $newModel->id = $id;
        $newModel->borlabsServicePackageKey = $existingModel->borlabsServicePackageKey ?? $postData['borlabsServicePackageKey'] ?? null;
        $newModel->description = $postData['description'];
        $newModel->fallbackCode = $postData['fallbackCode'];
        $newModel->key = $existingModel->key ?? $postData['key'];
        $newModel->language = $languageCode;
        $newModel->name = $postData['name'];
        $newModel->optInCode = $postData['optInCode'];
        $newModel->optOutCode = $postData['optOutCode'];
        $newModel->position = (int) $postData['position'];
        $newModel->providerId = (int) $postData['providerId'];
        $newModel->repeatableSettingsFields = $repeatableSettingsFields;

        if (isset($postData['repeatableSettingsFields'], $repeatableSettingsFields)) {
            foreach ($postData['repeatableSettingsFields'] as $repeatableSettingsFieldsPostData) {
                $newModel->repeatableSettingsFields = $this->updateRepeatableSettingsValuesFromFormFields(
                    $newModel->repeatableSettingsFields,
                    $repeatableSettingsFieldsPostData,
                );
            }
        }

        $newModel->settingsFields = $settingsFields;

        foreach ($postData['settingsFields'] as $settingsFieldsPostData) {
            $newModel->settingsFields = $this->updateSettingsValuesFromFormFields($newModel->settingsFields, $settingsFieldsPostData);
        }

        $newModel->serviceGroupId = (int) $postData['serviceGroupId'];
        $newModel->status = (bool) $postData['status'];
        $newModel->undeletable = (bool) ($existingModel->undeletable ?? $postData['undeletable'] ?? false);

        if ($newModel->id !== -1) {
            $this->serviceRepository->update($newModel);
        } else {
            $newModel = $this->serviceRepository->insert($newModel);
        }

        return $newModel->id;
    }

    public function saveWithRelatedData(int $id, string $languageCode, array $postData, ?int $sourceId = null): ?int
    {
        $serviceId = $this->save($id, $languageCode, $postData, $sourceId);

        if ($serviceId === null) {
            return null;
        }

        $service = $this->serviceRepository->findById($serviceId, ['serviceCookies', 'serviceLocations', 'serviceOptions']);
        $this->serviceCookieService->deleteAll($service);

        if (isset($service, $postData['serviceCookies'])) {
            $this->serviceCookieService->save($service, $postData['serviceCookies']);
        }

        $this->serviceLocationService->deleteAll($service);

        if (isset($service, $postData['serviceLocations'])) {
            $this->serviceLocationService->save($service, $postData['serviceLocations']);
        }

        $this->serviceOptionService->deleteAll($service);

        if (isset($service, $postData['serviceOptions'])) {
            $this->serviceOptionService->save($service, $postData['serviceOptions']);
        }

        $this->scriptConfigBuilder->updateJavaScriptConfigFileAndIncrementConfigVersion($languageCode);

        return $serviceId;
    }

    private function preparePostDataForLanguage(
        ServiceModel $originalService,
        array $postData,
        string $languageCode,
        bool $overwriteConfigurationOption = true,
        bool $overwriteTranslationOption = true,
        ?LanguageSpecificKeyValueDtoList $translations = null
    ): array {
        // Check if a service for the language exists
        $service = $this->serviceRepository->getByKey($originalService->key, $languageCode);

        $postData['id'] = '-1';
        $postData['key'] = $originalService->key;
        $postData['undeletable'] = $originalService->undeletable;

        if ($service !== null) {
            $postData['id'] = (string) $service->id;
        }

        // Keep configuration if the option is set to false
        if ($overwriteConfigurationOption === false && $service !== null) {
            $postData['borlabsServicePackageKey'] = $service->borlabsServicePackageKey ?? null;
            $postData['fallbackCode'] = (string) $service->fallbackCode;
            $postData['name'] = (string) $service->name;
            $postData['optInCode'] = $service->optInCode;
            $postData['optOutCode'] = $service->optOutCode;
            $postData['position'] = (string) $service->position;
            $postData['providerId'] = (string) $service->providerId;
            $postData['serviceGroupId'] = (string) $service->serviceGroupId;
            $postData['settingsFields'] = $postData['settingsFields'] ?? [];
            $postData['status'] = (string) $service->status;

            foreach ($service->settingsFields->list as $settingsField) {
                $postData['settingsFields'][$settingsField->formFieldCollectionName][$settingsField->key] = $settingsField->value;
            }
        } else {
            $postData['borlabsServicePackageKey'] = $originalService->borlabsServicePackageKey ?? null;
        }

        // Set translations if the option is set to true or the service is being created.
        $description = $postData['description'];

        if ($overwriteTranslationOption || $postData['id'] === '-1') {
            $translation = isset($translations->list) ? Searcher::findObject($translations->list, 'language', $languageCode) : null;

            if (
                isset($translation->translations->list)
                && is_array($translation->translations->list)
                && count($translation->translations->list)
            ) {
                $description = array_column($translation->translations->list, 'value', 'key')['description'] ?? $description;
            }
        } else {
            $description = $service->description;
        }

        $postData['description'] = $description;

        return $postData;
    }
}
