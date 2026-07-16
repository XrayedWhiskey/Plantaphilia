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

final class IabTcfVendorOverviewLocalizationStrings implements LocalizationInterface
{
    /**
     * @return array<array<string>>
     */
    public static function get(): array
    {
        return [
            // Alert messages
            'alert' => [
                'noVendorConfigured' => _x(
                    'No <translation-key id="IAB-TCF-Vendor">IAB TCF Vendor</translation-key> configured.',
                    'Backend / IAB TCF / Alert Message',
                    'borlabs-cookie',
                ),
            ],

            // Breadcrumbs
            'breadcrumb' => [
                'module' => _x(
                    'IAB TCF',
                    'Backend / IAB TCF / Breadcrumb',
                    'borlabs-cookie',
                ),
            ],

            // Buttons
            'button' => [
                'configureVendors' => _x(
                    '<translation-key id="Configure-Vendors">Configure Vendors</translation-key>',
                    'Backend / IAB TCF / Button',
                    'borlabs-cookie',
                ),
            ],

            // Fields
            'field' => [
                'iabTcfStatus' => _x(
                    '<translation-key id="IAB-TCF-Status">IAB TCF Status</translation-key>',
                    'Backend / IAB TCF / Field',
                    'borlabs-cookie',
                ),
            ],

            // Headlines
            'headline' => [
                'iabTcfVendors' => _x(
                    'IAB TCF Vendors',
                    'Backend / IAB TCF / Headline',
                    'borlabs-cookie',
                ),
            ],

            // Placeholder
            'placeholder' => [
                'search' => _x(
                    'Search Vendor...',
                    'Backend / IAB TCF / Placeholder',
                    'borlabs-cookie',
                ),
            ],

            // Tables
            'table' => [
                'features' => _x(
                    '<translation-key id="IAB-TCF-Features">Features</translation-key>',
                    'Backend / IAB TCF / Table',
                    'borlabs-cookie',
                ),
                'legIntPurposes' => _x(
                    'Legitimate Interests',
                    'Backend / IAB TCF / Table',
                    'borlabs-cookie',
                ),
                'name' => _x(
                    'Name',
                    'Backend / IAB TCF / Table',
                    'borlabs-cookie',
                ),
                'purposes' => _x(
                    '<translation-key id="IAB-TCF-Purposes">Purposes</translation-key>',
                    'Backend / IAB TCF / Table',
                    'borlabs-cookie',
                ),
                'specialFeatures' => _x(
                    '<translation-key id="IAB-TCF-Special-Features">Special Features</translation-key>',
                    'Backend / IAB TCF / Table',
                    'borlabs-cookie',
                ),
                'specialPurposes' => _x(
                    '<translation-key id="IAB-TCF-Special-Purposes">Special Purposes</translation-key>',
                    'Backend / IAB TCF / Table',
                    'borlabs-cookie',
                ),
                'status' => _x(
                    'Status',
                    'Backend / IAB TCF / Table',
                    'borlabs-cookie',
                ),
                'vendorId' => _x(
                    'Vendor Id',
                    'Backend / IAB TCF / Table',
                    'borlabs-cookie',
                ),
            ],

            // Text
            'text' => [
            ],

            // Things to know
            'thingsToKnow' => [
                'languageIndependent' => _x(
                    'Language independent',
                    'Backend / IAB TCF / Tip / Headline',
                    'borlabs-cookie',
                ),
                'languageIndependentExplained' => _x(
                    '<translation-key id="Vendors">Vendors</translation-key> are configured independently for all languages, but the status of whether or not to use the <translation-key id="IAB-TCF">IAB TCF</translation-key> is set per language.',
                    'Backend / IAB TCF / Tip / Text',
                    'borlabs-cookie',
                ),
                'statusOff' => _x(
                    'The <translation-key id="Vendor">Vendor</translation-key> is inactive and no consent for the vendor can be obtained.',
                    'Backend / IAB TCF / Tip / Text',
                    'borlabs-cookie',
                ),
                'statusOn' => _x(
                    'The <translation-key id="Vendor">Vendor</translation-key> is active and the consent for the vendor can be obtained.',
                    'Backend / IAB TCF / Tip / Text',
                    'borlabs-cookie',
                ),
                'symbolsExplained' => _x(
                    'Symbols explained',
                    'Backend / IAB TCF / Tip / Headline',
                    'borlabs-cookie',
                ),
                'vendorDetails' => _x(
                    'Click on the icon to view details about this vendor.',
                    'Backend / IAB TCF / Tip / Headline',
                    'borlabs-cookie',
                ),
            ],
        ];
    }
}
