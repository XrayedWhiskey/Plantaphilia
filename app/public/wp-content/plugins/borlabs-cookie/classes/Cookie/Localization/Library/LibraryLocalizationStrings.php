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

namespace Borlabs\Cookie\Localization\Library;

use Borlabs\Cookie\Localization\LocalizationInterface;

use function Borlabs\Cookie\System\WordPressGlobalFunctions\_x;

final class LibraryLocalizationStrings implements LocalizationInterface
{
    /**
     * @return array<array<string>>
     */
    public static function get(): array
    {
        // @noinspection HtmlUnknownTarget
        return [
            // Alert messages
            'alert' => [
                'compatibilityPatchInstallationNecessity' => _x(
                    'This package <span class="brlbs-cmpnt-important-text">must</span> always be installed if recommended by the <translation-key id="Navigation-Scanner">Scanner</translation-key>.',
                    'Backend / Library / Alert Message',
                    'borlabs-cookie',
                ),
                'compatibilityPatchServiceInformation' => _x(
                    'If the <translation-key id="Services">Services</translation-key> included in the <translation-key id="Compatibility-Patch">Compatibility Patch</translation-key> are not used on the website, they must be disabled under <translation-key id="Navigation-Consent-Management">Consent Management</translation-key> &raquo; <translation-key id="Navigation-Consent-Management-Services">Services</translation-key> after installing or updating the package.',
                    'Backend / Library / Alert Message',
                    'borlabs-cookie',
                ),
                'copyrights' => _x(
                    'All copyrights, trademarks, and other intellectual property rights mentioned or displayed in this library belong to their respective owners. Unless explicitly stated, these entities are not affiliated, endorsed by, or in any way associated with us.',
                    'Backend / Library / Alert Message',
                    'borlabs-cookie',
                ),
                'disclaimer' => _x(
                    'We expressly disclaim any liability for the timeliness and accuracy of the data provided.',
                    'Backend / Library / Alert Message',
                    'borlabs-cookie',
                ),
                'installationFailed' => _x(
                    'Installation failed. Navigate to <translation-key id="Borlabs-Cookie">Borlabs Cookie</translation-key> &raquo; <translation-key id="Navigation-System">System</translation-key> &raquo; <translation-key id="Navigation-System-Logs">Logs</translation-key>, and check the logs for more information.',
                    'Backend / Library / Alert Message',
                    'borlabs-cookie',
                ),
                'installationSuccessful' => _x(
                    'Installation successful.',
                    'Backend / Library / Alert Message',
                    'borlabs-cookie',
                ),
                'libraryRefreshedSuccessfully' => _x(
                    'Library refreshed successfully.',
                    'Backend / Library / Alert Message',
                    'borlabs-cookie',
                ),
                'packageIsDeprecated' => _x(
                    'This package has been marked as deprecated and should be uninstalled.',
                    'Backend / Library / Alert Message',
                    'borlabs-cookie',
                ),
                'packageIsNotInstalled' => _x(
                    'The package is not installed.',
                    'Backend / Cloud Scan / Alert',
                    'borlabs-cookie',
                ),
                'packageNotFound' => _x(
                    'Package not found.',
                    'Backend / Library / Alert Message',
                    'borlabs-cookie',
                ),
                'requiredPluginVersion' => _x(
                    'This package requires <translation-key id="Borlabs-Cookie">Borlabs Cookie</translation-key> version <strong><em>{{ version }}</em></strong> or newer. You are currently using version <strong><em>{{ currentVersion }}</em></strong>. To install, update, or re-install the package, please update <translation-key id="Borlabs-Cookie">Borlabs Cookie</translation-key> to the latest version to ensure compatibility with the library.',
                    'Backend / Library / Alert Message',
                    'borlabs-cookie',
                ),
                'successorPackageAvailable' => _x(
                    'Please use the successor package <a class="brlbs-cmpnt-link brlbs-cmpnt-link-with-icon" href="{{ link }}" target="_blank"><strong><em>{{ name }}</em></strong><span class="brlbs-cmpnt-external-link-icon"></span></a>.',
                    'Backend / Library / Alert Message',
                    'borlabs-cookie',
                ),
                'uninstallFailed' => _x(
                    'Uninstalling the <strong>{{ type }}</strong> <strong><em>{{ name }}</em></strong> failed.',
                    'Backend / Cloud Scan / Alert',
                    'borlabs-cookie',
                ),
                'uninstallFailedWithMessage' => _x(
                    'Uninstalling the <strong>{{ type }}</strong> <strong><em>{{ name }}</em></strong> failed: {{ message }}',
                    'Backend / Cloud Scan / Alert',
                    'borlabs-cookie',
                ),
                'uninstallSuccess' => _x(
                    'The package <strong><em>{{ name }}</em></strong> was successfully uninstalled.',
                    'Backend / Cloud Scan / Alert',
                    'borlabs-cookie',
                ),
            ],

            // Breadcrumbs
            'breadcrumb' => [
                'install' => _x(
                    'Package Installation',
                    'Backend / Library / Breadcrumb',
                    'borlabs-cookie',
                ),
                'installingMultiplePackages' => _x(
                    'Installing Multiple Packages',
                    'Backend / Library / Breadcrumb',
                    'borlabs-cookie',
                ),
                'module' => _x(
                    'Library',
                    'Backend / Library / Breadcrumb',
                    'borlabs-cookie',
                ),
                'reinstall' => _x(
                    'Package Reinstallation',
                    'Backend / Library / Breadcrumb',
                    'borlabs-cookie',
                ),
                'update' => _x(
                    'Package Update',
                    'Backend / Library / Breadcrumb',
                    'borlabs-cookie',
                ),
            ],

            // Buttons
            'button' => [
                'details' => _x(
                    'Details',
                    'Backend / Library / Button Title',
                    'borlabs-cookie',
                ),
                'goBackToLibrary' => _x(
                    'Go back to the library',
                    'Backend / Library / Button Title',
                    'borlabs-cookie',
                ),
                'goBackToScanResult' => _x(
                    'Go back to scan result',
                    'Backend / Library / Button Title',
                    'borlabs-cookie',
                ),
                'install' => _x(
                    '<translation-key id="Button-Install">Install</translation-key>',
                    'Backend / Library / Button Title',
                    'borlabs-cookie',
                ),
                'refreshLibrary' => _x(
                    'Refresh Library',
                    'Backend / Library / Button Title',
                    'borlabs-cookie',
                ),
                'reinstall' => _x(
                    '<translation-key id="Button-Reinstall">Reinstall</translation-key>',
                    'Backend / Library / Button Title',
                    'borlabs-cookie',
                ),
                'uninstall' => _x(
                    '<translation-key id="Button-Uninstall">Uninstall</translation-key>',
                    'Backend / Library / Button Title',
                    'borlabs-cookie',
                ),
                'update' => _x(
                    '<translation-key id="Button-Update">Update</translation-key>',
                    'Backend / Library / Button Title',
                    'borlabs-cookie',
                ),
            ],

            // Description List
            'descriptionList' => [
                'borlabsServiceUpdatedAt' => _x(
                    'Borlabs Service Modification Date',
                    'Backend / Library / Description List',
                    'borlabs-cookie',
                ),
                'installedAt' => _x(
                    'Installed at',
                    'Backend / Library / Description List',
                    'borlabs-cookie',
                ),
                'installedVersion' => _x(
                    'Installed Version',
                    'Backend / Library / Description List',
                    'borlabs-cookie',
                ),
                'latestVersion' => _x(
                    'Latest Version',
                    'Backend / Library / Description List',
                    'borlabs-cookie',
                ),
                'type' => _x(
                    'Type',
                    'Backend / Library / Description List',
                    'borlabs-cookie',
                ),
                'updatedAt' => _x(
                    'Updated at',
                    'Backend / Library / Description List',
                    'borlabs-cookie',
                ),
                'updateAvailable' => _x(
                    'Update available',
                    'Backend / Library / Description List',
                    'borlabs-cookie',
                ),
                'version' => _x(
                    'Version',
                    'Backend / Library / Description List',
                    'borlabs-cookie',
                ),
            ],

            // Fields
            'field' => [
                'autoUpdateEnabled' => _x(
                    'Enable Auto Update',
                    'Backend / Library / Field',
                    'borlabs-cookie',
                ),
                'autoUpdateOverwriteCode' => _x(
                    'Overwrite Code on Auto Update',
                    'Backend / Library / Field',
                    'borlabs-cookie',
                ),
                'autoUpdateOverwriteTranslation' => _x(
                    'Overwrite Translation on Auto Update',
                    'Backend / Library / Field',
                    'borlabs-cookie',
                ),
                'compatibilityPatches' => _x(
                    '<translation-key id="Compatibility-Patches">Compatibility Patches</translation-key>',
                    'Backend / Library / Field',
                    'borlabs-cookie',
                ),
                'contentBlockers' => _x(
                    '<translation-key id="Content-Blockers">Content Blockers</translation-key>',
                    'Backend / Library / Field',
                    'borlabs-cookie',
                ),
                'install' => _x(
                    'Install',
                    'Backend / Library / Field',
                    'borlabs-cookie',
                ),
                'installPackage' => _x(
                    'To install the package, simply click the <translation-key id="Button-Install">Install</translation-key> button.',
                    'Backend / Library / Field',
                    'borlabs-cookie',
                ),
                'overwriteCode' => _x(
                    '<translation-key id="Overwrite-Code">Overwrite Code</translation-key>',
                    'Backend / Library / Field',
                    'borlabs-cookie',
                ),
                'overwriteTranslation' => _x(
                    '<translation-key id="Overwrite-Translation">Overwrite Translation</translation-key>',
                    'Backend / Library / Field',
                    'borlabs-cookie',
                ),
                'packageListLastUpdate' => _x(
                    'Last Update',
                    'Backend / Library / Field',
                    'borlabs-cookie',
                ),
                'reinstall' => _x(
                    'Reinstall',
                    'Backend / Library / Field',
                    'borlabs-cookie',
                ),
                'reinstallPackage' => _x(
                    'To reinstall the package, simply click the <translation-key id="Button-Reinstall">Reinstall</translation-key> button.',
                    'Backend / Library / Field',
                    'borlabs-cookie',
                ),
                'scriptBlockers' => _x(
                    '<translation-key id="Script-Blockers">Script Blockers</translation-key>',
                    'Backend / Library / Field',
                    'borlabs-cookie',
                ),
                'services' => _x(
                    '<translation-key id="Services">Services</translation-key>',
                    'Backend / Library / Field',
                    'borlabs-cookie',
                ),
                'styleBlockers' => _x(
                    '<translation-key id="Style-Blockers">Style Blockers</translation-key>',
                    'Backend / Library / Field',
                    'borlabs-cookie',
                ),
                'uninstallPackage' => _x(
                    'To uninstall the package, simply click the <translation-key id="Button-Uninstall">Uninstall</translation-key> button.',
                    'Backend / Library / Field',
                    'borlabs-cookie',
                ),
                'updatePackage' => _x(
                    'To update the package, simply click the <translation-key id="Button-Update">Update</translation-key> button.',
                    'Backend / Library / Field',
                    'borlabs-cookie',
                ),
            ],

            // Headlines
            'headline' => [
                'autoUpdateSettings' => _x(
                    '<translation-key id="Auto-Update-Settings">Auto Update Settings</translation-key>',
                    'Backend / Library / Headline',
                    'borlabs-cookie',
                ),
                /** @see SetupAssistantLocalizationStrings::class The identical string is used here. */
                'configureLanguage' => _x(
                    'Configure Language',
                    'Backend / Library / Headline',
                    'borlabs-cookie',
                ),
                'contentBlocker' => _x(
                    '<translation-key id="Content-Blocker">Content Blocker</translation-key>',
                    'Backend / Library / Headline',
                    'borlabs-cookie',
                ),
                'description' => _x(
                    'Description',
                    'Backend / Library / Headline',
                    'borlabs-cookie',
                ),
                'detectedOnPages' => _x(
                    'Detected on pages',
                    'Backend / Library / Headline',
                    'borlabs-cookie',
                ),
                'followUp' => _x(
                    'Follow up',
                    'Backend / Library / Headline',
                    'borlabs-cookie',
                ),
                'installation' => _x(
                    'Installation',
                    'Backend / Library / Headline',
                    'borlabs-cookie',
                ),
                'installationResult' => _x(
                    'Installation Result',
                    'Backend / Library / Headline',
                    'borlabs-cookie',
                ),
                'installingMultiplePackages' => _x(
                    'Installing Multiple Packages',
                    'Backend / Library / Headline',
                    'borlabs-cookie',
                ),
                'packageComponents' => _x(
                    'Package Components',
                    'Backend / Library / Headline',
                    'borlabs-cookie',
                ),
                'packageInformation' => _x(
                    'Package Information',
                    'Backend / Library / Headline',
                    'borlabs-cookie',
                ),
                'preparation' => _x(
                    'Preparation',
                    'Backend / Library / Headline',
                    'borlabs-cookie',
                ),
                'progressAndResult' => _x(
                    'Progress &amp; Result',
                    'Backend / Library / Headline',
                    'borlabs-cookie',
                ),
                'refreshLibrary' => _x(
                    'Refresh Library',
                    'Backend / Library / Headline',
                    'borlabs-cookie',
                ),
                'reinstallation' => _x(
                    'Reinstallation',
                    'Backend / Library / Headline',
                    'borlabs-cookie',
                ),
                'service' => _x(
                    '<translation-key id="Service">Service</translation-key>',
                    'Backend / Library / Headline',
                    'borlabs-cookie',
                ),
                'settings' => _x(
                    'Settings',
                    'Backend / Library / Headline',
                    'borlabs-cookie',
                ),
                'uninstall' => _x(
                    'Uninstall',
                    'Backend / Library / Headline',
                    'borlabs-cookie',
                ),
                'uninstallPackage' => _x(
                    'Uninstall Package',
                    'Backend / Library / Headline',
                    'borlabs-cookie',
                ),
                'update' => _x(
                    'Update',
                    'Backend / Library / Headline',
                    'borlabs-cookie',
                ),
            ],

            // Hint
            'hint' => [
                'autoUpdateEnabled' => _x(
                    'If enabled, the package will be automatically updated to the latest version.',
                    'Backend / Library / Hint',
                    'borlabs-cookie',
                ),
                'autoUpdateOverwriteCode' => _x(
                    'If enabled, the code (<translation-key id="Content-Blocker">Content Blocker</translation-key>: <translation-key id="Preview-Blocked-Content-Image">Image</translation-key>, <translation-key id="Preview-Blocked-Content-HTML">HTML</translation-key>, <translation-key id="Preview-Blocked-Content-CSS">CSS</translation-key>, <translation-key id="Global">Global</translation-key> and <translation-key id="Initialization">Initialization</translation-key>;<br><translation-key id="Service">Service</translation-key>: <translation-key id="Opt-in-Code">Opt-in Code</translation-key>, <translation-key id="Opt-out-Code">Opt-out Code</translation-key> and <translation-key id="Fallback-Code">Fallback Code</translation-key>) will be overwritten with the code provided by the package. To retain your code modifications while only updating the component settings, you may disable this option.',
                    'Backend / Library / Hint',
                    'borlabs-cookie',
                ),
                'autoUpdateOverwriteTranslation' => _x(
                    'If enabled, the text is overwritten with the translation provided by the package when the package is automatically updated. To retain your translation while only updating the component settings, you may disable this option.',
                    'Backend / Library / Hint',
                    'borlabs-cookie',
                ),
                'compatibilityPatches' => _x(
                    'The <translation-key id="Compatibility-Patches">Compatibility Patches</translation-key> are included in this package and are necessary for the proper functioning of this package. They are installed when you click the <translation-key id="Button-Install">Install</translation-key> / <translation-key id="Button-Reinstall">Reinstall</translation-key> / <translation-key id="Button-Update">Update</translation-key> button. You can locate them later using the displayed key in the <translation-key id="Compatibility-Patches">Compatibility Patches</translation-key> view (<translation-key id="Navigation-System">System</translation-key> &raquo; <translation-key id="Navigation-System-Compatibility-Patches">Compatibility Patches</translation-key>).',
                    'Backend / Library / Hint',
                    'borlabs-cookie',
                ),
                'contentBlockers' => _x(
                    'The <translation-key id="Content-Blockers">Content Blockers</translation-key> are included in this package and are necessary for the proper functioning of this package. They are installed when you click the <translation-key id="Button-Install">Install</translation-key> / <translation-key id="Button-Reinstall">Reinstall</translation-key> / <translation-key id="Button-Update">Update</translation-key> button. You can locate them later using the displayed name and key in the <translation-key id="Content-Blockers">Content Blockers</translation-key> view (<translation-key id="Navigation-Blockers">Blockers</translation-key> &raquo; <translation-key id="Navigation-Blockers-Content-Blockers">Content Blockers</translation-key> &raquo; <translation-key id="Navigation-Blockers-Content-Blockers-Manage">Manage</translation-key>).',
                    'Backend / Library / Hint',
                    'borlabs-cookie',
                ),
                'overwriteCodeContentBlocker' => _x(
                    'If enabled, the code (<translation-key id="Preview-Blocked-Content-Image">Image</translation-key>, <translation-key id="Preview-Blocked-Content-HTML">HTML</translation-key>, <translation-key id="Preview-Blocked-Content-CSS">CSS</translation-key>, <translation-key id="Global">Global</translation-key> and <translation-key id="Initialization">Initialization</translation-key>) will be overwritten with the code provided by the package. To retain your code modifications while only updating the component settings, you may disable this option.',
                    'Backend / Library / Hint',
                    'borlabs-cookie',
                ),
                'overwriteCodeService' => _x(
                    'If enabled, the code (<translation-key id="Opt-in-Code">Opt-in Code</translation-key>, <translation-key id="Opt-out-Code">Opt-out Code</translation-key> and <translation-key id="Fallback-Code">Fallback Code</translation-key>) will be overwritten with the code provided by the package. To retain your code modifications while only updating the component settings, you may disable this option.',
                    'Backend / Library / Hint',
                    'borlabs-cookie',
                ),
                'overwriteTranslation' => _x(
                    'If enabled, the text will be overwritten with the translation provided by the package. To retain your translation while only updating the component settings, you may disable this option.',
                    'Backend / Library / Hint',
                    'borlabs-cookie',
                ),
                'scriptBlockers' => _x(
                    'The <translation-key id="Script-Blockers">Script Blockers</translation-key> are included in this package and are necessary for the proper functioning of this package. They are installed when you click the <translation-key id="Button-Install">Install</translation-key> / <translation-key id="Button-Reinstall">Reinstall</translation-key> / <translation-key id="Button-Update">Update</translation-key> button. You can locate them later using the displayed name and key in the <translation-key id="Script-Blockers">Script Blockers</translation-key> view (<translation-key id="Navigation-Blockers">Blockers</translation-key> &raquo; <translation-key id="Navigation-Blockers-Script-Blockers">Script Blockers</translation-key>).',
                    'Backend / Library / Hint',
                    'borlabs-cookie',
                ),
                'services' => _x(
                    'The <translation-key id="Services">Services</translation-key> are included in this package and are necessary for the proper functioning of this package. They are installed when you click the <translation-key id="Button-Install">Install</translation-key> / <translation-key id="Button-Reinstall">Reinstall</translation-key> / <translation-key id="Button-Update">Update</translation-key> button. You can locate them later using the displayed name and key in the <translation-key id="Services">Services</translation-key> view (<translation-key id="Navigation-Consent-Management">Consent Management</translation-key> &raquo; <translation-key id="Navigation-Consent-Management-Services">Services</translation-key>).',
                    'Backend / Library / Hint',
                    'borlabs-cookie',
                ),
                'styleBlockers' => _x(
                    'The <translation-key id="Style-Blockers">Style Blockers</translation-key> are included in this package and are necessary for the proper functioning of this package. They are installed when you click the <translation-key id="Button-Install">Install</translation-key> / <translation-key id="Button-Reinstall">Reinstall</translation-key> / <translation-key id="Button-Update">Update</translation-key> button. You can locate them later using the displayed name and key in the <translation-key id="Style-Blockers">Style Blockers</translation-key> view (<translation-key id="Navigation-Blockers">Blockers</translation-key> &raquo; <translation-key id="Navigation-Blockers-Style-Blockers">Style Blockers</translation-key>).',
                    'Backend / Library / Hint',
                    'borlabs-cookie',
                ),
            ],

            // Navigation
            'navigation' => [
                'all' => _x(
                    'All',
                    'Backend / Library / Navigation',
                    'borlabs-cookie',
                ),
                'filter' => _x(
                    'Filter',
                    'Backend / Library / Navigation',
                    'borlabs-cookie',
                ),
                'compatibilityPatches' => _x(
                    '<translation-key id="Compatibility-Patches">Compatibility Patches</translation-key>',
                    'Backend / Library / Navigation',
                    'borlabs-cookie',
                ),
                'contentBlockers' => _x(
                    '<translation-key id="Content-Blockers">Content Blockers</translation-key>',
                    'Backend / Library / Navigation',
                    'borlabs-cookie',
                ),
                'installedPackages' => _x(
                    'Installed Packages',
                    'Backend / Library / Navigation',
                    'borlabs-cookie',
                ),
                'scriptBlockers' => _x(
                    '<translation-key id="Script-Blockers">Script Blockers</translation-key>',
                    'Backend / Library / Navigation',
                    'borlabs-cookie',
                ),
                'services' => _x(
                    '<translation-key id="Services">Services</translation-key>',
                    'Backend / Library / Navigation',
                    'borlabs-cookie',
                ),
                'styleBlockers' => _x(
                    '<translation-key id="Style-Blockers">Style Blockers</translation-key>',
                    'Backend / Library / Navigation',
                    'borlabs-cookie',
                ),
            ],

            // Placeholder
            'placeholder' => [
                'search' => _x(
                    'Search',
                    'Backend / Library / Input Placeholder',
                    'borlabs-cookie',
                ),
            ],

            // Tables
            'table' => [
                'componentType' => _x(
                    'Type',
                    'Backend / Library / Table Headline',
                    'borlabs-cookie',
                ),
                'name' => _x(
                    'Name',
                    'Backend / Library / Table Headline',
                    'borlabs-cookie',
                ),
                'status' => _x(
                    'Status',
                    'Backend / Library / Table Headline',
                    'borlabs-cookie',
                ),
            ],

            // Text
            'text' => [
                'autoUpdatePlannedFor' => _x(
                    'Auto Update planned for',
                    'Backend / Library / Text',
                    'borlabs-cookie',
                ),
                'confirmUninstallPackage' => _x(
                    'Are you sure you want to uninstall the package?',
                    'Backend / Library / Text',
                    'borlabs-cookie',
                ),
                'package' => _x(
                    'Package',
                    'Backend / Library / Text',
                    'borlabs-cookie',
                ),
                'recommended' => _x(
                    'Recommended',
                    'Backend / Library / Text',
                    'borlabs-cookie',
                ),
            ],

            // Things to know
            'thingsToKnow' => [
                'autoUpdateExplainedA' => _x(
                    'If enabled, this feature will automatically update the package to its most recent version. We strongly recommend enabling this setting to enhance both compatibility and security.',
                    'Backend / Library / Things to know',
                    'borlabs-cookie',
                ),
                'autoUpdateExplainedB' => _x(
                    'Additionally, we advise allowing automatic updates to overwrite existing code and translations to guarantee optimal functionality.',
                    'Backend / Library / Things to know',
                    'borlabs-cookie',
                ),
                'autoUpdateExplainedC' => _x(
                    'You will receive an e-mail notification at least 24 hours prior to the automatic update of the package.',
                    'Backend / Library / Things to know',
                    'borlabs-cookie',
                ),
                'changeContentBlockerSettings' => _x(
                    '<translation-key id="Content-Blocker">Content Blocker</translation-key>: <em><translation-key id="Navigation-Blockers">Blockers</translation-key> &raquo; <translation-key id="Navigation-Blockers-Content-Blockers">Content Blockers</translation-key> &raquo; <translation-key id="Navigation-Blockers-Content-Blockers-Manage">Manage</translation-key></em>.',
                    'Backend / Library / Things to know',
                    'borlabs-cookie',
                ),
                'changeServiceSettings' => _x(
                    '<translation-key id="Service">Service</translation-key>: <em><translation-key id="Navigation-Consent-Management">Consent Management</translation-key> &raquo; <translation-key id="Navigation-Consent-Management-Services">Services</translation-key></em>',
                    'Backend / Library / Things to know',
                    'borlabs-cookie',
                ),
                'changeSettingsA' => _x(
                    'The package detail view is <span class="brlbs-cmpnt-important-text">not</span> the primary place to change settings for <translation-key id="Services">Services</translation-key> or <translation-key id="Content-Blockers">Content Blockers</translation-key>.',
                    'Backend / Library / Things to know',
                    'borlabs-cookie',
                ),
                'changeSettingsB' => _x(
                    'All settings can be found in the respective <translation-key id="Services">Services</translation-key> or <translation-key id="Content-Blockers">Content Blockers</translation-key> under &quot;<translation-key id="Additional-Settings">Additional Settings</translation-key>&quot;, where they are fully managed and saved.',
                    'Backend / Library / Things to know',
                    'borlabs-cookie',
                ),
                'changeSettingsC' => _x(
                    'The package detail view displays only a limited selection of settings, primarily mandatory fields designed for quick setup during the initial installation.',
                    'Backend / Library / Things to know',
                    'borlabs-cookie',
                ),
                'changeSettingsD' => _x(
                    'While these settings are saved, they do not include all available options and are not intended to be modified during an update or reinstallation.',
                    'Backend / Library / Things to know',
                    'borlabs-cookie',
                ),
                'changeSettingsE' => _x(
                    'To change a setting, please go to the appropriate section:',
                    'Backend / Library / Things to know',
                    'borlabs-cookie',
                ),
                'headlineAutoUpdateSettings' => _x(
                    '<translation-key id="Auto-Update-Settings">Auto Update Settings</translation-key>',
                    'Backend / Library / Things to know',
                    'borlabs-cookie',
                ),
                'headlineChangeSettings' => _x(
                    'Where settings are changed',
                    'Backend / Library / Things to know',
                    'borlabs-cookie',
                ),
                'headlineOverwriteSettingsExplained' => _x(
                    'Overwrite settings explained',
                    'Backend / Library / Things to know',
                    'borlabs-cookie',
                ),
                'headlinePackageAutoUpdateSymbol' => _x(
                    'Auto Update Symbol',
                    'Backend / Library / Things to know',
                    'borlabs-cookie',
                ),
                'overwriteSettingsExplainedA' => _x(
                    'The <translation-key id="Overwrite-Code">Overwrite Code</translation-key> and <translation-key id="Overwrite-Translation">Overwrite Translation</translation-key> settings are always enabled and are applied during updates and reinstalls.',
                    'Backend / Library / Things to know',
                    'borlabs-cookie',
                ),
                'overwriteSettingsExplainedB' => _x(
                    'As a result, they are automatically reactivated after a package has been updated or reinstalled.',
                    'Backend / Library / Things to know',
                    'borlabs-cookie',
                ),
                'overwriteSettingsExplainedC' => _x(
                    'For detailed information on these settings, click the <span class="bc-align-middle brlbs-cmpnt-info-icon"></span>-symbol next to them above.',
                    'Backend / Library / Things to know',
                    'borlabs-cookie',
                ),
                'overwriteSettingsExplainedD' => _x(
                    'In the <translation-key id="Auto-Update-Settings">Auto Update Settings</translation-key> section, you can configure the package to prevent code and translation from being overwritten during automatic updates.',
                    'Backend / Library / Things to know',
                    'borlabs-cookie',
                ),
                /** @see LibrarySettingsLocalizationStrings::class The identical string is used here. */
                'packageAutoUpdateSymbol' => _x(
                    'The symbol indicates that automatic updates are enabled for this package.',
                    'Backend / Library / Things to know',
                    'borlabs-cookie',
                ),
            ],
        ];
    }
}
