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

namespace Borlabs\Cookie\Dto\CompatibilityPatch;

use Borlabs\Cookie\Dto\AbstractDto;
use Borlabs\Cookie\Dto\System\FileDto;
use Borlabs\Cookie\Model\CompatibilityPatch\CompatibilityPatchModel;
use Borlabs\Cookie\Model\Package\PackageModel;

class CompatibilityPatchDetailsDto extends AbstractDto
{
    public CompatibilityPatchModel $compatibilityPatch;

    public ?FileDto $file;

    public PackageModel $package;

    public bool $validationStatus;

    public function __construct(
        CompatibilityPatchModel $compatibilityPatch,
        ?FileDto $file,
        PackageModel $package,
        bool $validationStatus
    ) {
        $this->compatibilityPatch = $compatibilityPatch;
        $this->file = $file;
        $this->package = $package;
        $this->validationStatus = $validationStatus;
    }
}
