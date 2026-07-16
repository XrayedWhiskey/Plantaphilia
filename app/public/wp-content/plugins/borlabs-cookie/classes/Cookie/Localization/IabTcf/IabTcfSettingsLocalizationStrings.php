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

final class IabTcfSettingsLocalizationStrings implements LocalizationInterface
{
    /**
     * @return array<array<string>>
     */
    public static function get(): array
    {
        return [
            // Alert messages
            'alert' => [
                'downloadGvlSuccessfully' => _x(
                    'Global Vendor List successfully downloaded.',
                    'Backend / IAB TCF Settings / Alert Message',
                    'borlabs-cookie',
                ),
            ],

            // Breadcrumbs
            'breadcrumb' => [
                'module' => _x(
                    'IAB TCF - Settings',
                    'Backend / IAB TCF Settings / Breadcrumb',
                    'borlabs-cookie',
                ),
            ],

            // Buttons
            'button' => [
                'updateGvl' => _x(
                    'Update GVL',
                    'Backend / IAB TCF Settings / Button',
                    'borlabs-cookie',
                ),
            ],

            // Fields
            'field' => [
                'compactLayout' => _x(
                    '<translation-key id="Compact-Layout">Compact Layout</translation-key>',
                    'Backend / IAB TCF Settings / Field',
                    'borlabs-cookie',
                ),
                'gvlStatus' => _x(
                    'Global Vendor List (GVL) Status',
                    'Backend / IAB TCF Settings / Field',
                    'borlabs-cookie',
                ),
                'hostnamesForConsentAddition' => _x(
                    '<translation-key id="Hostnames-for-Consent-Addition">Hostnames for Consent Addition</translation-key>',
                    'Backend / IAB TCF Settings / Field',
                    'borlabs-cookie',
                ),
                'iabTcfStatus' => _x(
                    '<translation-key id="IAB-TCF-Status">IAB TCF Status</translation-key>',
                    'Backend / IAB TCF Settings / Field',
                    'borlabs-cookie',
                ),
                'thirdPartyHostnamesForConsentAdditionList' => _x(
                    'Third-Party <translation-key id="Hostnames-for-Consent-Addition">Hostnames for Consent Addition</translation-key> List',
                    'Backend / IAB TCF Settings / Field',
                    'borlabs-cookie',
                ),
            ],

            // Headlines
            'headline' => [
                'generalSettings' => _x(
                    'General Settings',
                    'Backend / IAB TCF Settings / Headline',
                    'borlabs-cookie',
                ),
            ],

            // Hint
            'hint' => [
                'compactLayout' => _x(
                    'When <translation-key id="Compact-Layout">Compact Layout</translation-key> is enabled, the IAB TCF dialog will be displayed in a compact layout, to improve usability on mobile devices.',
                    'Backend / IAB TCF Settings / Hint',
                    'borlabs-cookie',
                ),
                'hostnamesForConsentAddition' => _x(
                    'One <translation-key id="Hostname">Hostname</translation-key> per line. When a <translation-key id="Hostname">Hostname</translation-key> is recognized in an <strong><em>&lt;a&gt;</em></strong> or <strong><em>&lt;img&gt;</em></strong> tag, the consent will be added to the link via the <strong><em>gdpr_consent</em></strong> parameter.',
                    'Backend / IAB TCF Settings / Headline',
                    'borlabs-cookie',
                ),
                'iabTcfStatus' => _x(
                    'When <translation-key id="IAB-TCF-Status">IAB TCF Status</translation-key> is enabled, certain settings will be overridden or bypassed. For instance, your chosen layout (<translation-key id="Navigation-Dialog-Widget">Dialog &amp; Widget</translation-key> &raquo; <translation-key id="Navigation-Dialog-Widget-Dialog">Dialog</translation-key> &raquo; <translation-key id="Navigation-Dialog-Widget-Dialog-Settings">Settings</translation-key> &raquo; <translation-key id="Layout">Layout</translation-key>) will be substituted with a designated IAB TCF layout. Additionally, the widget (<translation-key id="Navigation-Dialog-Widget">Dialog &amp; Widget</translation-key> &raquo; <translation-key id="Navigation-Dialog-Widget-Widget">Widget</translation-key> &raquo; <translation-key id="Show-Widget">Show Widget</translation-key>) will be turned on and cannot be turned off thereafter.',
                    'Backend / Service Groups / Hint',
                    'borlabs-cookie',
                ),
                'thirdPartyHostnamesForConsentAdditionList' => _x(
                    'A <translation-key id="Compatibility-Patch">Compatibility Patch</translation-key> or a third-party developer can add a <translation-key id="Hostname">Hostname</translation-key> to the <translation-key id="Hostnames-for-Consent-Addition">Hostnames for Consent Addition</translation-key> list using a filter.',
                    'Backend / IAB TCF Settings / Hint',
                    'borlabs-cookie',
                ),
            ],

            // Tables
            'table' => [
            ],

            // Text
            'text' => [
                'gvlDownloaded' => _x(
                    'Global Vendor List is downloaded',
                    'Backend / IAB TCF Settings / Text',
                    'borlabs-cookie',
                ),
                'gvlLastSyncAt' => _x(
                    'Last check with API',
                    'Backend / IAB TCF Settings / Text',
                    'borlabs-cookie',
                ),
                'gvlNotDownloaded' => _x(
                    'Global Vendor List was not downloaded',
                    'Backend / IAB TCF Settings / Text',
                    'borlabs-cookie',
                ),
            ],

            // Things to know
            'thingsToKnow' => [
                'headlineIabTcf' => _x(
                    'IAB TCF',
                    'Backend / IAB TCF Settings / Hint',
                    'borlabs-cookie',
                ),
                'iabTcfDescription' => _x(
                    'The IAB Transparency and Consent Framework (TCF) is a technical standard for the digital advertising industry. It is designed to help all parties in the digital advertising chain ensure that they comply with the EU\'s General Data Protection Regulation (GDPR) and ePrivacy Directive when processing personal data or accessing and/or storing information on a user\'s device, such as cookies, advertising identifiers, device identifiers and other tracking technologies.',
                    'Backend / IAB TCF Settings / Hint',
                    'borlabs-cookie',
                ),
            ],
        ];
    }
}
