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
 * @method static ScanModeEnum CURRENT_USER()
 * @method static ScanModeEnum GUEST()
 * @method static ScanModeEnum MANUAL()
 */
class ScanModeEnum extends AbstractEnum implements LocalizedEnumInterface
{
    public const CURRENT_USER = 'current-user';

    public const GUEST = 'guest';

    public const MANUAL = 'manual';

    public static function localized(): array
    {
        return [
            self::CURRENT_USER => _x('<translation-key id="Current-User">Current User</translation-key>', 'Backend / LocalScanner / Option', 'borlabs-cookie'),
            self::GUEST => _x('<translation-key id="Guest">Guest</translation-key>', 'Backend / LocalScanner / Option', 'borlabs-cookie'),
            self::MANUAL => _x('<translation-key id="Manual">Manual</translation-key>', 'Backend / LocalScanner / Option', 'borlabs-cookie'),
        ];
    }
}
