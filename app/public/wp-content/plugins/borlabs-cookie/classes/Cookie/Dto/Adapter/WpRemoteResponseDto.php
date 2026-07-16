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

namespace Borlabs\Cookie\Dto\Adapter;

use Borlabs\Cookie\Dto\AbstractDto;
use Borlabs\Cookie\DtoList\System\KeyValueDtoList;

class WpRemoteResponseDto extends AbstractDto
{
    public ?string $body = null;

    public ?KeyValueDtoList $errors = null;

    public ?KeyValueDtoList $headers = null;

    public bool $redirected = false;

    public ?int $responseCode = null;

    public ?string $responseMessage = null;

    public ?string $url = null;
}
