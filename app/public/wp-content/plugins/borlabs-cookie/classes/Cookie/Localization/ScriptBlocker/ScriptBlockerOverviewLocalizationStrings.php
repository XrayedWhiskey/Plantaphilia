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
 * The **ScriptBlockerOverviewLocalizationStrings** class contains various localized strings.
 *
 * @see \Borlabs\Cookie\Localization\ScriptBlocker\ScriptBlockerOverviewLocalizationStrings::get()
 */
final class ScriptBlockerOverviewLocalizationStrings implements LocalizationInterface
{
    /**
     * @return array<array<string>>
     */
    public static function get(): array
    {
        return [
            // Alert messages
            'alert' => [
                'noScriptBlockerConfigured' => _x(
                    'No <translation-key id="Script-Blocker">Script Blocker</translation-key> configured.',
                    'Backend / Script Blockers / Alert Message',
                    'borlabs-cookie',
                ),
            ],

            // Breadcrumbs
            'breadcrumb' => [
                'module' => _x(
                    'Script Blockers',
                    'Backend / Script Blockers / Breadcrumb',
                    'borlabs-cookie',
                ),
            ],

            // Headlines
            'headline' => [
                'scriptBlockers' => _x(
                    'Script Blockers',
                    'Backend / Script Blockers / Headline',
                    'borlabs-cookie',
                ),
            ],

            // Hint
            'hint' => [
            ],

            // Tables
            'table' => [
                'handles' => _x(
                    '<translation-key id="Handles">Handles</translation-key>',
                    'Backend / Script Blockers / Table Headline',
                    'borlabs-cookie',
                ),
                'id' => _x(
                    'ID',
                    'Backend / Script Blockers / Table Headline',
                    'borlabs-cookie',
                ),
                'name' => _x(
                    'Name',
                    'Backend / Script Blockers / Table Headline',
                    'borlabs-cookie',
                ),
                'onExist' => _x(
                    '<translation-key id="On-Exist">onExist</translation-key>',
                    'Backend / Script Blockers / Table Headline',
                    'borlabs-cookie',
                ),
                'phrases' => _x(
                    '<translation-key id="Phrases">Phrases</translation-key>',
                    'Backend / Script Blockers / Table Headline',
                    'borlabs-cookie',
                ),
                'status' => _x(
                    'Status',
                    'Backend / Script Blockers / Table Headline',
                    'borlabs-cookie',
                ),
            ],

            // Things to know
            'thingsToKnow' => [
                'headlinePurposeScriptBlockers' => _x(
                    'What is the purpose of the <translation-key id="Script-Blockers">Script Blockers</translation-key> section?',
                    'Backend / Script Blockers / Things to know / Headline',
                    'borlabs-cookie',
                ),
                'headlineSymbolsExplained' => _x(
                    'Symbols explained',
                    'Backend / Script Blockers / Things to know / Headline',
                    'borlabs-cookie',
                ),
                'languageIndependent' => _x(
                    'Language independent',
                    'Backend / Script Blockers / Tip / Headline',
                    'borlabs-cookie',
                ),
                'languageIndependentExplained' => _x(
                    '<translation-key id="Script-Blockers">Script Blockers</translation-key> are configured independently for all languages.',
                    'Backend / Script Blockers / Tip / Text',
                    'borlabs-cookie',
                ),
                'purposeScriptBlockersA' => _x(
                    'The <translation-key id="Script-Blockers">Script Blockers</translation-key> section allows you to block JavaScripts from <translation-key id="Plugins">Plugins</translation-key> whose JavaScript you can\'t enter into the <translation-key id="Opt-in-Code">Opt-in Code</translation-key> field of a <translation-key id="Service">Service</translation-key>.',
                    'Backend / Script Blockers / Things to know / Text',
                    'borlabs-cookie',
                ),
                'purposeScriptBlockersB' => _x(
                    'Once a <translation-key id="Script-Blocker">Script Blocker</translation-key> is created, <translation-key id="Borlabs-Cookie">Borlabs Cookie</translation-key> provides an unblock JavaScript that can be entered into the <translation-key id="Opt-in-Code">Opt-in Code</translation-key> field of a <translation-key id="Service">Service</translation-key>. The JavaScript becomes unblocked once a visitor consents to the <translation-key id="Service">Service</translation-key>.',
                    'Backend / Script Blockers / Things to know / Text',
                    'borlabs-cookie',
                ),
                'purposeScriptBlockersC' => _x(
                    'This usually allows you to continue using your favorite plugins, but their JavaScript will only run when a visitor gave consent.',
                    'Backend / Script Blockers / Things to know / Text',
                    'borlabs-cookie',
                ),
                'symbolExplainedActive' => _x(
                    'The <translation-key id="Script-Blocker">Script Blocker</translation-key> is enabled and actively prevents JavaScript execution if the <translation-key id="Handles">Handles</translation-key> or <translation-key id="Phrases">Phrases</translation-key> matches a term configured in the system.',
                    'Backend / Content Blockers / Things to know / Text',
                    'borlabs-cookie',
                ),
                'symbolExplainedDelete' => _x(
                    'Delete the <translation-key id="Script-Blocker">Script Blocker</translation-key>. Not available for a <translation-key id="Script-Blocker">Script Blocker</translation-key> installed via the <translation-key id="Navigation-Library">Library</translation-key>.',
                    'Backend / Content Blockers / Things to know / Text',
                    'borlabs-cookie',
                ),
                'symbolExplainedEdit' => _x(
                    'Edit the <translation-key id="Script-Blocker">Script Blocker</translation-key>.',
                    'Backend / Content Blockers / Things to know / Text',
                    'borlabs-cookie',
                ),
                'symbolExplainedInactive' => _x(
                    'The <translation-key id="Script-Blocker">Script Blocker</translation-key> is inactive, allowing all JavaScript to execute without filtering or restricting based on <translation-key id="Handles">Handles</translation-key> or <translation-key id="Phrases">Phrases</translation-key>.',
                    'Backend / Content Blockers / Things to know / Text',
                    'borlabs-cookie',
                ),
            ],
        ];
    }
}
