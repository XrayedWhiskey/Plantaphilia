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

namespace Borlabs\Cookie\Model\Job;

use Borlabs\Cookie\Model\AbstractModel;
use DateTimeInterface;

class JobModel extends AbstractModel
{
    public DateTimeInterface $createdAt;

    public ?DateTimeInterface $executedAt = null;

    public ?array $payload = null;

    public DateTimeInterface $plannedFor;

    public string $type;
}
