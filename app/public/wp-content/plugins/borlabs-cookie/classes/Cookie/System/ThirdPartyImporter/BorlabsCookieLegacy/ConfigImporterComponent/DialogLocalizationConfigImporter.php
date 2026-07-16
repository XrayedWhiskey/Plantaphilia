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

namespace Borlabs\Cookie\System\ThirdPartyImporter\BorlabsCookieLegacy\ConfigImporterComponent;

use Borlabs\Cookie\System\Config\DialogLocalization;
use Borlabs\Cookie\System\Language\Language;
use Borlabs\Cookie\System\Log\Log;

final class DialogLocalizationConfigImporter
{
    private DialogLocalization $dialogLocalization;

    private Language $language;

    private Log $log;

    public function __construct(DialogLocalization $dialogLocalization, Language $language, Log $log)
    {
        $this->dialogLocalization = $dialogLocalization;
        $this->language = $language;
        $this->log = $log;
    }

    public function import($legacyConfigData, string $languageCode)
    {
        $this->language->loadTextDomain($languageCode);
        $dialogLocalizationDto = $this->dialogLocalization->defaultConfig();
        $dialogLocalizationDto->detailsAcceptAllButton = (string) ($legacyConfigData['cookieBoxPreferenceTextAcceptAllButton'] ?? $dialogLocalizationDto->detailsAcceptAllButton);
        $dialogLocalizationDto->detailsAcceptOnlyEssential = (string) ($legacyConfigData['cookieBoxPreferenceTextRefuseLink'] ?? $dialogLocalizationDto->detailsAcceptOnlyEssential);
        $dialogLocalizationDto->detailsBackLink = (string) ($legacyConfigData['cookieBoxPreferenceTextBackLink'] ?? $dialogLocalizationDto->detailsBackLink);
        $dialogLocalizationDto->detailsDescription = (string) ($legacyConfigData['cookieBoxPreferenceTextDescription'] ?? $dialogLocalizationDto->detailsDescription);
        $dialogLocalizationDto->detailsHeadline = (string) ($legacyConfigData['cookieBoxPreferenceTextHeadline'] ?? $dialogLocalizationDto->detailsHeadline);
        $dialogLocalizationDto->detailsHideMoreInformationLink = (string) ($legacyConfigData['cookieBoxPreferenceTextHideCookieLink'] ?? $dialogLocalizationDto->detailsHideMoreInformationLink);
        $dialogLocalizationDto->detailsSaveConsentButton = (string) ($legacyConfigData['cookieBoxPreferenceTextSaveButton'] ?? $dialogLocalizationDto->detailsSaveConsentButton);
        $dialogLocalizationDto->detailsShowMoreInformationLink = (string) ($legacyConfigData['cookieBoxPreferenceTextShowCookieLink'] ?? $dialogLocalizationDto->detailsShowMoreInformationLink);
        $dialogLocalizationDto->detailsSwitchStatusActive = (string) ($legacyConfigData['cookieBoxPreferenceTextSwitchStatusActive'] ?? $dialogLocalizationDto->detailsSwitchStatusActive);
        $dialogLocalizationDto->detailsSwitchStatusInactive = (string) ($legacyConfigData['cookieBoxPreferenceTextSwitchStatusInactive'] ?? $dialogLocalizationDto->detailsSwitchStatusInactive);
        $dialogLocalizationDto->entranceAcceptAllButton = (string) ($legacyConfigData['cookieBoxPreferenceTextAcceptAllButton'] ?? $dialogLocalizationDto->entranceAcceptAllButton);
        $dialogLocalizationDto->entranceAcceptOnlyEssential = (string) ($legacyConfigData['cookieBoxTextRefuseLink'] ?? $dialogLocalizationDto->entranceAcceptOnlyEssential);
        $dialogLocalizationDto->entranceDescription = (string) ($legacyConfigData['cookieBoxTextDescription'] ?? $dialogLocalizationDto->entranceDescription);
        $dialogLocalizationDto->entranceHeadline = (string) ($legacyConfigData['cookieBoxTextHeadline'] ?? $dialogLocalizationDto->entranceHeadline);
        $dialogLocalizationDto->entrancePreferencesButton = (string) ($legacyConfigData['cookieBoxTextManageLink'] ?? $dialogLocalizationDto->entrancePreferencesButton);
        $dialogLocalizationDto->entrancePreferencesLink = (string) ($legacyConfigData['cookieBoxTextCookieDetailsLink'] ?? $dialogLocalizationDto->entrancePreferencesLink);
        $dialogLocalizationDto->entranceSaveConsentButton = (string) ($legacyConfigData['cookieBoxPreferenceTextSaveButton'] ?? $dialogLocalizationDto->entranceSaveConsentButton);
        $dialogLocalizationDto->imprintLink = (string) ($legacyConfigData['cookieBoxTextImprintLink'] ?? $dialogLocalizationDto->imprintLink);
        $dialogLocalizationDto->legalInformationDescriptionConfirmAge = (string) ($legacyConfigData['cookieBoxTextDescriptionConfirmAge'] ?? $dialogLocalizationDto->legalInformationDescriptionConfirmAge);
        $dialogLocalizationDto->legalInformationDescriptionIndividualSettings = (string) ($legacyConfigData['cookieBoxTextDescriptionIndividualSettings'] ?? $dialogLocalizationDto->legalInformationDescriptionIndividualSettings);
        $dialogLocalizationDto->legalInformationDescriptionMoreInformation = (string) ($legacyConfigData['cookieBoxTextDescriptionMoreInformation'] ?? $dialogLocalizationDto->legalInformationDescriptionMoreInformation);
        $dialogLocalizationDto->legalInformationDescriptionNonEuDataTransfer = (string) ($legacyConfigData['cookieBoxTextDescriptionNonEUDataTransfer'] ?? $dialogLocalizationDto->legalInformationDescriptionNonEuDataTransfer);
        $dialogLocalizationDto->legalInformationDescriptionNoObligation = (string) ($legacyConfigData['cookieBoxTextDescriptionNoObligation'] ?? $dialogLocalizationDto->legalInformationDescriptionNoObligation);
        $dialogLocalizationDto->legalInformationDescriptionPersonalData = (string) ($legacyConfigData['cookieBoxTextDescriptionPersonalData'] ?? $dialogLocalizationDto->legalInformationDescriptionPersonalData);
        $dialogLocalizationDto->legalInformationDescriptionRevoke = (string) ($legacyConfigData['cookieBoxTextDescriptionRevoke'] ?? $dialogLocalizationDto->legalInformationDescriptionRevoke);
        $dialogLocalizationDto->legalInformationDescriptionTechnology = (string) ($legacyConfigData['cookieBoxTextDescriptionTechnology'] ?? $dialogLocalizationDto->legalInformationDescriptionTechnology);
        $dialogLocalizationDto->privacyLink = (string) ($legacyConfigData['cookieBoxTextPrivacyLink'] ?? $dialogLocalizationDto->privacyLink);

        $dialogLocalizationDto->detailsDescription = str_replace('{privacyPageURL}', '{{ privacyPageUrl }}', $dialogLocalizationDto->detailsDescription);
        $dialogLocalizationDto->entranceDescription = str_replace('{privacyPageURL}', '{{ privacyPageUrl }}', $dialogLocalizationDto->entranceDescription);
        $dialogLocalizationDto->legalInformationDescriptionMoreInformation = str_replace('{privacyPageURL}', '{{ privacyPageUrl }}', $dialogLocalizationDto->legalInformationDescriptionMoreInformation);
        $dialogLocalizationDto->legalInformationDescriptionMoreInformation = str_replace(' class="_brlbs-cursor"', '', $dialogLocalizationDto->legalInformationDescriptionMoreInformation);
        $dialogLocalizationDto->legalInformationDescriptionRevoke = str_replace(
            '<a class="_brlbs-cursor" href="#" data-cookie-individual>',
            '<a href="#" role="button" aria-expanded="false" data-borlabs-cookie-actions="preferences">',
            $dialogLocalizationDto->legalInformationDescriptionRevoke,
        );

        $dialogLocalizationSaveStatus = $this->dialogLocalization->save($dialogLocalizationDto, $languageCode);
        $this->language->unloadBlogLanguage();

        $this->log->info(
            '[Import] Dialog localization ({{ languageCode }}) imported: {{ status }}',
            [
                'languageCode' => $languageCode,
                'status' => $dialogLocalizationSaveStatus ? 'Yes' : 'No',
            ],
        );
    }
}
