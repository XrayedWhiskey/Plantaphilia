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
 * @see \Borlabs\Cookie\Localization\ContentBlocker\ContentBlockerOverviewLocalizationStrings::get()
 */
final class ContentBlockerOverviewLocalizationStrings implements LocalizationInterface
{
    /**
     * @return array<array<string>>
     */
    public static function get(): array
    {
        return [
            // Alert messages
            'alert' => [
                'noContentBlockerConfigured' => _x(
                    'No <translation-key id="Content-Blocker">Content Blocker</translation-key> configured.',
                    'Backend / Content Blockers / Alert Message',
                    'borlabs-cookie',
                ),
            ],

            // Breadcrumbs
            'breadcrumb' => [
                'module' => _x(
                    'Content Blockers',
                    'Backend / Content Blockers / Breadcrumb',
                    'borlabs-cookie',
                ),
            ],

            // Headlines
            'headline' => [
                'contentBlockers' => _x(
                    'Content Blockers',
                    'Backend / Content Blockers / Headline',
                    'borlabs-cookie',
                ),
                'reset' => _x(
                    'Reset Content Blockers',
                    'Backend / Content Blockers / Headline',
                    'borlabs-cookie',
                ),
            ],

            // Hint
            'hint' => [
                'defaultContentBlocker' => _x(
                    'The <translation-key id="ContentBlockerDefault">Content Blocker: <em>Default</em></translation-key> is used for all external contents, for which no own <translation-key id="Content-Blocker">Content Blocker</translation-key> was created.',
                    'Backend / Content Blockers / Hint',
                    'borlabs-cookie',
                ),
                'reset' => _x(
                    'Please confirm that you want to reset all <translation-key id="Content-Blocker">Content Blocker</translation-key> settings. They will be reset to their default settings. Your custom <translation-key id="Content-Blockers">Content Blockers</translation-key> and the <translation-key id="Content-Blockers">Content Blockers</translation-key> installed via the <translation-key id="Navigation-Library">Library</translation-key> are not reset.',
                    'Backend / Content Blockers / Hint',
                    'borlabs-cookie',
                ),
            ],

            // Tables
            'table' => [
                'hosts' => _x(
                    'Host(s)',
                    'Backend / Content Blockers / Table Headline',
                    'borlabs-cookie',
                ),
                'id' => _x(
                    'ID',
                    'Backend / Content Blockers / Table Headline',
                    'borlabs-cookie',
                ),
                'name' => _x(
                    'Name',
                    'Backend / Content Blockers / Table Headline',
                    'borlabs-cookie',
                ),
                'status' => _x(
                    'Status',
                    'Backend / Content Blockers / Table Headline',
                    'borlabs-cookie',
                ),
            ],

            // Things to know
            'thingsToKnow' => [
                'headlineSymbolsExplained' => _x(
                    'Symbols explained',
                    'Backend / Content Blockers / Things to know / Headline',
                    'borlabs-cookie',
                ),
                'headlinePurposeContentBlockers' => _x(
                    'What is the purpose of the <translation-key id="Content-Blockers">Content Blockers</translation-key> section?',
                    'Backend / Content Blockers / Things to know / Headline',
                    'borlabs-cookie',
                ),
                'purposeContentBlockersA' => _x(
                    'Using <translation-key id="Content-Blockers">Content Blockers</translation-key>, you can automatically block iframes such as videos from <strong>YouTube</strong> or <translation-key id="oEmbeds">oEmbeds</translation-key> such as posts from <strong>Pinterest</strong>. Your visitor sees a message that a content has been blocked and has the possibility to reload this content by clicking on it. You can customize the text and design of the block message to suit your needs and theme.',
                    'Backend / Content Blockers / Things to know / Text',
                    'borlabs-cookie',
                ),
                'purposeContentBlockersB' => _x(
                    'General settings can be found under <translation-key id="Navigation-Blockers">Blockers</translation-key> &raquo; <translation-key id="Navigation-Blockers-Content-Blockers">Content Blockers</translation-key> &raquo; <translation-key id="Navigation-Blockers-Content-Blockers-Settings">Settings</translation-key>.',
                    'Backend / Content Blockers / Things to know / Text',
                    'borlabs-cookie',
                ),
                'purposeContentBlockersC' => _x(
                    'The visual appearance (colors, borders, etc.) can be customized under <translation-key id="Navigation-Blockers">Blockers</translation-key> &raquo; <translation-key id="Navigation-Blockers-Content-Blockers">Content Blockers</translation-key> &raquo; <translation-key id="Navigation-Blockers-Content-Blockers-Appearance">Appearance</translation-key>.',
                    'Backend / Content Blockers / Things to know / Text',
                    'borlabs-cookie',
                ),
                'symbolExplainedActive' => _x(
                    'The <translation-key id="Content-Blocker">Content Blocker</translation-key> is active and does block content (iframes) of the configured hosts.',
                    'Backend / Content Blockers / Things to know / Text',
                    'borlabs-cookie',
                ),
                'symbolExplainedDelete' => _x(
                    'Delete the <translation-key id="Content-Blocker">Content Blocker</translation-key>. Not available for <translation-key id="Content-Blocker">Content Blocker</translation-key>: <translation-key id="Content-Blocker-Default">Default</translation-key> or a <translation-key id="Content-Blocker">Content Blocker</translation-key> installed via the <translation-key id="Navigation-Library">Library</translation-key>.',
                    'Backend / Content Blockers / Things to know / Text',
                    'borlabs-cookie',
                ),
                'symbolExplainedEdit' => _x(
                    'Edit the <translation-key id="Content-Blocker">Content Blocker</translation-key>.',
                    'Backend / Content Blockers / Things to know / Text',
                    'borlabs-cookie',
                ),
                'symbolExplainedInactive' => _x(
                    'The <translation-key id="Content-Blocker">Content Blocker</translation-key> is inactive is and does not block content (iframes) of the configured hosts.',
                    'Backend / Content Blockers / Things to know / Text',
                    'borlabs-cookie',
                ),
            ],
        ];
    }
}
