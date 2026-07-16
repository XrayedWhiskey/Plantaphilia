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

namespace Borlabs\Cookie\System\LocalScanner;

use Borlabs\Cookie\Adapter\WpFunction;
use Borlabs\Cookie\Dto\LocalScanner\MatchedHandleDto;
use Borlabs\Cookie\Dto\LocalScanner\MatchedTagDto;
use Borlabs\Cookie\Dto\LocalScanner\ScanResultDto;
use Borlabs\Cookie\Dto\LocalScanner\UnmatchedHandleDto;
use Borlabs\Cookie\Dto\LocalScanner\UnmatchedTagDto;
use Borlabs\Cookie\Dto\System\KeyValueDto;
use Borlabs\Cookie\Enum\LocalScanner\HandleTypeEnum;
use Borlabs\Cookie\Enum\LocalScanner\TagTypeEnum;
use Borlabs\Cookie\Support\Searcher;
use Borlabs\Cookie\System\Transient\Transient;

final class ScanResultService
{
    private bool $isInitialized = false;

    private ScanRequestService $scanRequestService;

    private ScanResultDto $scanResultDto;

    private Transient $transient;

    private ?string $wordPressIncludesUrl = null;

    private ?string $wordPressPluginsUrl = null;

    private ?string $wordPressSiteUrl = null;

    private ?string $wordPressThemesUrl = null;

    private WpFunction $wpFunction;

    public function __construct(
        ScanRequestService $scanRequestService,
        Transient $transient,
        WpFunction $wpFunction
    ) {
        $this->scanResultDto = new ScanResultDto();
        $this->scanRequestService = $scanRequestService;
        $this->transient = $transient;
        $this->wpFunction = $wpFunction;
    }

    public function addMatchedHandle(string $handle, string $phrase, string $url): void
    {
        $this->init();
        $matchedHandle = new MatchedHandleDto(
            $this->determineHandleTypeByUrl($url),
            $handle,
            $phrase,
            $url,
        );
        $this->scanResultDto->matchedHandles->add($matchedHandle);
    }

    public function addMatchedTag(string $phrase, string $tag)
    {
        $this->init();
        $tagType = $this->determineTagType($tag);

        // Check if the matched tag isn't a matched handle
        $matches = [];

        if ($tagType->is(TagTypeEnum::SCRIPT())) {
            preg_match('/<script[^>]*\ssrc=(["\'])(.*?)\1[^>]*>/i', $tag, $matches);
        } else {
            preg_match('/<link[^>]*\shref=(["\'])(.*?)\1[^>]*>/i', $tag, $matches);
        }

        if (isset($matches[2])) {
            $matchedHandleUrl = Searcher::findObject($this->scanResultDto->matchedHandles->list, 'url', $matches[2]);

            if (isset($matchedHandleUrl)) {
                return;
            }
        }

        $matchedTag = new MatchedTagDto(
            $tagType,
            $phrase,
            $tag,
        );
        $this->scanResultDto->matchedTags->add($matchedTag);
    }

    public function addUnmatchedHandle(string $handle, string $url): void
    {
        $this->init();
        $unmatchedHandle = new UnmatchedHandleDto(
            $this->determineHandleTypeByUrl($url),
            $handle,
            $url,
        );
        $this->scanResultDto->unmatchedHandles->add($unmatchedHandle);
    }

    public function addUnmatchedTag(string $tag)
    {
        $this->init();
        $tagType = $this->determineTagType($tag);

        // Check if the unmatched tag isn't an unmatched handle
        $matches = [];

        if ($tagType->is(TagTypeEnum::SCRIPT())) {
            preg_match('/<script[^>]*\ssrc=(["\'])(.*?)\1[^>]*>/i', $tag, $matches);
        } else {
            preg_match('/<link[^>]*\shref=(["\'])(.*?)\1[^>]*>/i', $tag, $matches);
        }

        if (isset($matches[2])) {
            $matchedHandleUrl = Searcher::findObject($this->scanResultDto->matchedHandles->list, 'url', $matches[2]);
            $unmatchedHandleUrl = Searcher::findObject($this->scanResultDto->unmatchedHandles->list, 'url', $matches[2]);

            if (isset($matchedHandleUrl) || isset($unmatchedHandleUrl)) {
                return;
            }
        }

        $unmatchedTag = new UnmatchedTagDto(
            $tagType,
            $tag,
        );
        $this->scanResultDto->unmatchedTags->add($unmatchedTag);
    }

    public function determineHandleTypeByUrl(string $url): HandleTypeEnum
    {
        if (strpos($url, $this->wordPressThemesUrl) !== false) {
            return HandleTypeEnum::THEME();
        }

        if (strpos($url, $this->wordPressPluginsUrl) !== false) {
            return HandleTypeEnum::PLUGIN();
        }

        if (strpos($url, $this->wordPressIncludesUrl) !== false) {
            return HandleTypeEnum::CORE();
        }

        if (strpos($url, $this->wordPressSiteUrl) !== false) {
            return HandleTypeEnum::OTHER();
        }

        return HandleTypeEnum::EXTERNAL();
    }

    public function determineTagType(string $tag): TagTypeEnum
    {
        if (preg_match('/<link\b[^>]*>/i', $tag)) {
            return TagTypeEnum::LINK();
        }

        if (preg_match('/<script([^>]*)>(.*)<\/script>/Us', $tag)) {
            return TagTypeEnum::SCRIPT();
        }

        if (preg_match('/<style([^>]*)>(.*)<\/style>/Us', $tag)) {
            return TagTypeEnum::STYLE();
        }

        return TagTypeEnum::UNKNOWN();
    }

    public function getScanResult(string $scanRequestId): ?ScanResultDto
    {
        $scanResult = $this->transient->get('ScanResult' . $scanRequestId);

        if (isset($scanResult->value) && $scanResult->value instanceof ScanResultDto) {
            $scanResult = $scanResult->value;
            $scanResult->matchedHandles->sortListByPropertyNaturally('handle');
            $scanResult->unmatchedHandles->sortListByPropertyNaturally('handle');

            return $scanResult;
        }

        return null;
    }

    public function saveScanResult(): bool
    {
        return $this->transient->set(
            'ScanResult' . $this->scanRequestService->getScanRequestId(),
            new KeyValueDto('scanResult', $this->scanResultDto),
            60 * 60 * 24,
        );
    }

    private function init()
    {
        if ($this->isInitialized) {
            return;
        }

        $this->wordPressIncludesUrl = $this->wpFunction->includesUrl();
        $this->wordPressPluginsUrl = $this->wpFunction->pluginsUrl();
        $this->wordPressSiteUrl = $this->wpFunction->getSiteUrl();
        $this->wordPressThemesUrl = $this->wpFunction->getThemeRootUri();
        $this->isInitialized = true;
    }
}
