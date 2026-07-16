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

namespace Borlabs\Cookie\Localization\Dialog;

use Borlabs\Cookie\Localization\LocalizationInterface;

use function Borlabs\Cookie\System\WordPressGlobalFunctions\_x;

/**
 * The **DialogLocalizationLocalizationStrings** class contains various localized strings.
 *
 * @see \Borlabs\Cookie\Localization\Dialog\DialogLocalizationLocalizationStrings::get()
 */
final class DialogLocalizationLocalizationStrings implements LocalizationInterface
{
    /**
     * @return array<array<string>>
     */
    public static function get(): array
    {
        return [
            // Alert messages
            'alert' => [
            ],

            // Breadcrumbs
            'breadcrumb' => [
                'module' => _x(
                    'Dialog - Localization',
                    'Backend / Settings / Breadcrumb',
                    'borlabs-cookie',
                ),
            ],

            // Fields
            'field' => [
                'a11yDialogCloseButton' => _x(
                    'Dialog Close Button - Label',
                    'Backend / Dialog Localization / Label',
                    'borlabs-cookie',
                ),
                'a11yDialogCloseButtonExplained' => _x(
                    'Dialog Close Button - Explanation',
                    'Backend / Dialog Localization / Label',
                    'borlabs-cookie',
                ),
                'a11yProviderDialogExplained' => _x(
                    'Provider Dialog - Explanation',
                    'Backend / Dialog Localization / Label',
                    'borlabs-cookie',
                ),
                'a11yProviderListExplained' => _x(
                    'Provider List - Explanation',
                    'Backend / Dialog Localization / Label',
                    'borlabs-cookie',
                ),
                'a11yRegionServices' => _x(
                    'Region Services - Label',
                    'Backend / Dialog Localization / Label',
                    'borlabs-cookie',
                ),
                'a11yRegionServicesExplained' => _x(
                    'Region Services - Explanation',
                    'Backend / Dialog Localization / Label',
                    'borlabs-cookie',
                ),
                'a11yServiceGroupListExplained' => _x(
                    'Service Group List - Explanation',
                    'Backend / Dialog Localization / Label',
                    'borlabs-cookie',
                ),
                'a11yWidgetButton' => _x(
                    'Widget Button - Label',
                    'Backend / Dialog Localization / Label',
                    'borlabs-cookie',
                ),
                'consentHistoryLoading' => _x(
                    'Loading - Text',
                    'Backend / Dialog Localization / Label',
                    'borlabs-cookie',
                ),
                'consentHistoryNoData' => _x(
                    'No Consent Data - Text',
                    'Backend / Dialog Localization / Label',
                    'borlabs-cookie',
                ),
                'consentHistoryTableChoice' => _x(
                    'Choice',
                    'Backend / Dialog Localization / Label',
                    'borlabs-cookie',
                ),
                'consentHistoryTableConsentGiven' => _x(
                    'Consent given',
                    'Backend / Dialog Localization / Label',
                    'borlabs-cookie',
                ),
                'consentHistoryTableConsentWithdrawn' => _x(
                    'Consent Withdrawn',
                    'Backend / Dialog Localization / Label',
                    'borlabs-cookie',
                ),
                'consentHistoryTableConsents' => _x(
                    'Consents',
                    'Backend / Dialog Localization / Label',
                    'borlabs-cookie',
                ),
                'consentHistoryTableDate' => _x(
                    'Date',
                    'Backend / Dialog Localization / Label',
                    'borlabs-cookie',
                ),
                'consentHistoryTableVersion' => _x(
                    'Version',
                    'Backend / Dialog Localization / Label',
                    'borlabs-cookie',
                ),
                'consentHistoryTableVersionChanges' => _x(
                    'Changes',
                    'Backend / Dialog Localization / Label',
                    'borlabs-cookie',
                ),
                'consentHistoryTableVersionChangesAdded' => _x(
                    'Changes - Added',
                    'Backend / Dialog Localization / Label',
                    'borlabs-cookie',
                ),
                'consentHistoryTableVersionChangesRemoved' => _x(
                    'Changes - Removed',
                    'Backend / Dialog Localization / Label',
                    'borlabs-cookie',
                ),
                'detailsAcceptAllButton' => _x(
                    'Accept All - Button Text',
                    'Backend / Dialog Localization / Label',
                    'borlabs-cookie',
                ),
                'detailsAcceptOnlyEssential' => _x(
                    'Accept Only Essential - Button Text',
                    'Backend / Dialog Localization / Label',
                    'borlabs-cookie',
                ),
                'detailsBackLink' => _x(
                    'Back - Link Text',
                    'Backend / Dialog Localization / Label',
                    'borlabs-cookie',
                ),
                'detailsDescription' => _x(
                    'Description',
                    'Backend / Dialog Localization / Label',
                    'borlabs-cookie',
                ),
                'detailsDeselectAll' => _x(
                    'Deselect All - Link Text',
                    'Backend / IAB TCF Cookie Box / Link Text',
                    'borlabs-cookie',
                ),
                'detailsHeadline' => _x(
                    'Headline',
                    'Backend / Dialog Localization / Label',
                    'borlabs-cookie',
                ),
                'detailsHideMoreInformationLink' => _x(
                    'Hide More Information - Link Text',
                    'Backend / Dialog Localization / Label',
                    'borlabs-cookie',
                ),
                'detailsSaveConsentButton' => _x(
                    'Save Consent - Button Text',
                    'Backend / Dialog Localization / Label',
                    'borlabs-cookie',
                ),
                'detailsSelectAll' => _x(
                    'Select All - Link Text',
                    'Backend / IAB TCF Cookie Box / Link Text',
                    'borlabs-cookie',
                ),
                'detailsShowMoreInformationLink' => _x(
                    'Show More Information - Link Text',
                    'Backend / Dialog Localization / Label',
                    'borlabs-cookie',
                ),
                'detailsSwitchStatusActive' => _x(
                    'Switch Status - Active',
                    'Backend / Dialog Localization / Label',
                    'borlabs-cookie',
                ),
                'detailsSwitchStatusInactive' => _x(
                    'Switch Status - Inactive',
                    'Backend / Dialog Localization / Label',
                    'borlabs-cookie',
                ),
                'detailsTabConsentHistory' => _x(
                    'Consent History - Tab Title',
                    'Backend / Dialog Localization / Label',
                    'borlabs-cookie',
                ),
                'detailsTabProvider' => _x(
                    'Provider - Tab Title',
                    'Backend / Dialog Localization / Label',
                    'borlabs-cookie',
                ),
                'detailsTabServiceGroups' => _x(
                    'Service Group - Tab Title',
                    'Backend / Dialog Localization / Label',
                    'borlabs-cookie',
                ),
                'detailsTabServices' => _x(
                    'Services - Tab Title',
                    'Backend / Dialog Localization / Label',
                    'borlabs-cookie',
                ),
                'entranceAcceptAllButton' => _x(
                    'Accept All - Button Text',
                    'Backend / Dialog Localization / Label',
                    'borlabs-cookie',
                ),
                'entranceAcceptOnlyEssential' => _x(
                    'Accept Only Essential - Button Text',
                    'Backend / Dialog Localization / Label',
                    'borlabs-cookie',
                ),
                'entranceDescription' => _x(
                    'Description',
                    'Backend / Dialog Localization / Label',
                    'borlabs-cookie',
                ),
                'entranceHeadline' => _x(
                    'Headline',
                    'Backend / Dialog Localization / Label',
                    'borlabs-cookie',
                ),
                'entranceLanguageSwitcherLink' => _x(
                    'Language Switcher - Link Text',
                    'Backend / Dialog Localization / Label',
                    'borlabs-cookie',
                ),
                'entrancePreferencesButton' => _x(
                    'Preferences - Button Text',
                    'Backend / Dialog Localization / Label',
                    'borlabs-cookie',
                ),
                'entrancePreferencesLink' => _x(
                    'Preferences - Link Text',
                    'Backend / Dialog Localization / Label',
                    'borlabs-cookie',
                ),
                'entranceSaveConsentButton' => _x(
                    'Save Consent - Button Text',
                    'Backend / Dialog Localization / Label',
                    'borlabs-cookie',
                ),
                'iabTcfA11yPurposeListExplained' => _x(
                    'Accessibility - Purpose List - Explanation',
                    'Backend / Dialog Localization / Label',
                    'borlabs-cookie',
                ),
                'iabTcfA11yRegionTcfVendorConsentHistory' => _x(
                    'Accessibility - Region TCF Vendor Consent History - Label',
                    'Backend / Dialog Localization / Label',
                    'borlabs-cookie',
                ),
                'iabTcfA11yRegionTcfVendorConsentHistoryExplained' => _x(
                    'Accessibility - Region TCF Vendor Consent History - Explanation',
                    'Backend / Dialog Localization / Label',
                    'borlabs-cookie',
                ),
                'iabTcfA11yRegionVendors' => _x(
                    'Accessibility - Region Vendors - Label',
                    'Backend / Dialog Localization / Label',
                    'borlabs-cookie',
                ),
                'iabTcfA11yRegionVendorsExplained' => _x(
                    'Accessibility - Region Vendors - Explanation',
                    'Backend / Dialog Localization / Label',
                    'borlabs-cookie',
                ),
                'iabTcfA11yServiceGroupListExplained' => _x(
                    'Accessibility - Service Group List - Explanation',
                    'Backend / Dialog Localization / Label',
                    'borlabs-cookie',
                ),
                'iabTcfDataRetention' => _x(
                    'Data Retention - Text',
                    'Backend / IAB TCF Cookie Box / Text',
                    'borlabs-cookie',
                ),
                'iabTcfDataRetentionInDays' => _x(
                    'Standard Data Retention in Days - Text',
                    'Backend / Dialog Localization / Label',
                    'borlabs-cookie',
                ),
                'iabTcfDescriptionCookieInformation' => _x(
                    'Cookie Information - Description',
                    'Backend / IAB TCF Cookie Box / Text',
                    'borlabs-cookie',
                ),
                'iabTcfDescriptionIndiviualSettings' => _x(
                    'Individual Settings - Description',
                    'Backend / IAB TCF Cookie Box / Text',
                    'borlabs-cookie',
                ),
                'iabTcfDescriptionLegInt' => _x(
                    '<translation-key id="Tcf-Topic-Legitimate-Interest">Legitimate Interest</translation-key> - Description',
                    'Backend / IAB TCF Cookie Box / Text',
                    'borlabs-cookie',
                ),
                'iabTcfDescriptionMoreInformation' => _x(
                    '<translation-key id="Tcf-Topic-More-Information">More Information</translation-key> - Description',
                    'Backend / IAB TCF Cookie Box / Text',
                    'borlabs-cookie',
                ),
                'iabTcfDescriptionNoCommitment' => _x(
                    '<translation-key id="Tcf-Topic-No-Commitment">No Commitment</translation-key> - Description',
                    'Backend / IAB TCF Cookie Box / Text',
                    'borlabs-cookie',
                ),
                'iabTcfDescriptionPersonalData' => _x(
                    '<translation-key id="Tcf-Topic-Personal-Data">Personal Data</translation-key> - Description',
                    'Backend / IAB TCF Cookie Box / Text',
                    'borlabs-cookie',
                ),
                'iabTcfDescriptionRevoke' => _x(
                    '<translation-key id="Tcf-Topic-Revoke">Revoke</translation-key> - Description',
                    'Backend / IAB TCF Cookie Box / Text',
                    'borlabs-cookie',
                ),
                'iabTcfDescriptionTechnology' => _x(
                    '<translation-key id="Tcf-Topic-Technology">Technology</translation-key> - Description',
                    'Backend / IAB TCF Cookie Box / Text',
                    'borlabs-cookie',
                ),
                'iabTcfHeadlineConsentHistory' => _x(
                    'TCF Consent History - Headline',
                    'Backend / Dialog Localization / Label',
                    'borlabs-cookie',
                ),
                'iabTcfHeadlineConsentHistoryNonTcfStandard' => _x(
                    'Non-TCF Standard Consent History - Headline',
                    'Backend / IAB TCF Cookie Box / Headline',
                    'borlabs-cookie',
                ),
                'iabTcfHeadlineDataCategories' => _x(
                    'Data Categories - Headline',
                    'Backend / IAB TCF Cookie Box / Headline',
                    'borlabs-cookie',
                ),
                'iabTcfHeadlineFeatures' => _x(
                    '<translation-key id="IAB-TCF-Features">Features</translation-key> - Headline',
                    'Backend / IAB TCF Cookie Box / Headline',
                    'borlabs-cookie',
                ),
                'iabTcfHeadlineIllustrations' => _x(
                    'Illustrations - Headline',
                    'Backend / IAB TCF Cookie Box / Headline',
                    'borlabs-cookie',
                ),
                'iabTcfHeadlineLegitimateInterests' => _x(
                    'Legitimate Interests - Headline',
                    'Backend / IAB TCF Cookie Box / Headline',
                    'borlabs-cookie',
                ),
                'iabTcfHeadlineNonTcfCategories' => _x(
                    'Non-TCF Standard Categories- Headline',
                    'Backend / IAB TCF Cookie Box / Headline',
                    'borlabs-cookie',
                ),
                'iabTcfHeadlinePurposes' => _x(
                    '<translation-key id="IAB-TCF-Purposes">Purposes</translation-key> - Headline',
                    'Backend / IAB TCF Cookie Box / Headline',
                    'borlabs-cookie',
                ),
                'iabTcfHeadlineSpecialFeatures' => _x(
                    '<translation-key id="IAB-TCF-Special-Features">Special Features</translation-key> - Headline',
                    'Backend / IAB TCF Cookie Box / Headline',
                    'borlabs-cookie',
                ),
                'iabTcfHeadlineSpecialPurposes' => _x(
                    '<translation-key id="IAB-TCF-Special-Purposes">Special Purposes</translation-key> - Headline',
                    'Backend / IAB TCF Cookie Box / Headline',
                    'borlabs-cookie',
                ),
                'iabTcfHeadlineStandardDataRetention' => _x(
                    'Standard Data Retention - Headline',
                    'Backend / IAB TCF Cookie Box / Headline',
                    'borlabs-cookie',
                ),
                'iabTcfHeadlineStorageInformation' => _x(
                    'Storage Information - Headline',
                    'Backend / IAB TCF Cookie Box / Headline',
                    'borlabs-cookie',
                ),
                'iabTcfHeadlineVendorAdditionalInformation' => _x(
                    'Vendor Information - Headline',
                    'Backend / IAB TCF Cookie Box / Headline',
                    'borlabs-cookie',
                ),
                'iabTcfHeadlineVendorConsentHistory' => _x(
                    'Vendor Consent History - Headline',
                    'Backend / IAB TCF Cookie Box / Headline',
                    'borlabs-cookie',
                ),
                'iabTcfNonTcf' => _x(
                    'Non-TCF Standard - Icon Label',
                    'Backend / IAB TCF Cookie Box / Headline',
                    'borlabs-cookie',
                ),
                'iabTcfShowAllVendors' => _x(
                    'Show all Vendors - Button Text',
                    'Backend / IAB TCF Cookie Box / Link Text',
                    'borlabs-cookie',
                ),
                'iabTcfStorageInformationCookieRefresh' => _x(
                    'Cookie Refresh',
                    'Backend / IAB TCF Cookie Box / Table Headline',
                    'borlabs-cookie',
                ),
                'iabTcfStorageInformationDateDay' => _x(
                    'Day',
                    'Backend / IAB TCF Cookie Box / Text',
                    'borlabs-cookie',
                ),
                'iabTcfStorageInformationDateDays' => _x(
                    'Days',
                    'Backend / IAB TCF Cookie Box / Text',
                    'borlabs-cookie',
                ),
                'iabTcfStorageInformationDateLessThanADay' => _x(
                    'Less than a day',
                    'Backend / IAB TCF Cookie Box / Text',
                    'borlabs-cookie',
                ),
                'iabTcfStorageInformationDateMonth' => _x(
                    'Month',
                    'Backend / IAB TCF Cookie Box / Text',
                    'borlabs-cookie',
                ),
                'iabTcfStorageInformationDateMonths' => _x(
                    'Months',
                    'Backend / IAB TCF Cookie Box / Text',
                    'borlabs-cookie',
                ),
                'iabTcfStorageInformationMaxAgeSeconds' => _x(
                    'Lifetime',
                    'Backend / IAB TCF Cookie Box / Table Headline',
                    'borlabs-cookie',
                ),
                'iabTcfStorageInformationStatusNo' => _x(
                    'Status - No',
                    'Backend / IAB TCF Cookie Box / Text',
                    'borlabs-cookie',
                ),
                'iabTcfStorageInformationStatusYes' => _x(
                    'Status - Yes',
                    'Backend / IAB TCF Cookie Box / Text',
                    'borlabs-cookie',
                ),
                'iabTcfStorageInformationUsesCookies' => _x(
                    'Cookie Storage',
                    'Backend / IAB TCF Cookie Box / Table Headline',
                    'borlabs-cookie',
                ),
                'iabTcfStorageInformationUsesNonCookieAccess' => _x(
                    'Non-Cookie Access',
                    'Backend / IAB TCF Cookie Box / Table Headline',
                    'borlabs-cookie',
                ),
                'iabTcfTabCategories' => _x(
                    'Categories - Tab Title',
                    'Backend / IAB TCF Cookie Box / Headline',
                    'borlabs-cookie',
                ),
                'iabTcfTabLegitimateInterest' => _x(
                    'Legitimate Interest - Tab Title',
                    'Backend / IAB TCF Cookie Box / Headline',
                    'borlabs-cookie',
                ),
                'iabTcfTabVendors' => _x(
                    'Vendors - Tab Title',
                    'Backend / IAB TCF Cookie Box / Headline',
                    'borlabs-cookie',
                ),
                'iabTcfVendorLegitimateInterestClaim' => _x(
                    'Vendor Legitimate Interest Claim - Link Text',
                    'Backend / IAB TCF Cookie Box / Link Text',
                    'borlabs-cookie',
                ),
                'iabTcfVendorPlural' => _x(
                    'Vendors - Plural',
                    'Backend / IAB TCF Cookie Box / Text',
                    'borlabs-cookie',
                ),
                'iabTcfVendorPrivacyPolicy' => _x(
                    'Vendor Privacy Policy - Link Text',
                    'Backend / IAB TCF Cookie Box / Link Text',
                    'borlabs-cookie',
                ),
                'iabTcfVendorSearchPlaceholder' => _x(
                    'Vendor Search - Placeholder',
                    'Backend / IAB TCF Cookie Box / Placeholder ',
                    'borlabs-cookie',
                ),
                'iabTcfVendorSingular' => _x(
                    'Vendors - Singular',
                    'Backend / IAB TCF Cookie Box / Text',
                    'borlabs-cookie',
                ),
                'imprintLink' => _x(
                    'Imprint - Link Text',
                    'Backend / Dialog Localization / Headline',
                    'borlabs-cookie',
                ),
                'legalInformationDescriptionConfirmAge' => _x(
                    'Age - Description',
                    'Backend / Dialog Localization / Label',
                    'borlabs-cookie',
                ),
                'legalInformationDescriptionIndividualSettings' => _x(
                    '<translation-key id="Individual-Settings">Individual Settings</translation-key> - Description',
                    'Backend / Dialog Localization / Label',
                    'borlabs-cookie',
                ),
                'legalInformationDescriptionMoreInformation' => _x(
                    'More Information - Description',
                    'Backend / Dialog Localization / Label',
                    'borlabs-cookie',
                ),
                'legalInformationDescriptionNoObligation' => _x(
                    'No Obligation - Description',
                    'Backend / Dialog Localization / Label',
                    'borlabs-cookie',
                ),
                'legalInformationDescriptionNonEuDataTransfer' => _x(
                    '<translation-key id="Non-EU-Data-Transfer">Non-EU Data Transfer</translation-key> - Description',
                    'Backend / Dialog Localization / Label',
                    'borlabs-cookie',
                ),
                'legalInformationDescriptionPersonalData' => _x(
                    '<translation-key id="Personal-Data">Personal Data</translation-key> - Description',
                    'Backend / Dialog Localization / Label',
                    'borlabs-cookie',
                ),
                'legalInformationDescriptionRevoke' => _x(
                    'Revoke - Description',
                    'Backend / Dialog Localization / Label',
                    'borlabs-cookie',
                ),
                'legalInformationDescriptionTechnology' => _x(
                    'Technology - Description',
                    'Backend / Dialog Localization / Label',
                    'borlabs-cookie',
                ),
                'privacyLink' => _x(
                    'Privacy - Link Text',
                    'Backend / Dialog Localization / Headline',
                    'borlabs-cookie',
                ),
                'providerAddress' => _x(
                    'Adress',
                    'Backend / Dialog Localization / Headline',
                    'borlabs-cookie',
                ),
                'providerCloseButton' => _x(
                    'Close - Button Text',
                    'Backend / Dialog Localization / Headline',
                    'borlabs-cookie',
                ),
                'providerCookieUrl' => _x(
                    'Cookie URL',
                    'Backend / Dialog Localization / Headline',
                    'borlabs-cookie',
                ),
                'providerDescription' => _x(
                    'Description',
                    'Backend / Dialog Localization / Headline',
                    'borlabs-cookie',
                ),
                'providerInformationTitle' => _x(
                    'Provider Information - Title',
                    'Backend / Dialog Localization / Headline',
                    'borlabs-cookie',
                ),
                'providerName' => _x(
                    'Name',
                    'Backend / Dialog Localization / Headline',
                    'borlabs-cookie',
                ),
                'providerOptOutUrl' => _x(
                    'Opt-out URL',
                    'Backend / Dialog Localization / Headline',
                    'borlabs-cookie',
                ),
                'providerPartners' => _x(
                    'Partners',
                    'Backend / Dialog Localization / Headline',
                    'borlabs-cookie',
                ),
                'providerPlural' => _x(
                    'Providers - Plural',
                    'Backend / Dialog Localization / Text',
                    'borlabs-cookie',
                ),
                'providerPrivacyUrl' => _x(
                    'Privacy URL',
                    'Backend / Dialog Localization / Headline',
                    'borlabs-cookie',
                ),
                'providerSearchPlaceholder' => _x(
                    'Search - Placeholder',
                    'Backend / Dialog Localization / Headline',
                    'borlabs-cookie',
                ),
                'providerSingular' => _x(
                    'Provider - Singular',
                    'Backend / Dialog Localization / Headline',
                    'borlabs-cookie',
                ),
                'serviceDetailsTableCookieLifetime' => _x(
                    'Lifetime',
                    'Backend / Dialog Localization / Table Headline',
                    'borlabs-cookie',
                ),
                'serviceDetailsTableCookiePurpose' => _x(
                    'Cookie Purpose',
                    'Backend / Dialog Localization / Table Headline',
                    'borlabs-cookie',
                ),
                'serviceDetailsTableCookiePurposeFunctional' => _x(
                    'Cookie Purpose - Functional',
                    'Backend / Dialog Localization / Table Headline',
                    'borlabs-cookie',
                ),
                'serviceDetailsTableCookiePurposeTracking' => _x(
                    'Cookie Purpose - Tracking',
                    'Backend / Dialog Localization / Table Headline',
                    'borlabs-cookie',
                ),
                'serviceDetailsTableCookieType' => _x(
                    'Cookie Type',
                    'Backend / Dialog Localization / Table Headline',
                    'borlabs-cookie',
                ),
                'serviceDetailsTableCookieTypeHttp' => _x(
                    'Cookie Type - HTTP',
                    'Backend / Dialog Localization / Table Headline',
                    'borlabs-cookie',
                ),
                'serviceDetailsTableCookieTypeLocalStorage' => _x(
                    'Cookie Type - <translation-key id="Local-Storage">Local Storage</translation-key>',
                    'Backend / Dialog Localization / Table Headline',
                    'borlabs-cookie',
                ),
                'serviceDetailsTableCookieTypeSessionStorage' => _x(
                    'Cookie Type - <translation-key id="Session-Storage">Session Storage</translation-key>',
                    'Backend / Dialog Localization / Table Headline',
                    'borlabs-cookie',
                ),
                'serviceDetailsTableCookies' => _x(
                    'Cookies',
                    'Backend / Dialog Localization / Table Headline',
                    'borlabs-cookie',
                ),
                'serviceDetailsTableDescription' => _x(
                    'Description',
                    'Backend / Dialog Localization / Table Headline',
                    'borlabs-cookie',
                ),
                'serviceDetailsTableHosts' => _x(
                    'Hosts',
                    'Backend / Dialog Localization / Table Headline',
                    'borlabs-cookie',
                ),
                'serviceDetailsTableName' => _x(
                    'Name',
                    'Backend / Dialog Localization / Table Headline',
                    'borlabs-cookie',
                ),
                'serviceDetailsTableServiceOptionDataCollection' => _x(
                    'Service Option - Data Collection',
                    'Backend / Dialog Localization / Table Headline',
                    'borlabs-cookie',
                ),
                'serviceDetailsTableServiceOptionDataPurpose' => _x(
                    'Service Option - Data Purpose',
                    'Backend / Dialog Localization / Table Headline',
                    'borlabs-cookie',
                ),
                'serviceDetailsTableServiceOptionDistribution' => _x(
                    'Service Option - Distribution',
                    'Backend / Dialog Localization / Table Headline',
                    'borlabs-cookie',
                ),
                'serviceDetailsTableServiceOptionLegalBasis' => _x(
                    'Service Option - Legal Basis',
                    'Backend / Dialog Localization / Table Headline',
                    'borlabs-cookie',
                ),
                'serviceDetailsTableServiceOptionProcessingLocation' => _x(
                    'Service Option - Processing Location',
                    'Backend / Dialog Localization / Table Headline',
                    'borlabs-cookie',
                ),
                'serviceDetailsTableServiceOptionTechnology' => _x(
                    'Service Option - Technology',
                    'Backend / Dialog Localization / Table Headline',
                    'borlabs-cookie',
                ),
                'serviceDetailsTableServiceOptions' => _x(
                    'Service Options',
                    'Backend / Dialog Localization / Table Headline',
                    'borlabs-cookie',
                ),
                'servicePlural' => _x(
                    'Services - Plural',
                    'Backend / Dialog Localization / Text',
                    'borlabs-cookie',
                ),
                'serviceSearchPlaceholder' => _x(
                    'Search Service - Placeholder',
                    'Backend / Dialog Localization / Placeholder',
                    'borlabs-cookie',
                ),
                'serviceSingular' => _x(
                    'Services - Singular',
                    'Backend / Dialog Localization / Text',
                    'borlabs-cookie',
                ),
            ],

            // Headlines
            'headline' => [
                'a11y' => _x(
                    'Accessibility',
                    'Backend / Dialog Localization / Headline',
                    'borlabs-cookie',
                ),
                'consentHistoryTable' => _x(
                    'Consent History Table',
                    'Backend / Dialog Localization / Headline',
                    'borlabs-cookie',
                ),
                'dialogDetails' => _x(
                    'Dialog - Details',
                    'Backend / Dialog Localization / Headline',
                    'borlabs-cookie',
                ),
                'dialogEntrance' => _x(
                    'Dialog - Entrance',
                    'Backend / Dialog Localization / Headline',
                    'borlabs-cookie',
                ),
                'dialogIabTcf' => _x(
                    'IAB TCF',
                    'Backend / Dialog Localization / Headline',
                    'borlabs-cookie',
                ),
                'dialogIabTcfA11y' => _x(
                    'IAB TCF - Accessibility',
                    'Backend / Dialog Localization / Headline',
                    'borlabs-cookie',
                ),
                'dialogIabTcfVendorDetailsTable' => _x(
                    'IAB TCF - Vendor Details Table',
                    'Backend / Dialog Localization / Headline',
                    'borlabs-cookie',
                ),
                'dialogLegalInformation' => _x(
                    'Dialog - <translation-key id="Legal-Information">Legal Information</translation-key>',
                    'Backend / Dialog Localization / Headline',
                    'borlabs-cookie',
                ),
                'providerDetailsTable' => _x(
                    'Provider Details Table',
                    'Backend / Dialog Localization / Headline',
                    'borlabs-cookie',
                ),
                'resetDialogLocalization' => _x(
                    'Reset Dialog Localization Texts',
                    'Backend / Dialog Localization / Headline',
                    'borlabs-cookie',
                ),
                'serviceDetailsTable' => _x(
                    'Service Details Table',
                    'Backend / Dialog Localization / Headline',
                    'borlabs-cookie',
                ),
            ],

            // Hint
            'hint' => [
                'providerCloseButton' => _x(
                    'For content blocked by a <translation-key id="Content-Blocker">Content Blocker</translation-key>, there is always a button available to open the dialog with provider information. This is the text of the button used to close this dialog.',
                    'Backend / Dialog Appearance / Hint',
                    'borlabs-cookie',
                ),
                'reset' => _x(
                    'Please confirm that you want to reset all <translation-key id="Dialog-Localization">Dialog Localization</translation-key> texts. They will be reset to their default texts.',
                    'Backend / Dialog Appearance / Hint',
                    'borlabs-cookie',
                ),
            ],

            // Options (select | checkbox | radio)
            'option' => [
            ],

            // Placeholder
            'placeholder' => [
            ],

            // Text
            'text' => [
            ],

            // Things to know
            'thingsToKnow' => [
                'headlineIabTcf' => _x(
                    'What is the <translation-key id="IAB-TCF">IAB TCF</translation-key>?',
                    'Backend / Dialog Localization / Headline',
                    'borlabs-cookie',
                ),
                'headlineLegalInformation' => _x(
                    'Legal Information',
                    'Backend / Dialog Localization / Headline',
                    'borlabs-cookie',
                ),
                'headlineWhatIsTheDialogDetails' => _x(
                    'What is the <translation-key id="Dialog-Details">Dialog Details</translation-key>?',
                    'Backend / Dialog Localization / Headline',
                    'borlabs-cookie',
                ),
                'headlineWhatIsTheDialogEntrance' => _x(
                    'What is the <translation-key id="Dialog-Entrance">Dialog Entrance</translation-key>?',
                    'Backend / Dialog Localization / Headline',
                    'borlabs-cookie',
                ),
                'iabTcfA' => _x(
                    'When the <translation-key id="Iab-Tcf-Status">IAB TCF Status</translation-key> setting is enabled (found under <em><translation-key id="Navigation-Consent-Management">Consent Management</translation-key> &raquo; <translation-key id="Navigation-Iab-Tcf">IAB TCF</translation-key> &raquo; <translation-key id="Navigation-Iab-Tcf-Settings">Settings</translation-key></em>) the dialog entrance will display these texts instead of the <translation-key id="Legal-Information">Legal Information</translation-key> texts.',
                    'Backend / Dialog Localization / Text',
                    'borlabs-cookie',
                ),
                'iabTcfB' => _x(
                    'The dialog <span class="brlbs-cmpnt-important-text">must</span> inform about the topics: ',
                    'Backend / Dialog Localization / Text',
                    'borlabs-cookie',
                ),
                'iabTcfC' => _x(
                    'Texts <span class="brlbs-cmpnt-important-text">must</span> also distinguish between <translation-key id="Vendors">Vendors</translation-key> (TCF Standard) and <translation-key id="Providers">Providers</translation-key> (Non-TCF Standard).',
                    'Backend / Dialog Localization / Text',
                    'borlabs-cookie',
                ),
                'iabTcfD' => _x(
                    '<span class="brlbs-cmpnt-important-text">Warning</span>: Should your modifications infringe upon the IAB\'s TCF standards and we are alerted to a complaint from the IAB, your license will be irrevocably terminated without compensation.',
                    'Backend / Dialog Localization / Text',
                    'borlabs-cookie',
                ),
                'iabTcfF' => _x(
                    'Should you harbor any uncertainties regarding the compliance of your proposed alterations with the TCF standard, we strongly encourage you to reach out to us prior to implementing any changes.',
                    'Backend / Dialog Localization / Text',
                    'borlabs-cookie',
                ),
                'iabTcfTopicA' => _x(
                    '<translation-key id="Tcf-Topic-Technology">Technology</translation-key>: The technologies used, for example: cookies and their purpose. The number of <translation-key id="Vendors">Vendors</translation-key> <span class="brlbs-cmpnt-important-text">must</span> also be mentioned with the help of <strong><em>{{ totalVendors }}</em></strong>.',
                    'Backend / Dialog Localization / Text',
                    'borlabs-cookie',
                ),
                'iabTcfTopicB' => _x(
                    '<translation-key id="Tcf-Topic-Personal-Data">Personal Data</translation-key>: What types of personal data are processed?',
                    'Backend / Dialog Localization / Text',
                    'borlabs-cookie',
                ),
                'iabTcfTopicC' => _x(
                    '<translation-key id="Tcf-Topic-Legitimate-Interest">Legitimate Interest</translation-key>: You <span class="brlbs-cmpnt-important-text">must</span> inform about the legitimate interest that some <translation-key id="Vendors">Vendors</translation-key> claim for data processing.',
                    'Backend / Dialog Localization / Text',
                    'borlabs-cookie',
                ),
                'iabTcfTopicD' => _x(
                    '<translation-key id="Tcf-Topic-More-Information">More Information</translation-key>: Where the visitor can find more information about the <translation-key id="Vendors">Vendors</translation-key> and the data processed.',
                    'Backend / Dialog Localization / Text',
                    'borlabs-cookie',
                ),
                'iabTcfTopicE' => _x(
                    '<translation-key id="Tcf-Topic-No-Commitment">No Commitment</translation-key>: That the visitor can visit the website without giving consent.',
                    'Backend / Dialog Localization / Text',
                    'borlabs-cookie',
                ),
                'iabTcfTopicF' => _x(
                    '<translation-key id="Tcf-Topic-Revoke">Revoke</translation-key>: That the visitor can revoke the consent any time.',
                    'Backend / Dialog Localization / Text',
                    'borlabs-cookie',
                ),
                'iabTcfTopicG' => _x(
                    '<translation-key id="Tcf-Topic-Individual-Settings">Individual Settings</translation-key>: That due to individual settings certain functionalities of the website may be unavailable or operate suboptimally.',
                    'Backend / Dialog Localization / Text',
                    'borlabs-cookie',
                ),
                'legalInformationA' => _x(
                    'You can select which legal information topics you want to show to a visitor.',
                    'Backend / Dialog Localization / Text',
                    'borlabs-cookie',
                ),
                'legalInformationB' => _x(
                    'If you disable a text, make sure that its message appears or is part of another text displayed to a visitor.',
                    'Backend / Dialog Localization / Text',
                    'borlabs-cookie',
                ),
                'legalInformationC' => _x(
                    'You <span class="brlbs-cmpnt-important-text">must</span> inform a visitor about the following topics:',
                    'Backend / Dialog Localization / Text',
                    'borlabs-cookie',
                ),
                'legalInformationTopicA' => _x(
                    'A minimum age of 16 is required to consent to non-optional services.',
                    'Backend / Dialog Localization / Text',
                    'borlabs-cookie',
                ),
                'legalInformationTopicB' => _x(
                    'The used technologies, for example: Cookies.',
                    'Backend / Dialog Localization / Text',
                    'borlabs-cookie',
                ),
                'legalInformationTopicC' => _x(
                    'What type of personal data is used and for what purpose.',
                    'Backend / Dialog Localization / Text',
                    'borlabs-cookie',
                ),
                'legalInformationTopicD' => _x(
                    'Where more information about each service can be found.',
                    'Backend / Dialog Localization / Text',
                    'borlabs-cookie',
                ),
                'legalInformationTopicE' => _x(
                    '<sup>1</sup> That no consent is required to visit your website.',
                    'Backend / Dialog Localization / Text',
                    'borlabs-cookie',
                ),
                'legalInformationTopicF' => _x(
                    'The visitor can revoke the consent any time.',
                    'Backend / Dialog Localization / Text',
                    'borlabs-cookie',
                ),
                'legalInformationTopicG' => _x(
                    '<sup>1</sup> What happens when individual settings are applied.',
                    'Backend / Dialog Localization / Text',
                    'borlabs-cookie',
                ),
                'legalInformationTopicH' => _x(
                    '<sup>2</sup> That personal data can be transferred to non-eu countries with insufficient data protection.',
                    'Backend / Dialog Localization / Text',
                    'borlabs-cookie',
                ),
                'legalInformationTopicSmallPrintA' => _x(
                    '<sup>1</sup> It is recommended to display this information.',
                    'Backend / Dialog Localization / Text',
                    'borlabs-cookie',
                ),
                'legalInformationTopicSmallPrintB' => _x(
                    '<sup>2</sup> You <span class="brlbs-cmpnt-important-text">must</span> display the <translation-key id="Non-EU-Data-Transfer">Non-EU Data Transfer</translation-key> information if you use a service whose company is based in the USA.',
                    'Backend / Dialog Localization / Text',
                    'borlabs-cookie',
                ),
                'whatIsTheDialogEntrance' => _x(
                    'The <translation-key id="Dialog-Entrance">Dialog Entrance</translation-key> is the first thing your visitors see when they visit your website. It is a small dialog that informs your visitors what cookies and services your website uses and that they can change their consent settings in this dialog.',
                    'Backend / Dialog Localization / Text',
                    'borlabs-cookie',
                ),
                'whatIsTheDialogDetails' => _x(
                    'The <translation-key id="Dialog-Details">Dialog Details</translation-key> is the dialog that opens when your visitors click on the <translation-key id="Individual-Privacy-Preferences">Individual Privacy Preferences</translation-key> button in the <translation-key id="Dialog-Entrance">Dialog Entrance</translation-key>. It contains detailed information about the cookies and services your website uses and allows for more granular consent.',
                    'Backend / Dialog Localization / Text',
                    'borlabs-cookie',
                ),
            ],
        ];
    }
}
