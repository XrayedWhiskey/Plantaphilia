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

namespace Borlabs\Cookie\Dto\Config;

use Borlabs\Cookie\Dto\AbstractDto;

final class LanguageOptionDto extends AbstractDto
{
    public string $code;

    public string $name;

    public string $url;

    public function __construct(
        string $code,
        string $name,
        string $url
    ) {
        $this->code = $code;
        $this->name = $name;
        $this->url = $url;
    }
}
