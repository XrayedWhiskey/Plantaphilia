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

namespace Borlabs\Cookie\Localization\ThirdPartyImporter;

use Borlabs\Cookie\Localization\LocalizationInterface;

use function Borlabs\Cookie\System\WordPressGlobalFunctions\_x;

final class ThirdPartyImporterLocalizationStrings implements LocalizationInterface
{
    /**
     * @return array<array<string>>
     */
    public static function get(): array
    {
        return [
            // Headlines
            'headline' => [
                'customComponentsImported' => _x(
                    'Custom components imported',
                    'Backend / Third Party Importer / Headline',
                    'borlabs-cookie',
                ),
                'customContentBlockers' => _x(
                    'Custom <translation-key id="Content-Blockers">Content Blockers</translation-key>',
                    'Backend / Third Party Importer / Headline',
                    'borlabs-cookie',
                ),
                'customScriptBlockers' => _x(
                    'Custom <translation-key id="Script-Blockers">Script Blockers</translation-key>',
                    'Backend / Third Party Importer / Headline',
                    'borlabs-cookie',
                ),
                'customServices' => _x(
                    'Custom <translation-key id="Services">Services</translation-key>',
                    'Backend / Third Party Importer / Headline',
                    'borlabs-cookie',
                ),
                'importReport' => _x(
                    'Import report from Borlabs Cookie Legacy',
                    'Backend / Third Party Importer / Headline',
                    'borlabs-cookie',
                ),
                'presetComponentsImported' => _x(
                    'Preset components imported',
                    'Backend / Third Party Importer / Headline',
                    'borlabs-cookie',
                ),
                'presetContentBlockers' => _x(
                    'Preset <translation-key id="Content-Blockers">Content Blockers</translation-key>',
                    'Backend / Third Party Importer / Headline',
                    'borlabs-cookie',
                ),
                'presetServices' => _x(
                    'Preset <translation-key id="Services">Services</translation-key>',
                    'Backend / Third Party Importer / Headline',
                    'borlabs-cookie',
                ),
            ],

            // Tables
            'table' => [
                'componentName' => _x(
                    'Name',
                    'Backend / Third Party Importer / Table Headline',
                    'borlabs-cookie',
                ),
                'installationStatus' => _x(
                    'Installation Status',
                    'Backend / Third Party Importer / Table Headline',
                    'borlabs-cookie',
                ),
            ],

            // Text
            'text' => [
                'customComponentsImportedA' => _x(
                    'The following custom components were imported successfully.',
                    'Backend / Third Party Importer / Text',
                    'borlabs-cookie',
                ),
                'customComponentsImportedB' => _x(
                    'However, since Borlabs Cookie 3.0 requires additional information that was not included in Borlabs Cookie 2.0, you will need to manually review and complete the details of the imported components, such as the <translation-key id="Provider">Provider</translation-key> information.',
                    'Backend / Third Party Importer / Text',
                    'borlabs-cookie',
                ),
                'importFollowUpA' => _x(
                    'After a successful import, you must manually verify the information and functionality of all custom <translation-key id="Content-Blockers">Content Blockers</translation-key>, <translation-key id="Providers">Providers</translation-key>, and <translation-key id="Services">Services</translation-key>.',
                    'Backend / Planned Package Auto Update Mail / Text',
                    'borlabs-cookie',
                ),
                'importStatus' => _x(
                    'The import of Borlabs Cookie Legacy was <strong>{{ importStatus }}</strong>.',
                    'Backend / Planned Package Auto Update Mail / Text',
                    'borlabs-cookie',
                ),
                'importStatusUnsuccessful' => _x(
                    'unsuccessful',
                    'Backend / Planned Package Auto Update Mail / Text',
                    'borlabs-cookie',
                ),
                'importStatusSuccessful' => _x(
                    'successful',
                    'Backend / Planned Package Auto Update Mail / Text',
                    'borlabs-cookie',
                ),
                'presetComponentsImportedA' => _x(
                    'The preset components were imported successfully.',
                    'Backend / Third Party Importer / Text',
                    'borlabs-cookie',
                ),
                'presetComponentsImportedB' => _x(
                    'Since the IDs of some components differ between Borlabs Cookie 2.0 and 3.0, you need to review and update the IDs in any custom JavaScript code you may have used.',
                    'Backend / Third Party Importer / Text',
                    'borlabs-cookie',
                ),
                'subject' => _x(
                    'Borlabs Cookie: Import report from Borlabs Cookie Legacy',
                    'Backend / Third Party Importer / Text',
                    'borlabs-cookie',
                ),
            ],
        ];
    }
}
