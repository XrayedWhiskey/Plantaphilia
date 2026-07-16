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

namespace Borlabs\Cookie\Localization\Job;

use Borlabs\Cookie\Localization\LocalizationInterface;

use function Borlabs\Cookie\System\WordPressGlobalFunctions\_x;

class JobDetailsLocalizationStrings implements LocalizationInterface
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
                    'The <translation-key id="Job">Job</translation-key> could not be found.',
                    'Backend / Job / Alert Message',
                    'borlabs-cookie',
                ),
            ],

            // Breadcrumbs
            'breadcrumb' => [
                'module' => _x(
                    '<translation-key id="Jobs">Jobs</translation-key>',
                    'Backend / Job / Breadcrumb',
                    'borlabs-cookie',
                ),
                'details' => _x(
                    'Details',
                    'Backend / Job / Breadcrumb',
                    'borlabs-cookie',
                ),
            ],

            // Fields
            'field' => [
                'createdAt' => _x(
                    'Created at',
                    'Backend / Job / Field',
                    'borlabs-cookie',
                ),
                'executedAt' => _x(
                    'Executed at',
                    'Backend / Job / Field',
                    'borlabs-cookie',
                ),
                'id' => _x(
                    '<translation-key id="ID">ID</translation-key>',
                    'Backend / Job / Field',
                    'borlabs-cookie',
                ),
                'payload' => _x(
                    'Payload',
                    'Backend / Job / Field',
                    'borlabs-cookie',
                ),
                'plannedFor' => _x(
                    'Planned for',
                    'Backend / Job / Field',
                    'borlabs-cookie',
                ),
                'type' => _x(
                    'Type',
                    'Backend / Job / Field',
                    'borlabs-cookie',
                ),
            ],
        ];
    }
}
