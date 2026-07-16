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

namespace Borlabs\Cookie\Localization\Service;

use Borlabs\Cookie\Localization\LocalizationInterface;

use function Borlabs\Cookie\System\WordPressGlobalFunctions\_x;

/**
 * The **ServiceCreateLocalizationStrings** class contains various localized strings.
 */
final class ServiceLocationCreateEditLocalizationStrings implements LocalizationInterface
{
    /**
     * @return array<array<string>>
     */
    public static function get(): array
    {
        return [
            // Alert messages
            'alert' => [
                'noLocationConfigured' => _x(
                    'No <translation-key id="Location">Location</translation-key> configured.',
                    'Backend / Service Location / Alert Message',
                    'borlabs-cookie',
                ),
            ],

            // Buttons
            'button' => [
                'addLocation' => _x(
                    'Add Location',
                    'Backend / Service Location / Button',
                    'borlabs-cookie',
                ),
            ],

            // Fields
            'field' => [
                'hostname' => _x(
                    'Hostname',
                    'Backend / Service Location / Label',
                    'borlabs-cookie',
                ),
                'path' => _x(
                    'Path',
                    'Backend / Service Location / Label',
                    'borlabs-cookie',
                ),
            ],

            // Headlines
            'headline' => [
                'locations' => _x(
                    '<translation-key id="Locations">Locations</translation-key>',
                    'Backend / Service Location / Headline',
                    'borlabs-cookie',
                ),
            ],

            // Tables
            'table' => [
                'hostname' => _x(
                    'Hostname',
                    'Backend / Service Location / Table Headline',
                    'borlabs-cookie',
                ),
                'path' => _x(
                    'Path',
                    'Backend / Service Location / Table Headline',
                    'borlabs-cookie',
                ),
            ],

            // Things to know
            'thingsToKnow' => [
                'headlineHostnameExplained' => _x(
                    'What is a <translation-key id="Hostname">Hostname</translation-key>?',
                    'Backend / Service Location / Things to know / Headline',
                    'borlabs-cookie',
                ),
                'headlineLocationsExplained' => _x(
                    'What is the purpose of the <translation-key id="Locations">Locations</translation-key> section?',
                    'Backend / Service Location / Things to know / Headline',
                    'borlabs-cookie',
                ),
                'hostnameExplained' => _x(
                    'The <translation-key id="Hostname">Hostname</translation-key> is the domain name of the website. For example, if the URL of the website is <strong><em>https://www.example.com</em></strong>, the <translation-key id="Hostname">Hostname</translation-key> is <strong><em>www.example.com</em></strong>.',
                    'Backend / Service Location / Things to know / Text',
                    'borlabs-cookie',
                ),
                'locationsExplained' => _x(
                    'It may happen that an embedded <translation-key id="Service">Service</translation-key> on your website does not set a cookie, but a connection to a foreign domain is established. Therefore, it is advisable to record this domain here and thus name the service responsible for it, so that interested visitors can understand why this external connection occurs.',
                    'Backend / Service Location / Things to know / Text',
                    'borlabs-cookie',
                ),
            ],
        ];
    }
}
