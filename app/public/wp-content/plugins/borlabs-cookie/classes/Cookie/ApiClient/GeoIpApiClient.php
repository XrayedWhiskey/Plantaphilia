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
use Borlabs\Cookie\ApiClient\Transformer\AttachmentTransformer;
use Borlabs\Cookie\Dto\Attachment\AttachmentDto;
use Borlabs\Cookie\Exception\ApiClient\ApiClientInvalidLicenseException;
use Borlabs\Cookie\Exception\ApiClient\GeoIpApiClientException;
use Borlabs\Cookie\HttpClient\HttpClient;
use Borlabs\Cookie\System\License\License;

final class GeoIpApiClient
{
    public const API_URL = 'https://service.borlabs.io/api/v1';

    private AttachmentTransformer $attachmentTransformer;

    private HttpClient $httpClient;

    private License $license;

    private WpFunction $wpFunction;

    public function __construct(
        AttachmentTransformer $attachmentTransformer,
        HttpClient $httpClient,
        License $license,
        WpFunction $wpFunction
    ) {
        $this->attachmentTransformer = $attachmentTransformer;
        $this->httpClient = $httpClient;
        $this->license = $license;
        $this->wpFunction = $wpFunction;
    }

    /**
     * @throws \Borlabs\Cookie\Exception\ApiClient\ApiClientInvalidLicenseException
     * @throws \Borlabs\Cookie\Exception\ApiClient\GeoIpApiClientException
     * @throws \Borlabs\Cookie\Exception\HttpClient\ConnectionErrorException
     * @throws \Borlabs\Cookie\Exception\HttpClient\ServerErrorException
     */
    public function requestGeoIpDatabaseAttachmentData(): AttachmentDto
    {
        $licenseDto = $this->license->get();

        if (is_null($licenseDto)) {
            throw new ApiClientInvalidLicenseException('licenseMissing');
        }

        $serviceResponse = $this->httpClient->get(
            self::API_URL . '/attachments/geo-ip-country',
            (object) [
                'backendUrl' => $this->wpFunction->getSiteUrl(),
                'frontendUrl' => $this->wpFunction->getHomeUrl(),
                'licenseKey' => $licenseDto->licenseKey,
                'version' => BORLABS_COOKIE_VERSION,
            ],
            $licenseDto->siteSalt,
        );

        if ($serviceResponse->success !== true) {
            throw new GeoIpApiClientException($serviceResponse->messageCode);
        }

        return $this->attachmentTransformer->toDto($serviceResponse->data);
    }
}
