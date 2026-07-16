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

namespace Borlabs\Cookie\Localization\Log;

use Borlabs\Cookie\Localization\LocalizationInterface;

use function Borlabs\Cookie\System\WordPressGlobalFunctions\_x;

class LogOverviewLocalizationStrings implements LocalizationInterface
{
    /**
     * @return array<array<string>>
     */
    public static function get(): array
    {
        return [
            // Alert messages
            'alert' => [
                'noLogs' => _x(
                    'No <translation-key id="Logs">Logs</translation-key> found.',
                    'Backend / Log / Alert Message',
                    'borlabs-cookie',
                ),
            ],

            // Breadcrumbs
            'breadcrumb' => [
                'module' => _x(
                    '<translation-key id="Logs">Logs</translation-key>',
                    'Backend / Log / Breadcrumb',
                    'borlabs-cookie',
                ),
            ],

            // Headlines
            'headline' => [
                'logs' => _x(
                    '<translation-key id="Logs">Logs</translation-key>',
                    'Backend / Log / Headline',
                    'borlabs-cookie',
                ),
            ],

            // Placeholder
            'placeholder' => [
                'search' => _x(
                    'Search Process ID, Level or Message',
                    'Backend / Log / Placeholder',
                    'borlabs-cookie',
                ),
            ],

            // Tables
            'table' => [
                'backtrace' => _x(
                    'Has backtrace',
                    'Backend / Log / Table Headline',
                    'borlabs-cookie',
                ),
                'context' => _x(
                    'Has context',
                    'Backend / Log / Table Headline',
                    'borlabs-cookie',
                ),
                'createdAt' => _x(
                    'Created at',
                    'Backend / Log / Table Headline',
                    'borlabs-cookie',
                ),
                'level' => _x(
                    'Level',
                    'Backend / Log / Table Headline',
                    'borlabs-cookie',
                ),
                'message' => _x(
                    'Message',
                    'Backend / Log / Table Headline',
                    'borlabs-cookie',
                ),
                'processId' => _x(
                    '<translation-key id="Process-ID">Process ID</translation-key>',
                    'Backend / Log / Table Headline',
                    'borlabs-cookie',
                ),
            ],
        ];
    }
}
