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

namespace Borlabs\Cookie\Dto\Localization;

use Borlabs\Cookie\Dto\AbstractDto;

class LocalizedClassDto extends AbstractDto
{
    public string $className;

    public ?object $instance = null;

    public function __construct(
        string $className,
        ?object $instance = null
    ) {
        $this->className = $className;
        $this->instance = $instance;
    }
}
