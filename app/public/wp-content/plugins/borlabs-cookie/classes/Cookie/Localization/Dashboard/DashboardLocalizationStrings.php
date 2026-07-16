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

namespace Borlabs\Cookie\Localization\Dashboard;

use Borlabs\Cookie\Localization\LocalizationInterface;

use function Borlabs\Cookie\System\WordPressGlobalFunctions\_x;

/**
 * The **DashboardLocalizationStrings** class contains various localized strings.
 *
 * @see \Borlabs\Cookie\Localization\Dashboard\DashboardLocalizationStrings::get()
 */
final class DashboardLocalizationStrings implements LocalizationInterface
{
    /**
     * @return array<array<string>>
     */
    public static function get(): array
    {
        return [
            // Alert messages
            'alert' => [
                'noChartData' => _x(
                    'No data available yet. Please try again in a few hours.',
                    'Backend / Dashboard / Alert Message',
                    'borlabs-cookie',
                ),
            ],

            // Breadcrumbs
            'breadcrumb' => [
                'module' => _x(
                    'Dashboard',
                    'Backend / Dashboard / Breadcrumb',
                    'borlabs-cookie',
                ),
            ],

            // Buttons
            'button' => [
                'chartData7Days' => _x(
                    '7 Days',
                    'Backend / Dashboard / Button Title',
                    'borlabs-cookie',
                ),
                'chartData30Days' => _x(
                    '30 Days',
                    'Backend / Dashboard / Button Title',
                    'borlabs-cookie',
                ),
                'chartDataToday' => _x(
                    'Today',
                    'Backend / Dashboard / Button Title',
                    'borlabs-cookie',
                ),
                'chartDataServices30Days' => _x(
                    '30 Days by Service',
                    'Backend / Dashboard / Button Title',
                    'borlabs-cookie',
                ),
                'goToSetupAssistant' => _x(
                    'Go to Setup Assistant',
                    'Backend / Dashboard / Button Title',
                    'borlabs-cookie',
                ),
            ],

            // Headlines
            'headline' => [
                'acknowledgement' => _x(
                    'Acknowledgement',
                    'Backend / Dashboard / Headline',
                    'borlabs-cookie',
                ),
                'contributors' => _x(
                    'Contributors',
                    'Backend / Dashboard / Headline',
                    'borlabs-cookie',
                ),
                'cookieVersion' => _x(
                    '<span>Statistics</span> <small>-</small> <small>Cookie Version {{ cookieVersion }}</small>',
                    'Backend / Dashboard / Headline',
                    'borlabs-cookie',
                ),
                'news' => _x(
                    'News',
                    'Backend / Dashboard / Headline',
                    'borlabs-cookie',
                ),
                'telemetry' => _x(
                    'Telemetry data',
                    'Backend / Dashboard / Headline',
                    'borlabs-cookie',
                ),
            ],

            // Tables
            'table' => [
            ],

            // Text
            'text' => [
                'developerAndInfrastructure' => _x(
                    'Development &amp; Infrastructure',
                    'Backend / Dashboard / Text',
                    'borlabs-cookie',
                ),
                'furtherContributors' => _x(
                    'Further Contributors',
                    'Backend / Dashboard / Text',
                    'borlabs-cookie',
                ),
                'getBorlabsCookieReady' => _x(
                    'Get Borlabs Cookie ready in minutes.',
                    'Backend / Dashboard / Text',
                    'borlabs-cookie',
                ),
                'here' => _x(
                    'here',
                    'Backend / Dashboard / Text',
                    'borlabs-cookie',
                ),
                'inMemoryOfSergiiKovalenko' => _x(
                    'In memory of Sergii Kovalenko.',
                    'Backend / Dashboard / Text',
                    'borlabs-cookie',
                ),
                'localization' => _x(
                    'Localization',
                    'Backend / Dashboard / Text',
                    'borlabs-cookie',
                ),
                'telemetryA' => _x(
                    'We collect telemetry data to ensure the quality, security and further development of our software. This data helps us to diagnose faults, analyze usage, further product development and provide support. <strong class="brlbs-cmpnt-important-text">No personal data</strong> is collected - all information is of a purely technical nature.',
                    'Backend / Dashboard / Text',
                    'borlabs-cookie',
                ),
                'telemetryB' => _x(
                    'For more information about what data we collect and for what purpose, <a class="brlbs-cmpnt-link brlbs-cmpnt-link-with-icon" href="%s" rel="nofollow noreferrer" target="_blank"><span>click here</span><span class="brlbs-cmpnt-external-link-icon"></span></a>.',
                    'Backend / Dashboard / Text',
                    'borlabs-cookie',
                ),
            ],

            // URL
            'url' => [
                'knowledgeBase' => _x(
                    'https://borlabs.io/support/?utm_source=Borlabs+Cookie&utm_medium=Dashboard+Link&utm_campaign=Analysis',
                    'Backend / Dashboard / URL',
                    'borlabs-cookie',
                ),
                'telemetry' => _x(
                    'https://borlabs.io/borlabs-cookie/telemetry/',
                    'Backend / Dashboard / URL',
                    'borlabs-cookie',
                ),
            ],
        ];
    }
}
