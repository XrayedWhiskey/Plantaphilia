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
use Borlabs\Cookie\Repository\Job\JobRepository;
use DateTime;
use DateTimeInterface;

final class JobService
{
    private JobRepository $jobRepository;

    public function __construct(JobRepository $jobRepository)
    {
        $this->jobRepository = $jobRepository;
    }

    /**
     * @param DateTimeInterface $plannedFor The date when the job is planned to be executed.
     *                                      Due to the nature of the WordPress scheduling system, the job will not be executed at this exact time.
     * @param bool              $unique     If a job is unique, it will only be added if no job with the same type and payload exists.
     *                                      The plannedFor date will of the existing job will not be updated.
     *
     * @throws \Borlabs\Cookie\Exception\UnexpectedRepositoryOperationException
     */
    public function add(string $type, DateTimeInterface $plannedFor, bool $unique = false, ?array $payload = null): JobModel
    {
        if ($unique) {
            $existingJob = $this->get($type, $payload);

            if ($existingJob !== null) {
                return $existingJob;
            }
        }

        $model = new JobModel();
        $model->createdAt = new DateTime('now');
        $model->payload = $payload;
        $model->plannedFor = $plannedFor;
        $model->type = $type;

        return $this->jobRepository->insert($model);
    }

    public function delete(string $type, ?array $payload = null): bool
    {
        $jobs = $this->jobRepository->findPlannedJobsByTypeAndPayload($type, $payload);

        if ($jobs === null) {
            return true;
        }

        foreach ($jobs as $job) {
            $this->jobRepository->delete($job);
        }

        return true;
    }

    /**
     * If several jobs are found for the type and payload, the next scheduled job is returned.
     */
    public function get(string $type, ?array $payload = null): ?JobModel
    {
        return $this->jobRepository->findFirstPlannedJobByTypeAndPayload($type, $payload);
    }

    public function getAll(string $type): array
    {
        return $this->jobRepository->getAllPlannedJobsOfType($type);
    }
}
