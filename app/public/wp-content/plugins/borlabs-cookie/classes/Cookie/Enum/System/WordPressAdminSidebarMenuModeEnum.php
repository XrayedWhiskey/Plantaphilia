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
 * @method static WordPressAdminSidebarMenuModeEnum EXPANDED()
 * @method static WordPressAdminSidebarMenuModeEnum SIMPLIFIED()
 * @method static WordPressAdminSidebarMenuModeEnum STANDARD()
 */
class WordPressAdminSidebarMenuModeEnum extends AbstractEnum implements LocalizedEnumInterface
{
    public const EXPANDED = 'expanded';

    public const SIMPLIFIED = 'simplified';

    public const STANDARD = 'standard'; // Default

    public static function localized(): array
    {
        return [
            self::EXPANDED => _x('c) <translation-key id="WordPress-Admin-Sidebar-Menu-Mode-Expanded">Expanded</translation-key>', 'Backend / Sidebar Navigation Mode / Option', 'borlabs-cookie'),
            self::STANDARD => _x('b) <translation-key id="WordPress-Admin-Sidebar-Menu-Mode-Standard">Standard</translation-key>', 'Backend / Sidebar Navigation Mode / Option', 'borlabs-cookie'),
            self::SIMPLIFIED => _x('a) <translation-key id="WordPress-Admin-Sidebar-Menu-Mode-Simplified">Simplified</translation-key>', 'Backend / Sidebar Navigation Mode / Option', 'borlabs-cookie'),
        ];
    }
}
