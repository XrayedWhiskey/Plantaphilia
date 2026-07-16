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

use Borlabs\Cookie\ApiClient\Transformer\NewsListTransformer;
use Borlabs\Cookie\DtoList\News\NewsListDto;
use Borlabs\Cookie\Exception\ApiClient\NewsApiClientException;
use Borlabs\Cookie\HttpClient\HttpClientInterface;

final class NewsApiClient
{
    public const API_URL = 'https://service.borlabs.io/api/v1';

    private HttpClientInterface $httpClient;

    private NewsListTransformer $newsListTransformer;

    public function __construct(
        HttpClientInterface $httpClient,
        NewsListTransformer $newsListTransformer
    ) {
        $this->httpClient = $httpClient;
        $this->newsListTransformer = $newsListTransformer;
    }

    /**
     * @throws \Borlabs\Cookie\Exception\ApiClient\NewsApiClientException
     * @throws \Borlabs\Cookie\Exception\HttpClient\ConnectionErrorException
     */
    public function requestNews(): NewsListDto
    {
        $serviceResponse = $this->httpClient->get(
            self::API_URL . '/news',
            (object) [
                'product' => BORLABS_COOKIE_SLUG,
                'version' => BORLABS_COOKIE_VERSION,
            ],
        );

        if ($serviceResponse->success === false) {
            throw new NewsApiClientException($serviceResponse->messageCode);
        }

        return $this->newsListTransformer->toDto($serviceResponse->data);
    }
}
