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
 * The **ContentBlockerOverviewLocalizationStrings** class contains various localized strings.
 *
 * @see \Borlabs\Cookie\Localization\ContentBlocker\ContentBlockerSettingsLocalizationStrings::get()
 */
final class ContentBlockerSettingsLocalizationStrings implements LocalizationInterface
{
    /**
     * @return array<array<string>>
     */
    public static function get(): array
    {
        return [
            // Breadcrumbs
            'breadcrumb' => [
                'module' => _x(
                    'Content Blocker - Settings',
                    'Backend / Content Blockers Settings / Breadcrumb',
                    'borlabs-cookie',
                ),
            ],

            // Fields
            'field' => [
                'excludedHostnames' => _x(
                    'Hostname(s) Exclusion List',
                    'Backend / Content Blockers Settings / Label',
                    'borlabs-cookie',
                ),
                'removeIframesInFeeds' => _x(
                    'Remove Iframes and more in Feeds',
                    'Backend / Content Blockers Settings / Label',
                    'borlabs-cookie',
                ),
                'virtualExcludedHostnamesList' => _x(
                    '<translation-key id="Virtual-Hostnames-Exclusion-List">Virtual Hostnames Exclusion List</translation-key>',
                    'Backend / Content Blockers Settings / Label',
                    'borlabs-cookie',
                ),
            ],

            // Headlines
            'headline' => [
                'generalSettings' => _x(
                    'General Settings',
                    'Backend / Content Blockers Settings / Headline',
                    'borlabs-cookie',
                ),
                'reset' => _x(
                    'Reset Content Blocker Settings',
                    'Backend / Content Blockers Settings / Headline',
                    'borlabs-cookie',
                ),
            ],

            // Hint
            'hint' => [
                'excludedHostnames' => _x(
                    'One <translation-key id="Hostname">Hostname</translation-key> per line. When a <translation-key id="Hostname">Hostname</translation-key> is recognized (for example within the src-attribute of an iframe) the content will not be blocked.',
                    'Backend / Content Blockers Settings / Hint',
                    'borlabs-cookie',
                ),
                'removeIframesInFeeds' => _x(
                    'Removes iframes, blocked content and all output of the <translation-key id="Shortcodes">Shortcodes</translation-key> of <translation-key id="Borlabs-Cookie">Borlabs Cookie</translation-key> in feeds. Due technical limitations it is not possible to provide the click-to-load functionality in feeds.',
                    'Backend / Content Blockers Settings / Hint',
                    'borlabs-cookie',
                ),
                'reset' => _x(
                    'Please confirm that you want to reset all <translation-key id="Content-Blocker">Content Blocker</translation-key> settings. They will be reset to their default settings.',
                    'Backend / Content Blockers Settings / Hint',
                    'borlabs-cookie',
                ),
                'virtualExcludedHostnamesList' => _x(
                    'Every <translation-key id="Location">Location</translation-key> of a disabled <translation-key id="Content-Blocker">Content Blocker</translation-key> is added to the <translation-key id="Virtual-Hostnames-Exclusion-List">Virtual Hostnames Exclusion List</translation-key>. Additionally, a <translation-key id="Compatibility-Patch">Compatibility Patch</translation-key> or a third-party developer can add a <translation-key id="Hostname">Hostname</translation-key> to this list using a filter. When a <translation-key id="Hostname">Hostname</translation-key> is recognized the content will not be blocked.',
                    'Backend / Content Blockers Settings / Hint',
                    'borlabs-cookie',
                ),
            ],

            // Things to know
            'thingsToKnow' => [
                'headlineHostnameExplained' => _x(
                    'What is a <translation-key id="Hostname">Hostname</translation-key>?',
                    'Backend / Content Blockers Settings / Things to know / Headline',
                    'borlabs-cookie',
                ),
                'hostnameExplained' => _x(
                    'The <translation-key id="Hostname">Hostname</translation-key> is the domain name of the website. For example, if the URL of the website is <strong><em>https://www.example.com</em></strong>, the <translation-key id="Hostname">Hostname</translation-key> is <strong><em>www.example.com</em></strong>.',
                    'Backend / Content Blockers Settings / Things to know / Text',
                    'borlabs-cookie',
                ),
            ],
        ];
    }
}
