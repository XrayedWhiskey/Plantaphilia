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

final class CloudScanCreateLocalizationStrings implements LocalizationInterface
{
    /**
     * @return array<array<string>>
     */
    public static function get(): array
    {
        return [
            // Alert
            'alert' => [
            ],

            // Breadcrumbs
            'breadcrumb' => [
                'create' => _x(
                    'Create Scan',
                    'Backend / Cloud Scan / Breadcrumb',
                    'borlabs-cookie',
                ),
                'module' => _x(
                    'Scanner',
                    'Backend / Cloud Scan / Breadcrumb',
                    'borlabs-cookie',
                ),
            ],

            // Buttons
            'button' => [
                'back' => _x(
                    'Go back',
                    'Backend / Cloud Scan / Button',
                    'borlabs-cookie',
                ),
                'createScan' => _x(
                    '<translation-key id="Button-Create-Scan">Create scan</translation-key>',
                    'Backend / Cloud Scan / Button',
                    'borlabs-cookie',
                ),
            ],

            // Fields
            'field' => [
                'customScanUrls' => _x(
                    'Custom scan URLs',
                    'Backend / Cloud Scan / Field',
                    'borlabs-cookie',
                ),
                'enableCustomScanUrls' => _x(
                    'Enable custom URL to scan',
                    'Backend / Cloud Scan / Field',
                    'borlabs-cookie',
                ),
                'httpAuthRequired' => _x(
                    'HTTP authentication required',
                    'Backend / Cloud Scan / Field',
                    'borlabs-cookie',
                ),
                'httpAuthPassword' => _x(
                    'HTTP authentication - Password',
                    'Backend / Cloud Scan / Field',
                    'borlabs-cookie',
                ),
                'httpAuthUsername' => _x(
                    'HTTP authentication - Username',
                    'Backend / Cloud Scan / Field',
                    'borlabs-cookie',
                ),
                'scanPageUrl' => _x(
                    'Custom page',
                    'Backend / Cloud Scan / Field',
                    'borlabs-cookie',
                ),
                'selectPageType' => _x(
                    'Pages to scan',
                    'Backend / Cloud Scan / Field',
                    'borlabs-cookie',
                ),
                'selectScanType' => _x(
                    '<translation-key id="Scan-type">Scan type</translation-key>',
                    'Backend / Cloud Scan / Field',
                    'borlabs-cookie',
                ),
            ],

            // Headlines
            'headline' => [
                'scans' => _x(
                    'Select pages to scan',
                    'Backend / Cloud Scan / Headline',
                    'borlabs-cookie',
                ),
            ],

            // Hint
            'hint' => [
                'scanPageUrl' => _x(
                    'Add one URL per line.',
                    'Backend / Cloud Scan / Hint',
                    'borlabs-cookie',
                ),
            ],

            // Things to know
            'thingsToKnow' => [
                'headlineHowTheScannerWorks' => _x(
                    'How the scanner works',
                    'Backend / Cloud Scan / Things to know / Headline',
                    'borlabs-cookie',
                ),
                'headlineHttpAuth' => _x(
                    'HTTP authentication',
                    'Backend / Cloud Scan / Tip',
                    'borlabs-cookie',
                ),
                'headlinePagesToScan' => _x(
                    'Pages to scan',
                    'Backend / Cloud Scan / Tip',
                    'borlabs-cookie',
                ),
                'headlineScanType' => _x(
                    'Scan type',
                    'Backend / Cloud Scan / Tip',
                    'borlabs-cookie',
                ),
                'howTheScannerWorksA' => _x(
                    'The scanner will visit your website and analyze the cookies and external resources that are loaded.',
                    'Backend / Cloud Scan / Tip',
                    'borlabs-cookie',
                ),
                'howTheScannerWorksB' => _x(
                    'Ensure that your website is publicly accessible and not hindered by any <strong><em>Maintenance</em></strong> or <strong><em>Under construction</em></strong> page plugins.',
                    'Backend / Cloud Scan / Tip',
                    'borlabs-cookie',
                ),
                'howTheScannerWorksC' => _x(
                    'In cases where your website is secured through HTTP authentication, kindly refer to the subsequent section for detailed guidance on HTTP authentication.',
                    'Backend / Cloud Scan / Tip',
                    'borlabs-cookie',
                ),
                'httpAuth' => _x(
                    'If your website is protected by HTTP authentication, you can enter the username and password here, to allow the scanner to access your website. The credentials will be deleted after 7 days automatically.',
                    'Backend / Cloud Scan / Tip',
                    'borlabs-cookie',
                ),
                'numberOfSelectionOfSitesPerPostType' => _x(
                    'If you select this option, the expected scan will be about <strong>{{ numberOfSelectionOfSitesPerPostType }}</strong> pages.',
                    'Backend / Cloud Scan / Tip',
                    'borlabs-cookie',
                ),
                'pagesToScanCustom' => _x(
                    '<translation-key id="Custom">Custom</translation-key>: Use this option to scan a specific page of your website.',
                    'Backend / Cloud Scan / Tip',
                    'borlabs-cookie',
                ),
                'pagesToScanHomepage' => _x(
                    '<translation-key id="Homepage">Homepage</translation-key>: Use this option to scan the homepage of your website.',
                    'Backend / Cloud Scan / Tip',
                    'borlabs-cookie',
                ),
                'pagesToScanSelectionOfSitesPerPostType' => _x(
                    '<translation-key id="Selection-Of-Sites-Per-Post-Type">Selection of sites per post type</translation-key>: Use this option to scan a selection of pages of your website. The scanner selects the homepage, the newest and the oldest page of each post type and its archive.',
                    'Backend / Cloud Scan / Tip',
                    'borlabs-cookie',
                ),
                'scanTypeAudit' => _x(
                    '<translation-key id="Audit">Audit</translation-key>: Use this scan type to verify that your website does not load cookies or external resources without obtaining the appropriate consent.',
                    'Backend / Cloud Scan / Tip',
                    'borlabs-cookie',
                ),
                'scanTypeSetup' => _x(
                    '<translation-key id="Setup">Setup</translation-key>: Use this scan type to determine which cookies or external resources are used by your website. After the scan, you will receive recommendations for packages that you can install to block these cookies or external resources.',
                    'Backend / Cloud Scan / Tip',
                    'borlabs-cookie',
                ),
            ],
        ];
    }
}
