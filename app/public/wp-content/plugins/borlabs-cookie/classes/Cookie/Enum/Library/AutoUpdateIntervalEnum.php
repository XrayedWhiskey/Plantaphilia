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

namespace Borlabs\Cookie\Enum\Library;

use Borlabs\Cookie\Enum\AbstractEnum;
use Borlabs\Cookie\Enum\LocalizedEnumInterface;

/**
 * @method static static AFTER_24_HOURS()
 * @method static static WEEKDAY_1_MONDAY()
 * @method static static WEEKDAY_2_TUESDAY()
 * @method static static WEEKDAY_3_WEDNESDAY()
 * @method static static WEEKDAY_4_THURSDAY()
 * @method static static WEEKDAY_5_FRIDAY()
 * @method static static WEEKDAY_6_SATURDAY()
 * @method static static WEEKDAY_7_SUNDAY()
 */
class AutoUpdateIntervalEnum extends AbstractEnum implements LocalizedEnumInterface
{
    public const AFTER_24_HOURS = 'after-24-hours';

    public const WEEKDAY_1_MONDAY = 'monday';

    public const WEEKDAY_2_TUESDAY = 'tuesday';

    public const WEEKDAY_3_WEDNESDAY = 'wednesday';

    public const WEEKDAY_4_THURSDAY = 'thursday';

    public const WEEKDAY_5_FRIDAY = 'friday';

    public const WEEKDAY_6_SATURDAY = 'saturday';

    public const WEEKDAY_7_SUNDAY = 'sunday';

    public static function localized(): array
    {
        return [
            self::AFTER_24_HOURS => _x('After 24 hours', 'Backend / Library / Auto Update Interval', 'borlabs-cookie'),
            self::WEEKDAY_1_MONDAY => _x('Monday', 'Backend / Library / Auto Update Interval', 'borlabs-cookie'),
            self::WEEKDAY_2_TUESDAY => _x('Tuesday', 'Backend / Library / Auto Update Interval', 'borlabs-cookie'),
            self::WEEKDAY_3_WEDNESDAY => _x('Wednesday', 'Backend / Library / Auto Update Interval', 'borlabs-cookie'),
            self::WEEKDAY_4_THURSDAY => _x('Thursday', 'Backend / Library / Auto Update Interval', 'borlabs-cookie'),
            self::WEEKDAY_5_FRIDAY => _x('Friday', 'Backend / Library / Auto Update Interval', 'borlabs-cookie'),
            self::WEEKDAY_6_SATURDAY => _x('Saturday', 'Backend / Library / Auto Update Interval', 'borlabs-cookie'),
            self::WEEKDAY_7_SUNDAY => _x('Sunday', 'Backend / Library / Auto Update Interval', 'borlabs-cookie'),
        ];
    }
}
