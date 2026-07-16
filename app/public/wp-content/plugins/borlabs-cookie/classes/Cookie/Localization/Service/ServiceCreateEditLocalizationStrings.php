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
final class ServiceCreateEditLocalizationStrings implements LocalizationInterface
{
    /**
     * @return array<array<string>>
     */
    public static function get(): array
    {
        return [
            // Alert messages
            'alert' => [
                'noServiceGroupSelected' => _x(
                    'No <translation-key id="Service-Group">Service Group</translation-key> selected.',
                    'Backend / Services / Alert Message',
                    'borlabs-cookie',
                ),
                'providerNotOfCurrentLanguage' => _x(
                    'The selected <translation-key id="Provider">Provider</translation-key> is not available in the current language.',
                    'Backend / Services / Alert',
                    'borlabs-cookie',
                ),
                'selectedProviderDoesNotExist' => _x(
                    'The selected <translation-key id="Provider">Provider</translation-key> does not exist.',
                    'Backend / Services / Alert Message',
                    'borlabs-cookie',
                ),
                'selectedServiceGroupDoesNotExist' => _x(
                    'The selected <translation-key id="Service-Group">Service Group</translation-key> does not exist.',
                    'Backend / Services / Alert Message',
                    'borlabs-cookie',
                ),
                'serviceGroupNotOfCurrentLanguage' => _x(
                    'The selected <translation-key id="Service-Group">Service Group</translation-key> is not available in the current language.',
                    'Backend / Services / Alert Message',
                    'borlabs-cookie',
                ),
                'serviceNotOfCurrentLanguage' => _x(
                    'The selected <translation-key id="Service">Service</translation-key> is not available in the current language.',
                    'Backend / Services / Alert Message',
                    'borlabs-cookie',
                ),
            ],

            // Breadcrumbs
            'breadcrumb' => [
                'edit' => _x(
                    'Edit: {{ name }}',
                    'Backend / Service / Breadcrumb',
                    'borlabs-cookie',
                ),
                'module' => _x(
                    'Services',
                    'Backend / Services / Breadcrumb',
                    'borlabs-cookie',
                ),
                'new' => _x(
                    'New',
                    'Backend / Services / Breadcrumb',
                    'borlabs-cookie',
                ),
            ],

            // Fields
            'field' => [
                'description' => _x(
                    'Description',
                    'Backend / Services / Label',
                    'borlabs-cookie',
                ),
                'fallbackCode' => _x(
                    'Fallback Code',
                    'Backend / Services / Label',
                    'borlabs-cookie',
                ),
                'key' => _x(
                    'ID',
                    'Backend / Services / Label',
                    'borlabs-cookie',
                ),
                'name' => _x(
                    'Name',
                    'Backend / Services / Label',
                    'borlabs-cookie',
                ),
                'optInCode' => _x(
                    '<translation-key id="Opt-in-Code">Opt-in Code</translation-key>',
                    'Backend / Services / Label',
                    'borlabs-cookie',
                ),
                'optOutCode' => _x(
                    'Opt-out Code',
                    'Backend / Services / Label',
                    'borlabs-cookie',
                ),
                'optOutShortcode' => _x(
                    '<translation-key id="Opt-out-Shortcode">Opt-out Shortcode</translation-key>',
                    'Backend / Services / Label',
                    'borlabs-cookie',
                ),
                'position' => _x(
                    'Position',
                    'Backend / Services / Label',
                    'borlabs-cookie',
                ),
                'providerList' => _x(
                    'Provider List',
                    'Backend / Services / Label',
                    'borlabs-cookie',
                ),
                'serviceGroupId' => _x(
                    'Service Group',
                    'Backend / Services / Label',
                    'borlabs-cookie',
                ),
                'shortcode' => _x(
                    '<translation-key id="Shortcode">Shortcode</translation-key>',
                    'Backend / Services / Label',
                    'borlabs-cookie',
                ),
                'status' => _x(
                    'Status',
                    'Backend / Services / Label',
                    'borlabs-cookie',
                ),
            ],

            // Headlines
            'headline' => [
                'additionalSettings' => _x(
                    '<translation-key id="Additional-Settings">Additional Settings</translation-key>',
                    'Backend / Services / Headline',
                    'borlabs-cookie',
                ),
                'htmlAndJavascript' => _x(
                    'HTML &amp; JavaScript',
                    'Backend / Services / Headline',
                    'borlabs-cookie',
                ),
                'providerInformation' => _x(
                    'Provider Information',
                    'Backend / Services / Headline',
                    'borlabs-cookie',
                ),
                'serviceGroupInformation' => _x(
                    'Service Group Information',
                    'Backend / Services / Headline',
                    'borlabs-cookie',
                ),
                'serviceInformation' => _x(
                    'Service Information',
                    'Backend / Services / Headline',
                    'borlabs-cookie',
                ),
                'serviceSettings' => _x(
                    'Service Settings',
                    'Backend / Services / Headline',
                    'borlabs-cookie',
                ),
            ],

            // Hint
            'hint' => [
                'description' => _x(
                    'Explain to your visitors what this <translation-key id="Service">Service</translation-key> does.',
                    'Backend / Services / Hint',
                    'borlabs-cookie',
                ),
                'fallbackCode' => _x(
                    'This code will always be executed.',
                    'Backend / Services / Hint',
                    'borlabs-cookie',
                ),
                'key' => _x(
                    '<translation-key id="ID">ID</translation-key> must be set. The <translation-key id="ID">ID</translation-key> must be at least 3 characters long and may only contain: <strong><em>a-z - _</em></strong>',
                    'Backend / Services / Hint',
                    'borlabs-cookie',
                ),
                'name' => _x(
                    'Insert a name for this <translation-key id="Service">Service</translation-key>.',
                    'Backend / Services / Hint',
                    'borlabs-cookie',
                ),
                'optInCode' => _x(
                    'This code is executed after the visitor has given their consent.',
                    'Backend / Services / Hint',
                    'borlabs-cookie',
                ),
                'optOutCode' => _x(
                    'This code is executed only if the visitor has previously consented and now chooses to opt out. It is executed once.',
                    'Backend / Services / Hint',
                    'borlabs-cookie',
                ),
                'optOutShortcode' => _x(
                    'Use this <translation-key id="Shortcode">Shortcode</translation-key> to display an opt-out option for this <translation-key id="Service">Service</translation-key>.',
                    'Backend / Services / Hint',
                    'borlabs-cookie',
                ),
                'position' => _x(
                    'Determine the position where this <translation-key id="Service">Service</translation-key> is displayed in its <translation-key id="Service-Group">Service Group</translation-key>. Order follows natural numbers.',
                    'Backend / Services / Hint',
                    'borlabs-cookie',
                ),
                'provider' => _x(
                    'A <translation-key id="Service">Service</translation-key> must be linked to a <translation-key id="Provider">Provider</translation-key>. You may choose an existing <translation-key id="Provider">Provider</translation-key> or establish a new one. Please note, the option to create a new <translation-key id="Provider">Provider</translation-key> is available exclusively during the <translation-key id="Service">Service</translation-key> creation process within this view.',
                    'Backend / Services / Hint',
                    'borlabs-cookie',
                ),
                'serviceGroup' => _x(
                    'The <translation-key id="Service-Group">Service Group</translation-key> the <translation-key id="Service">Service</translation-key> is part of.',
                    'Backend / Services / Hint',
                    'borlabs-cookie',
                ),
                'shortcode' => _x(
                    'Use this <translation-key id="Shortcode">Shortcode</translation-key> to unblock JavaScript or content when user opted-in for this <translation-key id="Service">Service</translation-key>.',
                    'Backend / Services / Hint',
                    'borlabs-cookie',
                ),
                'status' => _x(
                    'The status of this <translation-key id="Service">Service</translation-key>. If enabled it is displayed to the visitor in the <translation-key id="Dialog">Dialog</translation-key>.',
                    'Backend / Services / Hint',
                    'borlabs-cookie',
                ),
            ],

            // Placeholder
            'placeholder' => [
                'blockThis' => _x(
                    '...block this...',
                    'Backend / Services / Input Placeholder',
                    'borlabs-cookie',
                ),
                'never' => _x(
                    'Never',
                    'Backend / Services / Input Placeholder',
                    'borlabs-cookie',
                ),
            ],

            // Tables
            'table' => [
                'cookieLifetime' => _x(
                    'Lifetime',
                    'Backend / Services / Table Headline',
                    'borlabs-cookie',
                ),
                'cookieName' => _x(
                    'Name',
                    'Backend / Services / Table Headline',
                    'borlabs-cookie',
                ),
                'cookiePurpose' => _x(
                    'Purpose',
                    'Backend / Services / Table Headline',
                    'borlabs-cookie',
                ),
                'cookieType' => _x(
                    'Type',
                    'Backend / Services / Table Headline',
                    'borlabs-cookie',
                ),
                'hostName' => _x(
                    'Name',
                    'Backend / Services / Table Headline',
                    'borlabs-cookie',
                ),
            ],

            // Things to know
            'thingsToKnow' => [
                'blockCookiesBeforeConsentExplainedA' => _x(
                    'If you enable this option <translation-key id="Borlabs-Cookie">Borlabs Cookie</translation-key> searches and deletes cookies that match the <translation-key id="Name">Name</translation-key> (<translation-key id="Cookies">Cookies</translation-key> &raquo; <translation-key id="Name">Name</translation-key>). You can also use <strong>*</strong> to search for multiple names, e.g. if you enter the Name <strong><em>example_*</em></strong> <translation-key id="Borlabs-Cookie">Borlabs Cookie</translation-key> will search and delete the cookies <strong><em>example_abc</em></strong> and <strong><em>example_xyz</em></strong>.',
                    'Backend / Services / Things to know / Text',
                    'borlabs-cookie',
                ),
                'blockCookiesBeforeConsentExplainedB' => _x(
                    'This function is mainly intended to delete session cookies set by third-party plugins via PHP. JavaScript cookies can also be deleted, but only if they belong to the same domain and do not have the <translation-key id="Secure-Attribute">Secure attribute</translation-key>.',
                    'Backend / Services / Things to know / Text',
                    'borlabs-cookie',
                ),
                'blockCookiesBeforeConsentExplainedC' => _x(
                    'Example: if the website operates under <strong><em>www.example.com</em></strong>, no cookie associated with <strong><em>example.com</em></strong> can be deleted.',
                    'Backend / Services / Things to know / Text',
                    'borlabs-cookie',
                ),
                'blockCookiesBeforeConsentExplainedD' => _x(
                    'Named cookies will be only set after consent by the visitor was given and the website was reloaded.',
                    'Backend / Services / Things to know / Text',
                    'borlabs-cookie',
                ),
                'headlineBlockCookiesBeforeConsent' => _x(
                    '<translation-key id="Block-Cookies-Before-Consent">Block Cookies Before Consent</translation-key>',
                    'Backend / Services / Things to know / Headline',
                    'borlabs-cookie',
                ),
                'headlinePrioritize' => _x(
                    '<translation-key id="Prioritize">Prioritize</translation-key>',
                    'Backend / Services / Things to know / Headline',
                    'borlabs-cookie',
                ),
                'headlinePurposeProvider' => _x(
                    'What is the purpose of <translation-key id="Provider">Provider</translation-key> information?',
                    'Backend / Services / Things to know / Headline',
                    'borlabs-cookie',
                ),
                'headlineOutOutShortcode' => _x(
                    'Opt-out Shortcode explained',
                    'Backend / Services / Things to know / Headline',
                    'borlabs-cookie',
                ),
                'headlineShortcode' => _x(
                    '<translation-key id="Shortcode">Shortcode</translation-key> explained',
                    'Backend / Services / Things to know / Headline',
                    'borlabs-cookie',
                ),
                'optOutShortcodeExplained' => _x(
                    'The <translation-key id="Shortcode">Shortcode</translation-key> can be used to display an opt-out option for this <translation-key id="Service">Service</translation-key>.',
                    'Backend / Services / Things to know / Text',
                    'borlabs-cookie',
                ),
                'prioritizeA' => _x(
                    'The <translation-key id="Opt-in-Code">Opt-in Code</translation-key> is loaded in <strong><em>&lt;head&gt;</em></strong> and is executed before the page is fully loaded.',
                    'Backend / Services / Things to know / Text',
                    'borlabs-cookie',
                ),
                'prioritizeB' => _x(
                    'This function cannot be combined with unblock code from a <translation-key id="Content-Blocker">Content Blocker</translation-key>, <translation-key id="Script-Blocker">Script Blocker</translation-key> or <translation-key id="Style-Blocker">Style Blocker</translation-key>.',
                    'Backend / Services / Things to know / Text',
                    'borlabs-cookie',
                ),
                'purposeProviderExplainedA' => _x(
                    'For legal compliance, every <translation-key id="Service">Service</translation-key> must be linked to a specific <translation-key id="Provider">Provider</translation-key>.',
                    'Backend / Services / Things to know / Text',
                    'borlabs-cookie',
                ),
                'purposeProviderExplainedB' => _x(
                    'In the <translation-key id="Provider">Provider</translation-key> section (found under <translation-key id="Navigation-Consent-Management">Consent Management</translation-key> &raquo; <translation-key id="Navigation-Consent-Management-Providers">Providers</translation-key>), you will find more information and also have the option to edit or create new <translation-key id="Provider">Provider</translation-key> entries.',
                    'Backend / Services / Things to know / Text',
                    'borlabs-cookie',
                ),
                'shortcodeExplainedA' => _x(
                    'The <translation-key id="Shortcode">Shortcode</translation-key> can be used to execute custom code associated with this type of <translation-key id="Service">Service</translation-key> once the visitor has given consent to the <translation-key id="Service">Service</translation-key>. This can be used, for example, to block a conversion pixel code.',
                    'Backend / Services / Things to know / Text',
                    'borlabs-cookie',
                ),
                'shortcodeExplainedB' => _x(
                    'The <translation-key id="Shortcode">Shortcode</translation-key> for example can be used in the <translation-key id="Meta-Box">Meta Box</translation-key> of <translation-key id="Borlabs-Cookie">Borlabs Cookie</translation-key>. You can find it for example in <translation-key id="Posts">Posts</translation-key> &raquo; <em><translation-key id="Your-Post">Your Post</translation-key></em> &raquo; <translation-key id="Borlabs-Cookie">Borlabs Cookie</translation-key> &raquo; <translation-key id="Custom-Code">Custom Code</translation-key>.',
                    'Backend / Services / Things to know / Text',
                    'borlabs-cookie',
                ),
                'shortcodesAvailableAfterCreation' => _x(
                    'The <translation-key id="Shortcodes">Shortcodes</translation-key> are not available until the <translation-key id="Service">Service</translation-key> is created.',
                    'Backend / Services / Things to know / Text',
                    'borlabs-cookie',
                ),
            ],
        ];
    }
}
