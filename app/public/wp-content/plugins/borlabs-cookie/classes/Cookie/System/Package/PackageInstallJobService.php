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
use Borlabs\Cookie\Job\PackageInstallJobHandler;
use Borlabs\Cookie\Model\Job\JobModel;
use Borlabs\Cookie\Model\Package\PackageModel;
use DateTime;

class PackageInstallJobService
{
    private JobService $jobService;

    private PackageInstallJobHandler $packageInstallJobHandler;

    public function __construct(
        JobService $jobService,
        PackageInstallJobHandler $packageInstallJobHandler
    ) {
        $this->jobService = $jobService;
        $this->packageInstallJobHandler = $packageInstallJobHandler;
    }

    public function getJob(PackageModel $package): ?JobModel
    {
        return $this->jobService->get(
            $this->packageInstallJobHandler::JOB_TYPE,
            $this->getPayload($package),
        );
    }

    public function updateJob(PackageModel $package)
    {
        $job = $this->getJob($package);

        if ($package->installedAt === null) {
            $this->jobService->add(
                $this->packageInstallJobHandler::JOB_TYPE,
                (new DateTime('now'))->modify('-1 day'),
                true,
                $this->getPayload($package),
            );
        } elseif ($job !== null) {
            $this->jobService->delete($job->type, $job->payload);
        }
    }

    private function getPayload(PackageModel $package): array
    {
        return [
            'borlabsServicePackageKey' => $package->borlabsServicePackageKey,
        ];
    }
}
