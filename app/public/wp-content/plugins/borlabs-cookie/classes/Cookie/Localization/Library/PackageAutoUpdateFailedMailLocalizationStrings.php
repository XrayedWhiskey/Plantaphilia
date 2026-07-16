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

final class PackageAutoUpdateFailedMailLocalizationStrings implements LocalizationInterface
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
                    'Backend / Package Auto Update Failed Mail / Table Headline',
                    'borlabs-cookie',
                ),
                'latestVersion' => _x(
                    'Latest Version',
                    'Backend / Package Auto Update Failed Mail / Table Headline',
                    'borlabs-cookie',
                ),
                'package' => _x(
                    'Package',
                    'Backend / Package Auto Update Failed Mail / Table Headline',
                    'borlabs-cookie',
                ),
            ],

            // Text
            'text' => [
                'automaticPackageUpdateFailedA' => _x(
                    'The automatic update of the package &quot;<strong>{{ packageName }}</strong>&quot; failed.',
                    'Backend / Package Auto Update Failed Mail / Text',
                    'borlabs-cookie',
                ),
                'automaticPackageUpdateFailedB' => _x(
                    'Log in to your WordPress account, navigate to <translation-key id="Borlabs-Cookie">Borlabs Cookie</translation-key> &raquo; <translation-key id="Navigation-System">System</translation-key> &raquo; <translation-key id="Navigation-System-Logs">Logs</translation-key>, and check the log with the <translation-key id="Process-ID">Process ID</translation-key> "<strong>{{ processId }}</strong>" for more information.',
                    'Backend / Package Auto Update Failed Mail / Text',
                    'borlabs-cookie',
                ),
                'subject' => _x(
                    'Borlabs Cookie: Automatic Update of {{ packageName }} failed',
                    'Backend / Package Auto Update Failed Mail / Text',
                    'borlabs-cookie',
                ),
            ],
        ];
    }
}
