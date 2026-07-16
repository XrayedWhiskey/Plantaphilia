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

namespace Borlabs\Cookie\Localization\LocalScanner;

use Borlabs\Cookie\Localization\LocalizationInterface;

use function Borlabs\Cookie\System\WordPressGlobalFunctions\_x;

/**
 * The **ScriptBlockerScanResultLocalizationStrings** class contains various localized strings.
 *
 * @see \Borlabs\Cookie\Localization\ScriptBlocker\ScriptBlockerScanResultLocalizationStrings::get()
 */
final class ScanResultLocalizationStrings implements LocalizationInterface
{
    /**
     * @return array<array<string>>
     */
    public static function get(): array
    {
        return [
            // Alert messages
            'alert' => [
                'noScanResult' => _x(
                    'No scan result available.',
                    'Backend / Local Scanner / Alert Message',
                    'borlabs-cookie',
                ),
            ],

            // Headlines
            'headline' => [
                'matchedHandles' => _x(
                    '<translation-key id="Matched-Handles">Matched Handles</translation-key>',
                    'Backend / Local Scanner / Headline',
                    'borlabs-cookie',
                ),
                'matchedTags' => _x(
                    '<translation-key id="Matched-Tags">Matched Tags</translation-key>',
                    'Backend / Local Scanner / Headline',
                    'borlabs-cookie',
                ),
                'unmatchedHandles' => _x(
                    '<translation-key id="Unmatched-Handles">Unmatched Handles</translation-key>',
                    'Backend / Local Scanner / Headline',
                    'borlabs-cookie',
                ),
                'unmatchedTags' => _x(
                    '<translation-key id="Unmatched-Tags">Unmatched Tags</translation-key>',
                    'Backend / Local Scanner / Headline',
                    'borlabs-cookie',
                ),
            ],

            // Hint
            'hint' => [
                'onExist' => _x(
                    'Specifies the name of the object that must be present for the script from the <strong>Tag</strong> column to be executed. Allowed characters include $, _, letters (both upper and lower case), and numbers. The first character must be $, _, or a letter, while subsequent characters can also include numbers.',
                    'Backend / Local Scanner / Hint',
                    'borlabs-cookie',
                ),
                'phrase' => _x(
                    'Enter a unique string found in the <strong>Tag</strong> to block the script. It should be unique enough that it is not found in other JavaScripts as well.',
                    'Backend / Local Scanner / Hint',
                    'borlabs-cookie',
                ),
            ],

            // Tables
            'table' => [
                'blockStatus' => _x(
                    'Block',
                    'Backend / Local Scanner / Table Headline',
                    'borlabs-cookie',
                ),
                'handle' => _x(
                    '<translation-key id="Handle">Handle</translation-key>',
                    'Backend / Local Scanner / Table Headline',
                    'borlabs-cookie',
                ),
                'onExist' => _x(
                    '<translation-key id="onExist">onExist</translation-key>',
                    'Backend / Local Scanner / Table Headline',
                    'borlabs-cookie',
                ),
                'phrase' => _x(
                    '<translation-key id="Phrase">Phrase</translation-key>',
                    'Backend / Local Scanner / Table Headline',
                    'borlabs-cookie',
                ),
                'tag' => _x(
                    'Tag',
                    'Backend / Local Scanner / Table Headline',
                    'borlabs-cookie',
                ),
                'type' => _x(
                    'Type',
                    'Backend / Local Scanner / Table Headline',
                    'borlabs-cookie',
                ),
                'url' => _x(
                    'URL',
                    'Backend / Local Scanner / Table Headline',
                    'borlabs-cookie',
                ),
            ],

            // Things to know
            'thingsToKnow' => [
                'handlesJavaScriptExplained' => _x(
                    '<translation-key id="Handles">Handles</translation-key> are used to identify the JavaScript files that are loaded on your website. For example, your installed <translation-key id="Themes">Themes</translation-key> and <translation-key id="Plugins">Plugins</translation-key> use the WordPress function <a class="brlbs-cmpnt-link brlbs-cmpnt-link-with-icon" href="https://developer.wordpress.org/reference/functions/wp_enqueue_script/" rel="nofollow noopener noreferrer" target="_blank"><strong><em>wp_enqueue_script()</em></strong><span class="brlbs-cmpnt-external-link-icon"></span></a> to define the <translation-key id="Handles">Handles</translation-key>.',
                    'Backend / Local Scanner / Headline',
                    'borlabs-cookie',
                ),
                'handlesStylesExplained' => _x(
                    '<translation-key id="Handles">Handles</translation-key> are used to identify the <translation-key id="CSS">CSS</translation-key> files that are loaded on your website. For example, your installed <translation-key id="Themes">Themes</translation-key> and <translation-key id="Plugins">Plugins</translation-key> use the WordPress function <a class="brlbs-cmpnt-link brlbs-cmpnt-link-with-icon" href="https://developer.wordpress.org/reference/functions/wp_enqueue_style/" rel="nofollow noopener noreferrer" target="_blank"><strong><em>wp_enqueue_style()</em></strong><span class="brlbs-cmpnt-external-link-icon"></span></a> to define the <translation-key id="Handles">Handles</translation-key>.',
                    'Backend / Local Scanner / Headline',
                    'borlabs-cookie',
                ),
                'handlesExplainedA' => _x(
                    '<translation-key id="Matched-Handles">Matched Handles</translation-key>:<br>These are <translation-key id="Handles">Handles</translation-key> that matches your <translation-key id="Search-Phrase">Search Phrase</translation-key>.',
                    'Backend / Local Scanner / Headline',
                    'borlabs-cookie',
                ),
                'handlesExplainedB' => _x(
                    '<translation-key id="Unmatched-Handles">Unmatched Handles</translation-key>:<br>These are <translation-key id="Handles">Handles</translation-key> that do not match a <translation-key id="Search-Phrase">Search Phrase</translation-key> but were found during the scan and maybe relevant.',
                    'Backend / Local Scanner / Headline',
                    'borlabs-cookie',
                ),
                'headlineHandlesExplained' => _x(
                    '<translation-key id="Handles">Handles</translation-key> explained',
                    'Backend / Local Scanner / Headline',
                    'borlabs-cookie',
                ),
                'headlineInputFieldsExplained' => _x(
                    'Input fields explained',
                    'Backend / Local Scanner / Things to know / Headline',
                    'borlabs-cookie',
                ),
                'headlineTagsExplained' => _x(
                    '<translation-key id="Tags">Tags</translation-key> explained',
                    'Backend / Local Scanner / Headline',
                    'borlabs-cookie',
                ),
                'handlesJavaScriptTagsExplainedA' => _x(
                    '<translation-key id="Tags">Tags</translation-key> refer to <strong><em>&lt;script&gt;</em></strong> tags that lack an associated <translation-key id="Handle">Handle</translation-key>, which can occur when JavaScript is embedded directly into your website\'s HTML code, bypassing the dedicated WordPress function typically used for this purpose.',
                    'Backend / Local Scanner / Headline',
                    'borlabs-cookie',
                ),
                'handlesStyleTagsExplainedA' => _x(
                    '<translation-key id="Tags">Tags</translation-key> refer to <strong><em>&lt;link&gt;</em></strong> tags and <strong><em>&lt;style&gt;</em></strong> tags that lack an associated <translation-key id="Handle">Handle</translation-key>, which can occur when <translation-key id="CSS">CSS</translation-key> is embedded directly into your website\'s HTML code, bypassing the dedicated WordPress function typically used for this purpose.',
                    'Backend / Local Scanner / Headline',
                    'borlabs-cookie',
                ),
                'handlesTagsExplainedA' => _x(
                    '<translation-key id="Matched-Tags">Matched Tags</translation-key>:<br>These are <translation-key id="Tags">Tags</translation-key> that matches your <translation-key id="Search-Phrase">Search Phrase</translation-key>.',
                    'Backend / Local Scanner / Headline',
                    'borlabs-cookie',
                ),
                'handlesTagsExplainedB' => _x(
                    '<translation-key id="Unmatched-Tags">Unmatched Tags</translation-key>:<br>These are <translation-key id="Tags">Tags</translation-key> that do not match a <translation-key id="Search-Phrase">Search Phrase</translation-key> but were found during the scan and maybe relevant.',
                    'Backend / Local Scanner / Headline',
                    'borlabs-cookie',
                ),
                'inputFieldsExplainedJavaScriptPhrase' => _x(
                    '<translation-key id="Phrase">Phrase</translation-key>:<br>Enter a unique string found in the <strong>Tag</strong> to block the script. It should be unique enough that it is not found in other JavaScripts as well.',
                    'Backend / Local Scanner / Headline',
                    'borlabs-cookie',
                ),
                'inputFieldsExplainedOnExistA' => _x(
                    '<translation-key id="onExist">onExist</translation-key>:<br>This feature is optional and ensures that once the JavaScript is unblocked, it does not execute until the object specified in the <translation-key id="onExist">onExist</translation-key> column is present on the website. This capability facilitates the resolution of race condition issues.',
                    'Backend / Local Scanner / Headline',
                    'borlabs-cookie',
                ),
                'inputFieldsExplainedOnExistB' => _x(
                    'Specifies the name of the object that must be present for the script from the <strong>Tag</strong> column to be executed. Allowed characters include $, _, letters (both upper and lower case), and numbers. The first character must be $, _, or a letter, while subsequent characters can also include numbers.',
                    'Backend / Local Scanner / Headline',
                    'borlabs-cookie',
                ),
                'inputFieldsExplainedOnExistExampleA' => _x(
                    'Example:',
                    'Backend / Local Scanner / Headline',
                    'borlabs-cookie',
                ),
                'inputFieldsExplainedOnExistExampleB' => _x(
                    'The example initiates loading an external library and concurrently calls the <strong><em>calendarLibrary.init()</em></strong> function. This could induce a race condition issue since, when <strong><em>calendarLibrary.init()</em></strong> is invoked, the <strong><em>calendar-library.js</em></strong> library—responsible for providing this method—might not yet be fully loaded.',
                    'Backend / Local Scanner / Headline',
                    'borlabs-cookie',
                ),
                'inputFieldsExplainedOnExistExampleC' => _x(
                    'To mitigate this race condition, block both the script tag that loads the <strong><em>calendar-library.js</em></strong> library and the script tag that calls <strong><em>calendarLibrary.init()</em></strong>. For the script tag where <strong><em>calendarLibrary.init()</em></strong> is called, enter <strong><em>calendarLibrary</em></strong> in the <translation-key id="onExist">onExist</translation-key> field.',
                    'Backend / Local Scanner / Headline',
                    'borlabs-cookie',
                ),
                'inputFieldsExplainedOnExistExampleD' => _x(
                    'If the <translation-key id="Script-Blocker">Script Blocker</translation-key> is now unblocked, the script-tag containing <strong><em>calendarLibrary.init()</em></strong> will not execute until the <strong><em>calendar-library.js</em></strong> has completely loaded.',
                    'Backend / Local Scanner / Headline',
                    'borlabs-cookie',
                ),
                'inputFieldsExplainedStylePhrase' => _x(
                    '<translation-key id="Phrase">Phrase</translation-key>:<br>Enter a unique string found in the <strong>Tag</strong> to block the <translation-key id="CSS">CSS</translation-key>. It should be unique enough that it is not found in other <strong><em>&lt;link&gt;</em></strong> tags or <strong><em>&lt;style&gt;</em></strong> tags as well.',
                    'Backend / Local Scanner / Headline',
                    'borlabs-cookie',
                ),
            ],

            // Validation
            'validation' => [
                'phraseDoesNotMatch' => _x(
                    'The phrase is not found in the JavaScript code on the left side.',
                    'Backend / Local Scanner / Validation',
                    'borlabs-cookie',
                ),
                'phraseIsNotUnique' => _x(
                    'The phrase is not unique and overlaps with a phrase for another JavaScript.',
                    'Backend / Local Scanner / Validation',
                    'borlabs-cookie',
                ),
            ],
        ];
    }
}
