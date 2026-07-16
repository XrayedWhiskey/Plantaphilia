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

namespace Borlabs\Cookie\Localization\License;

use Borlabs\Cookie\Localization\LocalizationInterface;

use function Borlabs\Cookie\System\WordPressGlobalFunctions\_x;

/**
 * The **LicenseLocalizationStrings** class contains various localized strings.
 *
 * @see \Borlabs\Cookie\Localization\License\LicenseLocalizationStrings::get()
 */
final class LicenseLocalizationStrings implements LocalizationInterface
{
    /**
     * @return array<array<string>>
     */
    public static function get(): array
    {
        return [
            // Alert messages
            'alert' => [
                'activateLicenseKey' => _x(
                    'Please activate your license key first. <a href="?page=borlabs-cookie-license">Click here</a> to enter your license key.',
                    'Backend / License / Alert Message',
                    'borlabs-cookie',
                ),
                'enterLicenseKey' => _x(
                    'Please enter your license key to receive updates.',
                    'Backend / License / Alert Message',
                    'borlabs-cookie',
                ),
                'invalidLicenseKey' => _x(
                    'Your license key is not valid.',
                    'Backend / License / Alert Message',
                    'borlabs-cookie',
                ),
                'invalidLicenseRequest' => _x(
                    'Your license request is not valid.',
                    'Backend / License / Alert Message',
                    'borlabs-cookie',
                ),
                'licenseExpired' => _x(
                    'Please renew your license key to receive updates. <a class="brlbs-cmpnt-link brlbs-cmpnt-link-with-icon" href="https://service.borlabs.io/" target="_blank" rel="nofollow noreferrer"><span>Click here</span><span class="brlbs-cmpnt-external-link-icon"></span></a> to log into your account and purchase a license renewal.',
                    'Backend / License / Alert Message',
                    'borlabs-cookie',
                ),
                'licenseExpiredFeatureNotAvailable' => _x(
                    'Your license has expired. This feature is not available with an expired license. <a class="brlbs-cmpnt-link brlbs-cmpnt-link-with-icon" href="https://service.borlabs.io/" target="_blank" rel="nofollow noreferrer"><span>Click here</span><span class="brlbs-cmpnt-external-link-icon"></span></a> to log into your account and purchase a license renewal.',
                    'Backend / License / Alert Message',
                    'borlabs-cookie',
                ),
                'licenseNotValidForCurrentBuild' => _x(
                    'Your license key is not valid for this version. This version of Borlabs Cookie was released after your license has expired, therefore you have to <a class="brlbs-cmpnt-link brlbs-cmpnt-link-with-icon" href="https://service.borlabs.io/" rel="nofollow noreferrer" target="_blank"><span>click here</span><span class="brlbs-cmpnt-external-link-icon"></span></a> to log into your account and purchase a license renewal.',
                    'Backend / License / Alert Message',
                    'borlabs-cookie',
                ),
                'licenseRefreshedSuccessfully' => _x(
                    'License information refreshed successfully.',
                    'Backend / License / Alert Message',
                    'borlabs-cookie',
                ),
                'licenseRegisteredSuccessfully' => _x(
                    'License registered successfully.',
                    'Backend / License / Alert Message',
                    'borlabs-cookie',
                ),
                'licenseRemovedSuccessfully' => _x(
                    'License removed successfully.',
                    'Backend / License / Alert Message',
                    'borlabs-cookie',
                ),
                'validLicenseRequired' => _x(
                    'A valid license is required to use this feature of Borlabs Cookie. <a href="?page=borlabs-cookie-license">Click here</a> to enter your license key, or <a class="brlbs-cmpnt-link brlbs-cmpnt-link-with-icon" href="https://service.borlabs.io/" rel="nofollow noreferrer" target="_blank"><span>click here</span><span class="brlbs-cmpnt-external-link-icon"></span></a> to log into your account and purchase a license renewal.',
                    'Backend / License / Alert Message',
                    'borlabs-cookie',
                ),
            ],

            // Breadcrumbs
            'breadcrumb' => [
                'module' => _x(
                    'License',
                    'Backend / License / Breadcrumb',
                    'borlabs-cookie',
                ),
            ],

            // Buttons
            'button' => [
                'refreshLicenseInformation' => _x(
                    'Refresh License Information',
                    'Backend / License / Button Title',
                    'borlabs-cookie',
                ),
                'removeLicense' => _x(
                    'Remove License',
                    'Backend / License / Button Title',
                    'borlabs-cookie',
                ),
            ],

            // Fields
            'field' => [
                'cloudScans' => _x(
                    'Cloud Scans',
                    'Backend / License / Label',
                    'borlabs-cookie',
                ),
                'confirmRefresh' => _x(
                    'Confirm Refresh',
                    'Backend / License / Label',
                    'borlabs-cookie',
                ),
                'confirmRemovingLicense' => _x(
                    'Confirm license removal',
                    'Backend / License / Label',
                    'borlabs-cookie',
                ),
                'licenseKey' => _x(
                    'License Key',
                    'Backend / License / Label',
                    'borlabs-cookie',
                ),
                'licenseName' => _x(
                    'License Name',
                    'Backend / License / Label',
                    'borlabs-cookie',
                ),
                'licenseStatus' => _x(
                    'License Status',
                    'Backend / License / Label',
                    'borlabs-cookie',
                ),
                'licenseValidUntil' => _x(
                    'Valid Until',
                    'Backend / License / Label',
                    'borlabs-cookie',
                ),
                'maxWebsites' => _x(
                    'Max Websites',
                    'Backend / License / Label',
                    'borlabs-cookie',
                ),
                'refresh' => _x(
                    'Refresh',
                    'Backend / License / Label',
                    'borlabs-cookie',
                ),
            ],

            // Headlines
            'headline' => [
                'removeLicense' => _x(
                    '<translation-key id="Remove-License">Remove License</translation-key>',
                    'Backend / License / Headline',
                    'borlabs-cookie',
                ),
                'yourLicense' => _x(
                    'Your License',
                    'Backend / License / Headline',
                    'borlabs-cookie',
                ),
                'yourLicenseInformation' => _x(
                    'Your License Information',
                    'Backend / License / Headline',
                    'borlabs-cookie',
                ),
            ],

            // Hint
            'hint' => [
                'licenseKey' => _x(
                    'Enter your License Key.',
                    'Backend / License / Hint',
                    'borlabs-cookie',
                ),
                'removeLicense' => _x(
                    'Please confirm that you want to remove your license data from this website. After the license data is removed you are able to enter your new license key.',
                    'Backend / License / Hint',
                    'borlabs-cookie',
                ),
            ],

            // Text
            'text' => [
                'expired' => _x(
                    'Your license has expired.',
                    'Backend / License / Text',
                    'borlabs-cookie',
                ),
                'valid' => _x(
                    'Your license is valid.',
                    'Backend / License / Text',
                    'borlabs-cookie',
                ),
            ],

            // Things to know
            'thingsToKnow' => [
                'customerPortal' => _x(
                    '<a class="brlbs-cmpnt-link brlbs-cmpnt-link-with-icon" href="https://service.borlabs.io/" rel="nofollow noreferrer" target="_blank"><span><translation-key id="Customer-Portal">Customer Portal</translation-key></span><span class="brlbs-cmpnt-external-link-icon"></span></a>',
                    'Backend / License / Things to know / Text',
                    'borlabs-cookie',
                ),
                'headlineRegistrationNotWorking' => _x(
                    'When Registration Doesn\'t Work',
                    'Backend / License / Things to know / Headline',
                    'borlabs-cookie',
                ),
                'headlineTransferringLicenseKeyToNewWebsite' => _x(
                    'Transferring a license key to a new website',
                    'Backend / License / Things to know / Headline',
                    'borlabs-cookie',
                ),
                'registrationNotWorkingA' => _x(
                    'If registration doesn\'t work, go to the customer portal and remove a website from the license you want to use.',
                    'Backend / License / Things to know / Text',
                    'borlabs-cookie',
                ),
                'registrationNotWorkingB' => _x(
                    'Also check the license details in the customer portal to make sure the website you\'re trying to register is not listed under <translation-key id="Blocked-Domain">Blocked Domain</translation-key>.',
                    'Backend / License / Things to know / Text',
                    'borlabs-cookie',
                ),
                'transferringLicenseKeyToNewWebsiteA' => _x(
                    'You can use the <translation-key id="Remove-License">Remove License</translation-key> option to remove the license from this website, or you can visit our <translation-key id="Customer-Portal">Customer Portal</translation-key> and remove the license from there.',
                    'Backend / License / Things to know / Text',
                    'borlabs-cookie',
                ),
            ],
        ];
    }
}
