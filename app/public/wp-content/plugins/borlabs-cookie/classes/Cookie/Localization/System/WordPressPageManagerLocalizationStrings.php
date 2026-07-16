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

namespace Borlabs\Cookie\Localization\System;

use Borlabs\Cookie\Localization\LocalizationInterface;

use function Borlabs\Cookie\System\WordPressGlobalFunctions\_x;

final class WordPressPageManagerLocalizationStrings implements LocalizationInterface
{
    /**
     * @return array<array<string>>
     */
    public static function get(): array
    {
        return [
            // MenuTitle
            'menuTitle' => [
                'adminAndPluginSettings' => _x(
                    '- Admin &amp; Plugin Settings',
                    'Backend / Page Manager / Menu Title',
                    'borlabs-cookie',
                ),
                'borlabsCookie' => _x(
                    'Borlabs Cookie',
                    'Backend / Page Manager / Menu Title',
                    'borlabs-cookie',
                ),
                'compatibilityPatches' => _x(
                    '- Compatibility Patches',
                    'Backend / Page Manager / Menu Title',
                    'borlabs-cookie',
                ),
                'consentLogs' => _x(
                    '- Consent Logs',
                    'Backend / Page Manager / Menu Title',
                    'borlabs-cookie',
                ),
                'consentManagement' => _x(
                    'Consent Management',
                    'Backend / Page Manager / Menu Title',
                    'borlabs-cookie',
                ),
                'contentBlockers' => _x(
                    'Content Blockers',
                    'Backend / Page Manager / Menu Title',
                    'borlabs-cookie',
                ),
                'contentBlockersAppearance' => _x(
                    '- Appearance',
                    'Backend / Page Manager / Menu Title',
                    'borlabs-cookie',
                ),
                'contentBlockersManage' => _x(
                    '- Manage',
                    'Backend / Page Manager / Menu Title',
                    'borlabs-cookie',
                ),
                'contentBlockersSettings' => _x(
                    '- Settings',
                    'Backend / Page Manager / Menu Title',
                    'borlabs-cookie',
                ),
                'dashboard' => _x(
                    'Dashboard',
                    'Backend / Page Manager / Menu Title',
                    'borlabs-cookie',
                ),
                'dialog' => _x(
                    'Dialog',
                    'Backend / Page Manager / Menu Title',
                    'borlabs-cookie',
                ),
                'dialogAppearance' => _x(
                    '- Appearance',
                    'Backend / Page Manager / Menu Title',
                    'borlabs-cookie',
                ),
                'dialogLocalization' => _x(
                    '- Localization',
                    'Backend / Page Manager / Menu Title',
                    'borlabs-cookie',
                ),
                'dialogSettings' => _x(
                    '- Settings',
                    'Backend / Page Manager / Menu Title',
                    'borlabs-cookie',
                ),
                'generalSettings' => _x(
                    '- General Settings',
                    'Backend / Page Manager / Menu Title',
                    'borlabs-cookie',
                ),
                'iabTcf' => _x(
                    '- IAB TCF',
                    'Backend / Page Manager / Menu Title',
                    'borlabs-cookie',
                ),
                'iabTcfManageVendors' => _x(
                    '- - Manage Vendors',
                    'Backend / Page Manager / Menu Title',
                    'borlabs-cookie',
                ),
                'iabTcfSettings' => _x(
                    '- - Settings',
                    'Backend / Page Manager / Menu Title',
                    'borlabs-cookie',
                ),
                'importExport' => _x(
                    '- Import &amp; Export',
                    'Backend / Page Manager / Menu Title',
                    'borlabs-cookie',
                ),
                'jobs' => _x(
                    '- Jobs',
                    'Backend / Page Manager / Menu Title',
                    'borlabs-cookie',
                ),
                'legacyImporter' => _x(
                    '- Legacy Importer',
                    'Backend / Page Manager / Menu Title',
                    'borlabs-cookie',
                ),
                'library' => _x(
                    'Library',
                    'Backend / Page Manager / Menu Title',
                    'borlabs-cookie',
                ),
                'libraryManage' => _x(
                    '- Manage',
                    'Backend / Page Manager / Menu Title',
                    'borlabs-cookie',
                ),
                'librarySettings' => _x(
                    '- Settings',
                    'Backend / Page Manager / Menu Title',
                    'borlabs-cookie',
                ),
                'license' => _x(
                    '- License',
                    'Backend / Page Manager / Menu Title',
                    'borlabs-cookie',
                ),
                'logs' => _x(
                    '- Logs',
                    'Backend / Page Manager / Menu Title',
                    'borlabs-cookie',
                ),
                'providers' => _x(
                    '- Providers',
                    'Backend / Page Manager / Menu Title',
                    'borlabs-cookie',
                ),
                'scanner' => _x(
                    'Scanner',
                    'Backend / Page Manager / Menu Title',
                    'borlabs-cookie',
                ),
                'scriptBlockers' => _x(
                    'Script Blockers',
                    'Backend / Page Manager / Menu Title',
                    'borlabs-cookie',
                ),
                'serviceGroups' => _x(
                    '- Service Groups',
                    'Backend / Page Manager / Menu Title',
                    'borlabs-cookie',
                ),
                'services' => _x(
                    '- Services',
                    'Backend / Page Manager / Menu Title',
                    'borlabs-cookie',
                ),
                'settings' => _x(
                    'Settings',
                    'Backend / Page Manager / Menu Title',
                    'borlabs-cookie',
                ),
                'setupAssistant' => _x(
                    '- Setup Assistant',
                    'Backend / Page Manager / Menu Title',
                    'borlabs-cookie',
                ),
                'styleBlockers' => _x(
                    'Style Blockers',
                    'Backend / Page Manager / Menu Title',
                    'borlabs-cookie',
                ),
                'system' => _x(
                    'System',
                    'Backend / Page Manager / Menu Title',
                    'borlabs-cookie',
                ),
                'widget' => _x(
                    'Widget',
                    'Backend / Page Manager / Menu Title',
                    'borlabs-cookie',
                ),
            ],

            // SiteTitle
            'siteTitle' => [
                'adminAndPluginSettings' => _x(
                    'Admin &amp; Plugin Settings',
                    'Backend / Page Manager / Site Title',
                    'borlabs-cookie',
                ),
                'borlabsCookie' => _x(
                    'Borlabs Cookie',
                    'Backend / Page Manager / Site Title',
                    'borlabs-cookie',
                ),
                'compatibilityPatches' => _x(
                    'Compatibility Patches',
                    'Backend / Page Manager / Site Title',
                    'borlabs-cookie',
                ),
                'consentLogs' => _x(
                    'Consent Logs',
                    'Backend / Page Manager / Site Title',
                    'borlabs-cookie',
                ),
                'consentManagement' => _x(
                    'Consent Management',
                    'Backend / Page Manager / Site Title',
                    'borlabs-cookie',
                ),
                'contentBlockers' => _x(
                    'Content Blockers',
                    'Backend / Page Manager / Site Title',
                    'borlabs-cookie',
                ),
                'contentBlockersAppearance' => _x(
                    'Content Blockers - Appearance',
                    'Backend / Page Manager / Site Title',
                    'borlabs-cookie',
                ),
                'contentBlockersManage' => _x(
                    'Content Blockers - Manage',
                    'Backend / Page Manager / Site Title',
                    'borlabs-cookie',
                ),
                'contentBlockersSettings' => _x(
                    'Content Blockers - Settings',
                    'Backend / Page Manager / Site Title',
                    'borlabs-cookie',
                ),
                'dashboard' => _x(
                    'Dashboard',
                    'Backend / Page Manager / Site Title',
                    'borlabs-cookie',
                ),
                'dialog' => _x(
                    'Dialog',
                    'Backend / Page Manager / Site Title',
                    'borlabs-cookie',
                ),
                'dialogAppearance' => _x(
                    '<translation-key id="Dialog-Appearance">Dialog - Appearance</translation-key>',
                    'Backend / Page Manager / Site Title',
                    'borlabs-cookie',
                ),
                'dialogLocalization' => _x(
                    'Dialog - Localization',
                    'Backend / Page Manager / Site Title',
                    'borlabs-cookie',
                ),
                'dialogSettings' => _x(
                    '<translation-key id="Dialog-Settings">Dialog - Settings</translation-key>',
                    'Backend / Page Manager / Site Title',
                    'borlabs-cookie',
                ),
                'generalSettings' => _x(
                    'General Settings',
                    'Backend / Page Manager / Site Title',
                    'borlabs-cookie',
                ),
                'iabTcf' => _x(
                    'IAB TCF',
                    'Backend / Page Manager / Menu Title',
                    'borlabs-cookie',
                ),
                'iabTcfManageVendors' => _x(
                    'IAB TCF Manage Vendors',
                    'Backend / Page Manager / Menu Title',
                    'borlabs-cookie',
                ),
                'iabTcfSettings' => _x(
                    'IAB TCF Settings',
                    'Backend / Page Manager / Menu Title',
                    'borlabs-cookie',
                ),
                'importExport' => _x(
                    'Import &amp; Export',
                    'Backend / Page Manager / Menu Title',
                    'borlabs-cookie',
                ),
                'jobs' => _x(
                    'Jobs',
                    'Backend / Page Manager / Site Title',
                    'borlabs-cookie',
                ),
                'legacyImporter' => _x(
                    'Legacy Importer',
                    'Backend / Page Manager / Site Title',
                    'borlabs-cookie',
                ),
                'library' => _x(
                    'Library',
                    'Backend / Page Manager / Site Title',
                    'borlabs-cookie',
                ),
                'libraryManage' => _x(
                    'Library - Manage',
                    'Backend / Page Manager / Site Title',
                    'borlabs-cookie',
                ),
                'librarySettings' => _x(
                    'Library - Settings',
                    'Backend / Page Manager / Site Title',
                    'borlabs-cookie',
                ),
                'license' => _x(
                    'License',
                    'Backend / Page Manager / Site Title',
                    'borlabs-cookie',
                ),
                'logs' => _x(
                    'Logs',
                    'Backend / Page Manager / Site Title',
                    'borlabs-cookie',
                ),
                'providers' => _x(
                    'Providers',
                    'Backend / Page Manager / Site Title',
                    'borlabs-cookie',
                ),
                'scanner' => _x(
                    'Scanner',
                    'Backend / Page Manager / Site Title',
                    'borlabs-cookie',
                ),
                'scriptBlockers' => _x(
                    'Script Blockers',
                    'Backend / Page Manager / Site Title',
                    'borlabs-cookie',
                ),
                'serviceGroups' => _x(
                    'Service Groups',
                    'Backend / Page Manager / Site Title',
                    'borlabs-cookie',
                ),
                'services' => _x(
                    'Services',
                    'Backend / Page Manager / Site Title',
                    'borlabs-cookie',
                ),
                'settings' => _x(
                    'Settings',
                    'Backend / Page Manager / Site Title',
                    'borlabs-cookie',
                ),
                'setupAssistant' => _x(
                    'Setup Assistant',
                    'Backend / Page Manager / Site Title',
                    'borlabs-cookie',
                ),
                'styleBlockers' => _x(
                    'Style Blockers',
                    'Backend / Page Manager / Site Title',
                    'borlabs-cookie',
                ),
                'system' => _x(
                    'System',
                    'Backend / Page Manager / Site Title',
                    'borlabs-cookie',
                ),
                'widget' => _x(
                    'Widget',
                    'Backend / Page Manager / Site Title',
                    'borlabs-cookie',
                ),
            ],
        ];
    }
}
