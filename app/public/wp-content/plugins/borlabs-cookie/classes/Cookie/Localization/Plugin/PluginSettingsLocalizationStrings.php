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

namespace Borlabs\Cookie\Localization\Plugin;

use Borlabs\Cookie\Localization\LocalizationInterface;

use function Borlabs\Cookie\System\WordPressGlobalFunctions\_x;

/**
 * The **PluginSettingsLocalizationStrings** class contains various localized strings.
 */
final class PluginSettingsLocalizationStrings implements LocalizationInterface
{
    /**
     * @return array<array<string>>
     */
    public static function get(): array
    {
        return [
            // Alert messages
            'alert' => [
                'automaticUpdateNotSetToAll' => _x(
                    'The <translation-key id="Automatic-Update">Automatic Update</translation-key> setting is not set to <translation-key id="All-versions">All versions</translation-key>. We highly recommend that you set it to <translation-key id="All-versions">All versions</translation-key> to ensure that your setup is compliant, compatible, and secure.',
                    'Backend / Plugin Settings / Alert Message',
                    'borlabs-cookie',
                ),
            ],

            // Breadcrumbs
            'breadcrumb' => [
                'module' => _x(
                    'Admin &amp; Plugin Settings',
                    'Backend / Plugin Settings / Breadcrumb',
                    'borlabs-cookie',
                ),
            ],

            // Headline
            'headline' => [
                'adminSettings' => _x(
                    'Admin Settings',
                    'Backend / Plugin Settings / Headline',
                    'borlabs-cookie',
                ),
                'debugSettings' => _x(
                    'Debug Settings',
                    'Backend / Plugin Settings / Headline',
                    'borlabs-cookie',
                ),
                'resetPluginSettings' => _x(
                    'Reset Admin &amp; Plugin Settings',
                    'Backend / Plugin Settings / Headline',
                    'borlabs-cookie',
                ),
                'updateSettings' => _x(
                    'Update Settings',
                    'Backend / Plugin Settings / Headline',
                    'borlabs-cookie',
                ),
            ],

            // Fields
            'field' => [
                'automaticUpdate' => _x(
                    '<translation-key id="Automatic-Update">Automatic Update</translation-key>',
                    'Backend / Plugin Settings / Field',
                    'borlabs-cookie',
                ),
                'clearThirdPartyCache' => _x(
                    '<translation-key id="Clear-Third-Party-Cache">Clear Third-Party Cache</translation-key>',
                    'Backend / Plugin Settings / Label',
                    'borlabs-cookie',
                ),
                'displayModeSettings' => _x(
                    '<translation-key id="Display-Mode-Settings">Display Mode of Settings</translation-key>',
                    'Backend / Plugin Settings / Label',
                    'borlabs-cookie',
                ),
                'enableDebugConsole' => _x(
                    '<translation-key id="Enable-Debug-Console">Enable Debug Console</translation-key>',
                    'Backend / Plugin Settings / Field',
                    'borlabs-cookie',
                ),
                'enableDebugLogging' => _x(
                    'Enable Debug Logging',
                    'Backend / Plugin Settings / Field',
                    'borlabs-cookie',
                ),
                'metaBox' => _x(
                    'Display Meta Box',
                    'Backend / Plugin Settings / Label',
                    'borlabs-cookie',
                ),
                'wordPressAdminSidebarMenuMode' => _x(
                    '<translation-key id="WordPress-Admin-Sidebar-Menu-Mode">WordPress Admin Sidebar Menu Mode</translation-key>',
                    'Backend / Plugin Settings / Label',
                    'borlabs-cookie',
                ),
            ],

            // Hints
            'hint' => [
                'automaticUpdate' => _x(
                    'Manages how updates for the <translation-key id="Borlabs-Cookie">Borlabs Cookie</translation-key> WordPress plugin are handled. For more details, see the <translation-key id="Things-to-know">Things to know</translation-key> section.',
                    'Backend / Plugin Settings / Hint',
                    'borlabs-cookie',
                ),
                'clearThirdPartyCache' => _x(
                    'Automatically clears the cache of third-party <translation-key id="Plugins">Plugins</translation-key> and <translation-key id="Themes">Themes</translation-key> following specific actions within <translation-key id="Borlabs-Cookie">Borlabs Cookie</translation-key>. This ensures that changes are instantly visible, eliminating the need to wait for the cache to expire.',
                    'Backend / Settings / Hint',
                    'borlabs-cookie',
                ),
                'displayModeSettings' => _x(
                    'Choose how settings are displayed across the system. Show all available options in <translation-key id="Display-Mode-Settings-Standard">Standard</translation-key> mode or only essential ones in <translation-key id="Display-Mode-Settings-Simplified">Simplified</translation-key> mode.',
                    'Backend / Plugin Settings / Hint',
                    'borlabs-cookie',
                ),
                'enableDebugConsole' => _x(
                    'The debug console helps identify and resolve issues with <translation-key id="Borlabs-Cookie">Borlabs Cookie</translation-key>. It is visible only to administrators and is not shown to your website visitors. ',
                    'Backend / Dashboard / Hint',
                    'borlabs-cookie',
                ),
                'enableDebugLogging' => _x(
                    'This setting should only be activated when urgently required. When activated, it generates a large number of log entries that can affect the performance of the website. The log entries can be found under <translation-key id="Navigation-System">System</translation-key> &raquo; <translation-key id="Navigation-System-Logs">Logs</translation-key>.',
                    'Backend / Dashboard / Hint',
                    'borlabs-cookie',
                ),
                'metaBox' => _x(
                    'Display the <translation-key id="Borlabs-Cookie">Borlabs Cookie</translation-key> <translation-key id="Meta-Box">Meta Box</translation-key> on the selected post types. The <translation-key id="Meta-Box">Meta Box</translation-key> allows you to add custom JavaScript on specific pages.',
                    'Backend / Settings / Hint',
                    'borlabs-cookie',
                ),
                'reset' => _x(
                    'Please confirm that you want to reset all <translation-key id="Admin-Plugin-Settings">Admin &amp; Plugin Settings</translation-key> settings. They will be reset to their default settings.',
                    'Backend / Plugin Settings / Hint',
                    'borlabs-cookie',
                ),
                'wordPressAdminSidebarMenuMode' => _x(
                    'Choose which menu items are displayed in the WordPress admin sidebar. Show only essential items in <translation-key id="WordPress-Admin-Sidebar-Menu-Mode-Simplified">Simplified</translation-key> mode, a balanced selection in <translation-key id="WordPress-Admin-Sidebar-Menu-Mode-Standard">Standard</translation-key> mode, or all available items in <translation-key id="WordPress-Admin-Sidebar-Menu-Mode-Expanded">Expanded</translation-key> mode.',
                    'Backend / Plugin Settings / Hint',
                    'borlabs-cookie',
                ),
            ],

            // Things to know
            'thingsToKnow' => [
                'automaticUpdateA' => _x(
                    'You can choose how <translation-key id="Borlabs-Cookie">Borlabs Cookie</translation-key> updates automatically. Select from one of the four available options.',
                    'Backend / Plugin Settings / Things to know / Text',
                    'borlabs-cookie',
                ),
                'automaticUpdateB' => _x(
                    '<translation-key id="All-versions">All versions</translation-key>: All updates are installed automatically. This option is recommended to keep your setup compliant, compatible, and secure.',
                    'Backend / Plugin Settings / Things to know / Text',
                    'borlabs-cookie',
                ),
                'automaticUpdateC' => _x(
                    '<translation-key id="Minor-versions">Minor versions</translation-key>: <translation-key id="MINOR">MINOR</translation-key>, <translation-key id="PATCH">PATCH</translation-key> and <translation-key id="HOTFIX">HOTFIX</translation-key> versions are installed automatically.',
                    'Backend / Plugin Settings / Things to know / Text',
                    'borlabs-cookie',
                ),
                'automaticUpdateD' => _x(
                    '<translation-key id="Patch-versions">Patch versions</translation-key>: <translation-key id="PATCH">PATCH</translation-key> and <translation-key id="HOTFIX">HOTFIX</translation-key> versions are installed automatically.',
                    'Backend / Plugin Settings / Things to know / Text',
                    'borlabs-cookie',
                ),
                'automaticUpdateE' => _x(
                    'A standard version number follows the format 1.0.3.0, where the segments represent <translation-key id="MAJOR">MAJOR</translation-key>, <translation-key id="MINOR">MINOR</translation-key>, <translation-key id="PATCH">PATCH</translation-key>, and <translation-key id="HOTFIX">HOTFIX</translation-key>, respectively.',
                    'Backend / Plugin Settings / Things to know / Text',
                    'borlabs-cookie',
                ),
                'debugConsoleA' => _x(
                    'When enabled, the debug console appears at the bottom of your website while you are logged in as an administrator. It is not visible to regular visitors.',
                    'Backend / Plugin Settings / Things to know / Text',
                    'borlabs-cookie',
                ),
                'debugConsoleB' => _x(
                    'The console automatically opens if one of its tests fails. In most cases, it also provides helpful hints about the possible cause of the issue.',
                    'Backend / Plugin Settings / Things to know / Text',
                    'borlabs-cookie',
                ),
                'debugConsoleC' => _x(
                    '<a class="brlbs-cmpnt-link brlbs-cmpnt-link-with-icon" href="%s" rel="nofollow noreferrer" target="_blank"><span>More information about <translation-key id="Debug-Console">Debug Console</translation-key></span><span class="brlbs-cmpnt-external-link-icon"></span></a>',
                    'Backend / Plugin Settings / Things to know / Text',
                    'borlabs-cookie',
                ),/** @see SetupAssistantLocalizationStrings::class The identical string is used here. */
                'displayModeSettingsA' => _x(
                    'The <translation-key id="Display-Mode-Settings">Display Mode of Settings</translation-key> determines how many configuration options are visible throughout the system.',
                    'Backend / Plugin Settings / Things to know / Text',
                    'borlabs-cookie',
                ),
                'displayModeSettingsB' => _x(
                    '<translation-key id="Display-Mode-Settings-Standard">Standard</translation-key>: All available settings are shown. Best for advanced or technical users.',
                    'Backend / Plugin Settings / Things to know / Text',
                    'borlabs-cookie',
                ),
                'displayModeSettingsC' => _x(
                    '<translation-key id="Display-Mode-Settings-Simplified">Simplified</translation-key>: Only the most essential settings are displayed. Ideal for non-technical users.',
                    'Backend / Plugin Settings / Things to know / Text',
                    'borlabs-cookie',
                ),
                /** @see SetupAssistantLocalizationStrings::class The identical string is used here. */
                'displayModeSettingsD' => _x(
                    'You can switch between modes at any time. Changing the display mode only affects which settings are visible. It does not modify your existing configurations or saved values.',
                    'Backend / Plugin Settings / Things to know / Text',
                    'borlabs-cookie',
                ),
                'headlineAutomaticUpdate' => _x(
                    'About <translation-key id="Automatic-Update">Automatic Update</translation-key>',
                    'Backend / Plugin Settings / Things to know / Headline',
                    'borlabs-cookie',
                ),
                'headlineDebugConsole' => _x(
                    'What is the <translation-key id="Debug-Console">Debug Console</translation-key>?',
                    'Backend / Plugin Settings / Things to know / Headline',
                    'borlabs-cookie',
                ),
                /** @see SetupAssistantLocalizationStrings::class The identical string is used here. */
                'headlineDisplayModeSettings' => _x(
                    'About <translation-key id="Display-Mode-Settings">Display Mode of Settings</translation-key>',
                    'Backend / Plugin Settings / Things to know / Headline',
                    'borlabs-cookie',
                ),
                'headlineWhatIsTheMetaBox' => _x(
                    'What is the Meta Box?',
                    'Backend / Plugin Settings / Things to know / Headline',
                    'borlabs-cookie',
                ),
                'whatIsTheMetaBox' => _x(
                    'If active the <translation-key id="Meta-Box">Meta Box</translation-key> is displayed in the selected post types. This allows you to execute code (JavaScript, HTML, <translation-key id="Shortcodes">Shortcodes</translation-key>) on the page and e.g. trigger a conversion pixel.',
                    'Backend / Plugin Settings / Things to know / Text',
                    'borlabs-cookie',
                ),
            ],

            // URL
            'url' => [
                'debugConsole' => _x(
                    'https://borlabs.io/kb/debug-console-test-results-explained/?utm_source=Borlabs+Cookie&amp;utm_medium=Things+to+know&amp;utm_campaign=Analysis',
                    'Backend / Plugin Settings / URL',
                    'borlabs-cookie',
                ),
            ],
        ];
    }
}
