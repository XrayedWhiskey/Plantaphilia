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

namespace Borlabs\Cookie\System\Localization;

class LocalizationTagDefinition
{
    public string $propertyName;

    public string $regex;

    public string $tagName;

    public function __construct(
        string $tagName,
        string $propertyName,
        string $regex
    ) {
        $this->tagName = $tagName;
        $this->propertyName = $propertyName;
        $this->regex = $regex;
    }
}
