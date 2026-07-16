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

namespace Borlabs\Cookie\ApiClient\Transformer;

use Borlabs\Cookie\Dto\Package\InstallationPackageDto;
use Borlabs\Cookie\Support\Traits\VersionNumberTrait;

final class InstallationPackageTransformer
{
    use VersionNumberTrait;

    public function toDto(object $package): InstallationPackageDto
    {
        $installationPackage = new InstallationPackageDto();
        $installationPackage->data = $package;

        return $installationPackage;
    }
}
