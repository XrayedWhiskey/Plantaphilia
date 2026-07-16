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

final class HttpMockClient implements HttpClientInterface
{
    public function __construct()
    {
    }

    public function get(
        string $url,
        object $data,
        ?string $salt = null
    ): ServiceResponseDto {
        return $this->execute('GET', $url, $data, $salt);
    }

    public function post(
        string $url,
        object $data,
        ?string $salt = null
    ): ServiceResponseDto {
        return $this->execute('POST', $url, $data, $salt);
    }

    private function execute(
        string $method,
        string $url,
        object $data,
        ?string $salt = null
    ): ServiceResponseDto {
        return new ServiceResponseDto(
            false,
            (string) 'Hello',
            (string) 'from the other side',
            (object) null,
        );
    }
}
