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

use Borlabs\Cookie\Container\Container;
use Borlabs\Cookie\Model\Job\JobModel;
use Borlabs\Cookie\Repository\Job\JobRepository;
use DateTime;

class JobQueueManager
{
    private const MAX_JOB_QUEUE_INTERATIONS = 10;

    private Container $container;

    private JobRepository $jobRepository;

    private array $registry = [
        PackageAutoUpdateFailedMailJobHandler::JOB_TYPE => PackageAutoUpdateFailedMailJobHandler::class,
        PackageAutoUpdateJobHandler::JOB_TYPE => PackageAutoUpdateJobHandler::class,
        PackageInstallFailedMailJobHandler::JOB_TYPE => PackageInstallFailedMailJobHandler::class,
        PackageInstallJobHandler::JOB_TYPE => PackageInstallJobHandler::class,
        PlannedPackageAutoUpdateMailJobHandler::JOB_TYPE => PlannedPackageAutoUpdateMailJobHandler::class,
        ThirdPartyClearCacheJobHandler::JOB_TYPE => ThirdPartyClearCacheJobHandler::class,
        UpdateCssFileJobHandler::JOB_TYPE => UpdateCssFileJobHandler::class,
        UpdateJavaScriptConfigFileJobHandler::JOB_TYPE => UpdateJavaScriptConfigFileJobHandler::class,
    ];

    public function __construct(
        Container $container,
        JobRepository $jobRepository
    ) {
        $this->container = $container;
        $this->jobRepository = $jobRepository;
    }

    public function processQueue(): void
    {
        $iteration = 1;

        while ($jobs = $this->jobRepository->getDueJobs()) {
            foreach ($jobs as $job) {
                /** @var JobModel $job */
                if (!isset($this->registry[$job->type])) {
                    $this->jobRepository->delete($job);

                    continue;
                }

                $jobHandler = $this->container->get(
                    $this->registry[$job->type],
                );

                // Set the job as executed before processing it to prevent a job from getting stuck due to an error during processing.
                $job->executedAt = new DateTime('now');
                $this->jobRepository->update($job);

                $jobHandler->handle($job);
            }

            ++$iteration;

            // Prevent infinite loop
            if ($iteration >= self::MAX_JOB_QUEUE_INTERATIONS) {
                break;
            }
        }
    }
}
