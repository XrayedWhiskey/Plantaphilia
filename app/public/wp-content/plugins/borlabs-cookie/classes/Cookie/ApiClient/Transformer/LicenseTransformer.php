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

use Borlabs\Cookie\Dto\License\LicenseDto;
use Borlabs\Cookie\Support\Transformer;

final class LicenseTransformer
{
    public function toDto(object $license): LicenseDto
    {
        return new LicenseDto(
            $license->licenseKey,
            Transformer::toKeyValueDtoList((array) $license->licenseMeta),
            $license->licenseName,
            $license->licenseSalt,
            $license->licenseType,
            $license->licenseValidUntil,
            $license->siteSalt,
        );
    }
}
