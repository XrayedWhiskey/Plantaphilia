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
 * @method static CookiePurposeEnum FUNCTIONAL()
 * @method static CookiePurposeEnum TRACKING()
 */
class CookiePurposeEnum extends AbstractEnum implements LocalizedEnumInterface
{
    public const FUNCTIONAL = 'functional';

    public const TRACKING = 'tracking';

    public static function localized(): array
    {
        return [
            self::FUNCTIONAL => _x('Functional', 'Backend / Services / Cookies', 'borlabs-cookie'),
            self::TRACKING => _x('Tracking', 'Backend / Services / Cookies', 'borlabs-cookie'),
        ];
    }
}
