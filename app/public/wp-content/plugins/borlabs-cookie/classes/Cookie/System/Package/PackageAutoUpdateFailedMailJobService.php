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

namespace Borlabs\Cookie\System\Package;

use Borlabs\Cookie\Job\JobService;
use Borlabs\Cookie\Job\PackageAutoUpdateFailedMailJobHandler;
use DateTime;

class PackageAutoUpdateFailedMailJobService
{
    private JobService $jobService;

    private PackageAutoUpdateFailedMailJobHandler $packageAutoUpdateFailedMailJobHandler;

    public function __construct(
        JobService $jobService,
        PackageAutoUpdateFailedMailJobHandler $packageAutoUpdateFailedMailJobHandler
    ) {
        $this->jobService = $jobService;
        $this->packageAutoUpdateFailedMailJobHandler = $packageAutoUpdateFailedMailJobHandler;
    }

    public function updateJob(array $payload)
    {
        $this->jobService->add(
            $this->packageAutoUpdateFailedMailJobHandler::JOB_TYPE,
            new DateTime('now'),
            true,
            $payload,
        );
    }
}
