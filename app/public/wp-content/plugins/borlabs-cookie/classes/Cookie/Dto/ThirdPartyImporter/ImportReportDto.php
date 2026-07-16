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

namespace Borlabs\Cookie\Dto\ThirdPartyImporter;

use Borlabs\Cookie\Dto\AbstractDto;
use Borlabs\Cookie\DtoList\Package\InstallationStatusDtoList;

final class ImportReportDto extends AbstractDto
{
    public bool $configImported = false;

    public InstallationStatusDtoList $customContentBlockersImported;

    public InstallationStatusDtoList $customScriptBlockersImported;

    public ?bool $customServiceGroupsImported = null;

    public InstallationStatusDtoList $customServicesImported;

    public InstallationStatusDtoList $presetContentBlockersImported;

    public bool $presetServiceGroupsImported = false;

    public InstallationStatusDtoList $presetServicesImported;
}
