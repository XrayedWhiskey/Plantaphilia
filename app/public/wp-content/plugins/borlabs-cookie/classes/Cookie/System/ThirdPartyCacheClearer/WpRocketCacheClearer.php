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

use function rocket_clean_domain;

final class WpRocketCacheClearer implements ThirdPartyCacheClearerInterface
{
    private Log $log;

    public function __construct(Log $log)
    {
        $this->log = $log;
    }

    public function clearCache()
    {
        if (function_exists('rocket_clean_domain')) {
            rocket_clean_domain();

            $this->log->debug('WP Rocket cache cleared.');
        }

        if (function_exists('rocket_clean_minify')) {
            rocket_clean_minify();

            $this->log->debug('WP Rocket asset cache cleared.');
        }
    }
}
