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
use Borlabs\Cookie\Job\PlannedPackageAutoUpdateMailJobHandler;
use Borlabs\Cookie\Repository\Package\PackageRepository;
use DateTime;

class PlannedPackageAutoUpdateMailJobService
{
    private JobService $jobService;

    private PackageRepository $packageRepository;

    private PlannedPackageAutoUpdateMailJobHandler $plannedPackageAutoUpdateMailJobHandler;

    public function __construct(
        JobService $jobService,
        PackageRepository $packageRepository,
        PlannedPackageAutoUpdateMailJobHandler $plannedPackageAutoUpdateMailJobHandler
    ) {
        $this->jobService = $jobService;
        $this->packageRepository = $packageRepository;
        $this->plannedPackageAutoUpdateMailJobHandler = $plannedPackageAutoUpdateMailJobHandler;
    }

    public function updateJob()
    {
        $updatablePackages = $this->packageRepository->getUpdatablePackages();

        if (count($updatablePackages) === 0) {
            $this->jobService->delete($this->plannedPackageAutoUpdateMailJobHandler::JOB_TYPE);

            return;
        }

        $this->jobService->add(
            $this->plannedPackageAutoUpdateMailJobHandler::JOB_TYPE,
            (new DateTime('now'))->modify('+ 1 hours'),
            true,
        );
    }
}
