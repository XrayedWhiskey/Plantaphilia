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

namespace Borlabs\Cookie\Dto\RestEndpoint;

use Borlabs\Cookie\Dto\AbstractDto;

abstract class AbstractDownloadResponseDto extends AbstractDto
{
    public bool $isDatabaseDownloaded = false;

    public ?string $lastSuccessfulCheckWithApiFormattedTime = null;

    public string $message;

    public string $messageRaw;

    public function __construct(
        bool $isDatabaseDownloaded,
        string $message,
        string $messageRaw,
        ?string $lastSuccessfulCheckWithApiFormattedTime = null
    ) {
        $this->lastSuccessfulCheckWithApiFormattedTime = $lastSuccessfulCheckWithApiFormattedTime;
        $this->isDatabaseDownloaded = $isDatabaseDownloaded;
        $this->message = $message;
        $this->messageRaw = $messageRaw;
    }
}
