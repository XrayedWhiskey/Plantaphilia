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

namespace Borlabs\Cookie\System\SetupAssistant;

use Borlabs\Cookie\Adapter\WpFunction;
use Borlabs\Cookie\Dto\SetupAssistant\LanguageSpecificPageUrlByKeywordTypeDto;
use Borlabs\Cookie\Dto\SetupAssistant\SetupConfigurationDto;
use Borlabs\Cookie\Dto\Telemetry\SetupAssistantUsageDto;
use Borlabs\Cookie\DtoList\SetupAssistant\LanguageSpecificPageUrlByKeywordTypeDtoList;
use Borlabs\Cookie\Enum\PageSelection\KeywordTypeEnum;
use Borlabs\Cookie\Exception\GenericException;
use Borlabs\Cookie\Exception\TranslatedException;
use Borlabs\Cookie\Localization\SetupAssistant\SetupAssistantFollowUpMailLocalizationStrings;
use Borlabs\Cookie\Repository\CloudScan\CloudScanSuggestionRepository;
use Borlabs\Cookie\Repository\Package\PackageRepository;
use Borlabs\Cookie\Repository\Provider\ProviderRepository;
use Borlabs\Cookie\Support\Searcher;
use Borlabs\Cookie\System\Config\DialogSettingsConfig;
use Borlabs\Cookie\System\Config\DialogStyleConfig;
use Borlabs\Cookie\System\Config\GeneralConfig;
use Borlabs\Cookie\System\Config\PluginConfig;
use Borlabs\Cookie\System\Language\Language;
use Borlabs\Cookie\System\Log\Log;
use Borlabs\Cookie\System\Mail\MailService;
use Borlabs\Cookie\System\PageSelection\PageSelectionService;
use Borlabs\Cookie\System\Script\ScriptConfigBuilder;
use Borlabs\Cookie\System\Style\StyleBuilder;
use Borlabs\Cookie\System\Telemetry\TelemetryService;
use Borlabs\Cookie\System\Template\Template;
use Borlabs\Cookie\System\ThirdPartyCacheClearer\ThirdPartyCacheClearerManager;

final class SetupAssistantService
{
    private CloudScanSuggestionRepository $cloudScanSuggestionRepository;

    private DialogSettingsConfig $dialogSettingsConfig;

    private DialogStyleConfig $dialogStyleConfig;

    private GeneralConfig $generalConfig;

    private Language $language;

    private Log $log;

    private MailService $mailService;

    private PackageRepository $packageRepository;

    private PageSelectionService $pageSelectionService;

    private PluginConfig $pluginConfig;

    private ProviderRepository $providerRepository;

    private ScriptConfigBuilder $scriptconfigBuilder;

    private StyleBuilder $styleBuilder;

    private TelemetryService $telemetryService;

    private Template $template;

    private ThirdPartyCacheClearerManager $thirdPartyCacheClearerManager;

    private WpFunction $wpFunction;

    public function __construct(
        CloudScanSuggestionRepository $cloudScanSuggestionRepository,
        DialogSettingsConfig $dialogSettingsConfig,
        DialogStyleConfig $dialogStyleConfig,
        GeneralConfig $generalConfig,
        Language $language,
        Log $log,
        MailService $mailService,
        PackageRepository $packageRepository,
        PageSelectionService $pageSelectionService,
        PluginConfig $pluginConfig,
        ProviderRepository $providerRepository,
        ScriptConfigBuilder $scriptconfigBuilder,
        StyleBuilder $styleBuilder,
        TelemetryService $telemetryService,
        Template $template,
        ThirdPartyCacheClearerManager $thirdPartyCacheClearerManager,
        WpFunction $wpFunction
    ) {
        $this->cloudScanSuggestionRepository = $cloudScanSuggestionRepository;
        $this->dialogSettingsConfig = $dialogSettingsConfig;
        $this->dialogStyleConfig = $dialogStyleConfig;
        $this->generalConfig = $generalConfig;
        $this->language = $language;
        $this->log = $log;
        $this->mailService = $mailService;
        $this->packageRepository = $packageRepository;
        $this->pageSelectionService = $pageSelectionService;
        $this->pluginConfig = $pluginConfig;
        $this->providerRepository = $providerRepository;
        $this->scriptconfigBuilder = $scriptconfigBuilder;
        $this->styleBuilder = $styleBuilder;
        $this->telemetryService = $telemetryService;
        $this->template = $template;
        $this->thirdPartyCacheClearerManager = $thirdPartyCacheClearerManager;
        $this->wpFunction = $wpFunction;
    }

    public function detectLegalPages(): LanguageSpecificPageUrlByKeywordTypeDtoList
    {
        $languages = $this->language->getLanguageList()->list;
        $list = new LanguageSpecificPageUrlByKeywordTypeDtoList();

        $keywordTypes = [
            KeywordTypeEnum::IMPRINT(),
            KeywordTypeEnum::PRIVACY(),
        ];

        foreach ($languages as $languageDto) {
            $languageCode = $languageDto->key;

            foreach ($keywordTypes as $keywordType) {
                $list->add($this->buildDetectedPageDto($keywordType, $languageCode));
            }
        }

        return $list;
    }

    public function finalizeSetup(SetupConfigurationDto $setupConfiguration, string $mailRecipient)
    {
        // Store for all languages
        $this->updateConfigurations($setupConfiguration);
        $this->generateAndSendFollowUpMail($setupConfiguration, $mailRecipient);

        if ($setupConfiguration->setupType === null) {
            return;
        }

        try {
            $this->telemetryService->sendTelemetryDataForSetupAssistantUsage(
                new SetupAssistantUsageDto(
                    $setupConfiguration->setupType,
                    $setupConfiguration->setupStartedAt->getTimestamp(),
                    $setupConfiguration->setupFinishedAt->getTimestamp(),
                ),
            );
            $this->telemetryService->sendTelemetryData();
        } catch (TranslatedException $e) {
            $this->log->error('Telemetry data could not be sent', [
                'exceptionMessage' => $e->getTranslatedMessage(),
            ]);
        } catch (GenericException $e) {
            $this->log->error('Telemetry data could not be sent', [
                'exceptionMessage' => $e->getMessage(),
                'exceptionStackTrace' => $e->getTraceAsString(),
                'exceptionType' => get_class($e),
            ]);
        }
    }

    public function generateFiles()
    {
        $languages = $this->language->getLanguageList();

        foreach ($languages->list as $languageOption) {
            $this->scriptconfigBuilder->updateJavaScriptConfigFileAndIncrementConfigVersion($languageOption->key);
            $this->styleBuilder->updateCssFileAndIncrementStyleVersion(
                $this->wpFunction->getCurrentBlogId(),
                $languageOption->key,
            );
        }

        $this->thirdPartyCacheClearerManager->clearCache();
    }

    private function buildDetectedPageDto(KeywordTypeEnum $keywordType, string $languageCode): LanguageSpecificPageUrlByKeywordTypeDto
    {
        return new LanguageSpecificPageUrlByKeywordTypeDto(
            $keywordType,
            $languageCode,
            $this->pageSelectionService->findPageUrlByKeywordType($keywordType, $languageCode),
        );
    }

    private function generateAndSendFollowUpMail(SetupConfigurationDto $setupConfiguration, string $mailRecipient)
    {
        $suggestions = $this->cloudScanSuggestionRepository->getByCloudScanId($setupConfiguration->cloudScanId);

        if (empty($suggestions)) {
            return;
        }

        $packagesWithFollowUpInstructions = [];

        foreach ($suggestions as $suggestion) {
            $package = $this->packageRepository->getByPackageKey($suggestion->borlabsServicePackageKey);

            if ($package === null) {
                continue;
            }

            if (isset($package->translations->list)) {
                $translation = Searcher::findObject($package->translations->list, 'language', $setupConfiguration->selectedLanguageCode);

                if ($translation === null && $setupConfiguration->selectedLanguageCode !== 'en') {
                    // Fallback
                    $translation = Searcher::findObject($package->translations->list, 'language', 'en');
                }

                if ($translation === null || empty($translation->followUp)) {
                    continue;
                }

                $packagesWithFollowUpInstructions[$package->borlabsServicePackageKey] = [
                    'followUp' => $translation->followUp,
                    'name' => $package->name,
                    'thumbnail' => $package->thumbnail,
                ];
            }
        }

        $templateData = [
            'data' => [
                'packages' => $packagesWithFollowUpInstructions,
            ],
        ];

        if (empty($packagesWithFollowUpInstructions)) {
            return;
        }

        $this->language->runInLanguageContext($setupConfiguration->selectedLanguageCode, function () use ($mailRecipient, $templateData) {
            $templateData['localized'] = SetupAssistantFollowUpMailLocalizationStrings::get();
            $mailBody = $this->template->getEngine()->render(
                'mail/library/setup-assistant-follow-up.html.twig',
                $templateData,
            );
            $this->mailService->sendMail(
                $mailRecipient,
                $templateData['localized']['text']['subject'],
                $mailBody,
            );
        });
    }

    private function updateConfigurations(SetupConfigurationDto $setupConfiguration)
    {
        $languages = $this->language->getLanguageList();

        foreach ($languages->list as $languageOption) {
            $generalConfig = $this->generalConfig->load($languageOption->key);
            $generalConfig->borlabsCookieStatus = true;
            $this->generalConfig->save($generalConfig, $languageOption->key);
            $dialogSettingsConfig = $this->dialogSettingsConfig->load($languageOption->key);
            $dialogSettingsConfig->showLogo = $setupConfiguration->showLogo;
            $dialogSettingsConfig->logo = $setupConfiguration->logo;
            $dialogSettingsConfig->logoHd = $setupConfiguration->logo;
            $dialogSettingsConfig->languageOptions = $setupConfiguration->languageOptions;

            $imprintPageData = Searcher::findObject($setupConfiguration->imprintPages->list, 'key', $languageOption->key);

            if ($imprintPageData) {
                $dialogSettingsConfig->imprintPageId = is_numeric($imprintPageData->value) ? (int) $imprintPageData->value : 0;
                $dialogSettingsConfig->imprintPageCustomUrl = filter_var($imprintPageData->value, FILTER_VALIDATE_URL) ? $imprintPageData->value : '';
                $dialogSettingsConfig->imprintPageUrl = $dialogSettingsConfig->imprintPageId ? $this->wpFunction->getPermalink($dialogSettingsConfig->imprintPageId) : $dialogSettingsConfig->imprintPageCustomUrl;
            }

            $privacyPageData = Searcher::findObject($setupConfiguration->privacyPages->list, 'key', $languageOption->key);

            if ($privacyPageData) {
                $dialogSettingsConfig->privacyPageId = is_numeric($privacyPageData->value) ? (int) $privacyPageData->value : 0;
                $dialogSettingsConfig->privacyPageCustomUrl = filter_var($privacyPageData->value, FILTER_VALIDATE_URL) ? $privacyPageData->value : '';
                $dialogSettingsConfig->privacyPageUrl = $dialogSettingsConfig->privacyPageId ? $this->wpFunction->getPermalink($dialogSettingsConfig->privacyPageId) : $dialogSettingsConfig->privacyPageCustomUrl;

                // Update provider
                $ownerOfThisWebsite = $this->providerRepository->getByKey('default', $languageOption->key);

                if ($ownerOfThisWebsite) {
                    $ownerOfThisWebsite->privacyUrl = $dialogSettingsConfig->privacyPageUrl;
                    $this->providerRepository->update($ownerOfThisWebsite);
                }
            }

            $this->dialogSettingsConfig->save($dialogSettingsConfig, $languageOption->key);
            $this->dialogStyleConfig->save($setupConfiguration->dialogStyle, $languageOption->key);
        }

        $pluginConfig = $this->pluginConfig->get();
        $pluginConfig->displayModeSettings = $setupConfiguration->displayModeSettings;
        $pluginConfig->wordPressAdminSidebarMenuMode = $setupConfiguration->wordPressAdminSidebarMenuMode;
        $this->pluginConfig->save($pluginConfig);
    }
}
