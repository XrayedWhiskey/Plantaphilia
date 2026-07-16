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

namespace Borlabs\Cookie\Localization\DebugConsole;

use Borlabs\Cookie\Localization\LocalizationInterface;

use function Borlabs\Cookie\System\WordPressGlobalFunctions\_x;

/**
 * The **DebugConsoleLocalizationStrings** class contains various localized strings.
 *
 * @see \Borlabs\Cookie\Localization\DebugConsole\DebugConsoleLocalizationStrings::get()
 */
final class DebugConsoleLocalizationStrings implements LocalizationInterface
{
    /**
     * @return array<array<string>>
     */
    public static function get(): array
    {
        return [
            // Buttons
            'button' => [
                'toggleTypeEntries' => _x(
                    'Toggle {{ type }} Entries',
                    'Frontend / Debug Console / Button',
                    'borlabs-cookie',
                ),
            ],

            // Headlines
            'headline' => [
                'title' => _x(
                    'Borlabs Cookie - Debug Console',
                    'Frontend / Debug Console / Headline',
                    'borlabs-cookie',
                ),
            ],

            // Tables
            'table' => [
                'additionalInformation' => _x(
                    'Additional Information',
                    'Frontend / Debug Console / Table',
                    'borlabs-cookie',
                ),
                'result' => _x(
                    'Result',
                    'Frontend / Debug Console / Table',
                    'borlabs-cookie',
                ),
                'test' => _x(
                    'Test',
                    'Frontend / Debug Console / Table',
                    'borlabs-cookie',
                ),
            ],

            // Test
            'test' => [
                'configLoaded' => _x(
                    'Config is loaded',
                    'Frontend / Debug Console / Text',
                    'borlabs-cookie',
                ),
                'configNotLoadedAsIntended' => _x(
                    'Config is not loaded as intended.',
                    'Frontend / Debug Console / Text',
                    'borlabs-cookie',
                ),
                'configNotMisplaced' => _x(
                    'Config is placed correctly',
                    'Frontend / Debug Console / Text',
                    'borlabs-cookie',
                ),
                'cookieDomain' => _x(
                    'Cookie Domain is set correctly',
                    'Frontend / Debug Console / Text',
                    'borlabs-cookie',
                ),
                'cookieSecureAttribute' => _x(
                    'Cookie <translation-key id="Secure-Attribute">Secure attribute</translation-key> is set correctly',
                    'Frontend / Debug Console / Text',
                    'borlabs-cookie',
                ),
                'dialogNotHidden' => _x(
                    'Dialog is not hidden',
                    'Frontend / Debug Console / Text',
                    'borlabs-cookie',
                ),
                'geoIp' => _x(
                    '<translation-key id="GeoIP">GeoIP</translation-key>',
                    'Frontend / Debug Console / Text',
                    'borlabs-cookie',
                ),
                'googleDataLayer' => _x(
                    'Google dataLayer',
                    'Frontend / Debug Console / Text',
                    'borlabs-cookie',
                ),
                'googleTagManager' => _x(
                    'Google Tag Manager',
                    'Frontend / Debug Console / Text',
                    'borlabs-cookie',
                ),
                'iabTcfDisabled' => _x(
                    'IAB TCF is disabled',
                    'Frontend / Debug Console / Text',
                    'borlabs-cookie',
                ),
                'iabTcfEnabledAndConfigured' => _x(
                    'IAB TCF is enabled and vendors are configured',
                    'Frontend / Debug Console / Text',
                    'borlabs-cookie',
                ),
                'installed' => _x(
                    'Borlabs Cookie is installed',
                    'Frontend / Debug Console / Text',
                    'borlabs-cookie',
                ),
                'pluginUrlSetCorrectly' => _x(
                    'Plugin URL is set correctly',
                    'Frontend / Debug Console / Text',
                    'borlabs-cookie',
                ),
                'respectDoNotTrack' => _x(
                    'Respect <translation-key id="Do-Not-Track">&quot;Do Not Track&quot;</translation-key>',
                    'Frontend / Debug Console / Text',
                    'borlabs-cookie',
                ),
                'restEndpointAccessiblity' => _x(
                    'REST endpoint is accessible',
                    'Frontend / Debug Console / Text',
                    'borlabs-cookie',
                ),
                'scriptBlockerMisconfiguration' => _x(
                    '<translation-key id="Script-Blocker">Script Blocker</translation-key> no misconfiguration',
                    'Frontend / Debug Console / Text',
                    'borlabs-cookie',
                ),
                'services' => _x(
                    'Services',
                    'Frontend / Debug Console / Text',
                    'borlabs-cookie',
                ),
            ],

            // Text
            'text' => [
                'baseUrl' => _x(
                    'Base URL',
                    'Frontend / Debug Console / Text',
                    'borlabs-cookie',
                ),
                'codeExecutionDisabled' => _x(
                    'Code execution is disabled',
                    'Frontend / Debug Console / Text',
                    'borlabs-cookie',
                ),
                'codeExecutionNotDisabled' => _x(
                    'Code execution is not disabled',
                    'Frontend / Debug Console / Text',
                    'borlabs-cookie',
                ),
                'configMisplaced' => _x(
                    'The configuration of Borlabs Cookie was placed incorrectly on the website, causing it not to function properly. This often occurs with optimization settings or plugins that combine and minify scripts. To identify the cause, disable all plugins and switch to a default theme. Then, enable each plugin and theme one by one to pinpoint the issue.',
                    'Frontend / Debug Console / Text',
                    'borlabs-cookie',
                ),
                'configNotLoaded' => _x(
                    'Configuration not found at the specified URL',
                    'Frontend / Debug Console / Text',
                    'borlabs-cookie',
                ),
                'configNotLoadedAsIntended' => _x(
                    'This is usually caused by a third party plugin that combines or minifies scripts. To identify the cause, disable all plugins and switch to a default theme. Then, enable each plugin and theme one by one to pinpoint the issue.',
                    'Frontend / Debug Console / Text',
                    'borlabs-cookie',
                ),
                'configurePluginUrl' => _x(
                    'To configure the <translation-key id="Plugin-URL">Plugin URL</translation-key>, navigate to <translation-key id="Borlabs-Cookie">Borlabs Cookie</translation-key> &raquo; <translation-key id="Settings">Settings</translation-key> &raquo; <translation-key id="Plugin-URL">Plugin URL</translation-key>.',
                    'Frontend / Debug Console / Text',
                    'borlabs-cookie',
                ),
                'configurePluginUrlAutomatically' => _x(
                    'To automatically configure the <translation-key id="Plugin-URL">Plugin URL</translation-key>, navigate to <translation-key id="Borlabs-Cookie">Borlabs Cookie</translation-key> &raquo; <translation-key id="Settings">Settings</translation-key>, and scroll down to the <translation-key id="Reset">Reset</translation-key> section. Once the reset is complete, re-enable the <translation-key id="Borlabs-Cookie-Status">Borlabs Cookie Status</translation-key> and ensure the protocol in the <translation-key id="Plugin-URL">Plugin URL</translation-key> is set to "{{ protocol }}".',
                    'Frontend / Debug Console / Text',
                    'borlabs-cookie',
                ),
                'configuredCookieDomain' => _x(
                    'Configured Cookie Domain',
                    'Frontend / Debug Console / Text',
                    'borlabs-cookie',
                ),
                'configuredHostname' => _x(
                    'Configured Hostname',
                    'Frontend / Debug Console / Text',
                    'borlabs-cookie',
                ),
                'configuredProtocol' => _x(
                    'Configured Protocol',
                    'Frontend / Debug Console / Text',
                    'borlabs-cookie',
                ),
                'configuredUrl' => _x(
                    'Configured URL',
                    'Frontend / Debug Console / Text',
                    'borlabs-cookie',
                ),
                'contentBlocker' => _x(
                    '<translation-key id="Content-Blocker">Content Blocker</translation-key>',
                    'Frontend / Debug Console / Text',
                    'borlabs-cookie',
                ),
                'correctCookieSecureStatus' => _x(
                    'Correct Cookie <translation-key id="Secure-Attribute">Secure attribute</translation-key> Status',
                    'Frontend / Debug Console / Text',
                    'borlabs-cookie',
                ),
                'correctUrl' => _x(
                    'Correct URL',
                    'Frontend / Debug Console / Text',
                    'borlabs-cookie',
                ),
                'currentCookieSecureStatus' => _x(
                    'Current Cookie <translation-key id="Secure-Attribute">Secure attribute</translation-key> Status',
                    'Frontend / Debug Console / Text',
                    'borlabs-cookie',
                ),
                'currentGeoIpCachingModeStatus' => _x(
                    'Current <translation-key id="GeoIP-Caching-Mode">GeoIP Caching Mode</translation-key> Status',
                    'Frontend / Debug Console / Text',
                    'borlabs-cookie',
                ),
                'currentGeoIpStatus' => _x(
                    'Current <translation-key id="GeoIP">GeoIP</translation-key> Status',
                    'Frontend / Debug Console / Text',
                    'borlabs-cookie',
                ),
                'currentHostname' => _x(
                    'Hostname of this website',
                    'Frontend / Debug Console / Text',
                    'borlabs-cookie',
                ),
                'currentProtocol' => _x(
                    'Protocol of this website',
                    'Frontend / Debug Console / Text',
                    'borlabs-cookie',
                ),
                'currentRespectDoNotTrackStatus' => _x(
                    'Current Respect <translation-key id="Do-Not-Track">&quot;Do Not Track&quot;</translation-key> Status',
                    'Frontend / Debug Console / Text',
                    'borlabs-cookie',
                ),
                'debugConsoleInformationA' => _x(
                    'This debug console is only displayed to administrators and is not visible to visitors.',
                    'Frontend / Debug Console / Text',
                    'borlabs-cookie',
                ),
                'debugConsoleInformationB' => _x(
                    'To disable the debug console, navigate to <translation-key id="Borlabs-Cookie">Borlabs Cookie</translation-key> &raquo; <translation-key id="Navigation-Settings">Settings</translation-key> &raquo; <translation-key id="Navigation-Settings-Admin-Plugin-Settings">Admin &amp; Plugin Settings</translation-key> and disable the setting <translation-key id="Enable-Debug-Console">Enable Debug Console</translation-key>.',
                    'Frontend / Debug Console / Text',
                    'borlabs-cookie',
                ),
                'dialogNotDisplayedDueDialogHiddenSetting' => _x(
                    'The base URL was included in the <translation-key id="Hide-Dialog-On-Pages">Hide Dialog on Pages</translation-key> list.',
                    'Frontend / Debug Console / Text',
                    'borlabs-cookie',
                ),
                'dialogNotDisplayedDueDialogHiddenSettingAdditionalInformation' => _x(
                    'The <translation-key id="Hide-Dialog-On-Pages">Hide Dialog on Pages</translation-key> list automatically includes the configured <translation-key id="Imprint-Page">Imprint Page</translation-key> and <translation-key id="Privacy-Page">Privacy Page</translation-key> URLs.',
                    'Frontend / Debug Console / Text',
                    'borlabs-cookie',
                ),
                'dialogNotDisplayedDueDialogHiddenSettingIsNotAnError' => _x(
                    'This is not an error if you intended to hide the dialog on this page.',
                    'Frontend / Debug Console / Text',
                    'borlabs-cookie',
                ),
                'dialogNotDisplayedDueRespectDoNotTrackSetting' => _x(
                    'As the setting is enabled in both Borlabs Cookie and your browser, the dialog is not displayed.',
                    'Frontend / Debug Console / Text',
                    'borlabs-cookie',
                ),
                'domain' => _x(
                    'Domain',
                    'Frontend / Debug Console / Text',
                    'borlabs-cookie',
                ),
                'geoIpCachingModeDisabled' => _x(
                    'The HTML request is used to determine whether consent is required.',
                    'Frontend / Debug Console / Text',
                    'borlabs-cookie',
                ),
                'geoIpCachingModeEnabled' => _x(
                    'The AJAX request is used to determine whether consent is required.',
                    'Frontend / Debug Console / Text',
                    'borlabs-cookie',
                ),
                'geoIpCountriesConfiguredCorrectlyA' => _x(
                    'Please verify that the <translation-key id="GeoIP">GeoIP</translation-key> countries are configured correctly.',
                    'Frontend / Debug Console / Text',
                    'borlabs-cookie',
                ),
                'geoIpCountriesConfiguredCorrectlyB' => _x(
                    'A common mistake is including your own country in the list of countries where the dialog is hidden.',
                    'Frontend / Debug Console / Text',
                    'borlabs-cookie',
                ),
                'googleAds' => _x(
                    'Google Ads',
                    'Frontend / Debug Console / Text',
                    'borlabs-cookie',
                ),
                'googleAnalytics' => _x(
                    'Google Analytics',
                    'Frontend / Debug Console / Text',
                    'borlabs-cookie',
                ),
                'googleAnalyticsScriptBlockerInstalled' => _x(
                    'Google Analytics Script Blocker service is installed',
                    'Frontend / Debug Console / Text',
                    'borlabs-cookie',
                ),
                'googleDataLayerIsEmpty' => _x(
                    'Google dataLayer is empty',
                    'Frontend / Debug Console / Text',
                    'borlabs-cookie',
                ),
                'googleTagConfiguredBeforeDefaultConsent' => _x(
                    'A Google tag is configured before the default consent settings are applied.',
                    'Frontend / Debug Console / Text',
                    'borlabs-cookie',
                ),
                'googleTagManager' => _x(
                    'Google Tag Manager',
                    'Frontend / Debug Console / Text',
                    'borlabs-cookie',
                ),
                'googleTagManagerCachedLocally' => _x(
                    'Google Tag Manager local caching',
                    'Frontend / Debug Console / Text',
                    'borlabs-cookie',
                ),
                'googleTagManagerConsentModeDisabled' => _x(
                    'Consent Mode is disabled',
                    'Frontend / Debug Console / Text',
                    'borlabs-cookie',
                ),
                'googleTagManagerId' => _x(
                    'Google Tag Manager ID',
                    'Frontend / Debug Console / Text',
                    'borlabs-cookie',
                ),
                'googleTagManagerLoadedBeforeDefaultConsent' => _x(
                    'Google Tag Manager is loaded before the default consent is set',
                    'Frontend / Debug Console / Text',
                    'borlabs-cookie',
                ),
                'googleTagManagerMappedServiceGroupDisabledOrEmpty' => _x(
                    'The following service group is deactivated, empty or does not exist, but is selected in the GTM settings',
                    'Frontend / Debug Console / Text',
                    'borlabs-cookie',
                ),
                'googleTagManagerNotInstalledButServiceWithGoogleTagManagerIdFound' => _x(
                    'Google Tag Manager package is not installed, but there is a service with the ID "google-tag-manager".',
                    'Frontend / Debug Console / Text',
                    'borlabs-cookie',
                ),
                'googleTagManagerPackageNotInstalled' => _x(
                    'Google Tag Manager package is not installed.',
                    'Frontend / Debug Console / Text',
                    'borlabs-cookie',
                ),
                'googleTagManagerServiceGroupMappingSuccessful' => _x(
                    'All selected GTM service groups are active and contain services.',
                    'Frontend / Debug Console / Text',
                    'borlabs-cookie',
                ),
                'iabTcfNoVendorsConfigured' => _x(
                    'No vendors are configured',
                    'Frontend / Debug Console / Text',
                    'borlabs-cookie',
                ),
                'listOfAllServicesIncludingConfigration' => _x(
                    'List of all services incl. configuration',
                    'Frontend / Debug Console / Text',
                    'borlabs-cookie',
                ),
                'protocol' => _x(
                    'Protocol',
                    'Frontend / Debug Console / Text',
                    'borlabs-cookie',
                ),
                'scriptBlockerMisconfiguration' => _x(
                    'The following <translation-key id="Services">Services</translation-key> may be configured incorrectly',
                    'Frontend / Debug Console / Text',
                    'borlabs-cookie',
                ),
                'scriptBlockerMisconfigurationInfoA' => _x(
                    'The unlock code of the <translation-key id="Script-Blocker">Script Blocker</translation-key> must only be stored in the <translation-key id="Content-Blocker">Content Blocker</translation-key> - not in the associated <translation-key id="Service">Service</translation-key>.',
                    'Frontend / Debug Console / Text',
                    'borlabs-cookie',
                ),
                'scriptBlockerMisconfigurationInfoB' => _x(
                    'To resolve the issue, remove the unblock code of the <translation-key id="Script-Blocker">Script Blocker</translation-key> from the <translation-key id="Service">Service</translation-key>.',
                    'Frontend / Debug Console / Text',
                    'borlabs-cookie',
                ),
                'service' => _x(
                    '<translation-key id="Service">Service</translation-key>',
                    'Frontend / Debug Console / Text',
                    'borlabs-cookie',
                ),
                'statusDisabled' => _x(
                    'Disabled',
                    'Frontend / Debug Console / Text',
                    'borlabs-cookie',
                ),
                'statusEnabled' => _x(
                    'Enabled',
                    'Frontend / Debug Console / Text',
                    'borlabs-cookie',
                ),
                'statusInstalled' => _x(
                    'installed',
                    'Frontend / Debug Console / Text',
                    'borlabs-cookie',
                ),
                'statusNotInstalled' => _x(
                    'not installed',
                    'Frontend / Debug Console / Text',
                    'borlabs-cookie',
                ),
                'undefinedSettingsKey' => _x(
                    'Settings key "{{ settingsKey }}" is undefined, please report to developer',
                    'Frontend / Debug Console / Text',
                    'borlabs-cookie',
                ),
            ],
        ];
    }
}
