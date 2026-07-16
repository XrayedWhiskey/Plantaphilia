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

namespace Borlabs\Cookie\Localization\SystemCheck;

use Borlabs\Cookie\Localization\LocalizationInterface;

use function Borlabs\Cookie\System\WordPressGlobalFunctions\_x;

/**
 * The **SystemCheckLocalizationStrings** class contains various localized strings.
 *
 * @see \Borlabs\Cookie\Localization\SystemCheck\SystemCheckLocalizationStrings::get()
 */
final class SystemCheckLocalizationStrings implements LocalizationInterface
{
    /**
     * @return array<array<string>>
     */
    public static function get(): array
    {
        return [
            // Alert messages
            'alert' => [
                'compatibilityPatchesDisabledByConstant' => _x(
                    'The <translation-key id="Compatibility-Patches">Compatibility Patches</translation-key> are disabled by the <strong>BORLABS_COOKIE_ENABLE_SAFE_MODE</strong> constant.',
                    'Backend / System Check / Alert Message',
                    'borlabs-cookie',
                ),
                'compatibilityPatchesDisabledByCookie' => _x(
                    'The <translation-key id="Compatibility-Patches">Compatibility Patches</translation-key> are disabled by the <strong>borlabsCookieSafeModeHash</strong> cookie.',
                    'Backend / System Check / Alert Message',
                    'borlabs-cookie',
                ),
                'customFolderDoesNotExist' => _x(
                    'The custom folder <strong>{{ folder }}</strong> does not exist.',
                    'Backend / System Check / Alert Message',
                    'borlabs-cookie',
                ),
                'folderIsNotWritable' => _x(
                    'The folder <strong>{{ folder }}</strong> is not writable. Please set the right permissions. See <a href="https://borlabs.io/folder-permissions/" rel="nofollow noreferrer" target="_blank">FAQ</a>.',
                    'Backend / System Check / Alert Message',
                    'borlabs-cookie',
                ),
                'languageConfigurationIsBroken' => _x(
                    'Your language configuration is broken. Disable all plugins except <translation-key id="Borlabs-Cookie">Borlabs Cookie</translation-key> until this message disappears. When you have found the plugin that is causing this error, check if an update is available and install it.',
                    'Backend / System Check / Alert Message',
                    'borlabs-cookie',
                ),
                'multilanguageConfigurationIncorrect' => _x(
                    'Your multilingual configuration is incorrect; no languages have been set up.',
                    'Backend / System Check / Alert Message',
                    'borlabs-cookie',
                ),
                'noSSLCertification' => _x(
                    'Your website is not using a SSL certification.',
                    'Backend / System Check / Alert Message',
                    'borlabs-cookie',
                ),
                'notScheduled' => _x(
                    'The following WP-Cron events are not scheduled: {{ events }}',
                    'Backend / System Check / Alert Message',
                    'borlabs-cookie',
                ),
                'overdueEvents' => _x(
                    'The following WP-Cron events are overdue: {{ events }}',
                    'Backend / System Check / Alert Message',
                    'borlabs-cookie',
                ),
                'sslConfigurationIsNotCorrect' => _x(
                    'Your SSL configuration is not correct. Please go to <translation-key id="SettingsGeneral">Settings &raquo; General</translation-key> and replace <strong><em>http://</em></strong> with <strong><em>https://</em></strong> in the settings <translation-key id="WordPressAddressURL">WordPress Address (URL)</translation-key> and <translation-key id="SiteAddressURL">Site Address (URL)</translation-key>.<br>WP_CONTENT_URL: {{ wp_content_url }}<br>$_SERVER[\'HTTPS\']: {{ https }}<br>$_SERVER[\'SERVER_PORT\']: {{ server_port }}',
                    'Backend / System Check / Alert Message',
                    'borlabs-cookie',
                ),
            ],

            // Buttons
            'button' => [
            ],

            // Headlines
            'headline' => [
                'systemStatus' => _x(
                    'System Status',
                    'Backend / System Check / Table Headline',
                    'borlabs-cookie',
                ),
            ],

            // Hint
            'hint' => [
            ],

            // Status
            'status' => [
                'active' => _x(
                    'Active',
                    'Backend / System Check / Text',
                    'borlabs-cookie',
                ),
                'error' => _x(
                    'Error',
                    'Backend / System Check / Text',
                    'borlabs-cookie',
                ),
                'inactive' => _x(
                    'Inactive',
                    'Backend / System Check / Text',
                    'borlabs-cookie',
                ),
                'ok' => _x(
                    'OK',
                    'Backend / System Check / Text',
                    'borlabs-cookie',
                ),
            ],

            // Tables
            'table' => [
                'borlabsCookieStatus' => _x(
                    'Borlabs Cookie Dialog Status',
                    'Backend / System Check / Table Headline',
                    'borlabs-cookie',
                ),
                'cacheFolder' => _x(
                    'Cache Folder',
                    'Backend / System Check / Table Headline',
                    'borlabs-cookie',
                ),
                'cronjob' => _x(
                    'Cronjobs',
                    'Backend / System Check / Table Headline',
                    'borlabs-cookie',
                ),
                'databaseSeeds' => _x(
                    'Default Entries',
                    'Backend / System Check / Table Headline',
                    'borlabs-cookie',
                ),
                'databaseTables' => _x(
                    'Database Tables',
                    'Backend / System Check / Table Headline',
                    'borlabs-cookie',
                ),
                'dbVersion' => _x(
                    'Database Version',
                    'Backend / System Check / Table Headline',
                    'borlabs-cookie',
                ),
                'defaultContentBlocker' => _x(
                    'Default Content Blocker',
                    'Backend / System Check / Table Headline',
                    'borlabs-cookie',
                ),
                'defaultCountries' => _x(
                    'Default Countries',
                    'Backend / System Check / Table Headline',
                    'borlabs-cookie',
                ),
                'defaultProviders' => _x(
                    'Default Providers',
                    'Backend / System Check / Table Headline',
                    'borlabs-cookie',
                ),
                'defaultServiceGroups' => _x(
                    'Default Service Groups',
                    'Backend / System Check / Table Headline',
                    'borlabs-cookie',
                ),
                'defaultServices' => _x(
                    'Default Services',
                    'Backend / System Check / Table Headline',
                    'borlabs-cookie',
                ),
                'defaultServicesInitSync' => _x(
                    'Default Services Initial Sync',
                    'Backend / System Check / Table Headline',
                    'borlabs-cookie',
                ),
                'general' => _x(
                    'General',
                    'Backend / System Check / Table Headline',
                    'borlabs-cookie',
                ),
                'geoIpDatabaseLastCheck' => _x(
                    'GeoIP Database Last Check with API',
                    'Backend / System Check / Table Headline',
                    'borlabs-cookie',
                ),
                'iabTcfVendorDatabaseLastCheck' => _x(
                    'IAB TCF Vendor Database Last Check with API',
                    'Backend / System Check / Table Headline',
                    'borlabs-cookie',
                ),
                'installedVersion' => _x(
                    'Current Borlabs Cookie Version',
                    'Backend / System Check / Table Headline',
                    'borlabs-cookie',
                ),
                'language' => _x(
                    'Language (Current / Default)',
                    'Backend / System Check / Table Headline',
                    'borlabs-cookie',
                ),
                'packageLibraryDatabaseLastCheck' => _x(
                    'Library Database Last Check with API',
                    'Backend / System Check / Table Headline',
                    'borlabs-cookie',
                ),
                'phpVersion' => _x(
                    'PHP Version',
                    'Backend / System Check / Table Headline',
                    'borlabs-cookie',
                ),
                'serverInformation' => _x(
                    'Server Information',
                    'Backend / System Check / Table Headline',
                    'borlabs-cookie',
                ),
                'ssl' => _x(
                    'SSL',
                    'Backend / System Check / Table Headline',
                    'borlabs-cookie',
                ),
                'storageFolder' => _x(
                    'Storage Folder',
                    'Backend / System Check / Table Headline',
                    'borlabs-cookie',
                ),
                'tableCloudScan' => _x(
                    'Cloud Scans',
                    'Backend / System Check / Table Headline',
                    'borlabs-cookie',
                ),
                'tableCloudScanExternalResource' => _x(
                    'Cloud Scan External Resources',
                    'Backend / System Check / Table Headline',
                    'borlabs-cookie',
                ),
                'tableCloudScanCookie' => _x(
                    'Cloud Scan Cookies',
                    'Backend / System Check / Table Headline',
                    'borlabs-cookie',
                ),
                'tableCloudScanSuggestion' => _x(
                    'Cloud Scan Suggestions',
                    'Backend / System Check / Table Headline',
                    'borlabs-cookie',
                ),
                'tableCompatibilityPatch' => _x(
                    'Compatibility Patches',
                    'Backend / System Check / Table Headline',
                    'borlabs-cookie',
                ),
                'tableConsentLog' => _x(
                    'Consent Logs',
                    'Backend / System Check / Table Headline',
                    'borlabs-cookie',
                ),
                'tableConsentStatisticByDayGroupedByServiceGroup' => _x(
                    'Consent Statistic by Day - <translation-key id="Service-Groups">Service Groups</translation-key>',
                    'Backend / System Check / Table Headline',
                    'borlabs-cookie',
                ),
                'tableConsentStatisticByDay' => _x(
                    'Consent Statistic by Day - <translation-key id="Services">Services</translation-key>',
                    'Backend / System Check / Table Headline',
                    'borlabs-cookie',
                ),
                'tableConsentStatisticByHourGroupedByServiceGroup' => _x(
                    'Consent Statistic by Hour - <translation-key id="Service-Groups">Service Groups</translation-key>',
                    'Backend / System Check / Table Headline',
                    'borlabs-cookie',
                ),
                'tableConsentStatisticByHour' => _x(
                    'Consent Statistic by Hour - <translation-key id="Services">Services</translation-key>',
                    'Backend / System Check / Table Headline',
                    'borlabs-cookie',
                ),
                'tableContentBlocker' => _x(
                    'Content Blockers',
                    'Backend / System Check / Table Headline',
                    'borlabs-cookie',
                ),
                'tableContentBlockerLocation' => _x(
                    'Content Blocker Locations',
                    'Backend / System Check / Table Headline',
                    'borlabs-cookie',
                ),
                'tableCountry' => _x(
                    'Countries',
                    'Backend / System Check / Table Headline',
                    'borlabs-cookie',
                ),
                'tableJob' => _x(
                    'Jobs',
                    'Backend / System Check / Table Headline',
                    'borlabs-cookie',
                ),
                'tableLog' => _x(
                    'Logs',
                    'Backend / System Check / Table Headline',
                    'borlabs-cookie',
                ),
                'tablePackage' => _x(
                    'Packages',
                    'Backend / System Check / Table Headline',
                    'borlabs-cookie',
                ),
                'tableProvider' => _x(
                    'Providers',
                    'Backend / System Check / Table Headline',
                    'borlabs-cookie',
                ),
                'tableScriptBlocker' => _x(
                    'Script Blockers',
                    'Backend / System Check / Table Headline',
                    'borlabs-cookie',
                ),
                'tableService' => _x(
                    'Services',
                    'Backend / System Check / Table Headline',
                    'borlabs-cookie',
                ),
                'tableServiceCookie' => _x(
                    'Service Cookies',
                    'Backend / System Check / Table Headline',
                    'borlabs-cookie',
                ),
                'tableServiceGroup' => _x(
                    'Service Groups',
                    'Backend / System Check / Table Headline',
                    'borlabs-cookie',
                ),
                'tableServiceLocation' => _x(
                    'Service Locations',
                    'Backend / System Check / Table Headline',
                    'borlabs-cookie',
                ),
                'tableServiceOption' => _x(
                    'Service Options',
                    'Backend / System Check / Table Headline',
                    'borlabs-cookie',
                ),
                'tableStyleBlocker' => _x(
                    'Style Blockers',
                    'Backend / System Check / Table Headline',
                    'borlabs-cookie',
                ),
                'tableVendor' => _x(
                    'IAB TCF Vendors',
                    'Backend / System Check / Table Headline',
                    'borlabs-cookie',
                ),
                'totalConsentLogs' => _x(
                    'Total Consent Logs',
                    'Backend / System Check / Table Headline',
                    'borlabs-cookie',
                ),
                'wordPressRestApiStatus' => _x(
                    'WordPress REST API',
                    'Backend / System Check / Table Headline',
                    'borlabs-cookie',
                ),
            ],
        ];
    }
}
