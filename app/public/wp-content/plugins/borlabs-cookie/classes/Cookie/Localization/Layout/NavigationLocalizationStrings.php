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

namespace Borlabs\Cookie\Localization\Layout;

use Borlabs\Cookie\Localization\LocalizationInterface;

use function Borlabs\Cookie\System\WordPressGlobalFunctions\_x;

/**
 * The **NavigationLocalizationStrings** class contains various localized strings.
 *
 * @see \Borlabs\Cookie\Localization\Layout\NavigationLocalizationStrings::get()
 */
final class NavigationLocalizationStrings implements LocalizationInterface
{
    /**
     * @return array<array<string>>
     */
    public static function get(): array
    {
        return [
            // Navigation
            'navigation' => [
                'adminAndPluginSettings' => _x(
                    '<translation-key id="Navigation-Settings-Admin-Plugin-Settings">Admin &amp; Plugin Settings</translation-key>',
                    'Backend / Global / Navigation Entry',
                    'borlabs-cookie',
                ),
                'blockers' => _x(
                    '<translation-key id="Navigation-Blockers">Blockers</translation-key>',
                    'Backend / Global / Navigation Entry',
                    'borlabs-cookie',
                ),
                'compatibilityPatches' => _x(
                    '<translation-key id="Navigation-System-Compatibility-Patches">Compatibility Patches</translation-key>',
                    'Backend / Global / Navigation Entry',
                    'borlabs-cookie',
                ),
                'consentLogs' => _x(
                    '<translation-key id="Navigation-Consent-Management-Consent-Logs">Consent Logs</translation-key>',
                    'Backend / Global / Navigation Entry',
                    'borlabs-cookie',
                ),
                'consentManagement' => _x(
                    '<translation-key id="Navigation-Consent-Management">Consent Management</translation-key>',
                    'Backend / Global / Navigation Entry',
                    'borlabs-cookie',
                ),
                'contentBlockerAppearance' => _x(
                    '<translation-key id="Navigation-Blockers-Content-Blockers-Appearance">Appearance</translation-key>',
                    'Backend / Global / Navigation Entry',
                    'borlabs-cookie',
                ),
                'contentBlockerManage' => _x(
                    '<translation-key id="Navigation-Blockers-Content-Blockers-Manage">Manage</translation-key>',
                    'Backend / Global / Navigation Entry',
                    'borlabs-cookie',
                ),
                'contentBlockerSettings' => _x(
                    '<translation-key id="Navigation-Blockers-Content-Blockers-Settings">Settings</translation-key>',
                    'Backend / Global / Navigation Entry',
                    'borlabs-cookie',
                ),
                'contentBlockers' => _x(
                    '<translation-key id="Navigation-Blockers-Content-Blockers">Content Blockers</translation-key>',
                    'Backend / Global / Navigation Entry',
                    'borlabs-cookie',
                ),
                'dashboard' => _x(
                    '<translation-key id="Navigation-Dashboard">Dashboard</translation-key>',
                    'Backend / Global / Navigation Entry',
                    'borlabs-cookie',
                ),
                'dialog' => _x(
                    '<translation-key id="Navigation-Dialog-Widget-Dialog">Dialog</translation-key>',
                    'Backend / Global / Navigation Entry',
                    'borlabs-cookie',
                ),
                'dialogAndWidget' => _x(
                    '<translation-key id="Navigation-Dialog-Widget">Dialog &amp; Widget</translation-key>',
                    'Backend / Global / Navigation Entry',
                    'borlabs-cookie',
                ),
                'dialogAppearance' => _x(
                    '<translation-key id="Navigation-Dialog-Widget-Dialog-Appearance">Appearance</translation-key>',
                    'Backend / Global / Navigation Entry',
                    'borlabs-cookie',
                ),
                'dialogLocalization' => _x(
                    '<translation-key id="Navigation-Dialog-Widget-Dialog-Localization">Localization</translation-key>',
                    'Backend / Global / Navigation Entry',
                    'borlabs-cookie',
                ),
                'dialogSettings' => _x(
                    '<translation-key id="Navigation-Dialog-Widget-Dialog-Settings">Settings</translation-key>',
                    'Backend / Global / Navigation Entry',
                    'borlabs-cookie',
                ),
                'generalSettings' => _x(
                    '<translation-key id="Navigation-Settings-General-Settings">General Settings</translation-key>',
                    'Backend / Global / Navigation Entry',
                    'borlabs-cookie',
                ),
                'help' => _x(
                    '<translation-key id="Navigation-System-Help-Support">Help &amp; Support</translation-key>',
                    'Backend / Global / Navigation Entry',
                    'borlabs-cookie',
                ),
                'iabTcf' => _x(
                    '<translation-key id="Navigation-Consent-Management-IAB-TCF">IAB TCF</translation-key>',
                    'Backend / Global / Navigation Entry',
                    'borlabs-cookie',
                ),
                'iabTcfManageVendors' => _x(
                    '<translation-key id="Navigation-Consent-Management-IAB-TCF-Manage-Vendors">Manage Vendors</translation-key>',
                    'Backend / Global / Navigation Entry',
                    'borlabs-cookie',
                ),
                'iabTcfSettings' => _x(
                    '<translation-key id="Navigation-Consent-Management-IAB-TCF-Settings">Settings</translation-key>',
                    'Backend / Global / Navigation Entry',
                    'borlabs-cookie',
                ),
                'importExport' => _x(
                    '<translation-key id="Navigation-System-Import-Export">Import &amp; Export</translation-key>',
                    'Backend / Global / Navigation Entry',
                    'borlabs-cookie',
                ),
                'jobs' => _x(
                    '<translation-key id="Navigation-System-Jobs">Jobs</translation-key>',
                    'Backend / Global / Navigation Entry',
                    'borlabs-cookie',
                ),
                'language' => _x(
                    '<translation-key id="Navigation-Language">Language</translation-key>',
                    'Backend / Global / Navigation Entry',
                    'borlabs-cookie',
                ),
                'legacyImporter' => _x(
                    '<translation-key id="Navigation-Legacy-Importer">Legacy Importer</translation-key>',
                    'Backend / Global / Navigation Entry',
                    'borlabs-cookie',
                ),
                'library' => _x(
                    '<translation-key id="Navigation-Library">Library</translation-key>',
                    'Backend / Global / Navigation Entry',
                    'borlabs-cookie',
                ),
                'libraryAndScanner' => _x(
                    '<translation-key id="Navigation-Library-Scanner">Library &amp; Scanner</translation-key>',
                    'Backend / Global / Navigation Entry',
                    'borlabs-cookie',
                ),
                'libraryInstalledPackages' => _x(
                    '<translation-key id="Navigation-Library-Installed-Packages">Installed Packages</translation-key>',
                    'Backend / Global / Navigation Entry',
                    'borlabs-cookie',
                ),
                'libraryManage' => _x(
                    '<translation-key id="Navigation-Library-Manage">Manage</translation-key>',
                    'Backend / Global / Navigation Entry',
                    'borlabs-cookie',
                ),
                'librarySettings' => _x(
                    '<translation-key id="Navigation-Library-Settings">Settings</translation-key>',
                    'Backend / Global / Navigation Entry',
                    'borlabs-cookie',
                ),
                'license' => _x(
                    '<translation-key id="Navigation-System-License">License</translation-key>',
                    'Backend / Global / Navigation Entry',
                    'borlabs-cookie',
                ),
                'logs' => _x(
                    '<translation-key id="Navigation-System-Logs">Logs</translation-key>',
                    'Backend / Global / Navigation Entry',
                    'borlabs-cookie',
                ),
                'openMenu' => _x(
                    'Open Menu',
                    'Backend / Global / Navigation Entry',
                    'borlabs-cookie',
                ),
                'providers' => _x(
                    '<translation-key id="Navigation-Consent-Management-Providers">Providers</translation-key>',
                    'Backend / Global / Navigation Entry',
                    'borlabs-cookie',
                ),
                'scanner' => _x(
                    '<translation-key id="Navigation-Scanner">Scanner</translation-key>',
                    'Backend / Global / Navigation Entry',
                    'borlabs-cookie',
                ),
                'scriptBlockers' => _x(
                    '<translation-key id="Navigation-Blockers-Script-Blockers">Script Blockers</translation-key>',
                    'Backend / Global / Navigation Entry',
                    'borlabs-cookie',
                ),
                'serviceGroups' => _x(
                    '<translation-key id="Navigation-Consent-Management-Service-Groups">Service Groups</translation-key>',
                    'Backend / Global / Navigation Entry',
                    'borlabs-cookie',
                ),
                'services' => _x(
                    '<translation-key id="Navigation-Consent-Management-Services">Services</translation-key>',
                    'Backend / Global / Navigation Entry',
                    'borlabs-cookie',
                ),
                'settings' => _x(
                    '<translation-key id="Navigation-Settings">Settings</translation-key>',
                    'Backend / Global / Navigation Entry',
                    'borlabs-cookie',
                ),
                'setupAssistant' => _x(
                    '<translation-key id="Navigation-Setup-Assistant">Setup Assistant</translation-key>',
                    'Backend / Global / Navigation Entry',
                    'borlabs-cookie',
                ),
                'styleBlockers' => _x(
                    '<translation-key id="Navigation-Blockers-Style-Blockers">Style Blockers</translation-key>',
                    'Backend / Global / Navigation Entry',
                    'borlabs-cookie',
                ),
                'system' => _x(
                    '<translation-key id="Navigation-System">System</translation-key>',
                    'Backend / Global / Navigation Entry',
                    'borlabs-cookie',
                ),
                'widget' => _x(
                    '<translation-key id="Navigation-Dialog-Widget-Widget">Widget</translation-key>',
                    'Backend / Global / Navigation Entry',
                    'borlabs-cookie',
                ),
            ],

            // URL
            'url' => [
                'help' => _x(
                    'https://borlabs.io/support/',
                    'Backend / Global / Navigation Entry',
                    'borlabs-cookie',
                ),
            ],
        ];
    }
}
