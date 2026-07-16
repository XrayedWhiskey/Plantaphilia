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
 * @see \Borlabs\Cookie\Localization\ContentBlocker\ContentBlockerAppearanceLocalizationStrings::get()
 */
final class ContentBlockerAppearanceLocalizationStrings implements LocalizationInterface
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
                    'Content Blocker - Appearance',
                    'Backend / Content Blockers Appearance / Breadcrumb',
                    'borlabs-cookie',
                ),
            ],

            // Fields
            'field' => [
                'backgroundColor' => _x(
                    '<translation-key id="Background-Color">Background Color</translation-key>',
                    'Backend / Content Blockers Appearance / Label',
                    'borlabs-cookie',
                ),
                'borderRadius' => _x(
                    'Border Radius',
                    'Backend / Content Blockers Appearance / Label',
                    'borlabs-cookie',
                ),
                'bottomLeft' => _x(
                    'Bottom Left',
                    'Backend / Content Blockers Appearance / Label',
                    'borlabs-cookie',
                ),
                'bottomRight' => _x(
                    'Bottom Right',
                    'Backend / Content Blockers Appearance / Label',
                    'borlabs-cookie',
                ),
                'button' => _x(
                    'Button',
                    'Backend / Content Blockers Appearance / Label',
                    'borlabs-cookie',
                ),
                'buttonBorderRadius' => _x(
                    'Button Border Radius',
                    'Backend / Dialog Appearance / Label',
                    'borlabs-cookie',
                ),
                'buttonColor' => _x(
                    'Button Color',
                    'Backend / Content Blockers Appearance / Label',
                    'borlabs-cookie',
                ),
                'buttonTextColor' => _x(
                    'Button Text Color',
                    'Backend / Content Blockers Appearance / Label',
                    'borlabs-cookie',
                ),
                'contentBlocker' => _x(
                    'Content Blocker',
                    'Backend / Content Blockers Appearance / Label',
                    'borlabs-cookie',
                ),
                'default' => _x(
                    'Default',
                    'Backend / Content Blockers Appearance / Label',
                    'borlabs-cookie',
                ),
                'fontFamily' => _x(
                    'Font Family',
                    'Backend / Content Blockers Appearance / Label',
                    'borlabs-cookie',
                ),
                'fontSize' => _x(
                    '<translation-key id="Font-Size-Base">Font Size (Base)</translation-key>',
                    'Backend / Content Blockers Appearance / Label',
                    'borlabs-cookie',
                ),
                'hover' => _x(
                    'Hover',
                    'Backend / Content Blockers Appearance / Label',
                    'borlabs-cookie',
                ),
                'linkColor' => _x(
                    'Link Color',
                    'Backend / Content Blockers Appearance / Label',
                    'borlabs-cookie',
                ),
                'opacity' => _x(
                    'Opacity',
                    'Backend / Content Blockers Appearance / Label',
                    'borlabs-cookie',
                ),
                'separatorColor' => _x(
                    'Separator Color',
                    'Backend / Content Blockers Appearance / Label',
                    'borlabs-cookie',
                ),
                'separatorWidth' => _x(
                    'Separator Width',
                    'Backend / Content Blockers Appearance / Label',
                    'borlabs-cookie',
                ),
                'textColor' => _x(
                    'Text Color',
                    'Backend / Content Blockers Appearance / Label',
                    'borlabs-cookie',
                ),
                'topLeft' => _x(
                    'Top Left',
                    'Backend / Content Blockers Appearance / Label',
                    'borlabs-cookie',
                ),
                'topRight' => _x(
                    'Top Right',
                    'Backend / Content Blockers Appearance / Label',
                    'borlabs-cookie',
                ),
                'useIndividualStyles' => _x(
                    '<translation-key id="Use-Individual-Content-Blocker-Styles">Use Individual Content Blocker Styles</translation-key>',
                    'Backend / Content Blockers Appearance / Label',
                    'borlabs-cookie',
                ),
            ],

            // Headlines
            'headline' => [
                'appearanceSettings' => _x(
                    'Appearance Settings',
                    'Backend / Content Blockers Appearance / Headline',
                    'borlabs-cookie',
                ),
                'reset' => _x(
                    'Reset Content Blocker Settings',
                    'Backend / Content Blockers Appearance / Headline',
                    'borlabs-cookie',
                ),
            ],

            // Hint
            'hint' => [
                'backgroundOpacity' => _x(
                    'Defines the visibility of the <translation-key id="Background-Color">Background Color</translation-key>.',
                    'Backend / Content Blockers Appearance / Hint',
                    'borlabs-cookie',
                ),
                'fontFamily' => _x(
                    'Choose which font you want to use. Your themes font is the default setting.',
                    'Backend / Content Blockers Appearance / Hint',
                    'borlabs-cookie',
                ),
                'fontSize' => _x(
                    'Based on the base font size, the font size of all elements are automatically adjusted proportionally.',
                    'Backend / Content Blockers Appearance / Hint',
                    'borlabs-cookie',
                ),
                'reset' => _x(
                    'Please confirm that you want to reset all <translation-key id="Content-Blockers">Content Blockers</translation-key> settings. They will be reset to their default settings.',
                    'Backend / Content Blockers Appearance / Hint',
                    'borlabs-cookie',
                ),
                'useIndividualStyles' => _x(
                    '<translation-key id="Content-Blockers">Content Blockers</translation-key> installed from the <translation-key id="Navigation-Library">Library</translation-key> will use their own color settings instead of the global color settings.',
                    'Backend / Content Blockers Appearance / Hint',
                    'borlabs-cookie',
                ),
            ],

            // Placeholder
            'placeholder' => [
                'fontFamily' => _x(
                    'Enter custom font family',
                    'Backend / Content Blockers Appearance / Input Placeholder',
                    'borlabs-cookie',
                ),
            ],

            // Things to know
            'thingsToKnow' => [
                'headlineIndividualSettings' => _x(
                    '<translation-key id="Content-Blocker">Content Blocker</translation-key> Individual Settings',
                    'Backend / Content Blockers Appearance / Things to know / Headline',
                    'borlabs-cookie',
                ),
                'individualSettingsA' => _x(
                    'Please note that the settings for <translation-key id="Button-Radius">Button Radius</translation-key>, <translation-key id="Button-Color">Button Color</translation-key>, and <translation-key id="Button-Text-Color">Button Text Color</translation-key> are often overwritten with the individual settings of a <translation-key id="Content-Blocker">Content Blocker</translation-key>.',
                    'Backend / Content Blockers Appearance / Things to know / Text',
                    'borlabs-cookie',
                ),
                'individualSettingsB' => _x(
                    'You can disable this behavior with the <translation-key id="Use-Individual-Content-Blocker-Styles">Use Individual Content Blocker Styles</translation-key> option.',
                    'Backend / Content Blockers Appearance / Things to know / Text',
                    'borlabs-cookie',
                ),
            ],
        ];
    }
}
