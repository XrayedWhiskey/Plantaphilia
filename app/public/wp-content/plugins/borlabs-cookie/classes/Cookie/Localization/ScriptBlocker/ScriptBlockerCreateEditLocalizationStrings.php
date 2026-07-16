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

namespace Borlabs\Cookie\Localization\ScriptBlocker;

use Borlabs\Cookie\Localization\LocalizationInterface;

use function Borlabs\Cookie\System\WordPressGlobalFunctions\_x;

/**
 * The **ScriptBlockerCreateEditLocalizationStrings** class contains various localized strings.
 *
 * @see \Borlabs\Cookie\Localization\ScriptBlocker\ScriptBlockerCreateEditLocalizationStrings::get()
 */
final class ScriptBlockerCreateEditLocalizationStrings implements LocalizationInterface
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
                    'Backend / Script Blockers / Breadcrumb',
                    'borlabs-cookie',
                ),
                'module' => _x(
                    'Script Blockers',
                    'Backend / Script Blockers / Breadcrumb',
                    'borlabs-cookie',
                ),
                'new' => _x(
                    'New',
                    'Backend / Script Blockers / Breadcrumb',
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
                    'Backend / Script Blockers / Field Label',
                    'borlabs-cookie',
                ),
                'handles' => _x(
                    'Handles',
                    'Backend / Script Blockers / Label',
                    'borlabs-cookie',
                ),
                'key' => _x(
                    'ID',
                    'Backend / Script Blockers / Label',
                    'borlabs-cookie',
                ),
                'name' => _x(
                    'Name',
                    'Backend / Script Blockers / Label',
                    'borlabs-cookie',
                ),
                'onExist' => _x(
                    '<translation-key id="onExist">onExist</translation-key>',
                    'Backend / Script Blockers / Label',
                    'borlabs-cookie',
                ),
                'phrases' => _x(
                    'Phrases',
                    'Backend / Script Blockers / Label',
                    'borlabs-cookie',
                ),
                'serviceOptInScriptTag' => _x(
                    'Service Opt-in Script Tag',
                    'Backend / Script Blockers / Label',
                    'borlabs-cookie',
                ),
                'status' => _x(
                    'Status',
                    'Backend / Script Blockers / Label',
                    'borlabs-cookie',
                ),
            ],

            // Headlines
            'headline' => [
                'scriptBlockerConfiguration' => _x(
                    'Script Blocker Configuration',
                    'Backend / Script Blockers / Headline',
                    'borlabs-cookie',
                ),
                'scriptBlockerInformation' => _x(
                    'Script Blocker Information',
                    'Backend / Script Blockers / Headline',
                    'borlabs-cookie',
                ),
                'scriptBlockerSettings' => _x(
                    'Script Blocker Settings',
                    'Backend / Script Blockers / Headline',
                    'borlabs-cookie',
                ),
            ],

            // Hint
            'hint' => [
                'contentBlockerGlobalJavaScript' => _x(
                    'If your intention is to unblock previously blocked JavaScripts upon unblocking a <translation-key id="Content-Blocker">Content Blocker</translation-key>, please insert the provided JavaScript into the <translation-key id="Global">Global</translation-key> field within your <translation-key id="Content-Blocker">Content Blocker</translation-key> settings.',
                    'Backend / Script Blockers / Hint',
                    'borlabs-cookie',
                ),
                'key' => _x(
                    '<translation-key id="ID">ID</translation-key> must be set. The <translation-key id="ID">ID</translation-key> must be at least 3 characters long and may only contain: <strong><em>a-z - _</em></strong>',
                    'Backend / Script Blockers / Hint',
                    'borlabs-cookie',
                ),
                'name' => _x(
                    'The name of the <translation-key id="Script-Blocker">Script Blocker</translation-key>.',
                    'Backend / Script Blockers / Hint',
                    'borlabs-cookie',
                ),
                'serviceOptInScriptTag' => _x(
                    'To allow the unblocking of previously blocked JavaScripts through user consent for a <translation-key id="Service">Service</translation-key>, please insert the ensuing script tag into the field labeled <translation-key id="Opt-in-Code">Opt-in Code</translation-key> within your <translation-key id="Service">Service</translation-key>.',
                    'Backend / Script Blockers / Hint',
                    'borlabs-cookie',
                ),
                'status' => _x(
                    'The status of this <translation-key id="Script-Blocker">Script Blocker</translation-key>. If the <translation-key id="Script-Blocker">Script Blocker</translation-key> is disabled, it will not block JavaScript.',
                    'Backend / Script Blockers / Hint',
                    'borlabs-cookie',
                ),
            ],
        ];
    }
}
