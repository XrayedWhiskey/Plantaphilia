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

use Borlabs\Cookie\Enum\Library\AutoUpdateIntervalEnum;
use Borlabs\Cookie\Job\JobService;
use Borlabs\Cookie\Job\PackageAutoUpdateJobHandler;
use Borlabs\Cookie\Model\Job\JobModel;
use Borlabs\Cookie\Model\Package\PackageModel;
use Borlabs\Cookie\Support\Traits\VersionNumberTrait;
use Borlabs\Cookie\System\Config\LibraryConfig;
use DateTime;

class PackageAutoUpdateJobService
{
    use VersionNumberTrait;

    private JobService $jobService;

    private LibraryConfig $libraryConfig;

    private PackageAutoUpdateJobHandler $packageAutoUpdateJobHandler;

    public function __construct(
        JobService $jobService,
        LibraryConfig $libraryConfig,
        PackageAutoUpdateJobHandler $packageAutoUpdateJobHandler
    ) {
        $this->jobService = $jobService;
        $this->libraryConfig = $libraryConfig;
        $this->packageAutoUpdateJobHandler = $packageAutoUpdateJobHandler;
    }

    public function getAllPlannedJobs(): array
    {
        return $this->jobService->getAll($this->packageAutoUpdateJobHandler::JOB_TYPE);
    }

    public function getJob(PackageModel $package): ?JobModel
    {
        return $this->jobService->get(
            $this->packageAutoUpdateJobHandler::JOB_TYPE,
            $this->getPayload($package),
        );
    }

    public function updateJob(PackageModel $package)
    {
        $job = $this->getJob($package);

        if ($package->installedAt !== null && $this->isUpdateAvailable($package) && $package->autoUpdateEnabled) {
            $libraryConfig = $this->libraryConfig->get();
            $plannedFor = new DateTime('tomorrow');
            $plannedFor->setTime(
                (int) preg_replace('/([0-9]{2}):([0-9]{2})/', '$1', $libraryConfig->packageAutoUpdateTime),
                (int) preg_replace('/([0-9]{2}):([0-9]{2})/', '$2', $libraryConfig->packageAutoUpdateTime),
            );

            if ($libraryConfig->packageAutoUpdateInterval->is(AutoUpdateIntervalEnum::AFTER_24_HOURS())) {
                $plannedFor->modify('+24 hours');
            } else {
                $plannedFor->modify('next ' . $libraryConfig->packageAutoUpdateInterval->value);
            }

            $this->jobService->add(
                $this->packageAutoUpdateJobHandler::JOB_TYPE,
                $plannedFor,
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

    private function isUpdateAvailable(PackageModel $package): bool
    {
        return $this->compareVersionNumber($package->borlabsServicePackageVersion, $package->version, '>');
    }
}
