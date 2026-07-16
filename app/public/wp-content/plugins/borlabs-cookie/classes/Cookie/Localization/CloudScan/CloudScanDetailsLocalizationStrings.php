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

namespace Borlabs\Cookie\Localization\CloudScan;

use Borlabs\Cookie\Localization\LocalizationInterface;

use function Borlabs\Cookie\System\WordPressGlobalFunctions\_x;

final class CloudScanDetailsLocalizationStrings implements LocalizationInterface
{
    /**
     * @return array<array<string>>
     */
    public static function get(): array
    {
        return [
            // Alert messages
            'alert' => [
                'noAdditionalSuggestionsFound' => _x(
                    'We have no additional suggestions.',
                    'Backend / Cloud Scan / Alert',
                    'borlabs-cookie',
                ),
                'noCookieFound' => _x(
                    'No cookie found.',
                    'Backend / Cloud Scan / Alert',
                    'borlabs-cookie',
                ),
                'noExternalResourceFound' => _x(
                    'No external resource found.',
                    'Backend / Cloud Scan / Alert',
                    'borlabs-cookie',
                ),
                'noSuggestionsFound' => _x(
                    'We found no suggestions.',
                    'Backend / Cloud Scan / Alert',
                    'borlabs-cookie',
                ),
            ],

            // Breadcrumbs
            'breadcrumb' => [
                'module' => _x(
                    'Scanner',
                    'Backend / Cloud Scan / Breadcrumb',
                    'borlabs-cookie',
                ),
                'scanning' => _x(
                    'Scanning',
                    'Backend / Cloud Scan / Breadcrumb',
                    'borlabs-cookie',
                ),
                'scanResults' => _x(
                    'Scan results',
                    'Backend / Cloud Scan / Breadcrumb',
                    'borlabs-cookie',
                ),
            ],

            // Buttons
            'button' => [
                'installSuggestedPackages' => _x(
                    'Install Suggested Packages',
                    'Backend / Cloud Scan / Button Title',
                    'borlabs-cookie',
                ),
            ],

            // Fields
            'field' => [
                'cloudScanId' => _x(
                    'Cloud Scan ID',
                    'Backend / Cloud Scan / Field',
                    'borlabs-cookie',
                ),
                'createdAt' => _x(
                    'Created at',
                    'Backend / Cloud Scan / Field',
                    'borlabs-cookie',
                ),
                'type' => _x(
                    'Type',
                    'Backend / Cloud Scan / Field',
                    'borlabs-cookie',
                ),
                'urls' => _x(
                    'URLs',
                    'Backend / Cloud Scan / Field',
                    'borlabs-cookie',
                ),
            ],

            // Headlines
            'headline' => [
                'cookies' => _x(
                    'Cookies',
                    'Backend / Cloud Scan / Headline',
                    'borlabs-cookie',
                ),
                'externalResoures' => _x(
                    'External resources',
                    'Backend / Cloud Scan / Headline',
                    'borlabs-cookie',
                ),
                'scannedPages' => _x(
                    'Scanned pages',
                    'Backend / Cloud Scan / Headline',
                    'borlabs-cookie',
                ),
                'scanResults' => _x(
                    'Scan results',
                    'Backend / Cloud Scan / Headline',
                    'borlabs-cookie',
                ),
                'suggestedPackagesAlreadyInstalled' => _x(
                    'Suggested Packages Already Installed',
                    'Backend / Cloud Scan / Headline',
                    'borlabs-cookie',
                ),
                'suggestedPackagesToInstall' => _x(
                    'Suggested Packages to Install',
                    'Backend / Cloud Scan / Headline',
                    'borlabs-cookie',
                ),
                'suggestions' => _x(
                    'Suggestions',
                    'Backend / Cloud Scan / Headline',
                    'borlabs-cookie',
                ),
            ],

            // Tables
            'table' => [
                'externalHostname' => _x(
                    '<translation-key id="External-hostname">External hostname</translation-key>',
                    'Backend / Cloud Scan / Table',
                    'borlabs-cookie',
                ),
                'failureType' => _x(
                    '<translation-key id="Failure-type">Failure type</translation-key>',
                    'Backend / Cloud Scan / Table',
                    'borlabs-cookie',
                ),
                'hostname' => _x(
                    'Hostname',
                    'Backend / Cloud Scan / Table',
                    'borlabs-cookie',
                ),
                'lifetime' => _x(
                    'Lifetime',
                    'Backend / Cloud Scan / Table',
                    'borlabs-cookie',
                ),
                'name' => _x(
                    'Name',
                    'Backend / Cloud Scan / Table',
                    'borlabs-cookie',
                ),
                'numberOfResources' => _x(
                    '<translation-key id="Number-of-resources">Number of resources</translation-key>',
                    'Backend / Cloud Scan / Table',
                    'borlabs-cookie',
                ),
                'pageUrl' => _x(
                    'Found on page',
                    'Backend / Cloud Scan / Table',
                    'borlabs-cookie',
                ),
                'path' => _x(
                    'Path',
                    'Backend / Cloud Scan / Table',
                    'borlabs-cookie',
                ),
                'resourceUrl' => _x(
                    'Resource URL',
                    'Backend / Cloud Scan / Table',
                    'borlabs-cookie',
                ),
                'status' => _x(
                    'Status',
                    'Backend / Cloud Scan / Table',
                    'borlabs-cookie',
                ),
                'suggestedPackage' => _x(
                    'Suggested package',
                    'Backend / Cloud Scan / Table',
                    'borlabs-cookie',
                ),
                'url' => _x(
                    'URL',
                    'Backend / Cloud Scan / Table',
                    'borlabs-cookie',
                ),
            ],

            // Text
            'text' => [
                'days' => _x(
                    'days',
                    'Backend / Cloud Scan / Text',
                    'borlabs-cookie',
                ),
                'failedCount' => _x(
                    'Failed',
                    'Backend / Cloud Scan / Text',
                    'borlabs-cookie',
                ),
                'finishedCount' => _x(
                    'Success',
                    'Backend / Cloud Scan / Text',
                    'borlabs-cookie',
                ),
                'scanInProgress' => _x(
                    'Scan in progress',
                    'Backend / Cloud Scan / Text',
                    'borlabs-cookie',
                ),
                'scanAnalysing' => _x(
                    'Scan is analysing',
                    'Backend / Cloud Scan / Text',
                    'borlabs-cookie',
                ),
                'scanningCount' => _x(
                    'Scanning',
                    'Backend / Cloud Scan / Text',
                    'borlabs-cookie',
                ),
                'session' => _x(
                    'Session',
                    'Backend / Cloud Scan / Text',
                    'borlabs-cookie',
                ),
                'suggestionsA' => _x(
                    'The scanner\'s analysis can make suggestions for installing packages that prevent the inclusion of external resources or improve compatibility between the theme and installed plugins.',
                    'Backend / Cloud Scan / Alert',
                    'borlabs-cookie',
                ),
                'suggestionsB' => _x(
                    'Click on the <translation-key id="Button-Details">Details</translation-key> button to get more information about the package. Once the package is installed, you will be redirected back to this page to proceed with the installation of the next package.',
                    'Backend / Cloud Scan / Alert',
                    'borlabs-cookie',
                ),
            ],

            // Things to know
            'thingsToKnow' => [
                'cookies' => _x(
                    'To comply with data protection, cookies must be blocked in most cases until consent is given. Only essential cookies may be set without consent. An essential cookie could be one that, for instance, stores the shopping cart contents or the selected language.',
                    'Backend / Cloud Scan / Tip',
                    'borlabs-cookie',
                ),
                'cookiesDetails' => _x(
                    'Click the icon to view sample locations associated with the detected cookie.',
                    'Backend / Cloud Scan / Tip',
                    'borlabs-cookie',
                ),
                'cookiesSuggestedPackageA' => _x(
                    '<translation-key id="Suggested-package">Suggested package</translation-key>: This column lists a recommended package to install to block the cookie if the cookie is not essential.',
                    'Backend / Cloud Scan / Tip',
                    'borlabs-cookie',
                ),
                'cookiesSuggestedPackageB' => _x(
                    'Note: This column may be empty if the cookie is loaded from another resource, but for which a package is suggested. Once the suggested package is installed, the entry with the blank suggestion column is likely to be removed in the subsequent scan.',
                    'Backend / Cloud Scan / Tip',
                    'borlabs-cookie',
                ),
                'headlineCookies' => _x(
                    'Cookies',
                    'Backend / Cloud Scan / Tip',
                    'borlabs-cookie',
                ),
                'headlineExternalResources' => _x(
                    'External resources',
                    'Backend / Cloud Scan / Tip',
                    'borlabs-cookie',
                ),
                'headlineStatus' => _x(
                    'Status',
                    'Backend / Cloud Scan / Tip',
                    'borlabs-cookie',
                ),
                'externalResources' => _x(
                    'External resources are resources that are loaded from a different domain than the page itself. For example, if you have a page with the URL <strong><em>https://www.example.com</em></strong> and this page loads a resource from <strong><em>https://www.example.org</em></strong>, then this resource is considered an external resource. To comply with data protection, in most cases, these external resources must be blocked until consent is given.',
                    'Backend / Cloud Scan / Tip',
                    'borlabs-cookie',
                ),
                'externalResourcesDetails' => _x(
                    'Click on the icon to get detailed information about the external resource. In the details you can find examples of discovered resources and their respective locations.',
                    'Backend / Cloud Scan / Tip',
                    'borlabs-cookie',
                ),
                'externalResourcesExternalHostname' => _x(
                    '<translation-key id="External-hostname">External hostname</translation-key>: Resources are being loaded from this source.',
                    'Backend / Cloud Scan / Tip',
                    'borlabs-cookie',
                ),
                'externalResourcesNumberOfResources' => _x(
                    '<translation-key id="Number-of-resources">Number of resources</translation-key>: The number of resources loaded from this source. The resources found are limited to 10 per hostname.',
                    'Backend / Cloud Scan / Tip',
                    'borlabs-cookie',
                ),
                'externalResourcesSuggestedPackageA' => _x(
                    '<translation-key id="Suggested-package">Suggested package</translation-key>: This column lists a recommended package to install to block the external resource.',
                    'Backend / Cloud Scan / Tip',
                    'borlabs-cookie',
                ),
                'externalResourcesSuggestedPackageB' => _x(
                    'Note: This column may be empty if the resource is loaded from another resource, but for which a package is suggested. Once the suggested package is installed, the entry with the blank suggestion column is likely to be removed in the subsequent scan.',
                    'Backend / Cloud Scan / Tip',
                    'borlabs-cookie',
                ),
                'statusFailed' => _x(
                    'Failed: The scanner could not scan the page. More information can be found in the <translation-key id="Failure-type">Failure type</translation-key> column.',
                    'Backend / Cloud Scan / Tip',
                    'borlabs-cookie',
                ),
                'statusFinished' => _x(
                    'Success: The page was scanned successfully.',
                    'Backend / Cloud Scan / Tip',
                    'borlabs-cookie',
                ),
                'statusScanning' => _x(
                    'Scanning: The page is being scanned.',
                    'Backend / Cloud Scan / Tip',
                    'borlabs-cookie',
                ),
            ],
        ];
    }
}
