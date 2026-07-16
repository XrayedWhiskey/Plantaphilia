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

namespace Borlabs\Cookie\Localization\LocalScanner;

use Borlabs\Cookie\Localization\LocalizationInterface;

use function Borlabs\Cookie\System\WordPressGlobalFunctions\_x;

/**
 * The **ScriptBlockerOverviewLocalizationStrings** class contains various localized strings.
 *
 * @see \Borlabs\Cookie\Localization\LocalScanner\LocalScannerLocalizationStrings::get()
 */
final class LocalScannerLocalizationStrings implements LocalizationInterface
{
    /**
     * @return array<array<string>>
     */
    public static function get(): array
    {
        return [
            // Alert messages
            'alert' => [
                'scanError' => _x(
                    'An error has occurred. Please check your browser\'s console for an error message. If the issue persists even after trying again and switching to <translation-key id="Scan-Mode">Scan Mode</translation-key>: <translation-key id="Manual">Manual</translation-key>, kindly reach out to our support team for assistance.',
                    'Backend / Local Scanner / Text',
                    'borlabs-cookie',
                ),
            ],

            // Buttons
            'button' => [
                'runScan' => _x(
                    'Run scan',
                    'Backend / Local Scanner / Button',
                    'borlabs-cookie',
                ),
            ],

            // Fields
            'field' => [
                'pageToScan' => _x(
                    'Page to scan',
                    'Backend / Local Scanner / Label',
                    'borlabs-cookie',
                ),
                'scanMode' => _x(
                    '<translation-key id="Scan-Mode">Scan Mode</translation-key>',
                    'Backend / Local Scanner / Label',
                    'borlabs-cookie',
                ),
                'searchPhrase' => _x(
                    '<translation-key id="Search-Phrase">Search Phrase</translation-key>',
                    'Backend / Local Scanner / Label',
                    'borlabs-cookie',
                ),
            ],

            // Headlines
            'headline' => [
                'scanProgress' => _x(
                    'Scan Progress',
                    'Backend / Local Scanner / Headline',
                    'borlabs-cookie',
                ),
                'scanSettings' => _x(
                    'Scan Settings',
                    'Backend / Local Scanner / Headline',
                    'borlabs-cookie',
                ),
            ],

            // Hint
            'hint' => [
                'searchPhrase' => _x(
                    'The string <translation-key id="Borlabs-Cookie">Borlabs Cookie</translation-key> is looking for. Separate multiple entries with a comma.',
                    'Backend / Local Scanner / Hint',
                    'borlabs-cookie',
                ),
            ],

            // Text
            'text' => [
                'manualScan' => _x(
                    'Click on <a class="brlbs-cmpnt-link" href="##SIGNED_URL##" target="_bank">this link</a> to scan manually.',
                    'Backend / Local Scanner / Text',
                    'borlabs-cookie',
                ),
            ],

            // Things to know
            'thingsToKnow' => [
                'headlineScanModesExplained' => _x(
                    '<translation-key id="Scan-Modes">Scan Modes</translation-key>',
                    'Backend / Local Scanner / Headline',
                    'borlabs-cookie',
                ),
                'scanModeCurrentUser' => _x(
                    '<translation-key id="Current-User">Current User</translation-key>:<br>The scan is performed as the currently logged in user. This may affect the results if the user has different rights than a guest user.',
                    'Backend / Local Scanner / Headline',
                    'borlabs-cookie',
                ),
                'scanModeCurrentUserJavaScriptExample' => _x(
                    'For example: Some plugins do not embed tracking JavaScript for users with the role of an administrator.',
                    'Backend / Local Scanner / Headline',
                    'borlabs-cookie',
                ),
                'scanModeGuest' => _x(
                    '<translation-key id="Guest">Guest</translation-key>:<br>The scan is performed as a guest user. This is the default setting.',
                    'Backend / Local Scanner / Headline',
                    'borlabs-cookie',
                ),
                'scanModeManual' => _x(
                    '<translation-key id="Manual">Manual</translation-key>:<br>When you use this mode, a signed URL is provided that you can use to run the scan by clicking on it. This is useful if the scan cannot be performed due to a server configuration or if your website is not accessible from the outside (<translation-key id="Local-Environment">Local Environment</translation-key> or <translation-key id="Staging-Environment">Staging Environment</translation-key>), for example.',
                    'Backend / Local Scanner / Headline',
                    'borlabs-cookie',
                ),
            ],
        ];
    }
}
