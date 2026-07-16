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

namespace Borlabs\Cookie\Dto\Package;

use Borlabs\Cookie\Dto\AbstractDto;
use Borlabs\Cookie\DtoList\Package\CompatibilityPatchComponentDtoList;
use Borlabs\Cookie\DtoList\Package\ContentBlockerComponentDtoList;
use Borlabs\Cookie\DtoList\Package\ScriptBlockerComponentDtoList;
use Borlabs\Cookie\DtoList\Package\ServiceComponentDtoList;
use Borlabs\Cookie\DtoList\Package\StyleBlockerComponentDtoList;

class ComponentDto extends AbstractDto
{
    public ?CompatibilityPatchComponentDtoList $compatibilityPatches = null;

    public ?ContentBlockerComponentDtoList $contentBlockers = null;

    public ?ScriptBlockerComponentDtoList $scriptBlockers = null;

    public ?ServiceComponentDtoList $services = null;

    public ?StyleBlockerComponentDtoList $styleBlockers = null;
}
