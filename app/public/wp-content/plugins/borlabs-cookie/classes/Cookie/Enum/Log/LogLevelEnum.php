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

namespace Borlabs\Cookie\Enum\Log;

use Borlabs\Cookie\Enum\AbstractEnum;

/**
 * @method static LogLevelEnum ALERT()
 * @method static LogLevelEnum CRITICAL()
 * @method static LogLevelEnum DEBUG()
 * @method static LogLevelEnum EMERGENCY()
 * @method static LogLevelEnum ERROR()
 * @method static LogLevelEnum INFO()
 * @method static LogLevelEnum NOTICE()
 * @method static LogLevelEnum WARNING()
 */
class LogLevelEnum extends AbstractEnum
{
    public const ALERT = 'alert';

    public const CRITICAL = 'critical';

    public const DEBUG = 'debug';

    public const EMERGENCY = 'emergency';

    public const ERROR = 'error';

    public const INFO = 'info';

    public const NOTICE = 'notice';

    public const WARNING = 'warning';
}
