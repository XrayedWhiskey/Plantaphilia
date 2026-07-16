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

namespace Borlabs\Cookie\ApiClient;

use Borlabs\Cookie\Adapter\WpFunction;
use Borlabs\Cookie\Dto\ApiClient\ServiceResponseDto;
use Borlabs\Cookie\Exception\HttpClient\ConnectionErrorException;
use Borlabs\Cookie\Exception\HttpClient\ServerErrorException;
use Borlabs\Cookie\HttpClient\HttpClientInterface;

final class TestWordPressRestApiApiClient
{
    private HttpClientInterface $httpClient;

    private WpFunction $wpFunction;

    public function __construct(
        HttpClientInterface $httpClient,
        WpFunction $wpFunction
    ) {
        $this->httpClient = $httpClient;
        $this->wpFunction = $wpFunction;
    }

    /**
     * @throws ConnectionErrorException
     * @throws ServerErrorException
     */
    public function requestTest(): ServiceResponseDto
    {
        return $this->httpClient->post(
            $this->wpFunction->getRestUrl() . 'borlabs-cookie/v1/test',
            (object) [],
        );
    }
}
