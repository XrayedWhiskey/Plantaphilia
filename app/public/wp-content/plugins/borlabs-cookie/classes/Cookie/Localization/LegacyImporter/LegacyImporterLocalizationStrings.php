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

namespace Borlabs\Cookie\Localization\LegacyImporter;

use Borlabs\Cookie\Localization\LocalizationInterface;

use function Borlabs\Cookie\System\WordPressGlobalFunctions\_x;

final class LegacyImporterLocalizationStrings implements LocalizationInterface
{
    /**
     * @return array<array<string>>
     */
    public static function get(): array
    {
        return [
            // Alert messages
            'alert' => [
                'importedSuccessfully' => _x(
                    'The data have been imported successfully.',
                    'Backend / Legacy Importer / Alert Message',
                    'borlabs-cookie',
                ),
                'importedUnsuccessfully' => _x(
                    'The data have not been imported successfully. Navigate to <translation-key id="Borlabs-Cookie">Borlabs Cookie</translation-key> &raquo; <translation-key id="Navigation-System">System</translation-key> &raquo; <translation-key id="Navigation-System-Logs">Logs</translation-key>, and check the logs for more information.',
                    'Backend / Legacy Importer / Alert Message',
                    'borlabs-cookie',
                ),
                'noImportData' => _x(
                    'No import data available.',
                    'Backend / Legacy Importer / Alert Message',
                    'borlabs-cookie',
                ),
            ],

            // Breadcrumbs
            'breadcrumb' => [
                'module' => _x(
                    'Legacy Importer',
                    'Backend / Legacy Importer / Breadcrumb',
                    'borlabs-cookie',
                ),
            ],

            // Buttons
            'button' => [
                'import' => _x(
                    'Import',
                    'Backend / Legacy Importer / Button',
                    'borlabs-cookie',
                ),
            ],

            // Fields
            'field' => [
                'confirmImport' => _x(
                    'Confirm the Import of Legacy Data',
                    'Backend / Legacy Importer / Field',
                    'borlabs-cookie',
                ),
                'loadBackwardsCompatibilityJavaScript' => _x(
                    'Load library for backward compatibility',
                    'Backend / Legacy Importer / Field',
                    'borlabs-cookie',
                ),
            ],

            // Headlines
            'headline' => [
                'contentBlockers' => _x(
                    'Content Blockers',
                    'Backend / Legacy Importer / Headline',
                    'borlabs-cookie',
                ),
                'importDataPreview' => _x(
                    'Import Data Preview',
                    'Backend / Legacy Importer / Headline',
                    'borlabs-cookie',
                ),
                'importLegacyData' => _x(
                    'Import Legacy Data',
                    'Backend / Legacy Importer / Headline',
                    'borlabs-cookie',
                ),
                'legacySettings' => _x(
                    'Legacy Settings',
                    'Backend / Legacy Importer / Headline',
                    'borlabs-cookie',
                ),
                'scriptBlockers' => _x(
                    'Script Blockers',
                    'Backend / Legacy Importer / Headline',
                    'borlabs-cookie',
                ),
                'serviceGroups' => _x(
                    'Service Groups',
                    'Backend / Legacy Importer / Headline',
                    'borlabs-cookie',
                ),
                'services' => _x(
                    'Services',
                    'Backend / Legacy Importer / Headline',
                    'borlabs-cookie',
                ),
            ],

            // Hint
            'hint' => [
                'importLegacyData' => _x(
                    'Imports the legacy data from Borlabs Cookie 2.x to Borlabs Cookie 3.x. Existing data will be overwritten.',
                    'Backend / Legacy Importer / Hint',
                    'borlabs-cookie',
                ),
                'loadBackwardsCompatibilityJavaScript' => _x(
                    'Loads the JavaScript library to support the Borlabs Cookie Legacy API, ensuring backward compatibility with most JavaScript functions. After enabling this option, visit your website and check the browser console for any related messages.',
                    'Backend / Legacy Importer / Hint',
                    'borlabs-cookie',
                ),
            ],

            // Tables
            'table' => [
                'language' => _x(
                    'Language',
                    'Backend / Legacy Importer / Table Headline',
                    'borlabs-cookie',
                ),
                'legacyKey' => _x(
                    'Legacy ID',
                    'Backend / Legacy Importer / Table Headline',
                    'borlabs-cookie',
                ),
                'name' => _x(
                    'Name',
                    'Backend / Legacy Importer / Table Headline',
                    'borlabs-cookie',
                ),
                'newKey' => _x(
                    'New ID',
                    'Backend / Legacy Importer / Table Headline',
                    'borlabs-cookie',
                ),
            ],

            // Things to know
            'thingsToKnow' => [
            ],
        ];
    }
}
