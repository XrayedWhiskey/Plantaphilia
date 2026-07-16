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
 * The **StyleBlockerOverviewLocalizationStrings** class contains various localized strings.
 *
 * @see \Borlabs\Cookie\Localization\StyleBlocker\StyleBlockerOverviewLocalizationStrings::get()
 */
final class StyleBlockerOverviewLocalizationStrings implements LocalizationInterface
{
    /**
     * @return array<array<string>>
     */
    public static function get(): array
    {
        return [
            // Alert messages
            'alert' => [
                'noStyleBlockerConfigured' => _x(
                    'No <translation-key id="Style-Blocker">Style Blocker</translation-key> configured.',
                    'Backend / Style Blockers / Alert Message',
                    'borlabs-cookie',
                ),
            ],

            // Breadcrumbs
            'breadcrumb' => [
                'module' => _x(
                    'Style Blockers',
                    'Backend / Style Blockers / Breadcrumb',
                    'borlabs-cookie',
                ),
            ],

            // Headlines
            'headline' => [
                'styleBlockers' => _x(
                    'Style Blockers',
                    'Backend / Style Blockers / Headline',
                    'borlabs-cookie',
                ),
            ],

            // Hint
            'hint' => [
            ],

            // Tables
            'table' => [
                'handles' => _x(
                    'Handles',
                    'Backend / Style Blockers / Table Headline',
                    'borlabs-cookie',
                ),
                'id' => _x(
                    'ID',
                    'Backend / Style Blockers / Table Headline',
                    'borlabs-cookie',
                ),
                'name' => _x(
                    'Name',
                    'Backend / Style Blockers / Table Headline',
                    'borlabs-cookie',
                ),
                'phrases' => _x(
                    'Phrases',
                    'Backend / Style Blockers / Table Headline',
                    'borlabs-cookie',
                ),
                'status' => _x(
                    'Status',
                    'Backend / Style Blockers / Table Headline',
                    'borlabs-cookie',
                ),
            ],

            // Things to know
            'thingsToKnow' => [
                'headlinePurposeStyleBlockers' => _x(
                    'What is the purpose of the <translation-key id="Style-Blockers">Style Blockers</translation-key> section?',
                    'Backend / Style Blockers / Things to know / Headline',
                    'borlabs-cookie',
                ),
                'headlineSymbolsExplained' => _x(
                    'Symbols explained',
                    'Backend / Style Blockers / Things to know / Headline',
                    'borlabs-cookie',
                ),
                'languageIndependent' => _x(
                    'Language independent',
                    'Backend / Style Blockers / Tip / Headline',
                    'borlabs-cookie',
                ),
                'languageIndependentExplained' => _x(
                    '<translation-key id="Style-Blockers">Style Blockers</translation-key> are configured independently for all languages.',
                    'Backend / Style Blockers / Tip / Text',
                    'borlabs-cookie',
                ),
                'purposeStyleBlockersA' => _x(
                    'The <translation-key id="Style-Blockers">Style Blockers</translation-key> section allows you to block <translation-key id="CSS">CSS</translation-key> from <translation-key id="Plugins">Plugins</translation-key> that, for example, embed <translation-key id="CSS">CSS</translation-key> to load external fonts like <translation-key id="Google-Fonts">Google Fonts</translation-key>.',
                    'Backend / Style Blockers / Things to know / Text',
                    'borlabs-cookie',
                ),
                'purposeStyleBlockersB' => _x(
                    'Once a <translation-key id="Style-Blocker">Style Blocker</translation-key> is created, <translation-key id="Borlabs-Cookie">Borlabs Cookie</translation-key> provides an unblock JavaScript that can be entered into the <translation-key id="Opt-in-Code">Opt-in Code</translation-key> field of a <translation-key id="Service">Service</translation-key>. The <translation-key id="CSS">CSS</translation-key> becomes unblocked once a visitor consents to the <translation-key id="Service">Service</translation-key>.',
                    'Backend / Style Blockers / Things to know / Text',
                    'borlabs-cookie',
                ),
                'symbolExplainedActive' => _x(
                    'The <translation-key id="Style-Blocker">Style Blocker</translation-key> is enabled and actively prevents <translation-key id="CSS">CSS</translation-key> execution if the <translation-key id="Handles">Handles</translation-key> or <translation-key id="Phrases">Phrases</translation-key> matches a term configured in the system.',
                    'Backend / Content Blockers / Things to know / Text',
                    'borlabs-cookie',
                ),
                'symbolExplainedDelete' => _x(
                    'Delete the <translation-key id="Style-Blocker">Style Blocker</translation-key>. Not available for a <translation-key id="Style-Blocker">Style Blocker</translation-key> installed via the <translation-key id="Navigation-Library">Library</translation-key>.',
                    'Backend / Content Blockers / Things to know / Text',
                    'borlabs-cookie',
                ),
                'symbolExplainedEdit' => _x(
                    'Edit the <translation-key id="Style-Blocker">Style Blocker</translation-key>.',
                    'Backend / Content Blockers / Things to know / Text',
                    'borlabs-cookie',
                ),
                'symbolExplainedInactive' => _x(
                    'The <translation-key id="Style-Blocker">Style Blocker</translation-key> is inactive, allowing all <translation-key id="CSS">CSS</translation-key> are executed without filtering or restricting based on <translation-key id="Handles">Handles</translation-key> or <translation-key id="Phrases">Phrases</translation-key>.',
                    'Backend / Content Blockers / Things to know / Text',
                    'borlabs-cookie',
                ),
            ],
        ];
    }
}
