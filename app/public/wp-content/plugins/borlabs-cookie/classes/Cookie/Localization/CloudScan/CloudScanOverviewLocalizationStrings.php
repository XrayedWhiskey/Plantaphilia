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

namespace Borlabs\Cookie\Localization\CloudScan;

use Borlabs\Cookie\Localization\LocalizationInterface;

use function Borlabs\Cookie\System\WordPressGlobalFunctions\_x;

final class CloudScanOverviewLocalizationStrings implements LocalizationInterface
{
    /**
     * @return array<array<string>>
     */
    public static function get(): array
    {
        return [
            // Alert messages
            'alert' => [
                'noCloudScanConfig' => _x(
                    'No <translation-key id="Scans">Scans</translation-key> configured.',
                    'Backend / Cloud Scan / Alert',
                    'borlabs-cookie',
                ),
            ],

            // Breadcrumbs
            'breadcrumb' => [
                'module' => _x(
                    'Scanner',
                    'Backend / Cloud Scan / Breadcrumb',
                    'borlabs-cookie',
                ),
            ],

            // Headlines
            'headline' => [
                'scans' => _x(
                    'Scanner',
                    'Backend / Cloud Scan / Headline',
                    'borlabs-cookie',
                ),
            ],

            // Tables
            'table' => [
                'cloudScanId' => _x(
                    'Cloud Scan ID',
                    'Backend / Cloud Scan / Table',
                    'borlabs-cookie',
                ),
                'createdAt' => _x(
                    'Created at',
                    'Backend / Cloud Scan / Table',
                    'borlabs-cookie',
                ),
                'scannedPages' => _x(
                    'Scanned pages',
                    'Backend / Cloud Scan / Table',
                    'borlabs-cookie',
                ),
                'status' => _x(
                    'Status',
                    'Backend / Cloud Scan / Table',
                    'borlabs-cookie',
                ),
                'type' => _x(
                    'Type',
                    'Backend / Cloud Scan / Table',
                    'borlabs-cookie',
                ),
            ],

            // Things to know
            'thingsToKnow' => [
                'functionalityOfTheScanner' => _x(
                    'The scanner will help you set up <translation-key id="Borlabs-Cookie">Borlabs Cookie</translation-key> that will help you make your website privacy compliant. Two scan options are available for this purpose.',
                    'Backend / Cloud Scan / Things to know / Text',
                    'borlabs-cookie',
                ),
                'headlineFunctionalityOfTheScanner' => _x(
                    'What is the functionality of the scanner?',
                    'Backend / Cloud Scan / Things to know / Headline',
                    'borlabs-cookie',
                ),
            ],
        ];
    }
}
