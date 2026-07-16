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

namespace Borlabs\Cookie\System\Style;

use Borlabs\Cookie\Job\JobService;
use Borlabs\Cookie\Job\UpdateCssFileJobHandler;
use DateTime;

class UpdateCssFileJobService
{
    private JobService $jobService;

    private UpdateCssFileJobHandler $updateCssFileJobHandler;

    public function __construct(
        JobService $jobService,
        UpdateCssFileJobHandler $updateCssFileJobHandler
    ) {
        $this->jobService = $jobService;
        $this->updateCssFileJobHandler = $updateCssFileJobHandler;
    }

    public function updateJob(int $blogId, string $languageCode)
    {
        $this->jobService->add(
            $this->updateCssFileJobHandler::JOB_TYPE,
            (new DateTime('now')),
            true,
            [
                'blogId' => $blogId,
                'languageCode' => $languageCode,
            ],
        );
    }
}
