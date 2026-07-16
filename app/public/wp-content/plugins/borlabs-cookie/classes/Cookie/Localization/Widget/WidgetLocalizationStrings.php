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

namespace Borlabs\Cookie\Localization\Widget;

use Borlabs\Cookie\Localization\LocalizationInterface;

use function Borlabs\Cookie\System\WordPressGlobalFunctions\_x;

/**
 * The **WidgetLocalizationStrings** class contains various localized strings.
 *
 * @see \Borlabs\Cookie\Localization\Widget\WidgetLocalizationStrings::get()
 */
final class WidgetLocalizationStrings implements LocalizationInterface
{
    /**
     * @return array<array<string>>
     */
    public static function get(): array
    {
        return [
            // Alert messages
            'alert' => [
                'settingNotAvailableBecauseIabTcfIsEnabled' => _x(
                    'The <translation-key id="IAB-TCF-Status">IAB TCF Status</translation-key> option is currently active, located under <em><translation-key id="Navigation-Consent-Management">Consent Management</translation-key> &raquo; <translation-key id="Navigation-Iab-Tcf">IAB TCF</translation-key> &raquo; <translation-key id="Navigation-Iab-Tcf-Settings">Settings</translation-key></em>. Therefore, modifications to this setting are not permitted.',
                    'Backend / Content Blocker Language String / Alert Message',
                    'borlabs-cookie',
                ),
            ],

            // Breadcrumbs
            'breadcrumb' => [
                'module' => _x(
                    'Widget',
                    'Backend / Widget / Breadcrumb',
                    'borlabs-cookie',
                ),
            ],

            // Fields
            'field' => [
                'color' => _x(
                    'Color',
                    'Backend / Widget / Label',
                    'borlabs-cookie',
                ),
                'icon' => _x(
                    'Icon',
                    'Backend / Widget / Label',
                    'borlabs-cookie',
                ),
                'position' => _x(
                    'Position',
                    'Backend / Widget / Label',
                    'borlabs-cookie',
                ),
                'show' => _x(
                    '<translation-key id="Show-Widget">Show Widget</translation-key>',
                    'Backend / Widget / Label',
                    'borlabs-cookie',
                ),
            ],

            // Headlines
            'headline' => [
                'resetWidgetSettings' => _x(
                    'Reset Widget Settings',
                    'Backend / Cookie Box / Headline',
                    'borlabs-cookie',
                ),
                'widgetSettings' => _x(
                    'Widget Settings',
                    'Backend / Widget / Headline',
                    'borlabs-cookie',
                ),
            ],

            // Hint
            'hint' => [
                'resetSettings' => _x(
                    'Please confirm that you want to reset all <translation-key id="Widget">Widget</translation-key> settings. They will be reset to their default settings.',
                    'Backend / Widget / Hint',
                    'borlabs-cookie',
                ),
            ],

            // Options (select | checkbox | radio)
            'option' => [
                'positionBottomLeft' => _x(
                    'Bottom Left',
                    'Backend / Widget / Select Option',
                    'borlabs-cookie',
                ),
                'positionBottomRight' => _x(
                    'Bottom Right',
                    'Backend / Widget / Select Option',
                    'borlabs-cookie',
                ),
            ],

            // Placeholder
            'placeholder' => [
            ],

            // Tables
            'table' => [
            ],

            // Things to know
            'thingsToKnow' => [
            ],
        ];
    }
}
