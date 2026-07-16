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

namespace Borlabs\Cookie\Localization\System;

use Borlabs\Cookie\Localization\LocalizationInterface;

use function Borlabs\Cookie\System\WordPressGlobalFunctions\_x;

final class GeoIpLocalizationStrings implements LocalizationInterface
{
    /**
     * @return array<array<string>>
     */
    public static function get(): array
    {
        return [
            // Alert messages
            'alert' => [
                'apiFailed' => _x(
                    'Connecting to the API failed. Please check that WordPress has access to the Internet or retry later.',
                    'Backend / GeoIp / Alert Message',
                    'borlabs-cookie',
                ),
                'movingFailed' => _x(
                    'Moving the database file failed.',
                    'Backend / GeoIp / Alert Message',
                    'borlabs-cookie',
                ),
                'notActivated' => _x(
                    'Please enable GeoIP before downloading the database.',
                    'Backend / GeoIp / Alert Message',
                    'borlabs-cookie',
                ),
                'successfullyCheckAndUpdated' => _x(
                    'The database was successfully updated.',
                    'Backend / GeoIp / Alert Message',
                    'borlabs-cookie',
                ),
                'successfullyCheckedAndCurrent' => _x(
                    'You already have the most up-to-date database.',
                    'Backend / GeoIp / Alert Message',
                    'borlabs-cookie',
                ),
                'unzipFailed' => _x(
                    'Unpacking the database to the temporary folder failed. Please check if the temporary folder is writable and the PHP module ZIP is installed.',
                    'Backend / GeoIp / Alert Message',
                    'borlabs-cookie',
                ),
            ],
        ];
    }
}
