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

/**
 * @method static PageStatusEnum FAILED()
 * @method static PageStatusEnum FINISHED()
 * @method static PageStatusEnum SCANNING()
 */
class PageStatusEnum extends AbstractEnum
{
    public const FAILED = 'failed';

    public const FINISHED = 'finished';

    public const SCANNING = 'scanning';
}
