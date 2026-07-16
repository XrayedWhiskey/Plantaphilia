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
final class ServiceCookieCreateEditLocalizationStrings implements LocalizationInterface
{
    /**
     * @return array<array<string>>
     */
    public static function get(): array
    {
        return [
            // Alert messages
            'alert' => [
                'noCookieConfigured' => _x(
                    'No <translation-key id="Cookie">Cookie</translation-key> configured.',
                    'Backend / Service Cookie / Alert Message',
                    'borlabs-cookie',
                ),
            ],

            // Buttons
            'button' => [
                'addCookie' => _x(
                    'Add Cookie',
                    'Backend / Service Cookie / Button',
                    'borlabs-cookie',
                ),
            ],

            // Fields
            'field' => [
                'lifetime' => _x(
                    'Lifetime',
                    'Backend / Service Cookie / Field',
                    'borlabs-cookie',
                ),
                'name' => _x(
                    'Name',
                    'Backend / Service Cookie / Field',
                    'borlabs-cookie',
                ),
            ],

            // Headlines
            'headline' => [
                'cookies' => _x(
                    'Cookies',
                    'Backend / Service Cookie / Headline',
                    'borlabs-cookie',
                ),
            ],

            // Tables
            'table' => [
                'description' => _x(
                    'Description',
                    'Backend / Service Cookie / Table Headline',
                    'borlabs-cookie',
                ),
                'hostname' => _x(
                    'Hostname',
                    'Backend / Service Cookie / Table Headline',
                    'borlabs-cookie',
                ),
                'lifetime' => _x(
                    'Lifetime',
                    'Backend / Service Cookie / Table Headline',
                    'borlabs-cookie',
                ),
                'name' => _x(
                    '<translation-key id="Name">Name</translation-key>',
                    'Backend / Service Cookie / Table Headline',
                    'borlabs-cookie',
                ),
                'path' => _x(
                    'Path',
                    'Backend / Service Cookie / Table Headline',
                    'borlabs-cookie',
                ),
                'purpose' => _x(
                    'Purpose',
                    'Backend / Service Cookie / Table Headline',
                    'borlabs-cookie',
                ),
                'type' => _x(
                    'Type',
                    'Backend / Service Cookie / Table Headline',
                    'borlabs-cookie',
                ),
            ],

            // Things to know
            'thingsToKnow' => [
                'cookiesExplained' => _x(
                    'You may input all cookies utilized by the <translation-key id="Service">Service</translation-key> here. Typically, information regarding which cookies are used and their respective purposes can be found on the website of the service you intend to add. For instance, details about the cookies used by Google Analytics can be located in its knowledge base.',
                    'Backend / Services / Things to know / Headline',
                    'borlabs-cookie',
                ),
                'headlineCookiesExplained' => _x(
                    'What is the purpose of the <translation-key id="Cookies">Cookies</translation-key> section?',
                    'Backend / Services / Things to know / Headline',
                    'borlabs-cookie',
                ),
                'headlineInputFieldsExplained' => _x(
                    'Input fields explained',
                    'Backend / Services / Things to know / Headline',
                    'borlabs-cookie',
                ),
                'inputFieldsExplainedDescription' => _x(
                    '<translation-key id="Description">Description</translation-key>: Enter a description of the cookie and explain its purpose. This is optional, but recommended.',
                    'Backend / Services / Things to know / Headline',
                    'borlabs-cookie',
                ),
                'inputFieldsExplainedDeveloperTools' => _x(
                    'The <translation-key id="Cookies">Cookies</translation-key> section of your browser\'s developer tools can be used to find the values for the <translation-key id="Name">Name</translation-key>, <translation-key id="Hostname">Hostname</translation-key>, <translation-key id="Path">Path</translation-key>, and <translation-key id="Lifetime">Lifetime</translation-key> fields.',
                    'Backend / Services / Things to know / Headline',
                    'borlabs-cookie',
                ),
                'inputFieldsExplainedLifetime' => _x(
                    '<translation-key id="Lifetime">Lifetime</translation-key>: The field generally matches the <translation-key id="Expires">Expires</translation-key> column in the <translation-key id="Cookies">Cookies</translation-key> section of your browser. Inform your visitor how many days the cookie will be stored or enter <translation-key id="Session">Session</translation-key> if the cookie is a session cookie and will be deleted when the browser is closed.',
                    'Backend / Services / Things to know / Headline',
                    'borlabs-cookie',
                ),
                'inputFieldsExplainedLifetimeDate' => _x(
                    'If you find a date in the <translation-key id="Expires">Expires</translation-key> column, this means that the cookie is a persistent cookie and will be deleted on the specified date.',
                    'Backend / Services / Things to know / Headline',
                    'borlabs-cookie',
                ),
                'inputFieldsExplainedLifetimeSession' => _x(
                    'If you find the value <translation-key id="Session">Session</translation-key> in the <translation-key id="Expires">Expires</translation-key> column of your browser, this means that the cookie is a session cookie and will be deleted when you close your browser.',
                    'Backend / Services / Things to know / Headline',
                    'borlabs-cookie',
                ),
                'inputFieldsExplainedHostname' => _x(
                    '<translation-key id="Hostname">Hostname</translation-key>: The field generally matches the <translation-key id="Domain">Domain</translation-key> column in the <translation-key id="Cookies">Cookies</translation-key> section of your browser. You can enter <strong>#</strong> into this field and <translation-key id="Borlabs-Cookie">Borlabs Cookie</translation-key> will display the domain of your website in the <translation-key id="Dialog">Dialog</translation-key>.',
                    'Backend / Services / Things to know / Headline',
                    'borlabs-cookie',
                ),
                'inputFieldsExplainedName' => _x(
                    '<translation-key id="Name">Name</translation-key>: The field generally matches the <translation-key id="Name">Name</translation-key> column in the <translation-key id="Cookies">Cookies</translation-key> section of your browser.',
                    'Backend / Services / Things to know / Headline',
                    'borlabs-cookie',
                ),
                'inputFieldsExplainedPath' => _x(
                    '<translation-key id="Path">Path</translation-key>: The field generally matches the <translation-key id="Path">Path</translation-key> column in the <translation-key id="Cookies">Cookies</translation-key> section of your browser. Usually, the path is <strong>/</strong>.',
                    'Backend / Services / Things to know / Headline',
                    'borlabs-cookie',
                ),
                'inputFieldsExplainedPurpose' => _x(
                    '<translation-key id="Purpose">Purpose</translation-key>: We distinguish in two types of <translation-key id="Purposes">Purposes</translation-key>: <translation-key id="Functional">Functional</translation-key> and <translation-key id="Tracking">Tracking</translation-key>.',
                    'Backend / Services / Things to know / Headline',
                    'borlabs-cookie',
                ),
                'inputFieldsExplainedPurposeFunctional' => _x(
                    '<translation-key id="Functional">Functional</translation-key> cookies are required for the basic functionality of the website.',
                    'Backend / Services / Things to know / Headline',
                    'borlabs-cookie',
                ),
                'inputFieldsExplainedPurposeTracking' => _x(
                    '<translation-key id="Tracking">Tracking</translation-key> cookies are used to analyze user behavior on the website and to optimize the website based on the results.',
                    'Backend / Services / Things to know / Headline',
                    'borlabs-cookie',
                ),
                'inputFieldsExplainedType' => _x(
                    '<translation-key id="Type">Type</translation-key>: Choose where information is stored in the browser. <translation-key id="HTTP">HTTP</translation-key> cookies are commonly used, alternatives like <translation-key id="Local-Storage">Local Storage</translation-key> or <translation-key id="Session-Storage">Session Storage</translation-key> do not possess predetermined lifetimes.',
                    'Backend / Services / Things to know / Headline',
                    'borlabs-cookie',
                ),
            ],
        ];
    }
}
