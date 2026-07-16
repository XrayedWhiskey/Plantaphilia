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

use Borlabs\Cookie\Adapter\WpFunction;
use Borlabs\Cookie\Dto\ApiClient\ServiceResponseDto;
use Borlabs\Cookie\Exception\HttpClient\ConnectionErrorException;
use Borlabs\Cookie\Exception\HttpClient\ServerErrorException;
use Borlabs\Cookie\Support\Hmac;
use Borlabs\Cookie\Support\Transformer;
use Borlabs\Cookie\System\Log\Log;

final class HttpClient implements HttpClientInterface
{
    private Log $log;

    private WpFunction $wpFunction;

    public function __construct(
        Log $log,
        WpFunction $wpFunction
    ) {
        $this->log = $log;
        $this->wpFunction = $wpFunction;
    }

    /**
     * @throws \Borlabs\Cookie\Exception\HttpClient\ConnectionErrorException
     * @throws \Borlabs\Cookie\Exception\HttpClient\ServerErrorException
     */
    public function delete(
        string $url,
        object $data,
        ?string $salt = null
    ): ServiceResponseDto {
        return $this->execute('DELETE', $url, $data, $salt);
    }

    /**
     * @throws \Borlabs\Cookie\Exception\HttpClient\ConnectionErrorException
     * @throws \Borlabs\Cookie\Exception\HttpClient\ServerErrorException
     */
    public function get(
        string $url,
        ?object $data = null,
        ?string $salt = null
    ): ServiceResponseDto {
        return $this->execute('GET', $url, $data, $salt);
    }

    /**
     * @throws \Borlabs\Cookie\Exception\HttpClient\ConnectionErrorException
     * @throws \Borlabs\Cookie\Exception\HttpClient\ServerErrorException
     */
    public function post(
        string $url,
        object $data,
        ?string $salt = null
    ): ServiceResponseDto {
        return $this->execute('POST', $url, $data, $salt);
    }

    private function addAuthenticationHeader(
        array $args,
        object $body,
        string $salt
    ): array {
        $args['headers'] = [
            'X-Borlabs-Cookie-Auth' => Hmac::hash($body, $salt),
        ];

        return $args;
    }

    /**
     * By default, cURL sends the "Expect" header all the time which severely impacts
     * performance. Instead, we'll send it if the body is larger than 1 mb like
     * Guzzle does.
     * Source: https://gist.github.com/carlalexander/c779b473f62dcd1a4ca26fcaa637ec59.
     */
    private function addExpectHeader(
        array $args,
        ?object $body
    ): array {
        $bodyLength = strlen(
            implode(
                '',
                Transformer::flattenArray((array) ($body ?? [])),
            ),
        );
        $args['headers']['expect'] = !empty($bodyLength) && $bodyLength > 1048576 ? '100-Continue' : '';

        return $args;
    }

    /**
     * @throws \Borlabs\Cookie\Exception\HttpClient\ConnectionErrorException
     * @throws \Borlabs\Cookie\Exception\HttpClient\ServerErrorException
     */
    private function execute(
        string $method,
        string $url,
        ?object $data = null,
        ?string $salt = null
    ): ServiceResponseDto {
        if ($data !== null) {
            $data = $this->prepareDataForWordPressHttpRequestApi($data);
        }

        $args = [
            'timeout' => 45,
            'body' => (array) $data,
        ];
        $url = $this->replaceURL($url);

        if (is_string($salt)) {
            $args = $this->addAuthenticationHeader($args, $data, $salt);
        }

        $args = $this->addExpectHeader($args, $data);

        if ($method === 'POST') {
            $response = $this->wpFunction->wpRemotePost(
                $url,
                $args,
            );
        } elseif ($method === 'DELETE') {
            $response = $this->wpFunction->wpRemotePost(
                $url,
                array_merge($args, ['method' => 'DELETE']),
            );
        } elseif ($method === 'GET') {
            $response = $this->wpFunction->wpRemoteGet(
                $url,
                $args,
            );
        }

        if (!isset($response)) {
            throw new ConnectionErrorException('unsupportedMethod', ['method' => $method]);
        }

        // Server error - e.g. domain not found or ssl certificate problem
        if (isset($response->errors)) {
            throw new ConnectionErrorException('cUrlError', ['errorMessage' => $response->errors->list[0]->value]);
        }

        if ($response->responseCode >= 500) {
            throw new ServerErrorException($response->responseCode . ' ' . $response->responseMessage);
        }

        $serviceResponse = (object) json_decode($response->body);

        // Requested resource not available or bad request
        if ($response->responseCode !== 200) {
            if ($url !== $response->url && $response->redirected) {
                $this->log->error(
                    'REST request failed due to unexpected redirection.',
                    [
                        'originalUrl' => $url,
                        'redirectedUrl' => $response->url,
                    ],
                );
            }

            return new ServiceResponseDto(
                false,
                $response->responseCode,
                $serviceResponse->messageCode ?? $response->responseMessage ?? 'unknown',
                $serviceResponse,
            );
        }

        return new ServiceResponseDto(
            true,
            200,
            '',
            $serviceResponse,
        );
    }

    /**
     * WordPress uses the get_object_vars() function to access the data of objects instead of using a __toString method.
     * Since all properties of our ENUMs, for example, are private, get_object_vars() cannot access their values.
     * Therefore, we need to convert all typed objects beforehand so that the WordPress HTTP Request API can handle them.
     */
    private function prepareDataForWordPressHttpRequestApi(
        object $data
    ): object {
        return json_decode(json_encode($data));
    }

    /**
     * Replaces the URL with the BORLABS_COOKIE_DEV_MODE_REPLACE_API_URLS constant.
     * Warning: Use the BORLABS_COOKIE_DEV_MODE_REPLACE_API_URLS constant only for development setups.
     */
    private function replaceURL(string $url): string
    {
        if (defined('BORLABS_COOKIE_DEV_MODE_REPLACE_API_URLS') && is_array(constant('BORLABS_COOKIE_DEV_MODE_REPLACE_API_URLS'))) {
            foreach (constant('BORLABS_COOKIE_DEV_MODE_REPLACE_API_URLS') as $key => $value) {
                $url = str_replace($key, $value, $url);
            }
        }

        return $url;
    }
}
