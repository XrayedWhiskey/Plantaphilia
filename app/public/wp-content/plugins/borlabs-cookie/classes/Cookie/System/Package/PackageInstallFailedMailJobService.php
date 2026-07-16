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
use Borlabs\Cookie\Job\PackageInstallFailedMailJobHandler;
use DateTime;

class PackageInstallFailedMailJobService
{
    private JobService $jobService;

    private PackageInstallFailedMailJobHandler $packageInstallFailedMailJobHandler;

    public function __construct(
        JobService $jobService,
        PackageInstallFailedMailJobHandler $packageInstallFailedMailJobHandler
    ) {
        $this->jobService = $jobService;
        $this->packageInstallFailedMailJobHandler = $packageInstallFailedMailJobHandler;
    }

    public function updateJob(array $payload)
    {
        $this->jobService->add(
            $this->packageInstallFailedMailJobHandler::JOB_TYPE,
            new DateTime('now'),
            true,
            $payload,
        );
    }
}
