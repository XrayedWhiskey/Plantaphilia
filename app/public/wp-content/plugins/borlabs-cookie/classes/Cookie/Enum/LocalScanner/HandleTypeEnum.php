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

namespace Borlabs\Cookie\Enum\LocalScanner;

use Borlabs\Cookie\Enum\AbstractEnum;
use Borlabs\Cookie\Enum\LocalizedEnumInterface;

/**
 * @method static HandleTypeEnum CORE()
 * @method static HandleTypeEnum EXTERNAL()
 * @method static HandleTypeEnum OTHER()
 * @method static HandleTypeEnum PLUGIN()
 * @method static HandleTypeEnum THEME()
 */
class HandleTypeEnum extends AbstractEnum implements LocalizedEnumInterface
{
    public const CORE = 'core';

    public const EXTERNAL = 'external';

    public const OTHER = 'other';

    public const PLUGIN = 'plugin';

    public const THEME = 'theme';

    public static function localized(): array
    {
        return [
            self::CORE => _x('Core', 'Backend / Local Scanner / Cookies', 'borlabs-cookie'),
            self::EXTERNAL => _x('External', 'Backend / Local Scanner / Cookies', 'borlabs-cookie'),
            self::OTHER => _x('Other', 'Backend / Local Scanner / Cookies', 'borlabs-cookie'),
            self::PLUGIN => _x('Plugin', 'Backend / Local Scanner / Cookies', 'borlabs-cookie'),
            self::THEME => _x('Theme', 'Backend / Local Scanner / Cookies', 'borlabs-cookie'),
        ];
    }
}
