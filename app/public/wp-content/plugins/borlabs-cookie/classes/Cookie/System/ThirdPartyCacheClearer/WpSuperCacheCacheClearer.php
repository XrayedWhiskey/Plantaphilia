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

use function wp_cache_clean_cache;

final class WpSuperCacheCacheClearer implements ThirdPartyCacheClearerInterface
{
    private Log $log;

    public function __construct(Log $log)
    {
        $this->log = $log;
    }

    public function clearCache()
    {
        if (function_exists('wp_cache_clean_cache')) {
            global $file_prefix;

            if (!isset($file_prefix)) {
                return;
            }

            wp_cache_clean_cache($file_prefix);

            $this->log->debug('WP Super Cache cache cleared.');
        }
    }
}
