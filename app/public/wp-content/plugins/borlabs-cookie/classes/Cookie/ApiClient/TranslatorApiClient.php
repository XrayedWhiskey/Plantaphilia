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
use Borlabs\Cookie\ApiClient\Transformer\LanguageSpecificKeyValueListTransformer;
use Borlabs\Cookie\DtoList\System\KeyValueDtoList;
use Borlabs\Cookie\DtoList\Translator\LanguageSpecificKeyValueDtoList;
use Borlabs\Cookie\DtoList\Translator\TargetLanguageEnumDtoList;
use Borlabs\Cookie\Enum\Translator\SourceLanguageEnum;
use Borlabs\Cookie\Exception\ApiClient\ApiClientInvalidLicenseException;
use Borlabs\Cookie\Exception\ApiClient\TranslatorApiClientException;
use Borlabs\Cookie\HttpClient\HttpClientInterface;
use Borlabs\Cookie\System\License\License;

final class TranslatorApiClient
{
    public const API_URL = 'https://service.borlabs.io/api/v1';

    private HttpClientInterface $httpClient;

    private LanguageSpecificKeyValueListTransformer $languageSpecificKeyValueListTransformer;

    private License $license;

    private WpFunction $wpFunction;

    public function __construct(
        HttpClientInterface $httpClient,
        LanguageSpecificKeyValueListTransformer $languageSpecificKeyValueListTransformer,
        License $license,
        WpFunction $wpFunction
    ) {
        $this->httpClient = $httpClient;
        $this->languageSpecificKeyValueListTransformer = $languageSpecificKeyValueListTransformer;
        $this->license = $license;
        $this->wpFunction = $wpFunction;
    }

    /**
     * @throws \Borlabs\Cookie\Exception\ApiClient\TranslatorApiClientException
     * @throws \Borlabs\Cookie\Exception\HttpClient\ConnectionErrorException
     * @throws \Borlabs\Cookie\Exception\HttpClient\ServerErrorException
     * @throws \Borlabs\Cookie\Exception\ApiClient\ApiClientInvalidLicenseException
     */
    public function translate(
        SourceLanguageEnum $sourceLanguage,
        TargetLanguageEnumDtoList $targetLanguages,
        KeyValueDtoList $sourceTexts
    ): LanguageSpecificKeyValueDtoList {
        $licenseDto = $this->license->get();

        if (is_null($licenseDto)) {
            throw new ApiClientInvalidLicenseException('licenseMissing');
        }

        $serviceResponse = $this->httpClient->post(
            self::API_URL . '/translate',
            (object) [
                'backendUrl' => $this->wpFunction->getSiteUrl(),
                'frontendUrl' => $this->wpFunction->getHomeUrl(),
                'licenseKey' => $licenseDto->licenseKey,
                'sourceLanguage' => $sourceLanguage->value,
                'sourceTexts' => array_column($sourceTexts->list, 'value'),
                'targetLanguages' => array_column(array_column($targetLanguages->list, 'targetLanguageEnum'), 'value'),
                'version' => BORLABS_COOKIE_VERSION,
            ],
        );

        if ($serviceResponse->success === false) {
            throw new TranslatorApiClientException($serviceResponse->messageCode);
        }

        return $this->languageSpecificKeyValueListTransformer->toDto($serviceResponse->data, $sourceTexts);
    }
}
