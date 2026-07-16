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

namespace Borlabs\Cookie\System\Provider;

use Borlabs\Cookie\Adapter\WpFunction;
use Borlabs\Cookie\Dto\System\KeyValueDto;
use Borlabs\Cookie\DtoList\System\KeyValueDtoList;
use Borlabs\Cookie\DtoList\Translator\LanguageSpecificKeyValueDtoList;
use Borlabs\Cookie\Model\Provider\ProviderModel;
use Borlabs\Cookie\Repository\Provider\ProviderRepository;
use Borlabs\Cookie\Support\Searcher;
use Borlabs\Cookie\System\Installer\Provider\ProviderDefaultEntries;
use Borlabs\Cookie\System\Language\Language;
use Borlabs\Cookie\System\Script\ScriptConfigBuilder;
use Borlabs\Cookie\System\Translator\TranslatorService;
use Borlabs\Cookie\Validator\Provider\ProviderValidator;

final class ProviderService
{
    private Language $language;

    private ProviderDefaultEntries $providerDefaultEntries;

    private ProviderRepository $providerRepository;

    private ProviderValidator $providerValidator;

    private ScriptConfigBuilder $scriptConfigBuilder;

    private TranslatorService $translatorService;

    private WpFunction $wpFunction;

    public function __construct(
        Language $language,
        ProviderDefaultEntries $providerDefaultEntries,
        ProviderRepository $providerRepository,
        ProviderValidator $providerValidator,
        ScriptConfigBuilder $scriptConfigBuilder,
        TranslatorService $translatorService,
        WpFunction $wpFunction
    ) {
        $this->language = $language;
        $this->providerDefaultEntries = $providerDefaultEntries;
        $this->providerRepository = $providerRepository;
        $this->providerValidator = $providerValidator;
        $this->scriptConfigBuilder = $scriptConfigBuilder;
        $this->translatorService = $translatorService;
        $this->wpFunction = $wpFunction;
    }

    public function getOrCreateProviderPerLanguage(
        int $originalProviderId,
        array $configurationLanguages,
        array $translationLanguages
    ): KeyValueDtoList {
        // Get model
        $originalProvider = $this->providerRepository->findById($originalProviderId);
        $providers = $this->providerRepository->getAllByKey($originalProvider->key);
        $providersPerLanguage = new KeyValueDtoList();
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
            $providerModel = Searcher::findObject($providers, 'language', $languageCode);

            if (($providerModel->id ?? null) === $originalProvider->id) {
                continue;
            }

            if ($providerModel !== null) {
                $providersPerLanguage->add(new KeyValueDto($languageCode, (string) $providerModel->id));
            } else {
                $missingLanguages->add(new KeyValueDto($languageCode, $languageCode));
            }
        }

        // Create missing languages of the Provider
        if (count($missingLanguages->list)) {
            $newProvidersPerLanguage = $this->handleAdditionalLanguages(
                $originalProviderId,
                [
                    'address' => $originalProvider->address,
                    'borlabsServiceProviderKey' => $originalProvider->borlabsServiceProviderKey ?? null,
                    'cookieUrl' => $originalProvider->cookieUrl,
                    'description' => $originalProvider->description,
                    'iabVendorId' => $originalProvider->iabVendorId,
                    'key' => $originalProvider->key,
                    'name' => $originalProvider->name,
                    'optOutUrl' => $originalProvider->optOutUrl,
                    'partners' => $originalProvider->partners !== null ? implode("\n", $originalProvider->partners) : '',
                    'privacyUrl' => $originalProvider->privacyUrl,
                ],
                $configurationLanguages,
                $translationLanguages,
            );

            foreach ($newProvidersPerLanguage->list as $languageServiceGroupId) {
                $providersPerLanguage->add(new KeyValueDto($languageServiceGroupId->key, (string) $languageServiceGroupId->value));
            }
        }

        return $providersPerLanguage;
    }

    public function handleAdditionalLanguages(
        int $originalProviderId,
        array $postData,
        array $configurationLanguages,
        array $translationLanguages
    ): KeyValueDtoList {
        $translations = $this->translatorService->translate(
            $this->language->getSelectedLanguageCode(),
            count($translationLanguages) ? $translationLanguages : $configurationLanguages,
            new KeyValueDtoList([
                new KeyValueDto('description', $postData['description']),
            ]),
        );

        $originalProvider = $this->providerRepository->findById($originalProviderId);
        $providerPerLanguageList = new KeyValueDtoList();

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
                $originalProvider,
                $postData,
                $languageCode,
                in_array($languageCode, $configurationLanguages, true),
                in_array($languageCode, $translationLanguages, true),
                $translations,
            );

            $providerId = $this->save(
                (int) $preparedPostData['id'],
                $languageCode,
                $preparedPostData,
            );

            if ($providerId !== null) {
                $providerPerLanguageList->add(new KeyValueDto($languageCode, $providerId));
            }
        }

        return $providerPerLanguageList;
    }

    public function reset(): bool
    {
        $this->language->runInLanguageContext(
            $this->language->getSelectedLanguageCode(),
            function () {
                $providerDefaultEntries = $this->providerDefaultEntries->getDefaultEntries(
                    $this->language->getSelectedLanguageCode(),
                );

                foreach ($providerDefaultEntries as $defaultProviderModel) {
                    if (isset($defaultProviderModel->borlabsServiceProviderKey)) {
                        $providerModel = $this->providerRepository->getByBorlabsServiceProviderKey($defaultProviderModel->borlabsServiceProviderKey);
                    } else {
                        $providerModel = $this->providerRepository->getByKey($defaultProviderModel->key);
                    }

                    if ($providerModel) {
                        $defaultProviderModel->id = $providerModel->id;

                        $this->providerRepository->update($defaultProviderModel);
                    } else {
                        $this->providerRepository->insert($defaultProviderModel);
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
        if (!$this->providerValidator->isValid($postData, $languageCode)) {
            return null;
        }

        $postData = $this->wpFunction->applyFilter('borlabsCookie/provider/modifyPostDataBeforeSaving', $postData);

        if ($id !== -1) {
            $existingModel = $this->providerRepository->findById($id);
        }

        $newModel = new ProviderModel();
        $newModel->id = $id;
        $newModel->address = $postData['address'];
        $newModel->borlabsServicePackageKey = $existingModel->borlabsServicePackageKey ?? $postData['borlabsServicePackageKey'] ?? null;
        $newModel->borlabsServiceProviderKey = $existingModel->borlabsServiceProviderKey ?? $postData['borlabsServiceProviderKey'] ?? null;
        $newModel->cookieUrl = $postData['cookieUrl'];
        $newModel->description = $postData['description'];
        $newModel->iabVendorId = !empty($postData['iabVendorId']) && $postData['iabVendorId'] !== '0' ? (int) $postData['iabVendorId'] : null;
        $newModel->key = $existingModel->key ?? $postData['key'];
        $newModel->language = $languageCode;
        $newModel->name = $postData['name'];
        $newModel->optOutUrl = $postData['optOutUrl'];
        $newModel->partners = $postData['partners'] ? explode("\n", $postData['partners']) : [];
        $newModel->privacyUrl = $postData['privacyUrl'];
        $newModel->undeletable = (bool) ($existingModel->undeletable ?? $postData['undeletable'] ?? false);

        if ($newModel->id !== -1) {
            $this->providerRepository->update($newModel);
        } else {
            $newModel = $this->providerRepository->insert($newModel);
        }

        $this->scriptConfigBuilder->updateJavaScriptConfigFileAndIncrementConfigVersion($languageCode);

        return $newModel->id;
    }

    private function preparePostDataForLanguage(
        ProviderModel $originalProvider,
        array $postData,
        string $languageCode,
        bool $overwriteConfigurationOption = true,
        bool $overwriteTranslationOption = true,
        ?LanguageSpecificKeyValueDtoList $translations = null
    ): array {
        // Check if a provider for the language exists
        $provider = $this->providerRepository->getByKey($originalProvider->key, $languageCode);

        $postData['borlabsServicePackageKey'] = $originalProvider->borlabsServicePackageKey ?? null;
        $postData['borlabsServiceProviderKey'] = $originalProvider->borlabsServiceProviderKey ?? null;
        $postData['id'] = '-1';
        $postData['key'] = $originalProvider->key;
        $postData['undeletable'] = $originalProvider->undeletable;

        if ($provider !== null) {
            $postData['id'] = (string) $provider->id;
        }

        // Keep configuration if the option is set to false
        if ($overwriteConfigurationOption === false && $provider !== null) {
            $postData['address'] = (string) $provider->address;
            $postData['borlabsServicePackageKey'] = $provider->borlabsServicePackageKey ?? null;
            $postData['borlabsServiceProviderKey'] = $provider->borlabsServiceProviderKey ?? null;
            $postData['cookieUrl'] = (string) $provider->cookieUrl;
            $postData['iabVendorId'] = $provider->iabVendorId ?? '0';
            $postData['name'] = $provider->name;
            $postData['optOutUrl'] = $provider->optOutUrl;
            $postData['partners'] = implode("\n", $provider->partners ?? []);
            $postData['privacyUrl'] = $provider->privacyUrl;
        }

        // Set translations if the option is set to true or the provider is being created.
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
            $description = $provider->description;
        }

        $postData['description'] = $description;

        return $postData;
    }
}
