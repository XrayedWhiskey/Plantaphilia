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

namespace Borlabs\Cookie\Dto\Telemetry;

use Borlabs\Cookie\Dto\AbstractDto;
use Borlabs\Cookie\Enum\SetupAssistant\SetupTypeEnum;

class SetupAssistantUsageDto extends AbstractDto
{
    public int $finishedAt;

    public int $startedAt;

    public SetupTypeEnum $type;

    public function __construct(SetupTypeEnum $type, int $startedAt, int $finishedAt)
    {
        $this->finishedAt = $finishedAt;
        $this->startedAt = $startedAt;
        $this->type = $type;
    }
}
