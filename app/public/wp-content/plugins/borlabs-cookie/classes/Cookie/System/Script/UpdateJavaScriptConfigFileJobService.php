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

namespace Borlabs\Cookie\System\Script;

use Borlabs\Cookie\Job\JobService;
use Borlabs\Cookie\Job\UpdateJavaScriptConfigFileJobHandler;
use DateTime;

class UpdateJavaScriptConfigFileJobService
{
    private JobService $jobService;

    private UpdateJavaScriptConfigFileJobHandler $updateJavaScriptConfigFileJobHandler;

    public function __construct(
        JobService $jobService,
        UpdateJavaScriptConfigFileJobHandler $updateJavaScriptConfigFileJobHandler
    ) {
        $this->jobService = $jobService;
        $this->updateJavaScriptConfigFileJobHandler = $updateJavaScriptConfigFileJobHandler;
    }

    public function updateJob(string $languageCode)
    {
        $this->jobService->add(
            $this->updateJavaScriptConfigFileJobHandler::JOB_TYPE,
            (new DateTime('now')),
            true,
            [
                'languageCode' => $languageCode,
            ],
        );
    }
}
