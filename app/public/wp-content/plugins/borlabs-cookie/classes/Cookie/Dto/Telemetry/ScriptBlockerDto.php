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
use Borlabs\Cookie\DtoList\System\KeyValueDtoList;

class ScriptBlockerDto extends AbstractDto
{
    public ?string $borlabsServicePackageKey = null;

    public ?KeyValueDtoList $handles;

    public string $key;

    public string $name;

    public ?KeyValueDtoList $onExist;

    public ?KeyValueDtoList $phrases;

    public bool $status;
}
