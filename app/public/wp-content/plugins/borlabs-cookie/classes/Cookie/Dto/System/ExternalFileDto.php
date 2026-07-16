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

use Borlabs\Cookie\Dto\AbstractDto;

class ExternalFileDto extends AbstractDto
{
    public ?string $hash = null;

    /**
     * This property must always be non-null.
     * It was inadvertently set to null due to a bug in a compatibility patch, resulting in a fatal error on a customer's website.
     */
    public ?string $url = null;

    public function __construct(
        ?string $url = null,
        ?string $hash = null
    ) {
        $this->url = $url;
        $this->hash = $hash;
    }
}
