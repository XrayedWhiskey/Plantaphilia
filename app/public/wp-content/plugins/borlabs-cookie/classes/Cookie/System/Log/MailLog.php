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

use Borlabs\Cookie\Adapter\WpFunction;
use Borlabs\Cookie\Dto\Log\LogDto;
use Borlabs\Cookie\Enum\Log\LogLevelEnum;

class MailLog implements LogInterface
{
    private WpFunction $wpFunction;

    public function __construct(WpFunction $wpFunction)
    {
        $this->wpFunction = $wpFunction;
    }

    public function addLogEntry(
        string $processId,
        LogLevelEnum $level,
        string $message,
        ?array $context = null,
        ?array $backtrace = null
    ): bool {
        $log = new LogDto($processId, $level, $message, $context, $backtrace);
        $this->sendLog($log);

        return true;
    }

    private function sendLog(LogDto $log)
    {
        $adminMail = $this->wpFunction->getBlogInfo('admin_email');

        $this->wpFunction->wpMail(
            $adminMail,
            $log->level->key . ' - Borlabs Cookie',
            '[' . $log->processId . '][' . $log->level->key . '][' . $log->stamp->format('Y-m-d H:i:s') . ']'
            . htmlspecialchars($log->message, ENT_QUOTES) . "\n\n"
            . "[CONTEXT]\n"
            . json_encode($log->context)
            . "\n[BACKTRACE]\n"
            . json_encode($log->backtrace),
        );
    }
}
