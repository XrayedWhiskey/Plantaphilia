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

final class CloudScanApiLocalizationStrings implements LocalizationInterface
{
    /**
     * @return array<array<string>>
     */
    public static function get(): array
    {
        return [
            // Alert
            'alert' => [
                'apiError' => _x(
                    'An error occurred while communicating with the Borlabs Cloud API. Please try again later.',
                    'Backend / Cloud Scan / Alert',
                    'borlabs-cookie',
                ),
                'connectivity' => _x(
                    'Connectivity problem',
                    'Backend / Cloud Scan / Alert',
                    'borlabs-cookie',
                ),
                'nameNotResolved' => _x(
                    'Name could not be resolved',
                    'Backend / Cloud Scan / Alert',
                    'borlabs-cookie',
                ),
                'noAnswerFromScanner' => _x(
                    'No answer from scanner',
                    'Backend / Cloud Scan / Alert',
                    'borlabs-cookie',
                ),
                'noScanPages' => _x(
                    'No valid pages to scan where sent to the Borlabs Cloud API.',
                    'Backend / Cloud Scan / Alert',
                    'borlabs-cookie',
                ),
                'other' => _x(
                    'Other problem',
                    'Backend / Cloud Scan / Alert',
                    'borlabs-cookie',
                ),
                'sslError' => _x(
                    'SSL error',
                    'Backend / Cloud Scan / Alert',
                    'borlabs-cookie',
                ),
                'timeout' => _x(
                    'Timeout',
                    'Backend / Cloud Scan / Alert',
                    'borlabs-cookie',
                ),
                'tooManyRedirects' => _x(
                    'Too many redirects',
                    'Backend / Cloud Scan / Alert',
                    'borlabs-cookie',
                ),
                'tooManyScanPages' => _x(
                    'The scan includes too many pages.',
                    'Backend / Cloud Scan / Alert',
                    'borlabs-cookie',
                ),
                'validation' => _x(
                    'The Borlabs Cloud API returned an error. Please check if Borlabs Cookie is up-to-date.',
                    'Backend / Cloud Scan / Alert',
                    'borlabs-cookie',
                ),
            ],
        ];
    }
}
