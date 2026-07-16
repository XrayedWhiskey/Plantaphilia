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
use Borlabs\Cookie\ApiClient\Transformer\LicenseTransformer;
use Borlabs\Cookie\Dto\License\LicenseDto;
use Borlabs\Cookie\Exception\ApiClient\LicenseApiClientException;
use Borlabs\Cookie\HttpClient\HttpClient;
use Borlabs\Cookie\Support\Database;

/**
 * Singleton class LicenseApiClient.
 *
 * The **LicenseApiClient** class provides methods for interacting with the license server.
 *
 * @see \Borlabs\Cookie\ApiClient\LicenseApiClient::register() Registers the website using the license key.
 */
final class LicenseApiClient
{
    public const API_URL = 'https://service.borlabs.io/api/v1';

    private HttpClient $httpClient;

    private LicenseTransformer $licenseTransformer;

    private WpFunction $wpFunction;

    public function __construct(
        HttpClient $httpClient,
        LicenseTransformer $licenseTransformer,
        WpFunction $wpFunction
    ) {
        $this->httpClient = $httpClient;
        $this->licenseTransformer = $licenseTransformer;
        $this->wpFunction = $wpFunction;
    }

    /**
     * Registers the website using the license key.
     * This method returns license information from the license server, which must be stored.
     *
     * @throws \Borlabs\Cookie\Exception\ApiClient\LicenseApiClientException
     * @throws \Borlabs\Cookie\Exception\HttpClient\ConnectionErrorException
     * @throws \Borlabs\Cookie\Exception\HttpClient\ServerErrorException
     */
    public function register(string $licenseKey): LicenseDto
    {
        $serviceResponse = $this->httpClient->post(
            self::API_URL . '/register/borlabs-cookie/site',
            (object) [
                'backendUrl' => $this->wpFunction->getSiteUrl(),
                'dbVersion' => Database::getDbVersion(),
                'frontendUrl' => $this->wpFunction->getHomeUrl(),
                'licenseKey' => trim($licenseKey),
                'phpVersion' => phpversion(), // Used to distinguish between PHP versions for backwards compatibility
                'version' => BORLABS_COOKIE_VERSION,
                'wpVersion' => $this->wpFunction->getBlogInfo('version'),
            ],
        );

        if ($serviceResponse->success === false) {
            throw new LicenseApiClientException($serviceResponse->messageCode);
        }

        return $this->licenseTransformer->toDto($serviceResponse->data);
    }

    /**
     * @throws \Borlabs\Cookie\Exception\ApiClient\LicenseApiClientException
     * @throws \Borlabs\Cookie\Exception\HttpClient\ConnectionErrorException
     * @throws \Borlabs\Cookie\Exception\HttpClient\ServerErrorException
     */
    public function unregister(LicenseDto $licenseDto): bool
    {
        $serviceResponse = $this->httpClient->delete(
            self::API_URL . '/unregister/borlabs-cookie/site',
            (object) [
                'backendUrl' => $this->wpFunction->getSiteUrl(),
                'frontendUrl' => $this->wpFunction->getHomeUrl(),
                'licenseKey' => $licenseDto->licenseKey,
                'siteSalt' => $licenseDto->siteSalt,
                'version' => BORLABS_COOKIE_VERSION,
            ],
        );

        if ($serviceResponse->success === false) {
            throw new LicenseApiClientException($serviceResponse->messageCode);
        }

        return true;
    }
}
