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
use Borlabs\Cookie\ApiClient\Transformer\LatestPluginVersionTransformer;
use Borlabs\Cookie\ApiClient\Transformer\PluginInformationTransformer;
use Borlabs\Cookie\Dto\WordPress\LatestPluginVersionDto;
use Borlabs\Cookie\Dto\WordPress\PluginInformationDto;
use Borlabs\Cookie\Exception\ApiClient\PluginUpdateApiClientException;
use Borlabs\Cookie\HttpClient\HttpClient;
use Borlabs\Cookie\System\License\License;
use Borlabs\Cookie\System\SystemCheck\SystemCheck;

final class PluginUpdateApiClient
{
    public const API_URL = 'https://service.borlabs.io/api/v1';

    private HttpClient $httpClient;

    private LatestPluginVersionTransformer $latestPluginVersionTransformer;

    private License $license;

    private PluginInformationTransformer $pluginInformationTransformer;

    private SystemCheck $systemCheck;

    private WpFunction $wpFunction;

    public function __construct(
        HttpClient $httpClient,
        LatestPluginVersionTransformer $latestPluginVersionTransformer,
        License $license,
        PluginInformationTransformer $pluginInformationTransformer,
        SystemCheck $systemCheck,
        WpFunction $wpFunction
    ) {
        $this->httpClient = $httpClient;
        $this->latestPluginVersionTransformer = $latestPluginVersionTransformer;
        $this->license = $license;
        $this->pluginInformationTransformer = $pluginInformationTransformer;
        $this->systemCheck = $systemCheck;
        $this->wpFunction = $wpFunction;
    }

    /**
     * @throws \Borlabs\Cookie\Exception\ApiClient\PluginUpdateApiClientException
     * @throws \Borlabs\Cookie\Exception\IncompatibleTypeException
     * @throws \Borlabs\Cookie\Exception\HttpClient\ConnectionErrorException
     * @throws \Borlabs\Cookie\Exception\HttpClient\ServerErrorException
     */
    public function requestLatestPluginVersion(): LatestPluginVersionDto
    {
        $licenseDto = $this->license->get();
        $serviceResponse = $this->httpClient->post(
            self::API_URL . '/latest-version/' . BORLABS_COOKIE_SLUG,
            (object) [
                'backendUrl' => $this->wpFunction->getSiteUrl(),
                'dbVersion' => $this->systemCheck->getDbVersion(),
                'frontendUrl' => $this->wpFunction->getHomeUrl(),
                'licenseKey' => $licenseDto->licenseKey ?? '',
                'phpVersion' => phpversion(),
                'product' => BORLABS_COOKIE_SLUG,
                'securityPatchesForExpiredLicenses' => !$this->license->isLicenseValid(),
                'version' => BORLABS_COOKIE_VERSION,
                'wpVersion' => $this->wpFunction->getBlogInfo('version'),
            ],
            $licenseDto->siteSalt ?? null,
        );

        if ($serviceResponse->success === false) {
            throw new PluginUpdateApiClientException($serviceResponse->messageCode);
        }

        return $this->latestPluginVersionTransformer->toDto($serviceResponse->data);
    }

    /**
     * @throws \Borlabs\Cookie\Exception\ApiClient\PluginUpdateApiClientException
     * @throws \Borlabs\Cookie\Exception\IncompatibleTypeException
     * @throws \Borlabs\Cookie\Exception\HttpClient\ConnectionErrorException
     * @throws \Borlabs\Cookie\Exception\HttpClient\ServerErrorException
     */
    public function requestPluginInformation(): PluginInformationDto
    {
        $licenseDto = $this->license->get();
        $serviceResponse = $this->httpClient->post(
            self::API_URL . '/plugin-information/' . BORLABS_COOKIE_SLUG,
            (object) [
                'backendUrl' => $this->wpFunction->getSiteUrl(),
                'frontendUrl' => $this->wpFunction->getHomeUrl(),
                'language' => $this->wpFunction->getLocale(),
                'licenseKey' => $licenseDto->licenseKey ?? '',
                'product' => BORLABS_COOKIE_SLUG,
                'version' => BORLABS_COOKIE_VERSION,
            ],
            $licenseDto->siteSalt ?? null,
        );

        if ($serviceResponse->success === false) {
            throw new PluginUpdateApiClientException($serviceResponse->messageCode);
        }

        return $this->pluginInformationTransformer->toDto($serviceResponse->data);
    }
}
