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

use Borlabs\Cookie\Adapter\WpFunction;
use Borlabs\Cookie\Dto\System\KeyValueDto;
use Borlabs\Cookie\DtoList\System\KeyValueDtoList;
use Borlabs\Cookie\DtoList\System\SettingsFieldDtoList;
use Borlabs\Cookie\DtoList\Translator\LanguageSpecificKeyValueDtoList;
use Borlabs\Cookie\Model\ContentBlocker\ContentBlockerModel;
use Borlabs\Cookie\Model\Provider\ProviderModel;
use Borlabs\Cookie\Model\Service\ServiceModel;
use Borlabs\Cookie\Repository\ContentBlocker\ContentBlockerLocationRepository;
use Borlabs\Cookie\Repository\ContentBlocker\ContentBlockerRepository;
use Borlabs\Cookie\Repository\Provider\ProviderRepository;
use Borlabs\Cookie\Repository\Service\ServiceRepository;
use Borlabs\Cookie\Support\Searcher;
use Borlabs\Cookie\System\Installer\ContentBlocker\ContentBlockerDefaultEntries;
use Borlabs\Cookie\System\Language\Language;
use Borlabs\Cookie\System\Package\Traits\SettingsFieldListTrait;
use Borlabs\Cookie\System\Provider\ProviderService;
use Borlabs\Cookie\System\Script\ScriptConfigBuilder;
use Borlabs\Cookie\System\Service\ServiceService;
use Borlabs\Cookie\System\Style\StyleBuilder;
use Borlabs\Cookie\System\Translator\TranslatorService;
use Borlabs\Cookie\Validator\ContentBlocker\ContentBlockerLanguageStringValidator;
use Borlabs\Cookie\Validator\ContentBlocker\ContentBlockerValidator;

class ContentBlockerService
{
    use SettingsFieldListTrait;

    private ContentBlockerDefaultEntries $contentBlockerDefaultEntries;

    private ContentBlockerDefaultSettingsFieldManager $contentBlockerDefaultSettingsFieldManager;

    private ContentBlockerLanguageStringValidator $contentBlockerLanguageStringValidator;

    private ContentBlockerLocationRepository $contentBlockerLocationRepository;

    private ContentBlockerLocationService $contentBlockerLocationService;

    private ContentBlockerRepository $contentBlockerRepository;

    private ContentBlockerValidator $contentBlockerValidator;

    private Language $language;

    private ProviderRepository $providerRepository;

    private ProviderService $providerService;

    private ScriptConfigBuilder $scriptConfigBuilder;

    private ServiceRepository $serviceRepository;

    private ServiceService $serviceService;

    private StyleBuilder $styleBuilder;

    private TranslatorService $translatorService;

    private WpFunction $wpFunction;

    /**
     * ContentBlockerService constructor.
     */
    public function __construct(
        ContentBlockerDefaultEntries $contentBlockerDefaultEntries,
        ContentBlockerDefaultSettingsFieldManager $contentBlockerDefaultSettingsFieldManager,
        ContentBlockerLanguageStringValidator $contentBlockerLanguageStringValidator,
        ContentBlockerLocationRepository $contentBlockerLocationRepository,
        ContentBlockerLocationService $contentBlockerLocationService,
        ContentBlockerRepository $contentBlockerRepository,
        ContentBlockerValidator $contentBlockerValidator,
        Language $language,
        ProviderRepository $providerRepository,
        ProviderService $providerService,
        ScriptConfigBuilder $scriptConfigBuilder,
        ServiceRepository $serviceRepository,
        ServiceService $serviceService,
        StyleBuilder $styleBuilder,
        TranslatorService $translatorService,
        WpFunction $wpFunction
    ) {
        $this->contentBlockerDefaultEntries = $contentBlockerDefaultEntries;
        $this->contentBlockerDefaultSettingsFieldManager = $contentBlockerDefaultSettingsFieldManager;
        $this->contentBlockerLanguageStringValidator = $contentBlockerLanguageStringValidator;
        $this->contentBlockerLocationRepository = $contentBlockerLocationRepository;
        $this->contentBlockerLocationService = $contentBlockerLocationService;
        $this->contentBlockerRepository = $contentBlockerRepository;
        $this->contentBlockerValidator = $contentBlockerValidator;
        $this->language = $language;
        $this->providerRepository = $providerRepository;
        $this->providerService = $providerService;
        $this->scriptConfigBuilder = $scriptConfigBuilder;
        $this->serviceRepository = $serviceRepository;
        $this->serviceService = $serviceService;
        $this->styleBuilder = $styleBuilder;
        $this->translatorService = $translatorService;
        $this->wpFunction = $wpFunction;
    }

    public function createOrUpdateContentBlockerPerLanguage(
        int $originalContentBlockerId,
        array $postData,
        array $configurationLanguages,
        array $translationLanguages
    ): KeyValueDtoList {
        // Get model with relations
        $originalContentBlocker = $this->contentBlockerRepository->findById($originalContentBlockerId, ['contentBlockerLocations',]);
        $contentBlockerPerLanguageList = new KeyValueDtoList();

        /** @var ProviderModel $provider */
        $provider = $this->providerRepository->findById($originalContentBlocker->providerId);
        $providerPerLanguageList = $this->providerService->getOrCreateProviderPerLanguage(
            $provider->id,
            $configurationLanguages,
            $translationLanguages,
        );

        $servicePerLanguageList = new KeyValueDtoList();

        if (isset($originalContentBlocker->serviceId)) {
            /** @var ServiceModel $service */
            $service = $this->serviceRepository->findById($originalContentBlocker->serviceId);
            $servicePerLanguageList = $this->serviceService->createOrUpdateServicePerLanguage(
                $service->id,
                [],
                $configurationLanguages,
                $translationLanguages,
            );
        }

        $contentBlockerPerLanguage = $this->handleAdditionalLanguages(
            $originalContentBlockerId,
            $postData,
            $configurationLanguages,
            $translationLanguages,
            $providerPerLanguageList,
            $servicePerLanguageList,
        );

        foreach ($contentBlockerPerLanguage->list as $languageContentBlockerId) {
            $contentBlockerPerLanguageList->add(new KeyValueDto($languageContentBlockerId->key, (string) $languageContentBlockerId->value));
        }

        $this->contentBlockerLocationService->handleAdditionalLanguages(
            array_map(static fn ($locationData) => (array) $locationData, $originalContentBlocker->contentBlockerLocations),
            $configurationLanguages,
            $translationLanguages,
            $contentBlockerPerLanguageList,
        );

        $languages = array_unique(
            array_merge($configurationLanguages, $translationLanguages),
        );

        foreach ($languages as $languageCode) {
            $this->scriptConfigBuilder->updateJavaScriptConfigFileAndIncrementConfigVersion($languageCode);
            $this->styleBuilder->updateCssFileAndIncrementStyleVersion(
                $this->wpFunction->getCurrentBlogId(),
                $languageCode,
            );
        }

        return $contentBlockerPerLanguageList;
    }

    public function handleAdditionalLanguages(
        int $originalContentBlockerId,
        array $postData,
        array $configurationLanguages,
        array $translationLanguages,
        KeyValueDtoList $providerPerLanguageList,
        KeyValueDtoList $servicePerLanguageList
    ): KeyValueDtoList {
        $sourceTexts = new KeyValueDtoList();
        $sourceTexts->add(new KeyValueDto('previewHtml', $postData['previewHtml']));

        if (isset($postData['languageStrings']) && is_array($postData['languageStrings'])) {
            foreach ($postData['languageStrings'] as $languageString) {
                $sourceTexts->add(new KeyValueDto('languageString_' . $languageString['key'], $languageString['value']));
            }
        }

        $translations = $this->translatorService->translate(
            $this->language->getSelectedLanguageCode(),
            count($translationLanguages) ? $translationLanguages : $configurationLanguages,
            $sourceTexts,
        );

        $originalContentBlocker = $this->contentBlockerRepository->findById($originalContentBlockerId);
        $contentBlockerPerLanguageList = new KeyValueDtoList();

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
                $originalContentBlocker,
                $postData,
                $languageCode,
                in_array($languageCode, $configurationLanguages, true),
                in_array($languageCode, $translationLanguages, true),
                $translations,
            );

            $providerId = Searcher::findObject($providerPerLanguageList->list, 'key', $languageCode)->value ?? null;
            $serviceId = Searcher::findObject($servicePerLanguageList->list, 'key', $languageCode)->value ?? 0;

            if (!isset($providerId)) {
                continue;
            }

            $preparedPostData['providerId'] = (string) $providerId;
            $preparedPostData['serviceId'] = (string) $serviceId;

            $contentBlockerId = $this->save(
                (int) $preparedPostData['id'],
                $languageCode,
                $preparedPostData,
                $originalContentBlockerId,
            );

            if ($contentBlockerId !== null) {
                $contentBlockerPerLanguageList->add(new KeyValueDto($languageCode, $contentBlockerId));
            }
        }

        return $contentBlockerPerLanguageList;
    }

    public function reset(): bool
    {
        $this->language->runInLanguageContext(
            $this->language->getSelectedLanguageCode(),
            function () {
                $contentBlockerDefaultEntries = $this->contentBlockerDefaultEntries->getDefaultEntries(
                    $this->language->getSelectedLanguageCode(),
                );

                foreach ($contentBlockerDefaultEntries as $defaultContentBlockerModel) {
                    $contentBlockerModel = $this->contentBlockerRepository->getByKey(
                        $defaultContentBlockerModel->key,
                        null,
                        true,
                    );

                    if ($contentBlockerModel) {
                        $defaultContentBlockerModel->id = $contentBlockerModel->id;

                        $this->contentBlockerRepository->update($defaultContentBlockerModel);
                    } else {
                        $contentBlockerModel = $this->contentBlockerRepository->insert($defaultContentBlockerModel);
                    }

                    // Delete content blocker locations
                    if (isset($contentBlockerModel->contentBlockerLocations)) {
                        foreach ($contentBlockerModel->contentBlockerLocations as $contentBlockerLocation) {
                            $this->contentBlockerLocationRepository->delete($contentBlockerLocation);
                        }
                    }
                }
            },
        );

        $this->scriptConfigBuilder->updateJavaScriptConfigFileAndIncrementConfigVersion(
            $this->language->getSelectedLanguageCode(),
        );
        $this->styleBuilder->updateCssFileAndIncrementStyleVersion(
            $this->wpFunction->getCurrentBlogId(),
            $this->language->getSelectedLanguageCode(),
        );

        return true;
    }

    public function save(int $id, string $languageCode, array $postData, ?int $sourceId = null): ?int
    {
        if (
            !$this->contentBlockerValidator->isValid($postData, $languageCode)
            || !$this->contentBlockerLanguageStringValidator->isValid($postData)
        ) {
            return null;
        }

        $postData = $this->wpFunction->applyFilter('borlabsCookie/contentBlocker/modifyPostDataBeforeSaving', $postData);

        if ($id !== -1) {
            $existingModel = $this->contentBlockerRepository->findById($id);
        } elseif ($sourceId !== null) {
            $sourceModel = $this->contentBlockerRepository->findById($sourceId);
        }

        $languageStrings = new KeyValueDtoList();

        if (isset($postData['languageStrings']) && is_array($postData['languageStrings'])) {
            foreach ($postData['languageStrings'] as $languageString) {
                $languageStrings->add(new KeyValueDto($languageString['key'], $languageString['value']));
            }
        }

        $repeatableSettingsFields = $existingModel->repeatableSettingsFields ?? $sourceModel->repeatableSettingsFields ?? null;
        $settingsFields = $existingModel->settingsFields ?? $sourceModel->settingsFields ?? new SettingsFieldDtoList();
        $defaultSettingsFields = $this->contentBlockerDefaultSettingsFieldManager->get($languageCode);

        foreach ($defaultSettingsFields->list as $defaultSettingsField) {
            $settingsFields->add($defaultSettingsField, true);
        }

        $newModel = new ContentBlockerModel();
        $newModel->id = $id;
        $newModel->borlabsServicePackageKey = $existingModel->borlabsServicePackageKey ?? $postData['borlabsServicePackageKey'] ?? null;
        $newModel->description = $existingModel->description ?? '';
        $newModel->javaScriptGlobal = $postData['javaScriptGlobal'] ?? '';
        $newModel->javaScriptInitialization = $postData['javaScriptInitialization'] ?? '';
        $newModel->key = $existingModel->key ?? $postData['key'];
        $newModel->language = $languageCode;
        $newModel->languageStrings = $languageStrings;
        $newModel->name = $postData['name'];
        $newModel->previewCss = $postData['previewCss'] ?? '';
        $newModel->previewHtml = $postData['previewHtml'] ?? '';
        $newModel->previewImage = $postData['previewImage'] ?? '';
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

        $newModel->serviceId = !empty($postData['serviceId']) && $postData['serviceId'] !== '0' ? (int) $postData['serviceId'] : null;
        $newModel->settingsFields = $settingsFields;

        foreach ($postData['settingsFields'] as $settingsFieldsPostData) {
            $newModel->settingsFields = $this->updateSettingsValuesFromFormFields($newModel->settingsFields, $settingsFieldsPostData);
        }

        $newModel->status = (bool) $postData['status'];
        $newModel->undeletable = (bool) ($existingModel->undeletable ?? $postData['undeletable'] ?? false);

        if ($newModel->id !== -1) {
            $this->contentBlockerRepository->update($newModel);
        } else {
            $newModel = $this->contentBlockerRepository->insert($newModel);
        }

        return $newModel->id;
    }

    public function saveWithRelatedData(int $id, string $languageCode, array $postData, ?int $sourceId = null): ?int
    {
        $contentBlockerId = $this->save($id, $languageCode, $postData, $sourceId);

        if ($contentBlockerId === null) {
            return null;
        }

        $contentBlocker = $this->contentBlockerRepository->findById($contentBlockerId, ['contentBlockerLocations',]);
        $this->contentBlockerLocationService->deleteAll($contentBlocker);

        if (isset($contentBlocker, $postData['contentBlockerLocations'])) {
            $this->contentBlockerLocationService->save($contentBlocker, $postData['contentBlockerLocations'] ?? []);
        }

        $this->scriptConfigBuilder->updateJavaScriptConfigFileAndIncrementConfigVersion($languageCode);
        $this->styleBuilder->updateCssFileAndIncrementStyleVersion(
            $this->wpFunction->getCurrentBlogId(),
            $languageCode,
        );

        return $contentBlockerId;
    }

    private function preparePostDataForLanguage(
        ContentBlockerModel $originalContentBlocker,
        array $postData,
        string $languageCode,
        bool $overwriteConfigurationOption = true,
        bool $overwriteTranslationOption = true,
        ?LanguageSpecificKeyValueDtoList $translations = null
    ): array {
        // Check if a Content Blocker for the language exists
        $contentBlocker = $this->contentBlockerRepository->getByKey($originalContentBlocker->key, $languageCode);

        $postData['id'] = '-1';
        $postData['key'] = $originalContentBlocker->key;
        $postData['undeletable'] = $originalContentBlocker->undeletable;

        if ($contentBlocker !== null) {
            $postData['id'] = (string) $contentBlocker->id;
        }

        // Keep configuration if the option is set to false
        if ($overwriteConfigurationOption === false && $contentBlocker !== null) {
            $postData['borlabsServicePackageKey'] = $contentBlocker->borlabsServicePackageKey ?? null;
            $postData['javaScriptGlobal'] = $contentBlocker->javaScriptGlobal;
            $postData['javaScriptInitialization'] = $contentBlocker->javaScriptInitialization;
            $postData['languageStrings'] = array_map(static fn ($keyValueObject) => (array) $keyValueObject, $contentBlocker->languageStrings->list);
            $postData['name'] = $contentBlocker->name;
            $postData['previewCss'] = $contentBlocker->previewCss;
            $postData['previewHtml'] = $contentBlocker->previewHtml;
            $postData['previewImage'] = $contentBlocker->previewImage;
            $postData['providerId'] = (string) $contentBlocker->providerId;
            $postData['serviceId'] = (string) $contentBlocker->serviceId;
            $postData['settingsFields'] = $postData['settingsFields'] ?? [];
            $postData['status'] = (string) $contentBlocker->status;

            foreach ($contentBlocker->settingsFields->list as $settingsField) {
                $postData['settingsFields'][$settingsField->formFieldCollectionName][$settingsField->key] = $settingsField->value;
            }
        } else {
            $postData['borlabsServicePackageKey'] = $originalContentBlocker->borlabsServicePackageKey ?? null;
        }

        // Set translations if the option is set to true or the Content Blocker is being created.
        $postLanguageStrings = $postData['languageStrings'] ?? [];
        $previewHtml = $postData['previewHtml'];

        if ($overwriteTranslationOption || $postData['id'] === '-1') {
            $translation = isset($translations->list) ? Searcher::findObject($translations->list, 'language', $languageCode) : null;

            if (
                isset($translation->translations->list)
                && is_array($translation->translations->list)
                && count($translation->translations->list)
            ) {
                $previewHtml = array_column($translation->translations->list, 'value', 'key')['previewHtml'] ?? $previewHtml;

                foreach ($postLanguageStrings as $index => $languageString) {
                    $postLanguageStrings[$index]['value'] = array_column($translation->translations->list, 'value', 'key')['languageString_' . $languageString['key']] ?? $languageString['value'];
                }
            }
        } else {
            $postLanguageStrings = array_map(static fn ($keyValueObject) => (array) $keyValueObject, $contentBlocker->languageStrings->list ?? []);
            $previewHtml = $contentBlocker->previewHtml;
        }

        $postData['languageStrings'] = $postLanguageStrings;
        $postData['previewHtml'] = $previewHtml;

        return $postData;
    }
}
