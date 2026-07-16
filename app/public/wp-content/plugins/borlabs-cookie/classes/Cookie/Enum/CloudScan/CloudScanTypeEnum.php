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

namespace Borlabs\Cookie\Enum\CloudScan;

use Borlabs\Cookie\Enum\AbstractEnum;
use Borlabs\Cookie\Enum\LocalizedEnumInterface;

/**
 * @method static CloudScanTypeEnum AUDIT()
 * @method static CloudScanTypeEnum SETUP()
 */
class CloudScanTypeEnum extends AbstractEnum implements LocalizedEnumInterface
{
    public const AUDIT = 'audit';

    public const SETUP = 'setup';

    public static function localized(): array
    {
        return [
            self::AUDIT => _x('Audit', 'Backend / Services / Cookies', 'borlabs-cookie'),
            self::SETUP => _x('Setup', 'Backend / Services / Cookies', 'borlabs-cookie'),
        ];
    }
}
