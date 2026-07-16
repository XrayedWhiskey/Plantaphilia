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

use function sg_cachepress_purge_cache;

final class SiteGroundSpeedOptimizerCacheClearer implements ThirdPartyCacheClearerInterface
{
    private Log $log;

    public function __construct(Log $log)
    {
        $this->log = $log;
    }

    public function clearCache()
    {
        if (function_exists('\sg_cachepress_purge_cache')) {
            sg_cachepress_purge_cache();

            $this->log->debug('SiteGround Speed Optimizer cache cleared.');
        }
    }
}
