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

namespace Borlabs\Cookie\HttpClient;

use Borlabs\Cookie\Dto\ApiClient\ServiceResponseDto;
use Borlabs\Cookie\Exception\HttpClient\ConnectionErrorException;
use Borlabs\Cookie\Exception\HttpClient\ServerErrorException;

interface HttpClientInterface
{
    /**
     * @throws ConnectionErrorException
     * @throws ServerErrorException
     */
    public function get(
        string $url,
        object $data,
        ?string $salt = null
    ): ServiceResponseDto;

    /**
     * @throws ConnectionErrorException
     * @throws ServerErrorException
     */
    public function post(
        string $url,
        object $data,
        ?string $salt = null
    ): ServiceResponseDto;
}
