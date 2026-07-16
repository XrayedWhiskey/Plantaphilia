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

namespace Borlabs\Cookie\Localization\Settings;

use Borlabs\Cookie\Localization\LocalizationInterface;

use function Borlabs\Cookie\System\WordPressGlobalFunctions\_x;

/**
 * The **SettingsLocalizationStrings** class contains various localized strings.
 *
 * @see \Borlabs\Cookie\Localization\Settings\SettingsLocalizationStrings::get()
 */
final class SettingsLocalizationStrings implements LocalizationInterface
{
    /**
     * @return array<array<string>>
     */
    public static function get(): array
    {
        return [
            // Alert messages
            'alert' => [
                'cookieDomainIsDifferent' => _x(
                    'Your configured domain is different from the website domain. The setting may be incorrect and will cause the Dialog to reappear on each page.',
                    'Backend / Settings / Alert Message',
                    'borlabs-cookie',
                ),
                'doNotTrackEnabled' => _x(
                    'You have enabled <translation-key id="Do-Not-Track">&quot;Do Not Track&quot;</translation-key> in your browser therefore you will not see the <translation-key id="Dialog">Dialog</translation-key> on your website.',
                    'Backend / Settings / Alert Message',
                    'borlabs-cookie',
                ),
                'reloadAfterOptInEnabledDanger' => _x(
                    'If this option is active, most likely all visits will be counted as "Direct visits" and the origin will be lost. We therefore recommend not to activate this option!',
                    'Backend / Settings / Alert Message',
                    'borlabs-cookie',
                ),
                'reloadAfterOptInEnabledWarning' => _x(
                    'Only activate this option if you need to reload your page after consent. Less than 1 &#37; of all <translation-key id="Borlabs-Cookie">Borlabs Cookie</translation-key> customers need this option enabled. If you don\'t know if you need this option, you won\'t need it.',
                    'Backend / Settings / Alert Message',
                    'borlabs-cookie',
                ),
            ],

            // Breadcrumbs
            'breadcrumb' => [
                'module' => _x(
                    'General Settings',
                    'Backend / Settings / Breadcrumb',
                    'borlabs-cookie',
                ),
            ],

            // Fields
            'field' => [
                'aggregateConsents' => _x(
                    'Aggregate Consents',
                    'Backend / Settings / Label',
                    'borlabs-cookie',
                ),
                'automaticCookieDomainAndPath' => _x(
                    'Automatic Domain and Path Detection',
                    'Backend / Settings / Label',
                    'borlabs-cookie',
                ),
                'borlabsCookieStatus' => _x(
                    '<translation-key id="Borlabs-Cookie-Status">Borlabs Cookie Status</translation-key>',
                    'Backend / Settings / Label',
                    'borlabs-cookie',
                ),
                'cookieDomain' => _x(
                    'Domain',
                    'Backend / Settings / Label',
                    'borlabs-cookie',
                ),
                'cookieLifetime' => _x(
                    'Cookie Lifetime in Days',
                    'Backend / Settings / Label',
                    'borlabs-cookie',
                ),
                'cookieLifetimeEssentialOnly' => _x(
                    'Cookie Lifetime in Days - Essential Only',
                    'Backend / Settings / Label',
                    'borlabs-cookie',
                ),
                'cookiePath' => _x(
                    'Path',
                    'Backend / Settings / Label',
                    'borlabs-cookie',
                ),
                'cookieSameSite' => _x(
                    '<translation-key id="SameSite-Attribute">SameSite attribute</translation-key>',
                    'Backend / Settings / Label',
                    'borlabs-cookie',
                ),
                'cookieSecure' => _x(
                    '<translation-key id="Secure-Attribute">Secure attribute</translation-key>',
                    'Backend / Settings / Label',
                    'borlabs-cookie',
                ),
                'cookieVersion' => _x(
                    'Cookie Version',
                    'Backend / Settings / Label',
                    'borlabs-cookie',
                ),
                'cookiesForBots' => _x(
                    'Cookies for Bots/Crawlers',
                    'Backend / Settings / Label',
                    'borlabs-cookie',
                ),
                'crossCookieDomains' => _x(
                    'Cross Cookie Domains',
                    'Backend / Settings / Label',
                    'borlabs-cookie',
                ),
                'disableBorlabsCookieOnPages' => _x(
                    'Disable Borlabs Cookie on Pages',
                    'Backend / Settings / Label',
                    'borlabs-cookie',
                ),
                'pluginUrl' => _x(
                    'Plugin URL',
                    'Backend / Settings / Label',
                    'borlabs-cookie',
                ),
                'reloadAfterOptIn' => _x(
                    'Reload After Opt-in',
                    'Backend / Settings / Label',
                    'borlabs-cookie',
                ),
                'reloadAfterOptOut' => _x(
                    'Reload After Opt-out',
                    'Backend / Settings / Label',
                    'borlabs-cookie',
                ),
                'respectDoNotTrack' => _x(
                    'Respect <translation-key id="Do-Not-Track">&quot;Do Not Track&quot;</translation-key>',
                    'Backend / Settings / Label',
                    'borlabs-cookie',
                ),
                'setupMode' => _x(
                    'Setup Mode',
                    'Backend / Settings / Label',
                    'borlabs-cookie',
                ),
                'updateCookieVersion' => _x(
                    '<translation-key id="UpdateCookieVersionForceRe-Selection">Update Cookie Version &amp; Force Re-Selection</translation-key>',
                    'Backend / Settings / Label',
                    'borlabs-cookie',
                ),
            ],

            // Headlines
            'headline' => [
                'consentSettings' => _x(
                    'Consent Settings',
                    'Backend / Settings / Headline',
                    'borlabs-cookie',
                ),
                'cookieSettings' => _x(
                    'Cookie Settings',
                    'Backend / Settings / Headline',
                    'borlabs-cookie',
                ),
                'generalSettings' => _x(
                    'General Settings',
                    'Backend / Settings / Headline',
                    'borlabs-cookie',
                ),
                'resetGeneralSettings' => _x(
                    'Reset General Settings',
                    'Backend / Settings / Headline',
                    'borlabs-cookie',
                ),
            ],

            // Hint
            'hint' => [
                'aggregateConsents' => _x(
                    'Aggregate the consents of all WordPress sites in one table.',
                    'Backend / Settings / Hint',
                    'borlabs-cookie',
                ),
                'automaticCookieDomainAndPath' => _x(
                    '<translation-key id="Borlabs-Cookie">Borlabs Cookie</translation-key> tries to automatically detect domain and path.',
                    'Backend / Settings / Hint',
                    'borlabs-cookie',
                ),
                'borlabsCookieStatus' => _x(
                    'Activates <translation-key id="Borlabs-Cookie">Borlabs Cookie</translation-key> on your website. Displays the <translation-key id="Dialog">Dialog</translation-key> and blocks iframes and other external media.',
                    'Backend / Settings / Hint',
                    'borlabs-cookie',
                ),
                'cookieDomain' => _x(
                    'Specify the domain scheme for which the cookie is valid. Example: If you enter <strong><em>example.com</em></strong> the cookie is also valid for subdomains like <strong><em>shop.example.com</em></strong>.',
                    'Backend / Settings / Hint',
                    'borlabs-cookie',
                ),
                'cookieLifetime' => _x(
                    'Number of days until the visitor is asked again for their consent. Remember to adjust the <translation-key id="Cookie-Lifetime">Cookie Lifetime</translation-key> information of the <translation-key id="Borlabs-Cookie">Borlabs Cookie</translation-key> <translation-key id="Cookie">Cookie</translation-key> under <translation-key id="Services">Services</translation-key> &raquo; <translation-key id="Borlabs-Cookie">Borlabs Cookie</translation-key>.',
                    'Backend / Settings / Hint',
                    'borlabs-cookie',
                ),
                'cookieLifetimeEssentialOnly' => _x(
                    'Number of days until the visitor is asked again for their consent, if the user has only given consent to essential <translation-key id="Services">Services</translation-key>. Remember to adjust the <translation-key id="Cookie-Lifetime">Cookie Lifetime</translation-key> information of the <translation-key id="Borlabs-Cookie">Borlabs Cookie</translation-key> <translation-key id="Cookie">Cookie</translation-key> under <translation-key id="Services">Services</translation-key> &raquo; <translation-key id="Borlabs-Cookie">Borlabs Cookie</translation-key>.',
                    'Backend / Settings / Hint',
                    'borlabs-cookie',
                ),
                'cookiePath' => _x(
                    'The path for which the cookie is valid. Default path: <strong>{{ networkPath }}</strong>',
                    'Backend / Settings / Hint',
                    'borlabs-cookie',
                ),
                'cookieSameSite' => _x(
                    'The <translation-key id="SameSite-Attribute">SameSite attribute</translation-key> defines how the browser should handle cookies in cross-site requests. We recommend using the <strong>Lax</strong> option.',
                    'Backend / Settings / Hint',
                    'borlabs-cookie',
                ),
                'cookieSecure' => _x(
                    'A cookie with the <translation-key id="Secure-Attribute">Secure attribute</translation-key> is sent to the server only in case of an encrypted request via the HTTPS protocol.',
                    'Backend / Settings / Hint',
                    'borlabs-cookie',
                ),
                'cookieVersion' => _x(
                    'Shows the version of the cookie of <translation-key id="Borlabs-Cookie">Borlabs Cookie</translation-key>. Increases with activating <translation-key id="UpdateCookieVersionForceRe-Selection">Update Cookie Version &amp; Force Re-Selection</translation-key>.',
                    'Backend / Settings / Hint',
                    'borlabs-cookie',
                ),
                'cookiesForBots' => _x(
                    'A bot/crawler is treated like a visitor who accepted all <translation-key id="Services">Services</translation-key>.',
                    'Backend / Settings / Hint',
                    'borlabs-cookie',
                ),
                'crossCookieDomains' => _x(
                    'Add one URL per line. Insert WordPress Address (URL). URL must end with <strong>/</strong>. Consent will be shared between websites.',
                    'Backend / Settings / Hint',
                    'borlabs-cookie',
                ),
                'disableBorlabsCookieOnPages' => _x(
                    '<translation-key id="Borlabs-Cookie">Borlabs Cookie</translation-key> will not be enabled on the specified pages. Add one URL per line. You can use wildcards (*) to match multiple pages. Example: <strong><em>https://example.com/course/*</em></strong> or <strong><em>*/course/*</em></strong>.',
                    'Backend / Settings / Hint',
                    'borlabs-cookie',
                ),
                'pluginUrl' => _x(
                    'The URL of the <translation-key id="Borlabs-Cookie">Borlabs Cookie</translation-key> plugin directory.',
                    'Backend / Settings / Hint',
                    'borlabs-cookie',
                ),
                'reloadAfterOptIn' => _x(
                    'If enabled, the website will be reloaded after the visitor saves their consent.',
                    'Backend / Settings / Hint',
                    'borlabs-cookie',
                ),
                'reloadAfterOptOut' => _x(
                    'If enabled, the website will be reloaded after the visitor opt out.',
                    'Backend / Settings / Hint',
                    'borlabs-cookie',
                ),
                'reset' => _x(
                    'Please confirm that you want to reset all <translation-key id="Settings">Settings</translation-key> settings. They will be reset to their default settings.',
                    'Backend / Dialog Appearance / Hint',
                    'borlabs-cookie',
                ),
                'respectDoNotTrack' => _x(
                    'A visitor with active <translation-key id="Do-Not-Track">&quot;Do Not Track&quot;</translation-key> setting will not see the <translation-key id="Dialog">Dialog</translation-key> and <translation-key id="Borlabs-Cookie">Borlabs Cookie</translation-key> automatically selects the <translation-key id="Accept-Only-Essential">Accept Only Essential</translation-key> option.',
                    'Backend / Settings / Hint',
                    'borlabs-cookie',
                ),
                'setupMode' => _x(
                    'With Setup Mode enabled, you can test your setup without having to enable <translation-key id="Borlabs-Cookie-Status">Borlabs Cookie Status</translation-key>. Only you will see the <translation-key id="Dialog">Dialog</translation-key> on your website.',
                    'Backend / Settings / Hint',
                    'borlabs-cookie',
                ),
                'updateCookieVersion' => _x(
                    'Updates the version of the cookie of <translation-key id="Borlabs-Cookie">Borlabs Cookie</translation-key>. This will cause the <translation-key id="Dialog">Dialog</translation-key> to reappear for visitors who have already selected an option.',
                    'Backend / Settings / Hint',
                    'borlabs-cookie',
                ),
            ],

            // Placeholder
            'placeholder' => [
            ],

            // Text
            'text' => [
                'version' => _x(
                    'Version',
                    'Backend / Settings / Text',
                    'borlabs-cookie',
                ),
            ],

            // Things to know
            'thingsToKnow' => [
                'aggregateConsentsA' => _x(
                    'Your WordPress is not a <translation-key id="Multisite-Network">Multisite Network</translation-key>, therefore you do not have do modify this setting in most cases.',
                    'Backend / Settings / Things to know / Alert Message',
                    'borlabs-cookie',
                ),
                'aggregateConsentsB' => _x(
                    'Depending on your <translation-key id="Multisite-Network">Multisite Network</translation-key> settings you can separate the consents, or have to aggregate them to get a complete consent history.',
                    'Backend / Settings / Things to know / Text',
                    'borlabs-cookie',
                ),
                'aggregateConsentsC' => _x(
                    'When you have to aggregate the consent:',
                    'Backend / Settings / Things to know / Text',
                    'borlabs-cookie',
                ),
                'aggregateConsentsD' => _x(
                    '- if one site is using only the domain (e.g. <strong><em>example.com</em></strong>) and the other site a subdomain (e.g. <strong><em>shop.example.com</em></strong>)',
                    'Backend / Settings / Things to know / Text',
                    'borlabs-cookie',
                ),
                'aggregateConsentsE' => _x(
                    '- if one site is using the root folder <strong>/</strong> and the other site a subfolder (e.g. <strong>/shop</strong>)',
                    'Backend / Settings / Things to know / Text',
                    'borlabs-cookie',
                ),
                'aggregateConsentsF' => _x(
                    'When you can separate the consent:',
                    'Backend / Settings / Things to know / Text',
                    'borlabs-cookie',
                ),
                'aggregateConsentsG' => _x(
                    '- if all sites are using different domains (e.g. <strong><em>example.com</em></strong> and <strong><em>my-example.com</em></strong>) or different subdomains (e.g. <strong><em>www.example.com</em></strong> and <strong><em>shop.example.com</em></strong>)',
                    'Backend / Settings / Things to know / Text',
                    'borlabs-cookie',
                ),
                'aggregateConsentsH' => _x(
                    '- if all sites are using different subfolders (e.g. <strong>/en</strong> and <strong>/de</strong>)',
                    'Backend / Settings / Things to know / Text',
                    'borlabs-cookie',
                ),
                'crossCookieDomainsA' => _x(
                    'The visitor\'s consent is transferred to all specified domains and WordPress installations, provided the websites are set up in the same way (<translation-key id="Service">Service</translation-key> and <translation-key id="Service-Group">Service Group</translation-key> IDs must match). Any consent or modification by the visitor will be shared with the specified domains.',
                    'Backend / Settings / Things to know / Text',
                    'borlabs-cookie',
                ),
                'crossCookieDomainsB' => _x(
                    '<a class="brlbs-cmpnt-link brlbs-cmpnt-link-with-icon" href="%s" rel="nofollow noreferrer" target="_blank"><span>More information about <translation-key id="Cross-Cookie-Domains">Cross Cookie Domains</translation-key></span><span class="brlbs-cmpnt-external-link-icon"></span></a>',
                    'Backend / Settings / Things to know / Text',
                    'borlabs-cookie',
                ),
                'headlineAggregateCookieConsent' => _x(
                    'Aggregate Consents',
                    'Backend / Settings / Things to know / Headline',
                    'borlabs-cookie',
                ),
                'headlineCrossCookieDomains' => _x(
                    '<translation-key id="Cross-Cookie-Domains">Cross Cookie Domains</translation-key>',
                    'Backend / Settings / Things to know / Headline',
                    'borlabs-cookie',
                ),
                'headlineWhatIsTheCookieVersion' => _x(
                    'What is the Cookie Version?',
                    'Backend / Settings / Things to know / Headline',
                    'borlabs-cookie',
                ),
                'whatIsTheCookieVersion' => _x(
                    'The cookie of <translation-key id="Borlabs-Cookie">Borlabs Cookie</translation-key> is assigned a version number. It is used to ask the visitor again for their consent if changes have been made to the <translation-key id="Services">Services</translation-key>. If the version number in the cookie differs from the current version number, the <translation-key id="Dialog">Dialog</translation-key> appears to the visitor.',
                    'Backend / Settings / Things to know / Text',
                    'borlabs-cookie',
                ),
            ],

            // URL
            'url' => [
                'crossCookieDomains' => _x(
                    'https://borlabs.io/kb/set-up-cross-domain-cookie/?utm_source=Borlabs+Cookie&amp;utm_medium=Things+to+know&amp;utm_campaign=Analysis',
                    'Backend / Settings / URL',
                    'borlabs-cookie',
                ),
            ],
        ];
    }
}
