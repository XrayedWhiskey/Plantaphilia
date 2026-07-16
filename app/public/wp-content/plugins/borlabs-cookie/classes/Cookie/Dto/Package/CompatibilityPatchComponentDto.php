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

class CompatibilityPatchComponentDto extends AbstractDto
{
    public string $downloadUrl;

    public string $hash;

    public string $key;

    public function __construct(string $key, string $downloadUrl, string $hash)
    {
        $this->downloadUrl = $downloadUrl;
        $this->hash = $hash;
        $this->key = $key;
    }
}
