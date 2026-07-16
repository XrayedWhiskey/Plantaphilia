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

final class PlannedPackageAutoUpdateMailLocalizationStrings implements LocalizationInterface
{
    /**
     * @return array<array<string>>
     */
    public static function get(): array
    {
        return [
            // Tables
            'table' => [
                'installedVersion' => _x(
                    'Installed Version',
                    'Backend / Planned Package Auto Update Mail / Table Headline',
                    'borlabs-cookie',
                ),
                'latestVersion' => _x(
                    'Latest Version',
                    'Backend / Planned Package Auto Update Mail / Table Headline',
                    'borlabs-cookie',
                ),
                'overwriteCode' => _x(
                    'Overwrite Code',
                    'Backend / Planned Package Auto Update Mail / Table Headline',
                    'borlabs-cookie',
                ),
                'overwriteTranslation' => _x(
                    'Overwrite Translation',
                    'Backend / Planned Package Auto Update Mail / Table Headline',
                    'borlabs-cookie',
                ),
                'package' => _x(
                    'Package',
                    'Backend / Planned Package Auto Update Mail / Table Headline',
                    'borlabs-cookie',
                ),
                'plannedFor' => _x(
                    'Planned for',
                    'Backend / Planned Package Auto Update Mail / Table Headline',
                    'borlabs-cookie',
                ),
            ],

            // Text
            'text' => [
                'automaticUpdatePlannedForFollowingPackages' => _x(
                    'An automatic update is planned for the following <strong>Borlabs Cookie</strong> packages:',
                    'Backend / Planned Package Auto Update Mail / Text',
                    'borlabs-cookie',
                ),
                'subject' => _x(
                    'Borlabs Cookie: {{ numberOfAutomaticPackageUpdates }} Automatic Updates Planned, {{ numberOfManualPackageUpdates }} Manual Updates Required',
                    'Backend / Planned Package Auto Update Mail / Text',
                    'borlabs-cookie',
                ),
                'updatablePackagesWithAutoUpdateDisabled' => _x(
                    'Updates are available for the following <translation-key id="Borlabs-Cookie">Borlabs Cookie</translation-key> packages, but must be updated manually as their automatic updating is disabled:',
                    'Backend / Planned Package Auto Update Mail / Text',
                    'borlabs-cookie',
                ),
            ],
        ];
    }
}
