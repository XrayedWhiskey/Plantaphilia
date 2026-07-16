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

namespace Borlabs\Cookie\Dto\Config;

/**
 * The **DialogLocalizationDto** class is used as a typed object that is passed within the system.
 *
 * The object contains all text strings that are displayed to a visitor of the website.
 *
 * @see \Borlabs\Cookie\System\Config\DialogLocalization
 */
final class DialogLocalizationDto extends AbstractConfigDto
{
    public string $a11yDialogCloseButton = '';

    public string $a11yDialogCloseButtonExplained = '';

    public string $a11yProviderDialogExplained = '';

    public string $a11yProviderListExplained = '';

    public string $a11yRegionServices = '';

    public string $a11yRegionServicesExplained = '';

    public string $a11yServiceGroupListExplained = '';

    public string $a11yWidgetButton = '';

    public string $consentHistoryLoading = '';

    public string $consentHistoryNoData = '';

    public string $consentHistoryTableChoice = '';

    public string $consentHistoryTableConsentGiven = '';

    public string $consentHistoryTableConsents = '';

    public string $consentHistoryTableConsentWithdrawn = '';

    public string $consentHistoryTableDate = '';

    public string $consentHistoryTableVersion = '';

    public string $consentHistoryTableVersionChanges = '';

    public string $consentHistoryTableVersionChangesAdded = '';

    public string $consentHistoryTableVersionChangesRemoved = '';

    public string $detailsAcceptAllButton = '';

    public string $detailsAcceptOnlyEssential = '';

    public string $detailsBackLink = '';

    public string $detailsDescription = '';

    public string $detailsDeselectAll = '';

    public string $detailsHeadline = '';

    public string $detailsHideMoreInformationLink = '';

    public string $detailsSaveConsentButton = '';

    public string $detailsSelectAll = '';

    public string $detailsShowMoreInformationLink = '';

    public string $detailsSwitchStatusActive = '';

    public string $detailsSwitchStatusInactive = '';

    public string $detailsTabConsentHistory = '';

    public string $detailsTabProvider = '';

    public string $detailsTabServiceGroups = '';

    public string $detailsTabServices = '';

    public string $entranceAcceptAllButton = '';

    public string $entranceAcceptOnlyEssential = '';

    public string $entranceDescription = '';

    public string $entranceHeadline = '';

    public string $entranceLanguageSwitcherLink = '';

    public string $entrancePreferencesButton = '';

    public string $entrancePreferencesLink = '';

    public string $entranceSaveConsentButton = '';

    public string $iabTcfA11yPurposeListExplained = '';

    public string $iabTcfA11yRegionTcfVendorConsentHistory = '';

    public string $iabTcfA11yRegionTcfVendorConsentHistoryExplained = '';

    public string $iabTcfA11yRegionVendors = '';

    public string $iabTcfA11yRegionVendorsExplained = '';

    public string $iabTcfA11yServiceGroupListExplained = '';

    public string $iabTcfDataRetention = '';

    public string $iabTcfDataRetentionInDays = '';

    public ?string $iabTcfDescriptionCookieInformation = ''; // Fixme: Remove nullable with 3.4

    public string $iabTcfDescriptionIndiviualSettings = '';

    public string $iabTcfDescriptionLegInt = '';

    public string $iabTcfDescriptionMoreInformation = '';

    public string $iabTcfDescriptionNoCommitment = '';

    public string $iabTcfDescriptionPersonalData = '';

    public string $iabTcfDescriptionRevoke = '';

    public string $iabTcfDescriptionTechnology = '';

    public string $iabTcfHeadlineConsentHistory = '';

    public string $iabTcfHeadlineConsentHistoryNonTcfStandard = '';

    public string $iabTcfHeadlineDataCategories = '';

    public string $iabTcfHeadlineFeatures = '';

    public string $iabTcfHeadlineIllustrations = '';

    public string $iabTcfHeadlineLegitimateInterests = '';

    public string $iabTcfHeadlineNonTcfCategories = '';

    public string $iabTcfHeadlinePurposes = '';

    public string $iabTcfHeadlineSpecialFeatures = '';

    public string $iabTcfHeadlineSpecialPurposes = '';

    public string $iabTcfHeadlineStandardDataRetention = '';

    public string $iabTcfHeadlineStorageInformation = '';

    public string $iabTcfHeadlineVendorAdditionalInformation = '';

    public string $iabTcfHeadlineVendorConsentHistory = '';

    public string $iabTcfNonTcf = '';

    public string $iabTcfShowAllProviders = '';

    public string $iabTcfShowAllVendors = '';

    public string $iabTcfStorageInformationCookieRefresh = '';

    public string $iabTcfStorageInformationDateDay = '';

    public string $iabTcfStorageInformationDateDays = '';

    public string $iabTcfStorageInformationDateLessThanADay = '';

    public string $iabTcfStorageInformationDateMonth = '';

    public string $iabTcfStorageInformationDateMonths = '';

    public string $iabTcfStorageInformationDomain = '';

    public string $iabTcfStorageInformationIdentifier = '';

    public string $iabTcfStorageInformationMaxAgeSeconds = '';

    public string $iabTcfStorageInformationPurposes = '';

    public string $iabTcfStorageInformationStatusNo = '';

    public string $iabTcfStorageInformationStatusYes = '';

    public string $iabTcfStorageInformationType = '';

    public string $iabTcfStorageInformationUsesCookies = '';

    public string $iabTcfStorageInformationUsesNonCookieAccess = '';

    public string $iabTcfTabCategories = '';

    public string $iabTcfTabLegitimateInterest = '';

    public string $iabTcfTabVendors = '';

    public string $iabTcfVendorLegitimateInterestClaim = '';

    public string $iabTcfVendorPlural = '';

    public string $iabTcfVendorPrivacyPolicy = '';

    public string $iabTcfVendorSearchPlaceholder = '';

    public string $iabTcfVendorSingular = '';

    public string $imprintLink = '';

    public string $legalInformationDescriptionConfirmAge = '';

    public string $legalInformationDescriptionIndividualSettings = '';

    public string $legalInformationDescriptionMoreInformation = '';

    public string $legalInformationDescriptionNonEuDataTransfer = '';

    public string $legalInformationDescriptionNoObligation = '';

    public string $legalInformationDescriptionPersonalData = '';

    public string $legalInformationDescriptionRevoke = '';

    public string $legalInformationDescriptionTechnology = '';

    public string $privacyLink = '';

    public string $providerAddress = '';

    public string $providerCloseButton = '';

    public string $providerCookieUrl = '';

    public string $providerDescription = '';

    public string $providerInformationTitle = '';

    public string $providerName = '';

    public string $providerOptOutUrl = '';

    public string $providerPartners = '';

    public string $providerPlural = '';

    public string $providerPrivacyUrl = '';

    public string $providerSearchPlaceholder = '';

    public string $providerSingular = '';

    public string $serviceDetailsTableCookieLifetime = '';

    public string $serviceDetailsTableCookiePurpose = '';

    public string $serviceDetailsTableCookiePurposeFunctional = '';

    public string $serviceDetailsTableCookiePurposeTracking = '';

    public string $serviceDetailsTableCookies = '';

    public string $serviceDetailsTableCookieType = '';

    public string $serviceDetailsTableCookieTypeHttp = '';

    public string $serviceDetailsTableCookieTypeLocalStorage = '';

    public string $serviceDetailsTableCookieTypeSessionStorage = '';

    public string $serviceDetailsTableDescription = '';

    public string $serviceDetailsTableHosts = '';

    public string $serviceDetailsTableName = '';

    public string $serviceDetailsTableServiceOptionDataCollection = '';

    public string $serviceDetailsTableServiceOptionDataPurpose = '';

    public string $serviceDetailsTableServiceOptionDistribution = '';

    public string $serviceDetailsTableServiceOptionLegalBasis = '';

    public string $serviceDetailsTableServiceOptionProcessingLocation = '';

    public string $serviceDetailsTableServiceOptions = '';

    public string $serviceDetailsTableServiceOptionTechnology = '';

    public string $servicePlural = '';

    public string $serviceSearchPlaceholder = '';

    public string $serviceSingular = '';
}
