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
use Borlabs\Cookie\Enum\ThirdPartyImporter\ComponentTypeEnum;

final class PreImportMetadataDto extends AbstractDto
{
    public ComponentTypeEnum $componentType;

    public ?string $language = null;

    public string $legacyKey;

    public string $name;

    public string $newKey;

    public function __construct(
        ComponentTypeEnum $componentType,
        string $name,
        string $legacyKey,
        string $newKey,
        ?string $language = null
    ) {
        $this->componentType = $componentType;
        $this->language = $language;
        $this->legacyKey = $legacyKey;
        $this->name = $name;
        $this->newKey = $newKey;
    }
}
