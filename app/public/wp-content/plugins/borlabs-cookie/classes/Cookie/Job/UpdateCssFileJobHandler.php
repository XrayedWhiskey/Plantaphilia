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
use Borlabs\Cookie\System\Log\Log;
use Borlabs\Cookie\System\Style\StyleBuilder;
use Borlabs\Cookie\System\ThirdPartyCacheClearer\ThirtPartyClearCacheJobService;

class UpdateCssFileJobHandler implements JobHandler
{
    public const JOB_TYPE = 'updateCssFile';

    public Log $log;

    private StyleBuilder $styleBuilder;

    private ThirtPartyClearCacheJobService $thirtPartyClearCacheJobService;

    public function __construct(
        Log $log,
        StyleBuilder $styleBuilder,
        ThirtPartyClearCacheJobService $thirtPartyClearCacheJobService
    ) {
        $this->log = $log;
        $this->styleBuilder = $styleBuilder;
        $this->thirtPartyClearCacheJobService = $thirtPartyClearCacheJobService;
    }

    public function handle(JobModel $job): void
    {
        $this->styleBuilder->updateCssFileAndIncrementStyleVersion(
            $job->payload['blogId'],
            $job->payload['languageCode'],
        );

        $this->thirtPartyClearCacheJobService->updateJob();
    }
}
