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

namespace Borlabs\Cookie\Localization\Library;

use Borlabs\Cookie\Localization\LocalizationInterface;

use function Borlabs\Cookie\System\WordPressGlobalFunctions\_x;

final class LibrarySettingsLocalizationStrings implements LocalizationInterface
{
    /**
     * @return array<array<string>>
     */
    public static function get(): array
    {
        // @noinspection HtmlUnknownTarget
        return [
            // Breadcrumbs
            'breadcrumb' => [
                'module' => _x(
                    'Library - Settings',
                    'Backend / Library Settings / Breadcrumb',
                    'borlabs-cookie',
                ),
            ],

            // Fields
            'field' => [
                'enableEmailNotificationsForUpdatablePackagesWithAutoUpdateDisabled' => _x(
                    'E-mail Notifications for &quot;Auto Update: Disabled&quot; Packages',
                    'Backend / Library Settings / Field',
                    'borlabs-cookie',
                ),
                'enableEmailNotificationsForUpdatablePackagesWithAutoUpdateEnabled' => _x(
                    'E-mail Notifications for &quot;Auto Update: Enabled&quot; Packages',
                    'Backend / Library Settings / Field',
                    'borlabs-cookie',
                ),
                'packageAutoUpdateEmailAddresses' => _x(
                    'E-mail Addresses',
                    'Backend / Library Settings / Field',
                    'borlabs-cookie',
                ),
                'packageAutoUpdateInterval' => _x(
                    'Auto Update Interval',
                    'Backend / Library Settings / Field',
                    'borlabs-cookie',
                ),
                'packageAutoUpdateTimeSpan' => _x(
                    'Auto Update Time Span',
                    'Backend / Library Settings / Field',
                    'borlabs-cookie',
                ),
            ],

            // Headlines
            'headline' => [
                'autoUpdateSettings' => _x(
                    '<translation-key id="Auto-Update-Settings">Auto Update Settings</translation-key>',
                    'Backend / Library Settings / Headline',
                    'borlabs-cookie',
                ),
                'reset' => _x(
                    'Reset Library Settings',
                    'Backend / Library Settings / Headline',
                    'borlabs-cookie',
                ),
            ],

            // Hint
            'hint' => [
                'enableEmailNotificationsForUpdatablePackagesWithAutoUpdateDisabled' => _x(
                    'If enabled, you will receive an e-mail notification when a package is available for updating, even if the auto update is disabled.',
                    'Backend / Library Settings / Hint',
                    'borlabs-cookie',
                ),
                'packageAutoUpdateEmailAddresses' => _x(
                    'Add one e-mail address per line to which the notification is to be sent. Enter at least one email address.',
                    'Backend / Library Settings / Hint',
                    'borlabs-cookie',
                ),
                'packageAutoUpdateTimeSpan' => _x(
                    'Set the time for automatic package updates. All updates are scheduled according to UTC-0.',
                    'Backend / Library Settings / Hint',
                    'borlabs-cookie',
                ),
            ],

            // Text
            'text' => [
                'packageAutoUpdateEmailAddressesInfoMessageA' => _x(
                    'Notifications about automatic updates are sent to this address.',
                    'Backend / Library Settings / Text',
                    'borlabs-cookie',
                ),
                'packageAutoUpdateEmailAddressesInfoMessageB' => _x(
                    'An email address <span class="brlbs-cmpnt-important-text">must</span> always be provided, even if the <translation-key id="Auto-Update">Auto Update</translation-key> feature is not being used.',
                    'Backend / Library Settings / Text',
                    'borlabs-cookie',
                ),
            ],

            // Things to know
            'thingsToKnow' => [
                'headlinePackageAutoUpdateInterval' => _x(
                    'Auto Update Interval',
                    'Backend / Library Settings / Things to know',
                    'borlabs-cookie',
                ),
                'headlinePackageAutoUpdateSymbol' => _x(
                    'Auto Update Symbol',
                    'Backend / Library Settings / Things to know',
                    'borlabs-cookie',
                ),
                'packageAutoUpdateIntervalA' => _x(
                    'Set the schedule for automatic package updates. By default, updates take place after at least 24 hours. Alternatively, you can specify a day of the week on which the updates should take place. There must be at least 24 hours between the time the update is detected and the desired day of the week.',
                    'Backend / Library Settings / Hint',
                    'borlabs-cookie',
                ),
                'packageAutoUpdateIntervalB' => _x(
                    'Example: If your planned day of the week is Wednesday 23:00 and the update was published on Tuesday at 15:00, Wednesday of the following week will be selected, even if the 24-hour minimum interval was observed.',
                    'Backend / Library Settings / Things to know',
                    'borlabs-cookie',
                ),
                /** @see LibraryLocalizationStrings::class The identical string is used here. */
                'packageAutoUpdateSymbol' => _x(
                    'The symbol indicates that automatic updates are enabled for this package.',
                    'Backend / Library Settings / Things to know',
                    'borlabs-cookie',
                ),
            ],
        ];
    }
}
