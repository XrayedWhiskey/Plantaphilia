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

namespace Borlabs\Cookie\Localization\IabTcf;

use Borlabs\Cookie\Localization\LocalizationInterface;

use function Borlabs\Cookie\System\WordPressGlobalFunctions\_x;

final class IabTcfVendorDetailsLocalizationStrings implements LocalizationInterface
{
    /**
     * @return array<array<string>>
     */
    public static function get(): array
    {
        return [
            // Description List
            'descriptionList' => [
                'cookieMaxAgeSeconds' => _x(
                    'Cookie Max Age (Seconds)',
                    'Backend / IAB TCF Vendor Details / Description List',
                    'borlabs-cookie',
                ),
                'dataCategories' => _x(
                    'Data Categories',
                    'Backend / IAB TCF Vendor Details / Description List',
                    'borlabs-cookie',
                ),
                'deviceStorageDisclosureUrl' => _x(
                    'Device Storage Disclosure URL',
                    'Backend / IAB TCF Vendor Details / Description List',
                    'borlabs-cookie',
                ),
                'features' => _x(
                    '<translation-key id="IAB-TCF-Features">Features</translation-key>',
                    'Backend / IAB TCF Vendor Details / Description List',
                    'borlabs-cookie',
                ),
                'legIntPurposes' => _x(
                    'Legitimate Interests',
                    'Backend / IAB TCF Vendor Details / Description List',
                    'borlabs-cookie',
                ),
                'legitimateInterestClaimUrl' => _x(
                    'Legitimate Interest Claim URL',
                    'Backend / IAB TCF Vendor Details / Description List',
                    'borlabs-cookie',
                ),
                'policyUrl' => _x(
                    'Policy URL',
                    'Backend / IAB TCF Vendor Details / Description List',
                    'borlabs-cookie',
                ),
                'purposes' => _x(
                    '<translation-key id="IAB-TCF-Purposes">Purposes</translation-key>',
                    'Backend / IAB TCF Vendor Details / Description List',
                    'borlabs-cookie',
                ),
                'specialFeatures' => _x(
                    '<translation-key id="IAB-TCF-Special-Features">Special Features</translation-key>',
                    'Backend / IAB TCF Vendor Details / Description List',
                    'borlabs-cookie',
                ),
                'specialPurposes' => _x(
                    '<translation-key id="IAB-TCF-Special-Purposes">Special Purposes</translation-key>',
                    'Backend / IAB TCF Vendor Details / Description List',
                    'borlabs-cookie',
                ),
                'standardDataRetention' => _x(
                    'Standard Data Retention',
                    'Backend / IAB TCF Vendor Details / Description List',
                    'borlabs-cookie',
                ),
                'usesCookies' => _x(
                    'Uses Cookies',
                    'Backend / IAB TCF Vendor Details / Description List',
                    'borlabs-cookie',
                ),
                'usesNonCookieAccess' => _x(
                    'Uses Non-Cookie Access',
                    'Backend / IAB TCF Vendor Details / Description List',
                    'borlabs-cookie',
                ),
            ],

            // Text
            'text' => [
                'dataRetention' => _x(
                    'Data Retention',
                    'Backend / IAB TCF Vendor Details / Text',
                    'borlabs-cookie',
                ),
                'days' => _x(
                    'Days',
                    'Backend / IAB TCF Vendor Details / Text',
                    'borlabs-cookie',
                ),
                'seconds' => _x(
                    'Seconds',
                    'Backend / IAB TCF Vendor Details / Text',
                    'borlabs-cookie',
                ),
            ],
        ];
    }
}
