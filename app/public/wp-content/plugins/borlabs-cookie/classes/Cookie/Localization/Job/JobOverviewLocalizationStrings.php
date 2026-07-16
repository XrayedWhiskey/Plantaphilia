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

class JobOverviewLocalizationStrings implements LocalizationInterface
{
    /**
     * @return array<array<string>>
     */
    public static function get(): array
    {
        return [
            // Alert messages
            'alert' => [
                'noJobs' => _x(
                    'No <translation-key id="Jobs">Jobs</translation-key> found.',
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
            ],

            // Headlines
            'headline' => [
                'jobs' => _x(
                    '<translation-key id="Jobs">Jobs</translation-key>',
                    'Backend / Job / Headline',
                    'borlabs-cookie',
                ),
            ],

            // Placeholder
            'placeholder' => [
                'search' => _x(
                    'Search Type or Payload',
                    'Backend / Job / Placeholder',
                    'borlabs-cookie',
                ),
            ],

            // Tables
            'table' => [
                'createdAt' => _x(
                    'Created at',
                    'Backend / Job / Table Headline',
                    'borlabs-cookie',
                ),
                'executedAt' => _x(
                    'Executed at',
                    'Backend / Job / Table Headline',
                    'borlabs-cookie',
                ),
                'id' => _x(
                    '<translation-key id="ID">ID</translation-key>',
                    'Backend / Job / Table Headline',
                    'borlabs-cookie',
                ),
                'payload' => _x(
                    'Payload',
                    'Backend / Job / Table Headline',
                    'borlabs-cookie',
                ),
                'plannedFor' => _x(
                    'Planned for',
                    'Backend / Job / Table Headline',
                    'borlabs-cookie',
                ),
                'type' => _x(
                    'Type',
                    'Backend / Job / Table Headline',
                    'borlabs-cookie',
                ),
            ],
        ];
    }
}
