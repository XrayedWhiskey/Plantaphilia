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
use Borlabs\Cookie\ApiClient\Transformer\InstallationPackageTransformer;
use Borlabs\Cookie\ApiClient\Transformer\PackageListTransformer;
use Borlabs\Cookie\ApiClient\Transformer\SuggstedPackageListTransformer;
use Borlabs\Cookie\Dto\Package\InstallationPackageDto;
use Borlabs\Cookie\DtoList\Package\PackageDtoList;
use Borlabs\Cookie\DtoList\Package\SuggestedPackageDtoList;
use Borlabs\Cookie\Exception\ApiClient\ApiClientInvalidLicenseException;
use Borlabs\Cookie\Exception\ApiClient\PackageApiClientException;
use Borlabs\Cookie\HttpClient\HttpClient;
use Borlabs\Cookie\System\CloudScan\CloudScanService;
use Borlabs\Cookie\System\License\License;

final class PackageApiClient
{
    public const API_URL = 'https://service.borlabs.io/api/v1';

    private CloudScanService $cloudScanService;

    private HttpClient $httpClient;

    private InstallationPackageTransformer $installationPackageTransformer;

    private License $license;

    private PackageListTransformer $packageListTransformer;

    private SuggstedPackageListTransformer $suggestedPackageListTransformer;

    private WpFunction $wpFunction;

    public function __construct(
        CloudScanService $cloudScanService,
        HttpClient $httpClient,
        InstallationPackageTransformer $installationPackageTransformer,
        License $license,
        PackageListTransformer $packageListTransformer,
        SuggstedPackageListTransformer $suggestedPackageListTransformer,
        WpFunction $wpFunction
    ) {
        $this->cloudScanService = $cloudScanService;
        $this->httpClient = $httpClient;
        $this->installationPackageTransformer = $installationPackageTransformer;
        $this->license = $license;
        $this->packageListTransformer = $packageListTransformer;
        $this->suggestedPackageListTransformer = $suggestedPackageListTransformer;
        $this->wpFunction = $wpFunction;
    }

    /**
     * @throws \Borlabs\Cookie\Exception\HttpClient\ConnectionErrorException
     * @throws \Borlabs\Cookie\Exception\ApiClient\PackageApiClientException
     * @throws \Borlabs\Cookie\Exception\HttpClient\ServerErrorException
     * @throws \Borlabs\Cookie\Exception\ApiClient\ApiClientInvalidLicenseException
     */
    public function requestPackage(string $packageKey): InstallationPackageDto
    {
        $licenseDto = $this->license->get();

        if (is_null($licenseDto)) {
            throw new ApiClientInvalidLicenseException('licenseMissing');
        }

        $serviceResponse = $this->httpClient->get(
            self::API_URL . '/package/' . $packageKey,
            (object) [
                'backendUrl' => $this->wpFunction->getSiteUrl(),
                'frontendUrl' => $this->wpFunction->getHomeUrl(),
                'licenseKey' => $licenseDto->licenseKey,
                'useDraftVersion' => defined('BORLABS_COOKIE_DEV_MODE_SHOW_DRAFT_PACKAGES') && constant('BORLABS_COOKIE_DEV_MODE_SHOW_DRAFT_PACKAGES'),
                'version' => BORLABS_COOKIE_VERSION,
            ],
            $licenseDto->siteSalt,
        );

        if ($serviceResponse->success === false) {
            throw new PackageApiClientException($serviceResponse->messageCode);
        }

        return $this->installationPackageTransformer->toDto($serviceResponse->data);
    }

    /**
     * @throws \Borlabs\Cookie\Exception\HttpClient\ConnectionErrorException
     * @throws \Borlabs\Cookie\Exception\ApiClient\PackageApiClientException
     * @throws \Borlabs\Cookie\Exception\HttpClient\ServerErrorException
     */
    public function requestPackages(): PackageDtoList
    {
        $serviceResponse = $this->httpClient->get(
            self::API_URL . '/packages',
            (object) [
                'showDraftPackages' => defined('BORLABS_COOKIE_DEV_MODE_SHOW_DRAFT_PACKAGES') && constant('BORLABS_COOKIE_DEV_MODE_SHOW_DRAFT_PACKAGES'),
                'showExperimentalPackages' => defined('BORLABS_COOKIE_DEV_MODE_SHOW_EXPERIMENTAL_PACKAGES') && constant('BORLABS_COOKIE_DEV_MODE_SHOW_EXPERIMENTAL_PACKAGES'),
            ],
        );

        if ($serviceResponse->success === false) {
            throw new PackageApiClientException($serviceResponse->messageCode);
        }

        return $this->packageListTransformer->toDto($serviceResponse->data);
    }

    /**
     * @throws \Borlabs\Cookie\Exception\ApiClient\PackageApiClientException
     * @throws \Borlabs\Cookie\Exception\HttpClient\ConnectionErrorException
     * @throws \Borlabs\Cookie\Exception\HttpClient\ServerErrorException
     * @throws \Borlabs\Cookie\Exception\ApiClient\ApiClientInvalidLicenseException
     */
    public function requestPackageSuggestions(bool $onlyEnabledPlugins = false, bool $onlyEnabledTheme = false): SuggestedPackageDtoList
    {
        $licenseDto = $this->license->get();

        if (is_null($licenseDto)) {
            throw new ApiClientInvalidLicenseException('licenseMissing');
        }

        $serviceResponse = $this->httpClient->post(
            self::API_URL . '/packages/suggestions',
            (object) [
                'backendUrl' => $this->wpFunction->getSiteUrl(),
                'frontendUrl' => $this->wpFunction->getHomeUrl(),
                'installedPlugins' => $this->cloudScanService->getInstalledPlugins($onlyEnabledPlugins)->list,
                'installedThemes' => $this->cloudScanService->getInstalledThemes($onlyEnabledTheme)->list,
                'licenseKey' => $licenseDto->licenseKey,
                'version' => BORLABS_COOKIE_VERSION,
            ],
            $licenseDto->siteSalt,
        );

        if ($serviceResponse->success === false) {
            throw new PackageApiClientException($serviceResponse->messageCode);
        }

        return $this->suggestedPackageListTransformer->toDto($serviceResponse->data);
    }
}
