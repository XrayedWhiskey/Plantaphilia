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

namespace Borlabs\Cookie\Enum\Service;

use Borlabs\Cookie\Enum\AbstractEnum;
use Borlabs\Cookie\Enum\LocalizedEnumInterface;

/**
 * @method static CookieTypeEnum HTTP()
 * @method static CookieTypeEnum LOCAL_STORAGE()
 * @method static CookieTypeEnum SESSION_STORAGE()
 */
class CookieTypeEnum extends AbstractEnum implements LocalizedEnumInterface
{
    public const HTTP = 'http';

    public const LOCAL_STORAGE = 'local_storage';

    public const SESSION_STORAGE = 'session_storage';

    public static function localized(): array
    {
        return [
            self::HTTP => _x('HTTP', 'Backend / Services / Cookies', 'borlabs-cookie'),
            self::LOCAL_STORAGE => _x('Local Storage', 'Backend / Services / Cookies', 'borlabs-cookie'),
            self::SESSION_STORAGE => _x('Session Storage', 'Backend / Services / Cookies', 'borlabs-cookie'),
        ];
    }
}
