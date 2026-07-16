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

namespace Borlabs\Cookie\Localization\ConsentLog;

use Borlabs\Cookie\Localization\LocalizationInterface;

use function Borlabs\Cookie\System\WordPressGlobalFunctions\_x;

class ConsentLogOverviewLocalizationStrings implements LocalizationInterface
{
    /**
     * @return array<array<string>>
     */
    public static function get(): array
    {
        return [
            // Alert messages
            'alert' => [
                'noConsentLogs' => _x(
                    'No <translation-key id="Consent-Logs">Consent Logs</translation-key> stored.',
                    'Backend / Consent Log / Alert Message',
                    'borlabs-cookie',
                ),
            ],

            // Breadcrumbs
            'breadcrumb' => [
                'module' => _x(
                    '<translation-key id="Consent-Logs">Consent Logs</translation-key>',
                    'Backend / Consent Log / Breadcrumb',
                    'borlabs-cookie',
                ),
            ],

            // Headlines
            'headline' => [
                'consentLogs' => _x(
                    '<translation-key id="Consent-Logs">Consent Logs</translation-key>',
                    'Backend / Consent Log / Headline',
                    'borlabs-cookie',
                ),
            ],

            // Hint
            'hint' => [
            ],

            // Placeholder
            'placeholder' => [
                'search' => _x(
                    'Search UID...',
                    'Backend / Consent Log / Placeholder',
                    'borlabs-cookie',
                ),
            ],

            // Tables
            'table' => [
                'cookieVersion' => _x(
                    'Cookie Version',
                    'Backend / Consent Log / Table Headline',
                    'borlabs-cookie',
                ),
                'iabTcfConsent' => _x(
                    '<translation-key id="IAB-TCF-Consent">IAB TCF Consent</translation-key>',
                    'Backend / Consent Log / Table Headline',
                    'borlabs-cookie',
                ),
                'serviceConsents' => _x(
                    'Service Consents',
                    'Backend / Consent Log / Table Headline',
                    'borlabs-cookie',
                ),
                'stamp' => _x(
                    'Stamp',
                    'Backend / Consent Log / Table Headline',
                    'borlabs-cookie',
                ),
                'uid' => _x(
                    'UID',
                    'Backend / Consent Log / Table Headline',
                    'borlabs-cookie',
                ),
            ],

            // Things to know
            'thingsToKnow' => [
                'consentLogDetails' => _x(
                    'Click on the icon to view details of the user\'s consents.',
                    'Backend / Consent Log / Things to know / Headline',
                    'borlabs-cookie',
                ),
                'consentLogsA' => _x(
                    'The <translation-key id="Consent-Logs">Consent Logs</translation-key> show you the history of the user\'s consents. You can see which services the user has consented to and which not.',
                    'Backend / Consent Log / Things to know / Text',
                    'borlabs-cookie',
                ),
                'consentLogsB' => _x(
                    'Due to the volume of consent logs that can be accumulated, only consent logs from the past 7 days are displayed here. However, you can access older log entries by inputting the UID into the search field.',
                    'Backend / Consent Log / Things to know / Text',
                    'borlabs-cookie',
                ),
                'headlineConsentLogs' => _x(
                    'What are Consent Logs?',
                    'Backend / Consent Log / Things to know / Headline',
                    'borlabs-cookie',
                ),
                'headlineSymbolsExplained' => _x(
                    'Symbols explained',
                    'Backend / Consent Log / Things to know / Headline',
                    'borlabs-cookie',
                ),
                'iabTcfConsentTrue' => _x(
                    '<translation-key id="IAB-TCF-Consent">IAB TCF Consent</translation-key>: The last consent has granted consent to the <translation-key id="IAB-TCF-Vendors">Vendors</translation-key> of the IAB TCF.',
                    'Backend / Consent Log / Things to know / Text',
                    'borlabs-cookie',
                ),
                'iabTcfConsentFalse' => _x(
                    '<translation-key id="IAB-TCF-Consent">IAB TCF Consent</translation-key>: The last consent did not granted consent to the <translation-key id="IAB-TCF-Vendors">Vendors</translation-key> of the IAB TCF.',
                    'Backend / Consent Log / Things to know / Text',
                    'borlabs-cookie',
                ),
            ],
        ];
    }
}
