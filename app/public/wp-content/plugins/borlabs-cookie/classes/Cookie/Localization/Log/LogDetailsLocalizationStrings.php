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

class LogDetailsLocalizationStrings implements LocalizationInterface
{
    /**
     * @return array<array<string>>
     */
    public static function get(): array
    {
        return [
            // Alert messages
            'alert' => [
                'notFound' => _x(
                    'The <translation-key id="Log">Log</translation-key> could not be found.',
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
                'details' => _x(
                    'Details',
                    'Backend / Log / Breadcrumb',
                    'borlabs-cookie',
                ),
            ],

            // Headlines
            'headline' => [
                'processHistory' => _x(
                    'Process History',
                    'Backend / Log / Headline',
                    'borlabs-cookie',
                ),
            ],

            // Fields
            'field' => [
                'backtrace' => _x(
                    'Backtrace',
                    'Backend / Log / Field',
                    'borlabs-cookie',
                ),
                'context' => _x(
                    'Context',
                    'Backend / Log / Field',
                    'borlabs-cookie',
                ),
                'level' => _x(
                    'Level',
                    'Backend / Log / Field',
                    'borlabs-cookie',
                ),
                'message' => _x(
                    'Message',
                    'Backend / Log / Field',
                    'borlabs-cookie',
                ),
                'processId' => _x(
                    '<translation-key id="Process-ID">Process ID</translation-key>',
                    'Backend / Log / Field',
                    'borlabs-cookie',
                ),
            ],
        ];
    }
}
