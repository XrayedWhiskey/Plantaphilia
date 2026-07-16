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

namespace Borlabs\Cookie\Job;

use Borlabs\Cookie\Model\Job\JobModel;
use Borlabs\Cookie\System\ThirdPartyCacheClearer\ThirdPartyCacheClearerManager;

class ThirdPartyClearCacheJobHandler implements JobHandler
{
    public const JOB_TYPE = 'thirdPartyClearCache';

    private ThirdPartyCacheClearerManager $thirdPartyCacheClearerManager;

    public function __construct(
        ThirdPartyCacheClearerManager $thirdPartyCacheClearerManager
    ) {
        $this->thirdPartyCacheClearerManager = $thirdPartyCacheClearerManager;
    }

    public function handle(JobModel $job): void
    {
        $this->thirdPartyCacheClearerManager->clearCache();
    }
}
