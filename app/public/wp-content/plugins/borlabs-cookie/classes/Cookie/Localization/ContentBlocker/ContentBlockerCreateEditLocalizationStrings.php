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

namespace Borlabs\Cookie\Localization\ContentBlocker;

use Borlabs\Cookie\Localization\LocalizationInterface;

use function Borlabs\Cookie\System\WordPressGlobalFunctions\_x;

/**
 * The **ContentBlockerCreateEditLocalizationStrings** class contains various localized strings.
 *
 * @see \Borlabs\Cookie\Localization\ContentBlocker\ContentBlockerCreateEditLocalizationStrings::get()
 */
final class ContentBlockerCreateEditLocalizationStrings implements LocalizationInterface
{
    /**
     * @return array<array<string>>
     */
    public static function get(): array
    {
        return [
            // Alert messages
            'alert' => [
                'contentBlockerNotOfCurrentLanguage' => _x(
                    'The selected <translation-key id="Content-Blocker">Content Blocker</translation-key> is not available in the current language.',
                    'Backend / Content Blockers / Alert Message',
                    'borlabs-cookie',
                ),
                'providerMismatch' => _x(
                    'The selected <translation-key id="Provider">Provider</translation-key> does not match the <translation-key id="Provider">Provider</translation-key> of the <translation-key id="Service">Service</translation-key>.',
                    'Backend / Content Blockers / Alert Message',
                    'borlabs-cookie',
                ),
                'providerNotOfCurrentLanguage' => _x(
                    'The selected <translation-key id="Provider">Provider</translation-key> is not available in the current language.',
                    'Backend / Content Blockers / Alert Message',
                    'borlabs-cookie',
                ),
                'selectedProviderDoesNotExist' => _x(
                    'The selected <translation-key id="Provider">Provider</translation-key> does not exist.',
                    'Backend / Content Blockers / Alert Message',
                    'borlabs-cookie',
                ),
                'selectedServiceDoesNotExist' => _x(
                    'The selected <translation-key id="Service">Service</translation-key> does not exist.',
                    'Backend / Content Blockers / Alert Message',
                    'borlabs-cookie',
                ),
                'serviceNotOfCurrentLanguage' => _x(
                    'The selected <translation-key id="Service">Service</translation-key> is not available in the current language.',
                    'Backend / Content Blockers / Alert Message',
                    'borlabs-cookie',
                ),
            ],

            // Breadcrumbs
            'breadcrumb' => [
                'edit' => _x(
                    'Edit: {{ name }}',
                    'Backend / Content Blockers / Breadcrumb',
                    'borlabs-cookie',
                ),
                'module' => _x(
                    'Content Blockers',
                    'Backend / Content Blockers / Breadcrumb',
                    'borlabs-cookie',
                ),
                'new' => _x(
                    'New',
                    'Backend / Content Blockers / Breadcrumb',
                    'borlabs-cookie',
                ),
            ],

            // Buttons
            'button' => [
                'applyPreset' => _x(
                    'Apply Preset',
                    'Backend / Content Blockers / Button Title',
                    'borlabs-cookie',
                ),
                'selectOrUploadImage' => _x(
                    'Select or Upload Image',
                    'Backend / Content Blockers / Button Title',
                    'borlabs-cookie',
                ),
                'useThisMedia' => _x(
                    'Use this media',
                    'Backend / Content Blockers / Button Title',
                    'borlabs-cookie',
                ),
            ],

            // Fields
            'field' => [
                'hosts' => _x(
                    'Host(s)',
                    'Backend / Content Blockers / Label',
                    'borlabs-cookie',
                ),
                'javaScriptGlobal' => _x(
                    '<translation-key id="Global">Global</translation-key>',
                    'Backend / Content Blockers / Label',
                    'borlabs-cookie',
                ),
                'javaScriptInitialization' => _x(
                    '<translation-key id="Initialization">Initialization</translation-key>',
                    'Backend / Content Blockers / Label',
                    'borlabs-cookie',
                ),
                'key' => _x(
                    'ID',
                    'Backend / Content Blockers / Label',
                    'borlabs-cookie',
                ),
                'name' => _x(
                    'Name',
                    'Backend / Content Blockers / Label',
                    'borlabs-cookie',
                ),
                'preset' => _x(
                    'Preset',
                    'Backend / Content Blockers / Label',
                    'borlabs-cookie',
                ),
                'previewCss' => _x(
                    '<translation-key id="Preview-Blocked-Content-CSS">CSS</translation-key>',
                    'Backend / Content Blockers / Label',
                    'borlabs-cookie',
                ),
                'previewHtml' => _x(
                    '<translation-key id="Preview-Blocked-Content-HTML">HTML</translation-key>',
                    'Backend / Content Blockers / Label',
                    'borlabs-cookie',
                ),
                'previewImage' => _x(
                    '<translation-key id="Preview-Blocked-Content-Image">Image</translation-key>',
                    'Backend / Content Blockers / Label',
                    'borlabs-cookie',
                ),
                'providerList' => _x(
                    'Provider List',
                    'Backend / Content Blockers / Label',
                    'borlabs-cookie',
                ),
                'serviceId' => _x(
                    'Service',
                    'Backend / Content Blockers / Label',
                    'borlabs-cookie',
                ),
                'shortcode' => _x(
                    '<translation-key id="Shortcode">Shortcode</translation-key>',
                    'Backend / Content Blockers / Label',
                    'borlabs-cookie',
                ),
                'status' => _x(
                    'Status',
                    'Backend / Content Blockers / Label',
                    'borlabs-cookie',
                ),
            ],

            // Headlines
            'headline' => [
                'additionalSettings' => _x(
                    '<translation-key id="Additional-Settings">Additional Settings</translation-key>',
                    'Backend / Content Blockers / Headline',
                    'borlabs-cookie',
                ),
                'contentBlockerInformation' => _x(
                    'Content Blocker Information',
                    'Backend / Content Blockers / Headline',
                    'borlabs-cookie',
                ),
                'contentBlockerSettings' => _x(
                    'Content Blocker Settings',
                    'Backend / Content Blockers / Headline',
                    'borlabs-cookie',
                ),
                'javaScript' => _x(
                    'JavaScript',
                    'Backend / Content Blockers / Headline',
                    'borlabs-cookie',
                ),
                'previewBlockedContent' => _x(
                    '<translation-key id="Preview-Blocked-Content">Preview Blocked Content</translation-key>',
                    'Backend / Content Blockers / Headline',
                    'borlabs-cookie',
                ),
                'providerInformation' => _x(
                    'Provider Information',
                    'Backend / Content Blockers / Headline',
                    'borlabs-cookie',
                ),
                'serviceInformation' => _x(
                    'Service Information',
                    'Backend / Content Blockers / Headline',
                    'borlabs-cookie',
                ),
            ],

            // Hint
            'hint' => [
                'javaScriptGlobal' => _x(
                    'Only use JavaScript, do not use <strong><em>&lt;script&gt;</em></strong> tags!',
                    'Backend / Content Blockers / Hint',
                    'borlabs-cookie',
                ),
                'javaScriptInitialization' => _x(
                    'Only use JavaScript, do not use <strong><em>&lt;script&gt;</em></strong> tags!',
                    'Backend / Content Blockers / Hint',
                    'borlabs-cookie',
                ),
                'key' => _x(
                    '<translation-key id="ID">ID</translation-key> must be set. The <translation-key id="ID">ID</translation-key> must be at least 3 characters long and may only contain: <strong><em>a-z - _</em></strong>',
                    'Backend / Content Blockers / Hint',
                    'borlabs-cookie',
                ),
                'name' => _x(
                    'The name of the <translation-key id="Content-Blocker">Content Blocker</translation-key> which can be used within the <translation-key id="Preview-Blocked-Content">Preview Blocked Content</translation-key> &raquo; <translation-key id="Preview-Blocked-Content-HTML">HTML</translation-key> code by using the variable <strong><em>{{ name }}</em></strong>.',
                    'Backend / Content Blockers / Hint',
                    'borlabs-cookie',
                ),
                'previewCss' => _x(
                    'This CSS will be used for the preview of the blocked content.',
                    'Backend / Content Blockers / Hint',
                    'borlabs-cookie',
                ),
                'previewHtml' => _x(
                    'This HTML will be used as the preview of the blocked content. You can use the following variables within the HTML code: <strong><em>{{ name }}</em></strong> to place the name of the <translation-key id="Content-Blocker">Content Blocker</translation-key> and <strong><em>{{ previewImage }}</em></strong> to place the URL of the <translation-key id="Preview-Blocked-Content-Image">Image</translation-key>.',
                    'Backend / Content Blockers / Hint',
                    'borlabs-cookie',
                ),
                'previewImage' => _x(
                    'This image is used as a preview for the blocked content (only with <translation-key id="Preset-B">Preset B</translation-key> and <translation-key id="Preset-C">Preset C</translation-key>). The URL of the image can be accessed within the <translation-key id="Preview-Blocked-Content">Preview Blocked Content</translation-key> &raquo; <translation-key id="Preview-Blocked-Content-HTML">HTML</translation-key> with the <strong><em>{{ previewImage }}</em></strong> variable.',
                    'Backend / Content Blockers / Hint',
                    'borlabs-cookie',
                ),
                'shortcode' => _x(
                    'Use this <translation-key id="Shortcode">Shortcode</translation-key> if automatic detection does not work.',
                    'Backend / Content Blockers / Hint',
                    'borlabs-cookie',
                ),
                'status' => _x(
                    'The status of this <translation-key id="Content-Blocker">Content Blocker</translation-key>. If enabled it does block content (iframes) of the configured <translation-key id="Locations">Locations</translation-key>.',
                    'Backend / Content Blockers / Hint',
                    'borlabs-cookie',
                ),
            ],

            // Options (select | checkbox | radio)
            'option' => [
                'noService' => _x(
                    'No Service',
                    'Backend / Content Blockers / Option',
                    'borlabs-cookie',
                ),
                'presetA' => _x(
                    'Preset A - Default',
                    'Backend / Content Blockers / Option',
                    'borlabs-cookie',
                ),
                'presetB' => _x(
                    '<translation-key id="Preset-B">Preset B</translation-key> - Default + Background Image',
                    'Backend / Content Blockers / Option',
                    'borlabs-cookie',
                ),
                'presetC' => _x(
                    '<translation-key id="Preset-C">Preset C</translation-key> - Video Player',
                    'Backend / Content Blockers / Option',
                    'borlabs-cookie',
                ),
                'presetD' => _x(
                    '<translation-key id="Preset-D">Preset D</translation-key> - Floating Icon',
                    'Backend / Content Blockers / Option',
                    'borlabs-cookie',
                ),
            ],

            // Placeholder
            'placeholder' => [
                'blockThis' => _x(
                    '...block this...',
                    'Backend / Content Blockers / Input Placeholder',
                    'borlabs-cookie',
                ),
            ],

            // Things to know
            'thingsToKnow' => [
                'forDevelopersA' => _x(
                    'The code is executed in a function that uses the variable <strong><em>el</em></strong> as a parameter. <strong><em>el</em></strong> contains the unlocked object.',
                    'Backend / Content Blockers / Things to know / Text',
                    'borlabs-cookie',
                ),
                'forDevelopersB' => _x(
                    'function (contentBlockerData, el) { /* Here is your initialization code */ }',
                    'Backend / Content Blockers / Things to know / Code Example',
                    'borlabs-cookie',
                ),
                'headlineForDevelopers' => _x(
                    'For Developers',
                    'Backend / Content Blockers / Things to know / Headline',
                    'borlabs-cookie',
                ),
                'headlineGlobalJavaScript' => _x(
                    'Global JavaScript',
                    'Backend / Content Blockers / Things to know / Headline',
                    'borlabs-cookie',
                ),
                'headlineInitializationJavaScript' => _x(
                    'Initialization JavaScript',
                    'Backend / Content Blockers / Things to know / Headline',
                    'borlabs-cookie',
                ),
                'headlinePurposeProvider' => _x(
                    'What is the purpose of <translation-key id="Provider">Provider</translation-key> information?',
                    'Backend / Content Blockers / Things to know / Headline',
                    'borlabs-cookie',
                ),
                'headlinePurposeService' => _x(
                    'What is the purpose of <translation-key id="Service">Service</translation-key> information?',
                    'Backend / Content Blockers / Things to know / Headline',
                    'borlabs-cookie',
                ),
                'headlineShortcode' => _x(
                    '<translation-key id="Shortcode">Shortcode</translation-key> explained',
                    'Backend / Content Blockers / Things to know / Headline',
                    'borlabs-cookie',
                ),
                'javaScriptGlobalA' => _x(
                    'JavaScript stored in the <translation-key id="Global">Global</translation-key> field is executed once a blocked content is unblocked by the visitor (meaning only once per page). Use this, for example, to load an external JavaScript library.',
                    'Backend / Content Blockers / Things to know / Text',
                    'borlabs-cookie',
                ),
                'javaScriptGlobalB' => _x(
                    'To execute the JavaScript stored in the <translation-key id="Global">Global</translation-key> field before the blocked content is loaded, activate the option <translation-key id="Execute-Global-code-before-unblocking">Execute Global code before unblocking</translation-key>.',
                    'Backend / Content Blockers / Things to know / Text',
                    'borlabs-cookie',
                ),
                'javaScriptGlobalC' => _x(
                    'If this option is enabled and a visitor unblocks the content, the JavaScript from the <translation-key id="Global">Global</translation-key> field will be executed before the blocked content is loaded.',
                    'Backend / Content Blockers / Things to know / Text',
                    'borlabs-cookie',
                ),
                'javaScriptGlobalD' => _x(
                    'function (contentBlockerData) { /* Here is your global code */ }',
                    'Backend / Content Blockers / Things to know / Code Example',
                    'borlabs-cookie',
                ),
                'javaScriptInitialization' => _x(
                    'JavaScript stored in the <translation-key id="Initialization">Initialization</translation-key> field is executed with every unblock of blocked content (meaning as many times as the visitor unblocks content). It is executed after the JavaScript from the <translation-key id="Global">Global</translation-key> field.',
                    'Backend / Content Blockers / Things to know / Text',
                    'borlabs-cookie',
                ),
                'purposeProviderExplainedA' => _x(
                    'For legal compliance, every <translation-key id="Content-Blocker">Content Blocker</translation-key> must be linked to a specific <translation-key id="Provider">Provider</translation-key>.',
                    'Backend / Content Blockers / Things to know / Text',
                    'borlabs-cookie',
                ),
                'purposeProviderExplainedB' => _x(
                    'In the <translation-key id="Provider">Provider</translation-key> section (found under <translation-key id="Navigation-Consent-Management">Consent Management</translation-key> &raquo; <translation-key id="Navigation-Consent-Management-Providers">Providers</translation-key>), you will find more information and also have the option to edit or create new <translation-key id="Provider">Provider</translation-key> entries.',
                    'Backend / Content Blockers / Things to know / Text',
                    'borlabs-cookie',
                ),
                'purposeServiceExplainedA' => _x(
                    'It\'s optional, but there are two good reasons why you should link a <translation-key id="Content-Blocker">Content Blocker</translation-key> to a specific <translation-key id="Service">Service</translation-key>.',
                    'Backend / Content Blockers / Things to know / Text',
                    'borlabs-cookie',
                ),
                'purposeServiceExplainedB' => _x(
                    'Linking a <translation-key id="Content-Blocker">Content Blocker</translation-key> to a <translation-key id="Service">Service</translation-key> enables automatic unblocking of content for visitors upon granting consent to the <translation-key id="Service">Service</translation-key> through the <translation-key id="Dialog">Dialog</translation-key>.',
                    'Backend / Content Blockers / Things to know / Text',
                    'borlabs-cookie',
                ),
                'purposeServiceExplainedC' => _x(
                    'You can extend the information about the <translation-key id="Content-Blocker">Content Blocker</translation-key>, for example with cookie information, via the linked <translation-key id="Service">Service</translation-key>.',
                    'Backend / Content Blockers / Things to know / Text',
                    'borlabs-cookie',
                ),
                'shortcodeExplained' => _x(
                    'Use the <translation-key id="Shortcode">Shortcode</translation-key> to block content that is not automatically blocked. If contents use the oEmbed format, for example <strong>Pinterest</strong> links, use the <translation-key id="Shortcode">Shortcode</translation-key> to avoid display errors and block the content: {{ shortcode }}',
                    'Backend / Content Blockers / Things to know / Text',
                    'borlabs-cookie',
                ),
                'shortcodesAvailableAfterCreation' => _x(
                    'The <translation-key id="Shortcode">Shortcode</translation-key> is not available until the <translation-key id="Content-Blocker">Content Blocker</translation-key> is created.',
                    'Backend / Content Blockers / Things to know / Text',
                    'borlabs-cookie',
                ),
            ],
        ];
    }
}
