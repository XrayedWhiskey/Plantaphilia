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

namespace Borlabs\Cookie\Localization\System;

use Borlabs\Cookie\Localization\LocalizationInterface;

use function Borlabs\Cookie\System\WordPressGlobalFunctions\_x;

final class DisplayModeSettingsToggleLocalizationStrings implements LocalizationInterface
{
    /**
     * @return array<array<string>>
     */
    public static function get(): array
    {
        return [
            // Alert messages
            'alert' => [
                'displayModeSettingsIsSetToSimplified' => _x(
                    'You\'re currently seeing fewer options because the <translation-key id="Display-Mode-Settings">Display Mode of Settings</translation-key> is set to <translation-key id="Display-Mode-Settings-Simplified">Simplified</translation-key>.<br>To view all options, temporarily switch to <translation-key id="Display-Mode-Settings-Standard">Standard</translation-key> by turning off the toggle on the left, or permanently change it under <translation-key id="Navigation-Settings">Settings</translation-key> &raquo; <translation-key id="Navigation-Settings-Admin-Plugin-Settings">Admin &amp; Plugin Settings</translation-key>.',
                    'Backend / Display Mode Settings Toggle / Alert Message',
                    'borlabs-cookie',
                ),
            ],

            // Fields
            'field' => [
                'displayModeSettingsToggle' => _x(
                    'Display Mode Settings',
                    'Backend / Display Mode Settings Toggle / Label',
                    'borlabs-cookie',
                ),
            ],
        ];
    }
}
