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

namespace Borlabs\Cookie\System\Config;

use Borlabs\Cookie\Dto\Config\DialogLocalizationDto;
use Borlabs\Cookie\Localization\DefaultLocalizationStrings;
use Borlabs\Cookie\System\Language\Language;

/**
 * @extends AbstractConfigManagerWithLanguage<DialogLocalizationDto>
 */
final class DialogLocalization extends AbstractConfigManagerWithLanguage
{
    /**
     * Name of `config_name` where the configuration will be stored. The name is automatically extended with a language
     * code.
     */
    public const CONFIG_NAME = 'DialogLocalization';

    /**
     * The property is set by {@see \Borlabs\Cookie\System\Config\AbstractConfigManagerWithLanguage}.
     */
    public static ?DialogLocalizationDto $baseConfigDto = null;

    /**
     * Returns an {@see \Borlabs\Cookie\Dto\Config\DialogLocalizationDto} object with all properties set to the default
     * values.
     */
    public function defaultConfig(): DialogLocalizationDto
    {
        $defaultConfig = new DialogLocalizationDto();
        $localization = DefaultLocalizationStrings::get()['dialog'];
        $defaultConfig->a11yDialogCloseButton = $localization['a11yDialogCloseButton'];
        $defaultConfig->a11yDialogCloseButtonExplained = $localization['a11yDialogCloseButtonExplained'];
        $defaultConfig->a11yProviderDialogExplained = $localization['a11yProviderDialogExplained'];
        $defaultConfig->a11yProviderListExplained = $localization['a11yProviderListExplained'];
        $defaultConfig->a11yRegionServices = $localization['a11yRegionServices'];
        $defaultConfig->a11yRegionServicesExplained = $localization['a11yRegionServicesExplained'];
        $defaultConfig->a11yServiceGroupListExplained = $localization['a11yServiceGroupListExplained'];
        $defaultConfig->a11yWidgetButton = $localization['a11yWidgetButton'];

        $defaultConfig->consentHistoryLoading = $localization['consentHistoryLoading'];
        $defaultConfig->consentHistoryNoData = $localization['consentHistoryNoData'];
        $defaultConfig->consentHistoryTableChoice = $localization['consentHistoryTableChoice'];
        $defaultConfig->consentHistoryTableConsentGiven = $localization['consentHistoryTableConsentGiven'];
        $defaultConfig->consentHistoryTableConsentWithdrawn = $localization['consentHistoryTableConsentWithdrawn'];
        $defaultConfig->consentHistoryTableDate = $localization['consentHistoryTableDate'];
        $defaultConfig->consentHistoryTableVersion = $localization['consentHistoryTableVersion'];
        $defaultConfig->consentHistoryTableConsents = $localization['consentHistoryTableConsents'];
        $defaultConfig->consentHistoryTableVersionChanges = $localization['consentHistoryTableVersionChanges'];
        $defaultConfig->consentHistoryTableVersionChangesAdded = $localization['consentHistoryTableVersionChangesAdded'];
        $defaultConfig->consentHistoryTableVersionChangesRemoved = $localization['consentHistoryTableVersionChangesRemoved'];

        $defaultConfig->detailsAcceptAllButton = $localization['detailsAcceptAllButton'];
        $defaultConfig->detailsAcceptOnlyEssential = $localization['detailsAcceptOnlyEssential'];
        $defaultConfig->detailsBackLink = $localization['detailsBackLink'];
        $defaultConfig->detailsDescription = $localization['detailsDescription'];
        $defaultConfig->detailsDeselectAll = $localization['detailsDeselectAll'];
        $defaultConfig->detailsHeadline = $localization['detailsHeadline'];
        $defaultConfig->detailsHideMoreInformationLink = $localization['detailsHideMoreInformationLink'];
        $defaultConfig->detailsSaveConsentButton = $localization['detailsSaveConsentButton'];
        $defaultConfig->detailsSelectAll = $localization['detailsSelectAll'];
        $defaultConfig->detailsShowMoreInformationLink = $localization['detailsShowMoreInformationLink'];
        $defaultConfig->detailsSwitchStatusActive = $localization['detailsSwitchStatusActive'];
        $defaultConfig->detailsSwitchStatusInactive = $localization['detailsSwitchStatusInactive'];
        $defaultConfig->detailsTabConsentHistory = $localization['detailsTabConsentHistory'];
        $defaultConfig->detailsTabProvider = $localization['detailsTabProvider'];
        $defaultConfig->detailsTabServices = $localization['detailsTabServices'];
        $defaultConfig->detailsTabServiceGroups = $localization['detailsTabServiceGroups'];

        $defaultConfig->entranceAcceptAllButton = $localization['entranceAcceptAllButton'];
        $defaultConfig->entranceAcceptOnlyEssential = $localization['entranceAcceptOnlyEssential'];
        $defaultConfig->entranceDescription = $localization['entranceDescription'];
        $defaultConfig->entranceHeadline = $localization['entranceHeadline'];
        $defaultConfig->entranceLanguageSwitcherLink = $localization['entranceLanguageSwitcherLink'];
        $defaultConfig->entrancePreferencesButton = $localization['entrancePreferencesButton'];
        $defaultConfig->entrancePreferencesLink = $localization['entrancePreferencesLink'];
        $defaultConfig->entranceSaveConsentButton = $localization['entranceSaveConsentButton'];

        $defaultConfig->iabTcfA11yPurposeListExplained = $localization['iabTcfA11yPurposeListExplained'];
        $defaultConfig->iabTcfA11yRegionTcfVendorConsentHistory = $localization['iabTcfA11yRegionTcfVendorConsentHistory'];
        $defaultConfig->iabTcfA11yRegionTcfVendorConsentHistoryExplained = $localization['iabTcfA11yRegionTcfVendorConsentHistoryExplained'];
        $defaultConfig->iabTcfA11yRegionVendors = $localization['iabTcfA11yRegionVendors'];
        $defaultConfig->iabTcfA11yRegionVendorsExplained = $localization['iabTcfA11yRegionVendorsExplained'];
        $defaultConfig->iabTcfA11yServiceGroupListExplained = $localization['iabTcfA11yServiceGroupListExplained'];
        $defaultConfig->iabTcfDataRetention = $localization['iabTcfDataRetention'];
        $defaultConfig->iabTcfDataRetentionInDays = $localization['iabTcfDataRetentionInDays'];
        $defaultConfig->iabTcfDescriptionCookieInformation = $localization['iabTcfDescriptionCookieInformation'];
        $defaultConfig->iabTcfDescriptionIndiviualSettings = $localization['iabTcfDescriptionIndiviualSettings'];
        $defaultConfig->iabTcfDescriptionLegInt = $localization['iabTcfDescriptionLegInt'];
        $defaultConfig->iabTcfDescriptionMoreInformation = $localization['iabTcfDescriptionMoreInformation'];
        $defaultConfig->iabTcfDescriptionNoCommitment = $localization['iabTcfDescriptionNoCommitment'];
        $defaultConfig->iabTcfDescriptionPersonalData = $localization['iabTcfDescriptionPersonalData'];
        $defaultConfig->iabTcfDescriptionRevoke = $localization['iabTcfDescriptionRevoke'];
        $defaultConfig->iabTcfDescriptionTechnology = $localization['iabTcfDescriptionTechnology'];
        $defaultConfig->iabTcfHeadlineConsentHistory = $localization['iabTcfHeadlineConsentHistory'];
        $defaultConfig->iabTcfHeadlineConsentHistoryNonTcfStandard = $localization['iabTcfHeadlineConsentHistoryNonTcfStandard'];
        $defaultConfig->iabTcfHeadlineDataCategories = $localization['iabTcfHeadlineDataCategories'];
        $defaultConfig->iabTcfHeadlineFeatures = $localization['iabTcfHeadlineFeatures'];
        $defaultConfig->iabTcfHeadlineIllustrations = $localization['iabTcfHeadlineIllustrations'];
        $defaultConfig->iabTcfHeadlineLegitimateInterests = $localization['iabTcfHeadlineLegitimateInterests'];
        $defaultConfig->iabTcfHeadlineNonTcfCategories = $localization['iabTcfHeadlineNonTcfCategories'];
        $defaultConfig->iabTcfHeadlinePurposes = $localization['iabTcfHeadlinePurposes'];
        $defaultConfig->iabTcfHeadlineSpecialFeatures = $localization['iabTcfHeadlineSpecialFeatures'];
        $defaultConfig->iabTcfHeadlineSpecialPurposes = $localization['iabTcfHeadlineSpecialPurposes'];
        $defaultConfig->iabTcfHeadlineStandardDataRetention = $localization['iabTcfHeadlineStandardDataRetention'];
        $defaultConfig->iabTcfHeadlineStorageInformation = $localization['iabTcfHeadlineStorageInformation'];
        $defaultConfig->iabTcfHeadlineVendorAdditionalInformation = $localization['iabTcfHeadlineVendorAdditionalInformation'];
        $defaultConfig->iabTcfHeadlineVendorConsentHistory = $localization['iabTcfHeadlineVendorConsentHistory'];
        $defaultConfig->iabTcfNonTcf = $localization['iabTcfNonTcf'];
        $defaultConfig->iabTcfShowAllProviders = $localization['iabTcfShowAllProviders'];
        $defaultConfig->iabTcfShowAllVendors = $localization['iabTcfShowAllVendors'];
        $defaultConfig->iabTcfStorageInformationCookieRefresh = $localization['iabTcfStorageInformationCookieRefresh'];
        $defaultConfig->iabTcfStorageInformationDateDay = $localization['iabTcfStorageInformationDateDay'];
        $defaultConfig->iabTcfStorageInformationDateDays = $localization['iabTcfStorageInformationDateDays'];
        $defaultConfig->iabTcfStorageInformationDateLessThanADay = $localization['iabTcfStorageInformationDateLessThanADay'];
        $defaultConfig->iabTcfStorageInformationDateMonth = $localization['iabTcfStorageInformationDateMonth'];
        $defaultConfig->iabTcfStorageInformationDateMonths = $localization['iabTcfStorageInformationDateMonths'];
        $defaultConfig->iabTcfStorageInformationDomain = $localization['iabTcfStorageInformationDomain'];
        $defaultConfig->iabTcfStorageInformationIdentifier = $localization['iabTcfStorageInformationIdentifier'];
        $defaultConfig->iabTcfStorageInformationMaxAgeSeconds = $localization['iabTcfStorageInformationMaxAgeSeconds'];
        $defaultConfig->iabTcfStorageInformationPurposes = $localization['iabTcfStorageInformationPurposes'];
        $defaultConfig->iabTcfStorageInformationStatusNo = $localization['iabTcfStorageInformationStatusNo'];
        $defaultConfig->iabTcfStorageInformationStatusYes = $localization['iabTcfStorageInformationStatusYes'];
        $defaultConfig->iabTcfStorageInformationType = $localization['iabTcfStorageInformationType'];
        $defaultConfig->iabTcfStorageInformationUsesCookies = $localization['iabTcfStorageInformationUsesCookies'];
        $defaultConfig->iabTcfStorageInformationUsesNonCookieAccess = $localization['iabTcfStorageInformationUsesNonCookieAccess'];
        $defaultConfig->iabTcfTabCategories = $localization['iabTcfTabCategories'];
        $defaultConfig->iabTcfTabLegitimateInterest = $localization['iabTcfTabLegitimateInterest'];
        $defaultConfig->iabTcfTabVendors = $localization['iabTcfTabVendors'];
        $defaultConfig->iabTcfVendorLegitimateInterestClaim = $localization['iabTcfVendorLegitimateInterestClaim'];
        $defaultConfig->iabTcfVendorPlural = $localization['iabTcfVendorPlural'];
        $defaultConfig->iabTcfVendorPrivacyPolicy = $localization['iabTcfVendorPrivacyPolicy'];
        $defaultConfig->iabTcfVendorSearchPlaceholder = $localization['iabTcfVendorSearchPlaceholder'];
        $defaultConfig->iabTcfVendorSingular = $localization['iabTcfVendorSingular'];

        $defaultConfig->imprintLink = $localization['imprintLink'];

        $defaultConfig->legalInformationDescriptionConfirmAge = $localization['legalInformationDescriptionConfirmAge'];
        $defaultConfig->legalInformationDescriptionIndividualSettings = $localization['legalInformationDescriptionIndividualSettings'];
        $defaultConfig->legalInformationDescriptionMoreInformation = $localization['legalInformationDescriptionMoreInformation'];
        $defaultConfig->legalInformationDescriptionNonEuDataTransfer = $localization['legalInformationDescriptionNonEuDataTransfer'];
        $defaultConfig->legalInformationDescriptionNoObligation = $localization['legalInformationDescriptionNoObligation'];
        $defaultConfig->legalInformationDescriptionPersonalData = $localization['legalInformationDescriptionPersonalData'];
        $defaultConfig->legalInformationDescriptionRevoke = $localization['legalInformationDescriptionRevoke'];
        $defaultConfig->legalInformationDescriptionTechnology = $localization['legalInformationDescriptionTechnology'];

        $defaultConfig->privacyLink = $localization['privacyLink'];

        $defaultConfig->providerAddress = $localization['providerAddress'];
        $defaultConfig->providerCloseButton = $localization['providerCloseButton'];
        $defaultConfig->providerCookieUrl = $localization['providerCookieUrl'];
        $defaultConfig->providerDescription = $localization['providerDescription'];
        $defaultConfig->providerInformationTitle = $localization['providerInformationTitle'];
        $defaultConfig->providerName = $localization['providerName'];
        $defaultConfig->providerOptOutUrl = $localization['providerOptOutUrl'];
        $defaultConfig->providerPartners = $localization['providerPartners'];
        $defaultConfig->providerPlural = $localization['providerPlural'];
        $defaultConfig->providerPrivacyUrl = $localization['providerPrivacyUrl'];
        $defaultConfig->providerSearchPlaceholder = $localization['providerSearchPlaceholder'];
        $defaultConfig->providerSingular = $localization['providerSingular'];

        $defaultConfig->serviceDetailsTableCookieLifetime = $localization['serviceDetailsTableCookieLifetime'];
        $defaultConfig->serviceDetailsTableCookiePurpose = $localization['serviceDetailsTableCookiePurpose'];
        $defaultConfig->serviceDetailsTableCookiePurposeFunctional = $localization['serviceDetailsTableCookiePurposeFunctional'];
        $defaultConfig->serviceDetailsTableCookiePurposeTracking = $localization['serviceDetailsTableCookiePurposeTracking'];
        $defaultConfig->serviceDetailsTableCookieType = $localization['serviceDetailsTableCookieType'];
        $defaultConfig->serviceDetailsTableCookieTypeHttp = $localization['serviceDetailsTableCookieTypeHttp'];
        $defaultConfig->serviceDetailsTableCookieTypeLocalStorage = $localization['serviceDetailsTableCookieTypeLocalStorage'];
        $defaultConfig->serviceDetailsTableCookieTypeSessionStorage = $localization['serviceDetailsTableCookieTypeSessionStorage'];
        $defaultConfig->serviceDetailsTableCookies = $localization['serviceDetailsTableCookies'];
        $defaultConfig->serviceDetailsTableDescription = $localization['serviceDetailsTableDescription'];
        $defaultConfig->serviceDetailsTableHosts = $localization['serviceDetailsTableHosts'];
        $defaultConfig->serviceDetailsTableName = $localization['serviceDetailsTableName'];
        $defaultConfig->serviceDetailsTableServiceOptionDataCollection = $localization['serviceDetailsTableServiceOptionDataCollection'];
        $defaultConfig->serviceDetailsTableServiceOptionDataPurpose = $localization['serviceDetailsTableServiceOptionDataPurpose'];
        $defaultConfig->serviceDetailsTableServiceOptionDistribution = $localization['serviceDetailsTableServiceOptionDistribution'];
        $defaultConfig->serviceDetailsTableServiceOptionLegalBasis = $localization['serviceDetailsTableServiceOptionLegalBasis'];
        $defaultConfig->serviceDetailsTableServiceOptionProcessingLocation = $localization['serviceDetailsTableServiceOptionProcessingLocation'];
        $defaultConfig->serviceDetailsTableServiceOptionTechnology = $localization['serviceDetailsTableServiceOptionTechnology'];
        $defaultConfig->serviceDetailsTableServiceOptions = $localization['serviceDetailsTableServiceOptions'];
        $defaultConfig->servicePlural = $localization['servicePlural'];
        $defaultConfig->serviceSearchPlaceholder = $localization['serviceSearchPlaceholder'];
        $defaultConfig->serviceSingular = $localization['serviceSingular'];

        return $defaultConfig;
    }

    /**
     * This method returns the {@see \Borlabs\Cookie\Dto\Config\DialogLocalizationDto} object with all properties for the
     * language specified when calling the {@see \Borlabs\Cookie\System\Config\DialogLocalization::load()} method.
     */
    public function get(): DialogLocalizationDto
    {
        $this->ensureConfigWasInitialized();

        return self::$baseConfigDto;
    }

    /**
     * Returns the {@see \Borlabs\Cookie\Dto\Config\DialogLocalizationDto} object of the specified language.
     * If no configuration is found for the language, the default text strings are used.
     */
    public function load(string $languageCode): DialogLocalizationDto
    {
        return $this->_load($languageCode);
    }

    /**
     * Saves the configuration of the specified language.
     */
    public function save(DialogLocalizationDto $config, string $languageCode): bool
    {
        return $this->_save($config, $languageCode);
    }
}
