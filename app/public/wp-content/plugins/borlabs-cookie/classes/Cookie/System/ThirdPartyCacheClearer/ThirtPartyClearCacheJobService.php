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

use Borlabs\Cookie\Job\JobService;
use Borlabs\Cookie\Job\ThirdPartyClearCacheJobHandler;
use DateTime;

final class ThirtPartyClearCacheJobService
{
    private JobService $jobService;

    private ThirdPartyClearCacheJobHandler $thirdPartyClearCacheJobHandler;

    public function __construct(
        ThirdPartyClearCacheJobHandler $thirdPartyClearCacheJobHandler,
        JobService $jobService
    ) {
        $this->thirdPartyClearCacheJobHandler = $thirdPartyClearCacheJobHandler;
        $this->jobService = $jobService;
    }

    /**
     * Adds or updates a clear cache job.
     */
    public function updateJob()
    {
        $this->jobService->add(
            $this->thirdPartyClearCacheJobHandler::JOB_TYPE,
            (new DateTime('now')),
            true,
        );
    }
}
