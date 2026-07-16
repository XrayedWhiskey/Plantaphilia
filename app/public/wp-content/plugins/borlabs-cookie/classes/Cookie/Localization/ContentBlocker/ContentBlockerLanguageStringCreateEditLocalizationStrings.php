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
 * The **ServiceCreateLocalizationStrings** class contains various localized strings.
 */
final class ContentBlockerLanguageStringCreateEditLocalizationStrings implements LocalizationInterface
{
    /**
     * @return array<array<string>>
     */
    public static function get(): array
    {
        return [
            // Alert messages
            'alert' => [
                'noLanguageString' => _x(
                    'No <translation-key id="Text-Placeholder">Text Placeholder</translation-key> configured.',
                    'Backend / Content Blocker Language String / Alert Message',
                    'borlabs-cookie',
                ),
            ],

            // Buttons
            'button' => [
                'addLanguageString' => _x(
                    'Add Text Placeholder',
                    'Backend / Content Blocker Language String / Button',
                    'borlabs-cookie',
                ),
            ],

            // Fields
            'field' => [
                'key' => _x(
                    'Key',
                    'Backend / Content Blocker Host / Label',
                    'borlabs-cookie',
                ),
                'text' => _x(
                    'Text',
                    'Backend / Content Blocker Host / Label',
                    'borlabs-cookie',
                ),
            ],

            // Headlines
            'headline' => [
                'languageStrings' => _x(
                    '<translation-key id="Text-Placeholder">Text Placeholder</translation-key>',
                    'Backend / Content Blocker Language String / Headline',
                    'borlabs-cookie',
                ),
            ],

            // Tables
            'table' => [
                'key' => _x(
                    '<translation-key id="Key">Key</translation-key>',
                    'Backend / Content Blocker Language String / Table Headline',
                    'borlabs-cookie',
                ),
                'text' => _x(
                    '<translation-key id="Text">Text</translation-key>',
                    'Backend / Content Blocker Language String / Table Headline',
                    'borlabs-cookie',
                ),
            ],

            // Things to know
            'thingsToKnow' => [
                'headlinePurposeLanguageStrings' => _x(
                    'What is the purpose of the <translation-key id="Text-Placeholder">Text Placeholder</translation-key> section?',
                    'Backend / Content Blocker Language String / Things to know / Headline',
                    'borlabs-cookie',
                ),
                'purposeLanguageStringsA' => _x(
                    '<translation-key id="Text-Placeholder">Text Placeholder</translation-key> facilitates the creation of text placeholders for your <translation-key id="Content-Blocker">Content Blocker</translation-key>, streamlining your text adjustment process.',
                    'Backend / Content Blocker Language String / Things to know / Text',
                    'borlabs-cookie',
                ),
                'purposeLanguageStringsB' => _x(
                    'The <translation-key id="Key">Key</translation-key> you specify can be placed in your <translation-key id="Preview-Blocked-Content">Preview Blocked Content</translation-key> &raquo; <translation-key id="Preview-Blocked-Content-HTML">HTML</translation-key> code using the <strong><em>{{ yourKey }}</em></strong> variable. It will be automatically replaced by the text specified in the <translation-key id="Text">Text</translation-key> field when the content is blocked.',
                    'Backend / Content Blocker Language String / Things to know / Text',
                    'borlabs-cookie',
                ),
            ],
        ];
    }
}
