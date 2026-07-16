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

namespace Borlabs\Cookie\System\ThirdPartyCacheClearer;

use Borlabs\Cookie\System\Log\Log;
use Purge;

final class LiteSpeedCacheCacheClearer implements ThirdPartyCacheClearerInterface
{
    private Log $log;

    public function __construct(Log $log)
    {
        $this->log = $log;
    }

    public function clearCache()
    {
        if (class_exists('Purge') && method_exists('Purge', 'purge_all')) {
            Purge::purge_all('Borlabs Cookie');

            $this->log->debug('LiteSpeed Cache cache cleared.');
        }
    }
}
