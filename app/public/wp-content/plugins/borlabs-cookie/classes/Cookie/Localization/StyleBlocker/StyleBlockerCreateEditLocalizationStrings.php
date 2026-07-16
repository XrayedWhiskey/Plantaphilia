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

namespace Borlabs\Cookie\Localization\StyleBlocker;

use Borlabs\Cookie\Localization\LocalizationInterface;

use function Borlabs\Cookie\System\WordPressGlobalFunctions\_x;

/**
 * The **StyleBlockerCreateEditLocalizationStrings** class contains various localized strings.
 *
 * @see \Borlabs\Cookie\Localization\StyleBlocker\StyleBlockerCreateEditLocalizationStrings::get()
 */
final class StyleBlockerCreateEditLocalizationStrings implements LocalizationInterface
{
    /**
     * @return array<array<string>>
     */
    public static function get(): array
    {
        return [
            // Alert messages
            'alert' => [
            ],

            // Breadcrumbs
            'breadcrumb' => [
                'edit' => _x(
                    'Edit: {{ name }}',
                    'Backend / Style Blocker / Breadcrumb',
                    'borlabs-cookie',
                ),
                'module' => _x(
                    'Style Blockers',
                    'Backend / Style Blocker / Breadcrumb',
                    'borlabs-cookie',
                ),
                'new' => _x(
                    'New',
                    'Backend / Style Blocker / Breadcrumb',
                    'borlabs-cookie',
                ),
            ],

            // Buttons
            'button' => [
            ],

            // Fields
            'field' => [
                'contentBlockerGlobalJavaScript' => _x(
                    'Content Blocker Global JavaScript',
                    'Backend / Script Blocker / Field Label',
                    'borlabs-cookie',
                ),
                'handles' => _x(
                    'Handles',
                    'Backend / Style Blocker / Label',
                    'borlabs-cookie',
                ),
                'key' => _x(
                    'ID',
                    'Backend / Style Blocker / Label',
                    'borlabs-cookie',
                ),
                'name' => _x(
                    'Name',
                    'Backend / Style Blocker / Label',
                    'borlabs-cookie',
                ),
                'phrases' => _x(
                    'Phrases',
                    'Backend / Style Blocker / Label',
                    'borlabs-cookie',
                ),
                'serviceOptInScriptTag' => _x(
                    'Service Opt-in Script Tag',
                    'Backend / Script Blocker / Label',
                    'borlabs-cookie',
                ),
                'status' => _x(
                    'Status',
                    'Backend / Style Blocker / Label',
                    'borlabs-cookie',
                ),
            ],

            // Headlines
            'headline' => [
                'styleBlockerConfiguration' => _x(
                    'Style Blocker Configuration',
                    'Backend / Style Blocker / Headline',
                    'borlabs-cookie',
                ),
                'styleBlockerInformation' => _x(
                    'Style Blocker Information',
                    'Backend / Style Blocker / Headline',
                    'borlabs-cookie',
                ),
                'styleBlockerSettings' => _x(
                    'Style Blocker Settings',
                    'Backend / Style Blocker / Headline',
                    'borlabs-cookie',
                ),
            ],

            // Hint
            'hint' => [
                'contentBlockerGlobalJavaScript' => _x(
                    'If your intention is to unblock previously blocked <translation-key id="Styles">Styles</translation-key> upon unblocking a <translation-key id="Content-Blocker">Content Blocker</translation-key>, please insert the provided JavaScript into the <translation-key id="Global">Global</translation-key> field within your <translation-key id="Content-Blocker">Content Blocker</translation-key> settings.',
                    'Backend / Script Blocker / Hint',
                    'borlabs-cookie',
                ),
                'key' => _x(
                    '<translation-key id="ID">ID</translation-key> must be set. The <translation-key id="ID">ID</translation-key> must be at least 3 characters long and may only contain: <strong><em>a-z - _</em></strong>',
                    'Backend / Style Blocker / Hint',
                    'borlabs-cookie',
                ),
                'name' => _x(
                    'The name of the <translation-key id="Style-Blocker">Style Blocker</translation-key>.',
                    'Backend / Style Blocker / Hint',
                    'borlabs-cookie',
                ),
                'serviceOptInScriptTag' => _x(
                    'To allow the unblocking of previously blocked <translation-key id="Styles">Styles</translation-key> through user consent for a <translation-key id="Service">Service</translation-key>, please insert the ensuing script tag into the field labeled <translation-key id="Opt-in-Code">Opt-in Code</translation-key> within your <translation-key id="Service">Service</translation-key>.',
                    'Backend / Script Blocker / Hint',
                    'borlabs-cookie',
                ),
                'status' => _x(
                    'The status of this <translation-key id="Style-Blocker">Style Blocker</translation-key>. If the <translation-key id="Style-Blocker">Style Blocker</translation-key> is disabled, it will not block stylesheets and style-tags.',
                    'Backend / Style Blocker / Hint',
                    'borlabs-cookie',
                ),
            ],

            // Placeholder
            'placeholder' => [
            ],

            // Things to know
            'thingsToKnow' => [
            ],
        ];
    }
}
