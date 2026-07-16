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

namespace Borlabs\Cookie\Controller\Admin\Dialog;

use Borlabs\Cookie\Controller\Admin\ControllerInterface;
use Borlabs\Cookie\Dto\System\KeyValueDto;
use Borlabs\Cookie\Dto\System\RequestDto;
use Borlabs\Cookie\DtoList\System\KeyValueDtoList;
use Borlabs\Cookie\Localization\Dialog\DialogLocalizationLocalizationStrings;
use Borlabs\Cookie\Localization\GlobalLocalizationStrings;
use Borlabs\Cookie\Support\Searcher;
use Borlabs\Cookie\System\Config\DialogLocalization;
use Borlabs\Cookie\System\Config\DialogSettingsConfig;
use Borlabs\Cookie\System\Language\Language;
use Borlabs\Cookie\System\Message\MessageManager;
use Borlabs\Cookie\System\Script\ScriptConfigBuilder;
use Borlabs\Cookie\System\Template\Template;
use Borlabs\Cookie\System\ThirdPartyCacheClearer\ThirdPartyCacheClearerManager;
use Borlabs\Cookie\System\Translator\TranslatorService;

/**
 * The **DialogLocalizationController** class takes care of displaying the "Dialog - Localization" section in the
 * backend. It also processes all requests that can be executed in the Dialog - Localization section.
 */
final class DialogLocalizationController implements ControllerInterface
{
    public const CONTROLLER_ID = 'borlabs-cookie-dialog-localization';

    private DialogLocalization $dialogLocalization;

    private DialogSettingsConfig $dialogSettingsConfig;

    private GlobalLocalizationStrings $globalLocalizationStrings;

    private Language $language;

    private MessageManager $messageManager;

    private ScriptConfigBuilder $scriptConfigBuilder;

    private Template $template;

    private ThirdPartyCacheClearerManager $thirdPartyCacheClearerManager;

    private TranslatorService $translatorService;

    public function __construct(
        DialogLocalization $dialogLocalization,
        DialogSettingsConfig $dialogSettingsConfig,
        GlobalLocalizationStrings $globalLocalizationStrings,
        Language $language,
        MessageManager $messageManager,
        ScriptConfigBuilder $scriptConfigBuilder,
        Template $template,
        ThirdPartyCacheClearerManager $thirdPartyCacheClearerManager,
        TranslatorService $translatorService
    ) {
        $this->dialogLocalization = $dialogLocalization;
        $this->dialogSettingsConfig = $dialogSettingsConfig;
        $this->globalLocalizationStrings = $globalLocalizationStrings;
        $this->language = $language;
        $this->messageManager = $messageManager;
        $this->scriptConfigBuilder = $scriptConfigBuilder;
        $this->template = $template;
        $this->thirdPartyCacheClearerManager = $thirdPartyCacheClearerManager;
        $this->translatorService = $translatorService;
    }

    public function reset(): bool
    {
        $this->language->runInLanguageContext(
            $this->language->getSelectedLanguageCode(),
            function () {
                $this->dialogLocalization->save($this->dialogLocalization->defaultConfig(), $this->language->getSelectedLanguageCode());
            },
        );
        $this->scriptConfigBuilder->updateJavaScriptConfigFileAndIncrementConfigVersion(
            $this->language->getSelectedLanguageCode(),
        );
        $this->thirdPartyCacheClearerManager->clearCache();

        $this->messageManager->success($this->globalLocalizationStrings::get()['alert']['resetSuccessfully']);

        return true;
    }

    /**
     * Is loaded by {@see \Borlabs\Cookie\System\WordPressAdminDriver\ControllerManager::load()} and gets information
     * what about to do.
     *
     * @throws \Borlabs\Cookie\Dependencies\Twig\Error\Error
     */
    public function route(RequestDto $request): ?string
    {
        $action = $request->postData['action'] ?? $request->getData['action'] ?? '';

        if ($action === 'reset') {
            $this->reset();
        } elseif ($action === 'save') {
            $this->save($request->postData);
        }

        return $this->viewOverview();
    }

    /**
     * Updates the configuration.
     *
     * @see \Borlabs\Cookie\System\Config\AbstractConfigManagerWithLanguage
     *
     * @param array<string> $postData
     */
    public function save(array $postData): bool
    {
        $localization = $this->dialogLocalization->get();
        $sourceTexts = new KeyValueDtoList();

        // Collect all strings for the translation.
        foreach ($localization as $key => $string) {
            if (isset($postData[$key])) {
                $sourceTexts->add(new KeyValueDto($key, $postData[$key]));
            }
        }

        // Save config for other languages
        $languages = array_keys(
            array_filter(
                $postData['languages']['translation'] ?? [],
                fn ($checked) => $checked === '1',
            ),
        );

        $translations = null;

        if (count($languages)) {
            $translations = $this->translatorService->translate(
                $this->language->getSelectedLanguageCode(),
                $languages,
                $sourceTexts,
            );

            if ($translations === null) {
                $this->messageManager->error($this->globalLocalizationStrings::get()['alert']['apiError']);
            }
        }

        foreach ($languages as $languageCode) {
            $translation = isset($translations->list) ? Searcher::findObject($translations->list, 'language', $languageCode) : null;

            if (
                isset($translation->translations->list)
                && is_array($translation->translations->list)
                && count($translation->translations->list)
            ) {
                foreach ($translation->translations->list as $translatedString) {
                    if (!isset($localization->{$translatedString->key})) {
                        continue;
                    }

                    $localization->{$translatedString->key} = $translatedString->value;
                }
            }

            $this->dialogLocalization->save($localization, $languageCode);
        }

        // Save config for this language. The save routine also updates the current language object.
        foreach ($localization as $key => $string) {
            if (isset($postData[$key])) {
                $localization->{$key} = $postData[$key];
            }
        }

        $this->dialogLocalization->save($localization, $this->language->getSelectedLanguageCode());

        $dialogSettingsConfig = $this->dialogSettingsConfig->get();
        $dialogSettingsConfig->legalInformationDescriptionConfirmAgeStatus = (bool) ($postData['legalInformationDescriptionConfirmAgeStatus'] ?? false);
        $dialogSettingsConfig->legalInformationDescriptionIndividualSettingsStatus = (bool) ($postData['legalInformationDescriptionIndividualSettingsStatus'] ?? false);
        $dialogSettingsConfig->legalInformationDescriptionMoreInformationStatus = (bool) ($postData['legalInformationDescriptionMoreInformationStatus'] ?? false);
        $dialogSettingsConfig->legalInformationDescriptionNonEuDataTransferStatus = (bool) ($postData['legalInformationDescriptionNonEuDataTransferStatus'] ?? false);
        $dialogSettingsConfig->legalInformationDescriptionNoObligationStatus = (bool) ($postData['legalInformationDescriptionNoObligationStatus'] ?? false);
        $dialogSettingsConfig->legalInformationDescriptionPersonalDataStatus = (bool) ($postData['legalInformationDescriptionPersonalDataStatus'] ?? false);
        $dialogSettingsConfig->legalInformationDescriptionRevokeStatus = (bool) ($postData['legalInformationDescriptionRevokeStatus'] ?? false);
        $dialogSettingsConfig->legalInformationDescriptionTechnologyStatus = (bool) ($postData['legalInformationDescriptionTechnologyStatus'] ?? false);

        // Save config for other languages
        $languages = array_keys(
            array_filter(
                $postData['languages']['configuration'] ?? [],
                fn ($checked) => $checked === '1',
            ),
        );

        foreach ($languages as $languageCode) {
            $this->dialogSettingsConfig->save($dialogSettingsConfig, $languageCode);
            $this->scriptConfigBuilder->updateJavaScriptConfigFileAndIncrementConfigVersion(
                $languageCode,
            );
        }

        // Save config for this language. The save routine also updates the current language object.
        $this->dialogSettingsConfig ->save($dialogSettingsConfig, $this->language->getSelectedLanguageCode());
        $this->scriptConfigBuilder->updateJavaScriptConfigFileAndIncrementConfigVersion(
            $this->language->getSelectedLanguageCode(),
        );
        $this->thirdPartyCacheClearerManager->clearCache();

        $this->messageManager->success($this->globalLocalizationStrings::get()['alert']['savedSuccessfully']);

        return true;
    }

    /**
     * Returns the overview.
     *
     * @throws \Borlabs\Cookie\Dependencies\Twig\Error\Error
     */
    public function viewOverview(): string
    {
        $templateData = [];
        $templateData['controllerId'] = self::CONTROLLER_ID;
        $templateData['localized'] = DialogLocalizationLocalizationStrings::get();
        $templateData['localized']['global'] = $this->globalLocalizationStrings::get();
        $templateData['data'] = (array) $this->dialogLocalization->get();
        $templateData['data'] = array_merge($templateData['data'], (array) $this->dialogSettingsConfig->get());
        $templateData['languages'] = $this->language->getLanguageList();

        return $this->template->getEngine()->render(
            'dialog/dialog-localization/dialog-localization.html.twig',
            $templateData,
        );
    }
}
