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

namespace Borlabs\Cookie\Localization\GeoIp;

use Borlabs\Cookie\Localization\LocalizationInterface;

final class GeoIpLocalizationStrings implements LocalizationInterface
{
    public static function get(): array
    {
        return [
            // Alert messages
            'alert' => [
                'createTemporaryFolderFailed' => _x(
                    'The temporary folder for the GeoIP database could not be created.',
                    'Backend / GeoIp / Alert Message',
                    'borlabs-cookie',
                ),
                'downloadFailed' => _x(
                    'The GeoIP database could not be downloaded.',
                    'Backend / GeoIp / Alert Message',
                    'borlabs-cookie',
                ),
                'fileMoveFailed' => _x(
                    'The GeoIP database could not be moved.',
                    'Backend / GeoIp / Alert Message',
                    'borlabs-cookie',
                ),
                'unzipFailed' => _x(
                    'The GeoIP database could not be unzipped.',
                    'Backend / GeoIp / Alert Message',
                    'borlabs-cookie',
                ),
            ],
        ];
    }
}
