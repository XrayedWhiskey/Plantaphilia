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

namespace Borlabs\Cookie\Localization;

use function Borlabs\Cookie\System\WordPressGlobalFunctions\_x;

/**
 * The **DefaultLocalizationStrings** class contains various localized strings.
 *
 * @see \Borlabs\Cookie\Localization\DefaultLocalizationStrings::get()
 */
final class DefaultLocalizationStrings implements LocalizationInterface
{
    /**
     * @return array<array<string>>
     */
    public static function get(): array
    {
        return [
            'contentBlocker' => [
                'a11yShowContentBlockerModalIconExplained' => _x(
                    'More information about the blocked content.',
                    'Seeder / Default Content Blocker / A11Y',
                    'borlabs-cookie',
                ),
                'defaultDescription' => _x(
                    'The <strong><em>Default</em> Content Blocker</strong> is a special type that is always used when no specific <strong>Content Blocker</strong> was found.<br>Therefore it is not possible to use the <strong>Unblock all</strong> feature.',
                    'Seeder / Default Content Blocker / Description',
                    'borlabs-cookie',
                ),
                'defaultName' => _x(
                    'Default',
                    'Seeder / Default Content Blocker / Name',
                    'borlabs-cookie',
                ),
                'acceptServiceUnblockContent' => _x(
                    'Accept required service and unblock content',
                    'Seeder / Default Content Blocker / Table Headline',
                    'borlabs-cookie',
                ),
                'description' => _x(
                    'You are currently viewing a placeholder content from <strong>{{ name }}</strong>. To access the actual content, click the button below. Please note that doing so will share data with third-party providers.',
                    'Seeder / Default Content Blocker / Button Text',
                    'borlabs-cookie',
                ),
                'moreInformation' => _x(
                    'More Information',
                    'Seeder / Default Content Blocker / Button Text',
                    'borlabs-cookie',
                ),
                'unblockButton' => _x(
                    'Unblock content',
                    'Seeder / Default Content Blocker / Button Text',
                    'borlabs-cookie',
                ),
            ],

            'dialog' => [
                'a11yDialogCloseButton' => _x(
                    'Close dialog',
                    'Frontend / Dialog / A11Y',
                    'borlabs-cookie',
                ),
                'a11yDialogCloseButtonExplained' => _x(
                    'This button closes the dialog. Its functionality is identical to the {{ entranceAcceptOnlyEssential }} button.',
                    'Frontend / Dialog / A11Y',
                    'borlabs-cookie',
                ),
                'a11yProviderDialogExplained' => _x(
                    'Below is a list detailing the provider whose service or content is presently blocked.',
                    'Frontend / Dialog / A11Y',
                    'borlabs-cookie',
                ),
                'a11yProviderListExplained' => _x(
                    'The following is a list of providers for whose services consent can be given.',
                    'Frontend / Dialog / A11Y',
                    'borlabs-cookie',
                ),
                'a11yRegionServices' => _x(
                    'Service list and Filter Option',
                    'Frontend / Dialog / A11Y',
                    'borlabs-cookie',
                ),
                'a11yRegionServicesExplained' => _x(
                    'This section contains a list of services for which you can provide consent. You can use the filter to narrow down the list by entering a search term. The list updates automatically as you type.',
                    'Frontend / Dialog / A11Y',
                    'borlabs-cookie',
                ),
                'a11yServiceGroupListExplained' => _x(
                    'The following is a list of service groups for which consent can be given. The first service group is essential and cannot be unchecked.',
                    'Frontend / Dialog / A11Y',
                    'borlabs-cookie',
                ),
                'a11yWidgetButton' => _x(
                    'Open Privacy Preferences Dialog',
                    'Frontend / Dialog / A11Y',
                    'borlabs-cookie',
                ),
                'consentHistoryLoading' => _x(
                    'Consent History Loading...',
                    'Frontend / Consent History / Text',
                    'borlabs-cookie',
                ),
                'consentHistoryNoData' => _x(
                    'No Consent Data',
                    'Frontend / Consent History / Text',
                    'borlabs-cookie',
                ),
                'consentHistoryTableChoice' => _x(
                    'Choice',
                    'Frontend / Consent History / Text',
                    'borlabs-cookie',
                ),
                'consentHistoryTableConsentGiven' => _x(
                    'Yes',
                    'Frontend / Consent History / Text',
                    'borlabs-cookie',
                ),
                'consentHistoryTableConsentWithdrawn' => _x(
                    'No',
                    'Frontend / Consent History / Text',
                    'borlabs-cookie',
                ),
                'consentHistoryTableConsents' => _x(
                    'Consents',
                    'Frontend / Consent History / Table Headline',
                    'borlabs-cookie',
                ),
                'consentHistoryTableDate' => _x(
                    'Date',
                    'Frontend / Consent History / Table Headline',
                    'borlabs-cookie',
                ),
                'consentHistoryTableVersion' => _x(
                    'Version',
                    'Frontend / Consent History / Table Headline',
                    'borlabs-cookie',
                ),
                'consentHistoryTableVersionChanges' => _x(
                    'Changes',
                    'Frontend / Consent History / Table Headline',
                    'borlabs-cookie',
                ),
                'consentHistoryTableVersionChangesAdded' => _x(
                    'Added',
                    'Frontend / Consent History / Text',
                    'borlabs-cookie',
                ),
                'consentHistoryTableVersionChangesRemoved' => _x(
                    'Removed',
                    'Frontend / Consent History / Text',
                    'borlabs-cookie',
                ),
                'detailsAcceptAllButton' => _x(
                    'Accept all',
                    'Frontend / Dialog / Button Title',
                    'borlabs-cookie',
                ),
                'detailsAcceptOnlyEssential' => _x(
                    'Accept only essential cookies',
                    'Frontend / Dialog / Link Text',
                    'borlabs-cookie',
                ),
                'detailsBackLink' => _x(
                    'Back',
                    'Frontend / Dialog / Link Text',
                    'borlabs-cookie',
                ),
                'detailsDescription' => _x(
                    'Here you will find an overview of all cookies used. You can give your consent to whole categories or display further information and select certain cookies.',
                    'Frontend / Dialog / Text',
                    'borlabs-cookie',
                ),
                'detailsDeselectAll' => _x(
                    'Deselect all',
                    'Frontend / Dialog / Link Text',
                    'borlabs-cookie',
                ),
                'detailsHeadline' => _x(
                    'Privacy Preference',
                    'Frontend / Dialog / Headline',
                    'borlabs-cookie',
                ),
                'detailsHideMoreInformationLink' => _x(
                    'Hide Information',
                    'Frontend / Dialog / Link Text',
                    'borlabs-cookie',
                ),
                'detailsSaveConsentButton' => _x(
                    'Save',
                    'Frontend / Dialog / Button Title',
                    'borlabs-cookie',
                ),
                'detailsSelectAll' => _x(
                    'Select all',
                    'Frontend / Dialog / Link Text',
                    'borlabs-cookie',
                ),
                'detailsShowMoreInformationLink' => _x(
                    'Show Information',
                    'Frontend / Dialog / Link Text',
                    'borlabs-cookie',
                ),
                'detailsSwitchStatusActive' => _x(
                    'On',
                    'Frontend / Dialog / Switch Button Status',
                    'borlabs-cookie',
                ),
                'detailsSwitchStatusInactive' => _x(
                    'Off',
                    'Frontend / Dialog / Switch Button Status',
                    'borlabs-cookie',
                ),
                'detailsTabConsentHistory' => _x(
                    'Consent History',
                    'Frontend / Dialog / Tab',
                    'borlabs-cookie',
                ),
                'detailsTabProvider' => _x(
                    'Provider',
                    'Frontend / Dialog / Tab',
                    'borlabs-cookie',
                ),
                'detailsTabServiceGroups' => _x(
                    'Service Groups',
                    'Frontend / Dialog / Tab',
                    'borlabs-cookie',
                ),
                'detailsTabServices' => _x(
                    'Services',
                    'Frontend / Dialog / Tab',
                    'borlabs-cookie',
                ),
                'entranceAcceptAllButton' => _x(
                    'I accept all',
                    'Frontend / Dialog / Button Title',
                    'borlabs-cookie',
                ),
                'entranceAcceptOnlyEssential' => _x(
                    'Accept only essential cookies',
                    'Frontend / Dialog / Link Text',
                    'borlabs-cookie',
                ),
                'entranceDescription' => _x(
                    'We need your consent before you can continue on our website.',
                    'Frontend / Dialog / Text',
                    'borlabs-cookie',
                ),
                'entranceHeadline' => _x(
                    'Privacy Preference',
                    'Frontend / Dialog / Headline',
                    'borlabs-cookie',
                ),
                'entranceLanguageSwitcherLink' => _x(
                    'Language',
                    'Frontend / Dialog / Link Text',
                    'borlabs-cookie',
                ),
                'entrancePreferencesButton' => _x(
                    'Individual Privacy Preferences',
                    'Frontend / Dialog / Link Text',
                    'borlabs-cookie',
                ),
                'entrancePreferencesLink' => _x(
                    'Preferences',
                    'Frontend / Dialog / Link Text',
                    'borlabs-cookie',
                ),
                'entranceSaveConsentButton' => _x(
                    'Save Consent',
                    'Frontend / Dialog / Link Text',
                    'borlabs-cookie',
                ),
                'iabTcfA11yPurposeListExplained' => _x(
                    'The following is a list of purposes of the IAB Transparency and Consent Framework (TCF) for which consent can be given. The TCF was created to help publishers, technology providers and advertisers comply with the EU GDPR and ePrivacy Directive.',
                    'Frontend / IAB TCF Dialog / A11Y',
                    'borlabs-cookie',
                ),
                'iabTcfA11yRegionTcfVendorConsentHistory' => _x(
                    'TCF Vendors Consents and Filter Option',
                    'Frontend / Dialog / A11Y',
                    'borlabs-cookie',
                ),
                'iabTcfA11yRegionTcfVendorConsentHistoryExplained' => _x(
                    'This section contains a list of vendors along with information about your consent choices. You can use the filter to narrow down the list by entering a search term. The list updates automatically as you type.',
                    'Frontend / Dialog / A11Y',
                    'borlabs-cookie',
                ),
                'iabTcfA11yRegionVendors' => _x(
                    'Vendor List and Filter Option',
                    'Frontend / Dialog / A11Y',
                    'borlabs-cookie',
                ),
                'iabTcfA11yRegionVendorsExplained' => _x(
                    'This section contains a list of vendors for which you can provide consent. You can use the filter to narrow down the list by entering a search term. The list updates automatically as you type.',
                    'Frontend / Dialog / A11Y',
                    'borlabs-cookie',
                ),
                'iabTcfA11yServiceGroupListExplained' => _x(
                    'The following is a list of service groups for which consent can be given. The first service group is essential and cannot be unchecked. These service groups are not part of the TCF standard.',
                    'Frontend / IAB TCF Dialog / A11Y',
                    'borlabs-cookie',
                ),
                'iabTcfDataRetention' => _x(
                    'Data Retention:',
                    'Frontend / IAB TCF Dialog / Text',
                    'borlabs-cookie',
                ),
                'iabTcfDataRetentionInDays' => _x(
                    'Days',
                    'Frontend / IAB TCF Dialog / Text',
                    'borlabs-cookie',
                ),
                'iabTcfDescriptionCookieInformation' => _x(
                    'The choices you make regarding the purposes and vendors listed in this notice are stored in a cookie named {{ iabTcfCookieName }} for up to {{ cookieLifetime }} days.',
                    'Frontend / IAB TCF Dialog / Text',
                    'borlabs-cookie',
                ),
                'iabTcfDescriptionIndiviualSettings' => _x(
                    'Please note that based on individual settings not all functions of the site may be available.',
                    'Frontend / IAB TCF Dialog / Text',
                    'borlabs-cookie',
                ),
                'iabTcfDescriptionLegInt' => _x(
                    'Some of our <a href="#" role="button" data-borlabs-cookie-actions="vendors">{{ totalVendors }} partners</a> process your data (revocable at any time) based on <a href="#" role="button" data-borlabs-cookie-actions="leg-int">legitimate interest</a>.',
                    'Frontend / IAB TCF Dialog / Text',
                    'borlabs-cookie',
                ),
                'iabTcfDescriptionMoreInformation' => _x(
                    'You can find more information about the use of your data and about our partners under <a href="#" role="button" data-borlabs-cookie-actions="preferences">Settings</a> or in our privacy policy.',
                    'Frontend / IAB TCF Dialog / Text',
                    'borlabs-cookie',
                ),
                'iabTcfDescriptionNoCommitment' => _x(
                    'There is no obligation to agree to the processing of your data in order to use this offer.',
                    'Frontend / IAB TCF Dialog / Text',
                    'borlabs-cookie',
                ),
                'iabTcfDescriptionPersonalData' => _x(
                    'Personal data can be processed (e.g. recognition features, IP addresses), for example for personalized ads and content or ad and content measurement.',
                    'Frontend / IAB TCF Dialog / Text',
                    'borlabs-cookie',
                ),
                'iabTcfDescriptionRevoke' => _x(
                    'We cannot display certain contents without your consent. You can revoke or adjust your selection at any time under <a href="#" role="button" data-borlabs-cookie-actions="preferences">Settings</a>. Your selection will only be applied to this offer.',
                    'Frontend / IAB TCF Dialog / Text',
                    'borlabs-cookie',
                ),
                'iabTcfDescriptionTechnology' => _x(
                    'We need your consent for us and our {{ totalVendors }} partners to use cookies and other technologies to deliver relevant content and advertising to you. This is how we finance and optimize our website.',
                    'Frontend / IAB TCF Dialog / Text',
                    'borlabs-cookie',
                ),
                'iabTcfHeadlineConsentHistory' => _x(
                    'TCF Vendors Consents',
                    'Frontend / IAB TCF Dialog / Consent History',
                    'borlabs-cookie',
                ),
                'iabTcfHeadlineConsentHistoryNonTcfStandard' => _x(
                    'Non-TCF Standard Consents',
                    'Frontend / IAB TCF Dialog / Consent History',
                    'borlabs-cookie',
                ),
                'iabTcfHeadlineDataCategories' => _x(
                    'Data Categories',
                    'Frontend / IAB TCF Dialog / Consent History',
                    'borlabs-cookie',
                ),
                'iabTcfHeadlineFeatures' => _x(
                    'Features',
                    'Frontend / IAB TCF Dialog / Headline',
                    'borlabs-cookie',
                ),
                'iabTcfHeadlineIllustrations' => _x(
                    'Illustrations',
                    'Frontend / IAB TCF Dialog / Headline',
                    'borlabs-cookie',
                ),
                'iabTcfHeadlineLegitimateInterests' => _x(
                    'Legitimate Interests',
                    'Frontend / IAB TCF Dialog / Headline',
                    'borlabs-cookie',
                ),
                'iabTcfHeadlineNonTcfCategories' => _x(
                    'Non-TCF Standard Categories',
                    'Frontend / IAB TCF Dialog / Headline',
                    'borlabs-cookie',
                ),
                'iabTcfHeadlinePurposes' => _x(
                    'Purposes',
                    'Frontend / IAB TCF Dialog / Headline',
                    'borlabs-cookie',
                ),
                'iabTcfHeadlineSpecialFeatures' => _x(
                    'Special Features',
                    'Frontend / IAB TCF Dialog / Headline',
                    'borlabs-cookie',
                ),
                'iabTcfHeadlineSpecialPurposes' => _x(
                    'Special Purposes',
                    'Frontend / IAB TCF Dialog / Headline',
                    'borlabs-cookie',
                ),
                'iabTcfHeadlineStandardDataRetention' => _x(
                    'Standard Data Retention',
                    'Frontend / IAB TCF Dialog / Headline',
                    'borlabs-cookie',
                ),
                'iabTcfHeadlineStorageInformation' => _x(
                    'Storage Information',
                    'Frontend / IAB TCF Dialog / Headline',
                    'borlabs-cookie',
                ),
                'iabTcfHeadlineVendorAdditionalInformation' => _x(
                    'Vendor Information',
                    'Frontend / IAB TCF Dialog / Vendors',
                    'borlabs-cookie',
                ),
                'iabTcfHeadlineVendorConsentHistory' => _x(
                    'History',
                    'Frontend / IAB TCF Dialog / Vendors',
                    'borlabs-cookie',
                ),
                'iabTcfNonTcf' => _x(
                    'Non-TCF Standard',
                    'Frontend / IAB TCF Dialog / Headline',
                    'borlabs-cookie',
                ),
                'iabTcfShowAllProviders' => _x(
                    'Show all providers',
                    'Frontend / IAB TCF Dialog / Provider',
                    'borlabs-cookie',
                ),
                'iabTcfShowAllVendors' => _x(
                    'Show all vendors',
                    'Frontend / IAB TCF Dialog / Provider',
                    'borlabs-cookie',
                ),
                'iabTcfStorageInformationCookieRefresh' => _x(
                    'Cookie Refresh',
                    'Frontend / IAB TCF Dialog / Text',
                    'borlabs-cookie',
                ),
                'iabTcfStorageInformationDateDay' => _x(
                    'Day',
                    'Frontend / IAB TCF Dialog / Date',
                    'borlabs-cookie',
                ),
                'iabTcfStorageInformationDateDays' => _x(
                    'Days',
                    'Frontend / IAB TCF Dialog / Date',
                    'borlabs-cookie',
                ),
                'iabTcfStorageInformationDateLessThanADay' => _x(
                    'Less than a day',
                    'Frontend / IAB TCF Dialog / Date',
                    'borlabs-cookie',
                ),
                'iabTcfStorageInformationDateMonth' => _x(
                    'Month',
                    'Frontend / IAB TCF Dialog / Date',
                    'borlabs-cookie',
                ),
                'iabTcfStorageInformationDateMonths' => _x(
                    'Months',
                    'Frontend / IAB TCF Dialog / Date',
                    'borlabs-cookie',
                ),
                'iabTcfStorageInformationDomain' => _x(
                    'Domain',
                    'Frontend / IAB TCF Dialog / Text',
                    'borlabs-cookie',
                ),
                'iabTcfStorageInformationIdentifier' => _x(
                    'Identifier',
                    'Frontend / IAB TCF Dialog / Text',
                    'borlabs-cookie',
                ),
                'iabTcfStorageInformationMaxAgeSeconds' => _x(
                    'Lifetime',
                    'Frontend / IAB TCF Dialog / Text',
                    'borlabs-cookie',
                ),
                'iabTcfStorageInformationPurposes' => _x(
                    'Purpose(s)',
                    'Frontend / IAB TCF Dialog / Text',
                    'borlabs-cookie',
                ),
                'iabTcfStorageInformationStatusNo' => _x(
                    'No',
                    'Frontend / IAB TCF Dialog / Text',
                    'borlabs-cookie',
                ),
                'iabTcfStorageInformationStatusYes' => _x(
                    'Yes',
                    'Frontend / IAB TCF Dialog / Text',
                    'borlabs-cookie',
                ),
                'iabTcfStorageInformationType' => _x(
                    'Type',
                    'Frontend / IAB TCF Dialog / Text',
                    'borlabs-cookie',
                ),
                'iabTcfStorageInformationUsesCookies' => _x(
                    'Cookie Storage',
                    'Frontend / IAB TCF Dialog / Text',
                    'borlabs-cookie',
                ),
                'iabTcfStorageInformationUsesNonCookieAccess' => _x(
                    'Non-Cookie Access',
                    'Frontend / IAB TCF Dialog / Text',
                    'borlabs-cookie',
                ),
                'iabTcfTabCategories' => _x(
                    'Categories',
                    'Frontend / IAB TCF Dialog / Headline',
                    'borlabs-cookie',
                ),
                'iabTcfTabLegitimateInterest' => _x(
                    'Legitimate Interest',
                    'Frontend / IAB TCF Dialog / Headline',
                    'borlabs-cookie',
                ),
                'iabTcfTabVendors' => _x(
                    'Vendors',
                    'Frontend / IAB TCF Dialog / Headline',
                    'borlabs-cookie',
                ),
                'iabTcfVendorLegitimateInterestClaim' => _x(
                    'Legitimate Interest Claim',
                    'Frontend / IAB TCF Dialog / Headline',
                    'borlabs-cookie',
                ),
                'iabTcfVendorPlural' => _x(
                    'Vendors',
                    'Frontend / IAB TCF Dialog / Provider',
                    'borlabs-cookie',
                ),
                'iabTcfVendorPrivacyPolicy' => _x(
                    'Privacy Policy',
                    'Frontend / IAB TCF Dialog / Headline',
                    'borlabs-cookie',
                ),
                'iabTcfVendorSearchPlaceholder' => _x(
                    'Search vendors...',
                    'Frontend / IAB TCF Dialog / Provider',
                    'borlabs-cookie',
                ),
                'iabTcfVendorSingular' => _x(
                    'Vendor',
                    'Frontend / IAB TCF Dialog / Provider',
                    'borlabs-cookie',
                ),
                'imprintLink' => _x(
                    'Imprint',
                    'Frontend / Dialog / Link Text',
                    'borlabs-cookie',
                ),
                'legalInformationDescriptionConfirmAge' => _x(
                    'If you are under 16 and wish to give consent to optional services, you must ask your legal guardians for permission.',
                    'Frontend / Dialog / Text',
                    'borlabs-cookie',
                ),
                'legalInformationDescriptionIndividualSettings' => _x(
                    'Please note that based on individual settings not all functions of the site may be available.',
                    'Frontend / Dialog / Text',
                    'borlabs-cookie',
                ),
                'legalInformationDescriptionMoreInformation' => _x(
                    'You can find more information about the use of your data in our <a href="{{ privacyPageUrl }}">privacy policy</a>.',
                    'Frontend / Dialog / Text',
                    'borlabs-cookie',
                ),
                'legalInformationDescriptionNoObligation' => _x(
                    'There is no obligation to consent to the processing of your data in order to use this offer.',
                    'Frontend / Dialog / Text',
                    'borlabs-cookie',
                ),
                'legalInformationDescriptionNonEuDataTransfer' => _x(
                    'Some services process personal data in the USA. With your consent to use these services, you also consent to the processing of your data in the USA pursuant to Art. 49 (1) lit. a GDPR. The ECJ classifies the USA as a country with insufficient data protection according to EU standards. For example, there is a risk that U.S. authorities will process personal data in surveillance programs without any existing possibility of legal action for Europeans.',
                    'Frontend / Dialog / Text',
                    'borlabs-cookie',
                ),
                'legalInformationDescriptionPersonalData' => _x(
                    'Personal data may be processed (e.g. IP addresses), for example for personalized ads and content or ad and content measurement.',
                    'Frontend / Dialog / Text',
                    'borlabs-cookie',
                ),
                'legalInformationDescriptionRevoke' => _x(
                    'You can revoke or adjust your selection at any time under <a href="#" role="button" data-borlabs-cookie-actions="preferences">Settings</a>.',
                    'Frontend / Dialog / Text',
                    'borlabs-cookie',
                ),
                'legalInformationDescriptionTechnology' => _x(
                    'We use cookies and other technologies on our website. Some of them are essential, while others help us to improve this website and your experience.',
                    'Frontend / Dialog / Text',
                    'borlabs-cookie',
                ),
                'privacyLink' => _x(
                    'Privacy Policy',
                    'Frontend / Dialog / Link Text',
                    'borlabs-cookie',
                ),
                'providerAddress' => _x(
                    'Address',
                    'Frontend / Dialog / Provider',
                    'borlabs-cookie',
                ),
                'providerCloseButton' => _x(
                    'Close',
                    'Frontend / Dialog / Provider',
                    'borlabs-cookie',
                ),
                'providerCookieUrl' => _x(
                    'Cookie URL',
                    'Frontend / Dialog / Provider',
                    'borlabs-cookie',
                ),
                'providerDescription' => _x(
                    'Description',
                    'Frontend / Dialog / Provider',
                    'borlabs-cookie',
                ),
                'providerInformationTitle' => _x(
                    'Provider Information',
                    'Frontend / Dialog / Provider',
                    'borlabs-cookie',
                ),
                'providerName' => _x(
                    'Provider Name',
                    'Frontend / Dialog / Provider',
                    'borlabs-cookie',
                ),
                'providerOptOutUrl' => _x(
                    'Opt-Out URL',
                    'Frontend / Dialog / Provider',
                    'borlabs-cookie',
                ),
                'providerPartners' => _x(
                    'Partners',
                    'Frontend / Dialog / Provider',
                    'borlabs-cookie',
                ),
                'providerPlural' => _x(
                    'Providers',
                    'Frontend / Dialog / Provider',
                    'borlabs-cookie',
                ),
                'providerPrivacyUrl' => _x(
                    'Privacy Policy URL',
                    'Frontend / Dialog / Provider',
                    'borlabs-cookie',
                ),
                'providerSearchPlaceholder' => _x(
                    'Search providers...',
                    'Frontend / Dialog / Provider',
                    'borlabs-cookie',
                ),
                'providerSingular' => _x(
                    'Provider',
                    'Frontend / Dialog / Provider',
                    'borlabs-cookie',
                ),
                'serviceDetailsTableCookieLifetime' => _x(
                    'Lifetime',
                    'Frontend / Dialog / Table Headline',
                    'borlabs-cookie',
                ),
                'serviceDetailsTableCookiePurpose' => _x(
                    'Purpose',
                    'Frontend / Dialog / Table Headline',
                    'borlabs-cookie',
                ),
                'serviceDetailsTableCookiePurposeFunctional' => _x(
                    'Functional',
                    'Frontend / Dialog / Table Headline',
                    'borlabs-cookie',
                ),
                'serviceDetailsTableCookiePurposeTracking' => _x(
                    'Tracking',
                    'Frontend / Dialog / Table Headline',
                    'borlabs-cookie',
                ),
                'serviceDetailsTableCookieType' => _x(
                    'Type',
                    'Frontend / Dialog / Table Headline',
                    'borlabs-cookie',
                ),
                'serviceDetailsTableCookieTypeHttp' => _x(
                    'HTTP',
                    'Frontend / Dialog / Table Headline',
                    'borlabs-cookie',
                ),
                'serviceDetailsTableCookieTypeLocalStorage' => _x(
                    'Local Storage',
                    'Frontend / Dialog / Table Headline',
                    'borlabs-cookie',
                ),
                'serviceDetailsTableCookieTypeSessionStorage' => _x(
                    'Session Storage',
                    'Frontend / Dialog / Table Headline',
                    'borlabs-cookie',
                ),
                'serviceDetailsTableCookies' => _x(
                    'Cookie(s)',
                    'Frontend / Dialog / Table Headline',
                    'borlabs-cookie',
                ),
                'serviceDetailsTableDescription' => _x(
                    'Description',
                    'Frontend / Dialog / Table Headline',
                    'borlabs-cookie',
                ),
                'serviceDetailsTableHosts' => _x(
                    'Hosts',
                    'Frontend / Dialog / Table Headline',
                    'borlabs-cookie',
                ),
                'serviceDetailsTableName' => _x(
                    'Name',
                    'Frontend / Dialog / Table Headline',
                    'borlabs-cookie',
                ),
                'serviceDetailsTableServiceOptionDataCollection' => _x(
                    'Data Collection',
                    'Frontend / Dialog / Table Headline',
                    'borlabs-cookie',
                ),
                'serviceDetailsTableServiceOptionDataPurpose' => _x(
                    'Data Purpose',
                    'Frontend / Dialog / Table Headline',
                    'borlabs-cookie',
                ),
                'serviceDetailsTableServiceOptionDistribution' => _x(
                    'Distribution',
                    'Frontend / Dialog / Table Headline',
                    'borlabs-cookie',
                ),
                'serviceDetailsTableServiceOptionLegalBasis' => _x(
                    'Legal Basis',
                    'Frontend / Dialog / Table Headline',
                    'borlabs-cookie',
                ),
                'serviceDetailsTableServiceOptionProcessingLocation' => _x(
                    'Processing Location',
                    'Frontend / Dialog / Table Headline',
                    'borlabs-cookie',
                ),
                'serviceDetailsTableServiceOptionTechnology' => _x(
                    'Technology',
                    'Frontend / Dialog / Table Headline',
                    'borlabs-cookie',
                ),
                'serviceDetailsTableServiceOptions' => _x(
                    'Service Options',
                    'Frontend / Dialog / Table Headline',
                    'borlabs-cookie',
                ),
                'servicePlural' => _x(
                    'Services',
                    'Frontend / Dialog / Headline',
                    'borlabs-cookie',
                ),
                'serviceSearchPlaceholder' => _x(
                    'Search Services...',
                    'Frontend / Dialog / Services',
                    'borlabs-cookie',
                ),
                'serviceSingular' => _x(
                    'Service',
                    'Frontend / Dialog / Headline',
                    'borlabs-cookie',
                ),
                'supportBorlabsCookieText' => _x(
                    'Borlabs Cookie',
                    'Frontend / Global / Text',
                    'borlabs-cookie',
                ),
                'supportBorlabsCookieUrl' => esc_attr_x(
                    'https://borlabs.io/borlabs-cookie/',
                    'Frontend / Dialog / URL',
                    'borlabs-cookie',
                ),
                'uid' => _x(
                    'UID',
                    'Frontend / Dialog / Consent History',
                    'borlabs-cookie',
                ),
            ],

            'provider' => [
                'unknownDescription' => _x(
                    'This is a service that we cannot assign to any provider.',
                    'Seeder / Default Provider / Description',
                    'borlabs-cookie',
                ),
                'unknownName' => _x(
                    'Unknown',
                    'Seeder / Default Provider / Name',
                    'borlabs-cookie',
                ),
                'websiteOwnerEntryDescription' => _x(
                    'This is the owner of this website. The owner is responsible for the content of this website and for the processing of your personal data.',
                    'Seeder / Default Provider / Text',
                    'borlabs-cookie',
                ),
                'websiteOwnerName' => _x(
                    'Owner of this website',
                    'Seeder / Default Provider / Name',
                    'borlabs-cookie',
                ),
            ],

            'service' => [
                'borlabsCookieDescription' => _x(
                    'Saves the visitors preferences selected in the Dialog of Borlabs Cookie.',
                    'Seeder / Default Service / Description',
                    'borlabs-cookie',
                ),
                'borlabsCookieName' => _x(
                    'Borlabs Cookie',
                    'Seeder / Default Service / Description',
                    'borlabs-cookie',
                ),
                'borlabsCookieServiceCookieDescription' => _x(
                    'This cookie stores information regarding consent for service groups and individual services.',
                    'Seeder / Default Service / Cookie Description',
                    'borlabs-cookie',
                ),
                'borlabsCookieServiceCookieLifetime' => _x(
                    '60 days',
                    'Seeder / Default Service / Cookie Description',
                    'borlabs-cookie',
                ),
            ],

            'serviceGroup' => [
                'essentialDescription' => _x(
                    'Essential services enable basic functions and are necessary for the proper function of the website.',
                    'Seeder / Default Service Group / Description',
                    'borlabs-cookie',
                ),
                'essentialName' => _x(
                    'Essential',
                    'Seeder / Default Service Group / Name',
                    'borlabs-cookie',
                ),
                'externalMediaDescription' => _x(
                    'Content from video platforms and social media platforms is blocked by default. If External Media services are accepted, access to those contents no longer requires manual consent.',
                    'Seeder / Default Service Group / Description',
                    'borlabs-cookie',
                ),
                'externalMediaName' => _x(
                    'External Media',
                    'Seeder / Default Service Group / Name',
                    'borlabs-cookie',
                ),
                'marketingDescription' => _x(
                    'Marketing services are used by third-party advertisers or publishers to display personalized ads. They do this by tracking visitors across websites.',
                    'Seeder / Default Service Group / Description',
                    'borlabs-cookie',
                ),
                'marketingName' => _x(
                    'Marketing',
                    'Seeder / Default Service Group / Name',
                    'borlabs-cookie',
                ),
                'statisticsDescription' => _x(
                    'Statistics cookies collect usage information, enabling us to gain insights into how our visitors interact with our website.',
                    'Seeder / Default Service Group / Description',
                    'borlabs-cookie',
                ),
                'statisticsName' => _x(
                    'Statistics',
                    'Seeder / Default Service Group / Name',
                    'borlabs-cookie',
                ),
                'unclassifiedDescription' => _x(
                    'Unclassified services are services that we are in the process of classifying. Only limited information about the use of the data and its purpose is available for these services.',
                    'Seeder / Default Service Group / Description',
                    'borlabs-cookie',
                ),
                'unclassifiedName' => _x(
                    'Unclassified',
                    'Seeder / Default Service Group / Name',
                    'borlabs-cookie',
                ),
            ],

            'shortcodes' => [
                'openConsentPreferences' => _x(
                    'Open Consent Preferences',
                    'Frontend / Shortcode / Button Title',
                    'borlabs-cookie',
                ),
            ],
        ];
    }
}
