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
 * @method static PackageTypeEnum COMPATIBILITY_PATCH()
 * @method static PackageTypeEnum CONTENT_BLOCKER()
 * @method static PackageTypeEnum SCRIPT_BLOCKER()
 * @method static PackageTypeEnum SERVICE()
 * @method static PackageTypeEnum STYLE_BLOCKER()
 * @method static PackageTypeEnum UNKNOWN()
 */
class PackageTypeEnum extends AbstractEnum implements LocalizedEnumInterface
{
    public const COMPATIBILITY_PATCH = 'compatibility-patch';

    public const CONTENT_BLOCKER = 'content-blocker';

    public const SCRIPT_BLOCKER = 'script-blocker';

    public const SERVICE = 'service';

    public const STYLE_BLOCKER = 'style-blocker';

    public const UNKNOWN = 'unknown';

    public static function localized(): array
    {
        return [
            self::COMPATIBILITY_PATCH => _x('Compatibility Patch', 'Backend / Package / Package Type', 'borlabs-cookie'),
            self::CONTENT_BLOCKER => _x('Content Blocker', 'Backend / Package / Package Type', 'borlabs-cookie'),
            self::SCRIPT_BLOCKER => _x('Script Blocker', 'Backend / Package / Package Type', 'borlabs-cookie'),
            self::SERVICE => _x('service', 'Backend / Package / Package Type', 'borlabs-cookie'),
            self::STYLE_BLOCKER => _x('Style Blocker', 'Backend / Package / Package Type', 'borlabs-cookie'),
            self::UNKNOWN => _x('Unknown', 'Backend / Package / Package Type', 'borlabs-cookie'),
        ];
    }
}
