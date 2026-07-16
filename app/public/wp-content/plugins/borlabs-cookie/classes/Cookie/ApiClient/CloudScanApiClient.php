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
use Borlabs\Cookie\ApiClient\Transformer\CloudScanTransformer;
use Borlabs\Cookie\Dto\CloudScan\ScanResponseDto;
use Borlabs\Cookie\DtoList\CloudScan\InstalledPluginDtoList;
use Borlabs\Cookie\DtoList\CloudScan\InstalledThemeDtoList;
use Borlabs\Cookie\Enum\CloudScan\CloudScanTypeEnum;
use Borlabs\Cookie\Exception\ApiClient\ApiClientInvalidLicenseException;
use Borlabs\Cookie\Exception\ApiClient\CloudScanApiClientException;
use Borlabs\Cookie\HttpClient\HttpClient;
use Borlabs\Cookie\System\License\License;

class CloudScanApiClient
{
    public const API_URL = 'https://service.borlabs.io/api/v1';

    private CloudScanTransformer $cloudScanTransformer;

    private HttpClient $httpClient;

    private License $license;

    private WpFunction $wpFunction;

    public function __construct(
        CloudScanTransformer $cloudScanTransformer,
        HttpClient $httpClient,
        License $license,
        WpFunction $wpFunction
    ) {
        $this->cloudScanTransformer = $cloudScanTransformer;
        $this->httpClient = $httpClient;
        $this->license = $license;
        $this->wpFunction = $wpFunction;
    }

    /**
     * @throws \Borlabs\Cookie\Exception\ApiClient\ApiClientInvalidLicenseException
     * @throws \Borlabs\Cookie\Exception\ApiClient\CloudScanApiClientException
     * @throws \Borlabs\Cookie\Exception\HttpClient\ConnectionErrorException
     * @throws \Borlabs\Cookie\Exception\HttpClient\ServerErrorException
     */
    public function getScan(string $id): ScanResponseDto
    {
        $licenseDto = $this->license->get();

        if (is_null($licenseDto)) {
            throw new ApiClientInvalidLicenseException('licenseMissing');
        }

        $serviceResponse = $this->httpClient->get(
            self::API_URL . '/scans/' . $id,
            (object) [
                'backendUrl' => $this->wpFunction->getSiteUrl(),
                'frontendUrl' => $this->wpFunction->getHomeUrl(),
                'licenseKey' => $licenseDto->licenseKey,
                'product' => BORLABS_COOKIE_SLUG,
                'version' => BORLABS_COOKIE_VERSION,
            ],
            $licenseDto->siteSalt,
        );

        if ($serviceResponse->success === false) {
            throw new CloudScanApiClientException($serviceResponse->messageCode);
        }

        if (!isset($serviceResponse->data)) {
            throw new CloudScanApiClientException('invalidResponse');
        }

        return $this->cloudScanTransformer->toDto($serviceResponse->data);
    }

    /**
     * @throws CloudScanApiClientException
     * @throws \Borlabs\Cookie\Exception\HttpClient\ConnectionErrorException
     * @throws \Borlabs\Cookie\Exception\HttpClient\ServerErrorException
     * @throws \Borlabs\Cookie\Exception\ApiClient\ApiClientInvalidLicenseException
     */
    public function requestScanCreation(
        InstalledPluginDtoList $installedPlugins,
        InstalledThemeDtoList $installedThemes,
        array $urls,
        CloudScanTypeEnum $scanType,
        ?string $httpAuthUsername = null,
        ?string $httpAuthPassword = null
    ): ScanResponseDto {
        $licenseDto = $this->license->get();

        if (is_null($licenseDto)) {
            throw new ApiClientInvalidLicenseException('licenseMissing');
        }

        $serviceResponse = $this->httpClient->post(
            self::API_URL . '/scans',
            (object) [
                'backendUrl' => $this->wpFunction->getSiteUrl(),
                'basicAuth' => isset($httpAuthUsername, $httpAuthPassword) ? [
                    'password' => $httpAuthPassword,
                    'username' => $httpAuthUsername,
                ] : null,
                'frontendUrl' => $this->wpFunction->getHomeUrl(),
                'installedPlugins' => $installedPlugins->list,
                'installedThemes' => $installedThemes->list,
                'licenseKey' => $licenseDto->licenseKey,
                'product' => BORLABS_COOKIE_SLUG,
                'type' => $scanType->value,
                'urls' => $urls,
                'version' => BORLABS_COOKIE_VERSION,
            ],
            $licenseDto->siteSalt,
        );

        if ($serviceResponse->success === false) {
            throw new CloudScanApiClientException($serviceResponse->messageCode ?? null);
        }

        if (!isset($serviceResponse->data)) {
            throw new CloudScanApiClientException('invalidResponse');
        }

        return $this->cloudScanTransformer->toDto($serviceResponse->data);
    }
}
