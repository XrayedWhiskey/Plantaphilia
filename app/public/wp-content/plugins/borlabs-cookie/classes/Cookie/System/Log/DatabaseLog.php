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

namespace Borlabs\Cookie\System\Log;

use Borlabs\Cookie\Enum\Log\LogLevelEnum;
use Borlabs\Cookie\Model\Log\LogModel;
use Borlabs\Cookie\Repository\Log\LogRepository;
use DateTime;

class DatabaseLog implements LogInterface
{
    private LogRepository $logRepository;

    public function __construct(LogRepository $logRepository)
    {
        $this->logRepository = $logRepository;
    }

    /**
     * @throws \Borlabs\Cookie\Exception\PropertyDoesNotExistException
     * @throws \Borlabs\Cookie\Exception\UnexpectedRepositoryOperationException
     */
    public function addLogEntry(
        string $processId,
        LogLevelEnum $level,
        string $message,
        ?array $context = null,
        ?array $backtrace = null
    ): bool {
        $log = new LogModel();
        $log->backtrace = $backtrace;
        $log->createdAt = new DateTime();
        $log->context = $context;
        $log->level = $level;
        $log->message = $message;
        $log->processId = $processId;

        $this->logRepository->insert($log);

        return true;
    }
}
