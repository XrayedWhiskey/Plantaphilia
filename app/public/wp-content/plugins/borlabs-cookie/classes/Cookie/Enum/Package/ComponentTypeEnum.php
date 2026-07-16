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

namespace Borlabs\Cookie\Enum\Package;

use Borlabs\Cookie\Enum\AbstractEnum;
use Borlabs\Cookie\Enum\LocalizedEnumInterface;

/**
 * @method static ComponentTypeEnum COMPATIBILITY_PATCH()
 * @method static ComponentTypeEnum CONTENT_BLOCKER()
 * @method static ComponentTypeEnum PROVIDER()
 * @method static ComponentTypeEnum SCRIPT_BLOCKER()
 * @method static ComponentTypeEnum SERVICE()
 * @method static ComponentTypeEnum STYLE_BLOCKER()
 * @method static ComponentTypeEnum UNKNOWN()
 */
class ComponentTypeEnum extends AbstractEnum implements LocalizedEnumInterface
{
    public const COMPATIBILITY_PATCH = 'compatibility-patch';

    public const CONTENT_BLOCKER = 'content-blocker';

    public const PROVIDER = 'provider';

    public const SCRIPT_BLOCKER = 'script-blocker';

    public const SERVICE = 'service';

    public const STYLE_BLOCKER = 'style-blocker';

    public const UNKNOWN = 'unknown';

    public static function localized(): array
    {
        return [
            self::COMPATIBILITY_PATCH => _x('Compatibility Patch', 'Backend / Package / Component Type', 'borlabs-cookie'),
            self::CONTENT_BLOCKER => _x('Content Blocker', 'Backend / Package / Component Type', 'borlabs-cookie'),
            self::PROVIDER => _x('Provider', 'Backend / Package / Component Type', 'borlabs-cookie'),
            self::SCRIPT_BLOCKER => _x('Script Blocker', 'Backend / Package / Component Type', 'borlabs-cookie'),
            self::SERVICE => _x('Service', 'Backend / Package / Component Type', 'borlabs-cookie'),
            self::STYLE_BLOCKER => _x('Style Blocker', 'Backend / Package / Component Type', 'borlabs-cookie'),
            self::UNKNOWN => '-',
        ];
    }
}
