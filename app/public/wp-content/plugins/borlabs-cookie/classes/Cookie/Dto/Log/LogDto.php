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

namespace Borlabs\Cookie\Dto\Log;

use Borlabs\Cookie\Dto\AbstractDto;
use Borlabs\Cookie\Enum\Log\LogLevelEnum;
use DateTime;

class LogDto extends AbstractDto
{
    public ?array $backtrace = null;

    public ?array $context = null;

    public LogLevelEnum $level;

    public string $message;

    public string $processId;

    public DateTime $stamp;

    public function __construct(
        string $processId,
        LogLevelEnum $level,
        string $message,
        ?array $context = null,
        ?array $backtrace = null
    ) {
        $this->backtrace = $backtrace;
        $this->context = $context;
        $this->level = $level;
        $this->message = $message;
        $this->processId = $processId;
        $this->stamp = new DateTime();
    }
}
