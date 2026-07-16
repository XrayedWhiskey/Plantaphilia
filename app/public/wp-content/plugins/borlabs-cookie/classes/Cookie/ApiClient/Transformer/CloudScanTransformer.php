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

namespace Borlabs\Cookie\ApiClient\Transformer;

use Borlabs\Cookie\Dto\CloudScan\CookieDto;
use Borlabs\Cookie\Dto\CloudScan\CookieExampleDto;
use Borlabs\Cookie\Dto\CloudScan\ExternalResourceDto;
use Borlabs\Cookie\Dto\CloudScan\ExternalResourceExampleDto;
use Borlabs\Cookie\Dto\CloudScan\PageDto;
use Borlabs\Cookie\Dto\CloudScan\ScanResponseDto;
use Borlabs\Cookie\Dto\CloudScan\SuggestionDto;
use Borlabs\Cookie\Dto\CloudScan\SuggestionPageDto;
use Borlabs\Cookie\DtoList\CloudScan\CookieDtoList;
use Borlabs\Cookie\DtoList\CloudScan\CookieExampleDtoList;
use Borlabs\Cookie\DtoList\CloudScan\ExternalResourceDtoList;
use Borlabs\Cookie\DtoList\CloudScan\ExternalResourceExampleDtoList;
use Borlabs\Cookie\DtoList\CloudScan\PageDtoList;
use Borlabs\Cookie\DtoList\CloudScan\SuggestionDtoList;
use Borlabs\Cookie\DtoList\CloudScan\SuggestionPagesDtoList;
use Borlabs\Cookie\Enum\CloudScan\CloudScanStatusEnum;
use Borlabs\Cookie\Enum\CloudScan\CloudScanTypeEnum;
use Borlabs\Cookie\Enum\CloudScan\PageFailureTypeEnum;
use Borlabs\Cookie\Enum\CloudScan\PageStatusEnum;
use DateTime;

final class CloudScanTransformer
{
    public function toDto(object $cloudScan): ScanResponseDto
    {
        $dto = new ScanResponseDto();
        $dto->cookies = $this->getCookies($cloudScan->cookies);
        $dto->externalResources = $this->getExternalResources($cloudScan->externalResources);
        $dto->finishedAt = $cloudScan->finishedAt !== null ? new DateTime($cloudScan->finishedAt) : null;
        $dto->id = $cloudScan->id;
        $dto->pages = $this->getPages($cloudScan->pages);
        $dto->status = CloudScanStatusEnum::fromValue($cloudScan->status);
        $dto->suggestions = $this->getSuggestions($cloudScan->suggestions);
        $dto->type = CloudScanTypeEnum::fromValue($cloudScan->type);

        return $dto;
    }

    private function getCookies(?array $cookies): CookieDtoList
    {
        return new CookieDtoList(
            array_map(
                fn ($cookie) => new CookieDto(
                    $cookie->name,
                    $cookie->hostname,
                    $cookie->path,
                    new CookieExampleDtoList(
                        array_map(
                            fn ($example) => new CookieExampleDto(
                                $example->pageId,
                                $example->pageUrl,
                            ),
                            $cookie->examples,
                        ),
                    ),
                    $cookie->lifetime,
                    $cookie->packageKey,
                ),
                $cookies,
            ),
        );
    }

    private function getExternalResources(?array $externalResources): ExternalResourceDtoList
    {
        return new ExternalResourceDtoList(
            array_map(
                fn ($externalResource) => new ExternalResourceDto(
                    $externalResource->hostname,
                    new ExternalResourceExampleDtoList(
                        array_map(
                            fn ($example) => new ExternalResourceExampleDto(
                                $example->pageId,
                                $example->pageUrl,
                                $example->resourceUrl,
                            ),
                            $externalResource->examples,
                        ),
                    ),
                    $externalResource->packageKey,
                ),
                $externalResources,
            ),
        );
    }

    private function getPages(?array $pages): PageDtoList
    {
        return new PageDtoList(
            array_map(
                fn ($page) => new PageDto(
                    $page->url,
                    PageStatusEnum::fromValue($page->status),
                    $page->failureType !== null ? PageFailureTypeEnum::fromValue($page->failureType) : null,
                ),
                $pages,
            ),
        );
    }

    private function getSuggestions(?array $suggestions): SuggestionDtoList
    {
        return new SuggestionDtoList(
            array_map(
                fn ($suggestion) => new SuggestionDto(
                    $suggestion->packageKey,
                    new SuggestionPagesDtoList(
                        array_map(
                            fn ($url) => new SuggestionPageDto($url->url),
                            $suggestion->pages,
                        ),
                    ),
                ),
                $suggestions,
            ),
        );
    }
}
