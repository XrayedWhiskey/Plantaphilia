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

namespace Borlabs\Cookie\Localization\ImportExport;

use Borlabs\Cookie\Localization\LocalizationInterface;

use function Borlabs\Cookie\System\WordPressGlobalFunctions\_x;

final class ImportExportLocalizationStrings implements LocalizationInterface
{
    /**
     * @return array<array<string>>
     */
    public static function get(): array
    {
        return [
            // Alert messages
            'alert' => [
                'encodingError' => _x(
                    'The imported data is not valid base64 encoded.',
                    'Backend / Import & Export / Alert Message',
                    'borlabs-cookie',
                ),
                'importedSuccessfully' => _x(
                    'The settings have been imported successfully.',
                    'Backend / Import & Export / Alert Message',
                    'borlabs-cookie',
                ),
                'importedUnsuccessfully' => _x(
                    'The settings have not been imported successfully.',
                    'Backend / Import & Export / Alert Message',
                    'borlabs-cookie',
                ),
                'jsonError' => _x(
                    'The imported data is not valid JSON.',
                    'Backend / Import & Export / Alert Message',
                    'borlabs-cookie',
                ),
            ],

            // Breadcrumbs
            'breadcrumb' => [
                'module' => _x(
                    'Import &amp; Export',
                    'Backend / Import & Export / Breadcrumb',
                    'borlabs-cookie',
                ),
            ],

            // Buttons
            'button' => [
                'importSettings' => _x(
                    '<translation-key id="Import-Settings">Import Settings</translation-key>',
                    'Backend / Import & Export / Button',
                    'borlabs-cookie',
                ),
            ],

            // Description List
            'descriptionList' => [
                'ImportingFollowingSettings' => _x(
                    '<translation-key id="Importing-the-following-settings">Importing the following settings</translation-key>',
                    'Backend  / Import & Export /  Description List',
                    'borlabs-cookie',
                ),
            ],

            // Fields
            'field' => [
                'contentBlockerStyleConfig' => _x(
                    'Content Blocker - Appearance',
                    'Backend / Import & Export / Field',
                    'borlabs-cookie',
                ),
                'dialogLocalization' => _x(
                    'Dialog - Localization',
                    'Backend / Import & Export / Field',
                    'borlabs-cookie',
                ),
                'dialogSettingsConfig' => _x(
                    '<translation-key id="Dialog-Settings">Dialog - Settings</translation-key>',
                    'Backend / Import & Export / Field',
                    'borlabs-cookie',
                ),
                'dialogStyleConfig' => _x(
                    '<translation-key id="Dialog-Appearance">Dialog - Appearance</translation-key>',
                    'Backend / Import & Export / Field',
                    'borlabs-cookie',
                ),
                'exportData' => _x(
                    'Export Data',
                    'Backend / Import & Export / Field',
                    'borlabs-cookie',
                ),
                'importData' => _x(
                    'Import Data',
                    'Backend / Import & Export / Field',
                    'borlabs-cookie',
                ),
                'widgetConfig' => _x(
                    'Widget',
                    'Backend / Import & Export / Field',
                    'borlabs-cookie',
                ),
            ],

            // Headlines
            'headline' => [
                'exportSettings' => _x(
                    'Export Settings',
                    'Backend / Import & Export / Headline',
                    'borlabs-cookie',
                ),
                'importSettings' => _x(
                    'Import Settings',
                    'Backend / Import & Export / Headline',
                    'borlabs-cookie',
                ),
            ],

            // Hint
            'hint' => [
                'dialogLocalization' => _x(
                    'The settings for which <translation-key id="Legal-Information">Legal Information</translation-key> texts should be displayed are part of the <translation-key id="Dialog-Settings">Dialog - Settings</translation-key> data.',
                    'Backend / Import & Export / Hint',
                    'borlabs-cookie',
                ),
            ],

            // Things to know
            'thingsToKnow' => [
                'howToExportSettings' => _x(
                    'Choose the settings you wish to export, then either click the clipboard icon or highlight the text and copy it to your clipboard.',
                    'Backend / Import & Export / Things to know',
                    'borlabs-cookie',
                ),
                'howToImportSettingsA' => _x(
                    'Paste the desired settings into the text field, then click the <translation-key id="Import-Settings">Import Settings</translation-key> button.',
                    'Backend / Import & Export / Things to know',
                    'borlabs-cookie',
                ),
                'howToImportSettingsB' => _x(
                    'You can review which settings will be imported in the list below, labeled as <translation-key id="Importing-the-following-settings">Importing the following settings</translation-key>.',
                    'Backend / Import & Export / Things to know',
                    'borlabs-cookie',
                ),
                'headlineHowToExportSettings' => _x(
                    'How to export settings',
                    'Backend / Import & Export / Things to know',
                    'borlabs-cookie',
                ),
                'headlineHowToImportSettings' => _x(
                    'How to import settings',
                    'Backend / Import & Export / Things to know',
                    'borlabs-cookie',
                ),
                'noBorlabsCookieLegacySettings' => _x(
                    'Importing settings from <translation-key id="Borlabs-Cookie">Borlabs Cookie</translation-key> 2.x is currently not feasible and will remain impossible in the future.',
                    'Backend / Import & Export / Things to know',
                    'borlabs-cookie',
                ),
                'noComponentExportA' => _x(
                    'Exporting settings such as <translation-key id="Services">Services</translation-key>, <translation-key id="Content-Blockers">Content Blockers</translation-key>, <translation-key id="Providers">Providers</translation-key>, etc. is not feasible and will remain impossible in the future.',
                    'Backend / Import & Export / Things to know',
                    'borlabs-cookie',
                ),
                'noComponentExportB' => _x(
                    'To ensure you have the most up-to-date data, it is advisable to consistently rely on the library.',
                    'Backend / Import & Export / Things to know',
                    'borlabs-cookie',
                ),
            ],
        ];
    }
}
