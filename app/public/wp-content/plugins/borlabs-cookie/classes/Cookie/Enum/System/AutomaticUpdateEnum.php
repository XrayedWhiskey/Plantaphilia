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
 * @method static AutomaticUpdateEnum AUTO_UPDATE_ALL()
 * @method static AutomaticUpdateEnum AUTO_UPDATE_MINOR()
 * @method static AutomaticUpdateEnum AUTO_UPDATE_NONE()
 * @method static AutomaticUpdateEnum AUTO_UPDATE_PATCH()
 */
class AutomaticUpdateEnum extends AbstractEnum implements LocalizedEnumInterface
{
    public const AUTO_UPDATE_ALL = 'auto-update-all';

    public const AUTO_UPDATE_MINOR = 'auto-update-minor';

    public const AUTO_UPDATE_NONE = 'auto-update-none';

    public const AUTO_UPDATE_PATCH = 'auto-update-patch';

    public static function localized(): array
    {
        return [
            self::AUTO_UPDATE_ALL => _x('a) <translation-key id="All-versions">All versions</translation-key> (recommended)', 'Backend / Plugin Update / Option', 'borlabs-cookie'),
            self::AUTO_UPDATE_MINOR => _x('b) <translation-key id="Minor-versions">Minor versions</translation-key>', 'Backend / Plugin Update / Option', 'borlabs-cookie'),
            self::AUTO_UPDATE_NONE => _x('d) <translation-key id="No-automatic-update">No automatic update</translation-key>', 'Backend / Plugin Update / Option', 'borlabs-cookie'),
            self::AUTO_UPDATE_PATCH => _x('c) <translation-key id="Patch-versions">Patch versions</translation-key>', 'Backend / Plugin Update / Option', 'borlabs-cookie'),
        ];
    }
}
