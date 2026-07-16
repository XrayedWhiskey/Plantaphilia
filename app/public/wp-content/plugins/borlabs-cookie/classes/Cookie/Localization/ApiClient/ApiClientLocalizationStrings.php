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

namespace Borlabs\Cookie\Localization\ApiClient;

use Borlabs\Cookie\Localization\LocalizationInterface;

use function Borlabs\Cookie\System\WordPressGlobalFunctions\_x;

class ApiClientLocalizationStrings implements LocalizationInterface
{
    /**
     * @return array<array<string>>
     */
    public static function get(): array
    {
        return [
            // Alert
            'alert' => [
                'badRequest' => _x(
                    'The request could not be processed. Please try again later.',
                    'Backend / API Client / Alert',
                    'borlabs-cookie',
                ),
                'invalidInstalledProduct' => _x(
                    'The installed product is invalid. Please try again later.',
                    'Backend / API Client / Alert',
                    'borlabs-cookie',
                ),
                'invalidLicenseKey' => _x(
                    'The license key is invalid. Please check the license key.',
                    'Backend / API Client / Alert',
                    'borlabs-cookie',
                ),
                'invalidSiteData' => _x(
                    'Your site data is not valid. To remove the license key, please use our customer portal. <a class="brlbs-cmpnt-link brlbs-cmpnt-link-with-icon" href="https://service.borlabs.io/" rel="nofollow noreferrer" target="_blank"><span>service.borlabs.io</span><span class="brlbs-cmpnt-external-link-icon"></span></a>.',
                    'Backend / API Client / Alert',
                    'borlabs-cookie',
                ),
                'licenseBlockedDomain' => _x(
                    'The license key has been blocked for this domain. Check your license key in our customer portal <a class="brlbs-cmpnt-link brlbs-cmpnt-link-with-icon" href="https://service.borlabs.io/" rel="nofollow noreferrer" target="_blank"><span>service.borlabs.io</span><span class="brlbs-cmpnt-external-link-icon"></span></a>.',
                    'Backend / API Client / Alert',
                    'borlabs-cookie',
                ),
                'licenseMissing' => _x(
                    'The license key is missing. Please enter a license key.',
                    'Backend / API Client / Alert',
                    'borlabs-cookie',
                ),
                'licenseQuotaExceeded' => _x(
                    'It is not possible to register another website with this license key. Remove the license key from another website to register it here. Check your license key in our customer portal <a class="brlbs-cmpnt-link brlbs-cmpnt-link-with-icon" href="https://service.borlabs.io/" rel="nofollow noreferrer" target="_blank"><span>service.borlabs.io</span><span class="brlbs-cmpnt-external-link-icon"></span></a>.',
                    'Backend / API Client / Alert',
                    'borlabs-cookie',
                ),
                'notFound' => _x(
                    'The requested resource could not be found. Please try again later.',
                    'Backend / API Client / Alert',
                    'borlabs-cookie',
                ),
                'pluginOutdated' => _x(
                    'The operation cannot be completed because the installed version of Borlabs Cookie is outdated. Please update to the latest version to maintain compatibility with our API.',
                    'Backend / API Client / Alert',
                    'borlabs-cookie',
                ),
                'scanQuotaExceeded' => _x(
                    'The scan quota has been exceeded. Please try again later.',
                    'Backend / API Client / Alert',
                    'borlabs-cookie',
                ),
                'serviceUnavailable' => _x(
                    'The service is currently unavailable. Please try again later.',
                    'Backend / API Client / Alert',
                    'borlabs-cookie',
                ),
                'tooManyScanPages' => _x(
                    'The maximum number of pages to scan has been exceeded. Please try again later.',
                    'Backend / API Client / Alert',
                    'borlabs-cookie',
                ),
                'unauthorized' => _x(
                    'The request could not be authorized. Please try again later.',
                    'Backend / API Client / Alert',
                    'borlabs-cookie',
                ),
                'validation' => _x(
                    'The request could not be validated. Please try again later.',
                    'Backend / API Client / Alert',
                    'borlabs-cookie',
                ),
            ],
        ];
    }
}
