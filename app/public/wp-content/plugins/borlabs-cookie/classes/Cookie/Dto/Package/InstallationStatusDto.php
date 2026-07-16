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
use Borlabs\Cookie\DtoList\Package\InstallationStatusDtoList;
use Borlabs\Cookie\Enum\Package\ComponentTypeEnum;
use Borlabs\Cookie\Enum\Package\InstallationStatusEnum;

class InstallationStatusDto extends AbstractDto
{
    public ComponentTypeEnum $componentType;

    public ?string $failureMessage;

    public int $id;

    public string $key;

    public string $name;

    public InstallationStatusEnum $status;

    public ?InstallationStatusDtoList $subComponentsInstallationStatus = null;

    public function __construct(
        InstallationStatusEnum $status,
        ComponentTypeEnum $componentType,
        string $key,
        string $name,
        int $id = -1,
        ?InstallationStatusDtoList $subComponentsInstallationStatus = null,
        ?string $failureMessage = null
    ) {
        $this->componentType = $componentType;
        $this->failureMessage = $failureMessage;
        $this->id = $id;
        $this->key = $key;
        $this->name = $name;
        $this->status = $status;
        $this->subComponentsInstallationStatus = $subComponentsInstallationStatus;
    }
}
