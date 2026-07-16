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

namespace Borlabs\Cookie\Dto\System;

final class VersionNumberWithHotfixDto extends VersionNumberDto
{
    public int $hotfix = 0;

    public function __construct(int $major, int $minor = 0, int $patch = 0, int $hotfix = 0)
    {
        parent::__construct($major, $minor, $patch);

        $this->hotfix = $hotfix;
    }
}
