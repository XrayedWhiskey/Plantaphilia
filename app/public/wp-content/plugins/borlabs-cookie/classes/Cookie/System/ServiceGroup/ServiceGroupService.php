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

namespace Borlabs\Cookie\System\ServiceGroup;

use Borlabs\Cookie\Adapter\WpFunction;
use Borlabs\Cookie\Dto\System\KeyValueDto;
use Borlabs\Cookie\DtoList\System\KeyValueDtoList;
use Borlabs\Cookie\DtoList\Translator\LanguageSpecificKeyValueDtoList;
use Borlabs\Cookie\Model\ServiceGroup\ServiceGroupModel;
use Borlabs\Cookie\Repository\ServiceGroup\ServiceGroupRepository;
use Borlabs\Cookie\Support\Searcher;
use Borlabs\Cookie\System\Installer\ServiceGroup\ServiceGroupDefaultEntries;
use Borlabs\Cookie\System\Language\Language;
use Borlabs\Cookie\System\Script\ScriptConfigBuilder;
use Borlabs\Cookie\System\Translator\TranslatorService;
use Borlabs\Cookie\Validator\ServiceGroup\ServiceGroupValidator;

class ServiceGroupService
{
    private Language $language;

    private ScriptConfigBuilder $scriptConfigBuilder;

    private ServiceGroupDefaultEntries $serviceGroupDefaultEntries;

    private ServiceGroupRepository $serviceGroupRepository;

    private ServiceGroupValidator $serviceGroupValidator;

    private TranslatorService $translatorService;

    private WpFunction $wpFunction;

    public function __construct(
        Language $language,
        ScriptConfigBuilder $scriptConfigBuilder,
        ServiceGroupDefaultEntries $serviceGroupDefaultEntries,
        ServiceGroupRepository $serviceGroupRepository,
        ServiceGroupValidator $serviceGroupValidator,
        TranslatorService $translatorService,
        WpFunction $wpFunction
    ) {
        $this->language = $language;
        $this->scriptConfigBuilder = $scriptConfigBuilder;
        $this->serviceGroupDefaultEntries = $serviceGroupDefaultEntries;
        $this->serviceGroupRepository = $serviceGroupRepository;
        $this->serviceGroupValidator = $serviceGroupValidator;
        $this->translatorService = $translatorService;
        $this->wpFunction = $wpFunction;
    }

    public function getOrCreateServiceGroupsPerLanguage(
        int $originalServiceGroupId,
        array $configurationLanguages,
        array $translationLanguages
    ): KeyValueDtoList {
        // Get model
        $originalServiceGroup = $this->serviceGroupRepository->findById($originalServiceGroupId);
        $serviceGroups = $this->serviceGroupRepository->getAllByKey($originalServiceGroup->key);
        $serviceGroupsPerLanguage = new KeyValueDtoList();
        $missingLanguages = new KeyValueDtoList();

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
            $serviceGroupModel = Searcher::findObject($serviceGroups, 'language', $languageCode);

            if (isset($serviceGroupModel->id) && $serviceGroupModel->id === $originalServiceGroup->id) {
                continue;
            }

            if ($serviceGroupModel !== null) {
                $serviceGroupsPerLanguage->add(new KeyValueDto($languageCode, (string) $serviceGroupModel->id));
            } else {
                $missingLanguages->add(new KeyValueDto($languageCode, $languageCode));
            }
        }

        // Create missing languages of the Service Group
        if (count($missingLanguages->list)) {
            $newServiceGroupsPerLanguage = $this->handleAdditionalLanguages(
                $originalServiceGroupId,
                [
                    'description' => $originalServiceGroup->description,
                    'key' => $originalServiceGroup->key,
                    'name' => $originalServiceGroup->name,
                    'position' => (string) $originalServiceGroup->position,
                    'preSelected' => (string) $originalServiceGroup->preSelected,
                    'status' => (string) $originalServiceGroup->status,
                ],
                $configurationLanguages,
                $translationLanguages,
            );

            foreach ($newServiceGroupsPerLanguage->list as $languageServiceGroupId) {
                $serviceGroupsPerLanguage->add(new KeyValueDto($languageServiceGroupId->key, (string) $languageServiceGroupId->value));
            }
        }

        foreach ($languages as $languageCode) {
            $this->scriptConfigBuilder->updateJavaScriptConfigFileAndIncrementConfigVersion($languageCode);
        }

        return $serviceGroupsPerLanguage;
    }

    public function handleAdditionalLanguages(
        int $originalServiceGroupId,
        array $postData,
        array $configurationLanguages,
        array $translationLanguages
    ): KeyValueDtoList {
        $translations = $this->translatorService->translate(
            $this->language->getSelectedLanguageCode(),
            count($translationLanguages) ? $translationLanguages : $configurationLanguages,
            new KeyValueDtoList([
                new KeyValueDto('description', $postData['description']),
                new KeyValueDto('name', $postData['name']),
            ]),
        );

        $originalServiceGroup = $this->serviceGroupRepository->findById($originalServiceGroupId);
        $serviceGroupPerLanguageList = new KeyValueDtoList();

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
                $originalServiceGroup,
                $postData,
                $languageCode,
                in_array($languageCode, $configurationLanguages, true),
                in_array($languageCode, $translationLanguages, true),
                $translations,
            );

            $serviceGroupId = $this->save(
                (int) $preparedPostData['id'],
                $languageCode,
                $preparedPostData,
            );

            if ($serviceGroupId !== null) {
                $serviceGroupPerLanguageList->add(new KeyValueDto($languageCode, $serviceGroupId));
            }
        }

        return $serviceGroupPerLanguageList;
    }

    public function reset(): bool
    {
        $this->language->runInLanguageContext(
            $this->language->getSelectedLanguageCode(),
            function () {
                $serviceGroupDefaultEntries = $this->serviceGroupDefaultEntries->getDefaultEntries(
                    $this->language->getSelectedLanguageCode(),
                );

                foreach ($serviceGroupDefaultEntries as $defaultServiceGroupModel) {
                    $serviceGroupModel = $this->serviceGroupRepository->getByKey($defaultServiceGroupModel->key);

                    if ($serviceGroupModel) {
                        $defaultServiceGroupModel->id = $serviceGroupModel->id;

                        $this->serviceGroupRepository->update($defaultServiceGroupModel);
                    } else {
                        $this->serviceGroupRepository->insert($defaultServiceGroupModel);
                    }
                }
            },
        );

        $this->scriptConfigBuilder->updateJavaScriptConfigFileAndIncrementConfigVersion(
            $this->language->getSelectedLanguageCode(),
        );

        return true;
    }

    public function save(int $id, string $languageCode, array $postData): ?int
    {
        if (!$this->serviceGroupValidator->isValid($postData, $languageCode)) {
            return null;
        }

        $postData = $this->wpFunction->applyFilter('borlabsCookie/serviceGroup/modifyPostDataBeforeSaving', $postData);

        if ($id !== -1) {
            $existingModel = $this->serviceGroupRepository->findById($id);
        }

        $newModel = new ServiceGroupModel();
        $newModel->id = $id;
        $newModel->description = $postData['description'];
        $newModel->key = $existingModel->key ?? $postData['key'];
        $newModel->language = $languageCode;
        $newModel->name = $postData['name'];
        $newModel->position = (int) $postData['position'];
        $newModel->preSelected = (bool) $postData['preSelected'];
        $newModel->status = (bool) $postData['status'];
        $newModel->undeletable = (bool) ($existingModel->undeletable ?? $postData['undeletable'] ?? false);

        if ($newModel->id !== -1) {
            $this->serviceGroupRepository->update($newModel);
        } else {
            $newModel = $this->serviceGroupRepository->insert($newModel);
        }

        $this->scriptConfigBuilder->updateJavaScriptConfigFileAndIncrementConfigVersion(
            $languageCode,
        );

        return $newModel->id;
    }

    private function preparePostDataForLanguage(
        ServiceGroupModel $originalServiceGroup,
        array $postData,
        string $languageCode,
        bool $overwriteConfigurationOption = true,
        bool $overwriteTranslationOption = true,
        ?LanguageSpecificKeyValueDtoList $translations = null
    ): array {
        // Check if a service group for the language exists
        $serviceGroup = $this->serviceGroupRepository->getByKey($originalServiceGroup->key, $languageCode);

        $postData['id'] = '-1';
        $postData['key'] = $originalServiceGroup->key;
        $postData['undeletable'] = $originalServiceGroup->undeletable;

        if ($serviceGroup !== null) {
            $postData['id'] = (string) $serviceGroup->id;
        }

        // Keep configuration if the option is set to false
        if ($overwriteConfigurationOption === false && $serviceGroup !== null) {
            $postData['position'] = (string) $serviceGroup->position;
            $postData['preSelected'] = (string) $serviceGroup->preSelected;
            $postData['status'] = (string) $serviceGroup->status;
        }

        // Set translations if the option is set to true or the service group is being created.
        $description = $postData['description'];
        $name = $postData['name'];

        if ($overwriteTranslationOption || $postData['id'] === '-1') {
            $translation = isset($translations->list) ? Searcher::findObject($translations->list, 'language', $languageCode) : null;

            if (
                isset($translation->translations->list)
                && is_array($translation->translations->list)
                && count($translation->translations->list)
            ) {
                $description = array_column($translation->translations->list, 'value', 'key')['description'] ?? $description;
                $name = array_column($translation->translations->list, 'value', 'key')['name'] ?? $name;
            }
        } else {
            $description = $serviceGroup->description;
            $name = $serviceGroup->name;
        }

        $postData['description'] = $description;
        $postData['name'] = $name;

        return $postData;
    }
}
