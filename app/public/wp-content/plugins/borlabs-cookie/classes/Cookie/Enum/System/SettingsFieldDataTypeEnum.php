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
 * @method static SettingsFieldDataTypeEnum BOOLEAN()
 * @method static SettingsFieldDataTypeEnum INFORMATION()
 * @method static SettingsFieldDataTypeEnum SELECT()
 * @method static SettingsFieldDataTypeEnum SYSTEM_SERVICE_GROUP()
 * @method static SettingsFieldDataTypeEnum TEXT()
 */
class SettingsFieldDataTypeEnum extends AbstractEnum implements LocalizedEnumInterface
{
    public const BOOLEAN = 'boolean';

    public const INFORMATION = 'information';

    public const SELECT = 'select';

    public const SYSTEM_SERVICE_GROUP = 'system-service-group';

    public const TEXT = 'text';

    public static function localized(): array
    {
        return [
            self::BOOLEAN => _x('Boolean', 'Backend / Settings / Fields', 'borlabs-cookie'),
            self::INFORMATION => _x('Information', 'Backend / Settings / Fields', 'borlabs-cookie'),
            self::SELECT => _x('Select', 'Backend / Settings / Fields', 'borlabs-cookie'),
            self::SYSTEM_SERVICE_GROUP => _x('Service Group', 'Backend / Settings / Fields', 'borlabs-cookie'),
            self::TEXT => _x('Text', 'Backend / Settings / Fields', 'borlabs-cookie'),
        ];
    }
}
