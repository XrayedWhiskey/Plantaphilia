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

namespace Borlabs\Cookie\Enum\System;

use Borlabs\Cookie\Enum\AbstractEnum;
use Borlabs\Cookie\Enum\LocalizedEnumInterface;

/**
 * @method static DisplayModeSettingsEnum SIMPLIFIED()
 * @method static DisplayModeSettingsEnum STANDARD()
 */
class DisplayModeSettingsEnum extends AbstractEnum implements LocalizedEnumInterface
{
    public const SIMPLIFIED = 'simplified';

    public const STANDARD = 'standard'; // Default

    public static function localized(): array
    {
        return [
            self::SIMPLIFIED => _x('a) <translation-key id="Display-Mode-Settings-Simplified">Simplified</translation-key>', 'Backend / Display Mode Settings / Option', 'borlabs-cookie'),
            self::STANDARD => _x('b) <translation-key id="Display-Mode-Settings-Standard">Standard</translation-key>', 'Backend / Display Mode Settings / Option', 'borlabs-cookie'),
        ];
    }
}
