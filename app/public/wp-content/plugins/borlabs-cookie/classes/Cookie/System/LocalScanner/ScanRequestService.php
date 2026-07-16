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

use Borlabs\Cookie\Dto\LocalScanner\ScanRequestOptionDto;
use Borlabs\Cookie\Dto\LocalScanner\ScanRequestResponseDto;
use Borlabs\Cookie\Dto\System\KeyValueDto;
use Borlabs\Cookie\Dto\System\RequestDto;
use Borlabs\Cookie\DtoList\System\KeyValueDtoList;
use Borlabs\Cookie\Support\Hmac;
use Borlabs\Cookie\Support\Randomizer;
use Borlabs\Cookie\Support\Sanitizer;
use Borlabs\Cookie\System\License\License;
use Borlabs\Cookie\System\Transient\Transient;

class ScanRequestService
{
    private bool $isScanRequest = false;

    private License $license;

    private string $scanRequestId;

    private ScanRequestOptionDto $scanRequestOptionDto;

    private Transient $transient;

    public function __construct(
        License $license,
        ScanRequestOptionDto $scanRequestOptionDto,
        Transient $transient
    ) {
        $this->license = $license;
        $this->scanRequestOptionDto = $scanRequestOptionDto;
        $this->transient = $transient;
    }

    public function getScanRequestId(): string
    {
        return $this->scanRequestId;
    }

    public function init(RequestDto $request)
    {
        /**
         * Detect if the request is a scan request (local or cloud).
         * Starting with version 3.3.16, the field has been renamed to avoid being blocked by 6G firewall rules, such as those implemented by All-in-One Security.
         */
        $scanRequestData = (object) $request->getData['__borlabsCookieScannerConfig'];
        $this->scanRequestId = $scanRequestData->scanRequestId ?? 'NoId';
        $this->scanRequestId = Sanitizer::stripNonMatchingCharacters($this->scanRequestId, '[^A-Z]+[^a-zA-Z]+');
        $scanSalt = $this->transient->get('ScanRequestId' . $this->scanRequestId)->value ?? '';
        $salt = $this->license->get()->siteSalt ?? $scanSalt;

        $isValid = Hmac::isValid(
            $scanRequestData,
            $salt,
            $request->getData['__borlabsCookieSignature'],
        );

        if ($isValid === false) {
            return;
        }

        if ((int) ($scanRequestData->expires ?? 0) < time()) {
            return;
        }

        if (!defined('DONOTCACHEPAGE')) {
            // Disables caching of WordPress plugins.
            define('DONOTCACHEPAGE', true);

            // Prevent a crawler from indexing a page requested by the scanner.
            if (!headers_sent()) {
                header('X-Robots-Tag: noindex');
            }
        }

        $this->isScanRequest = true;

        foreach ($scanRequestData as $key => $value) {
            if (isset($this->scanRequestOptionDto->{$key}) && $value === '1') {
                $this->scanRequestOptionDto->{$key} = true;
            }
        }
    }

    public function isScanRequest(): bool
    {
        return $this->isScanRequest;
    }

    public function isScriptScanRequest(): bool
    {
        return $this->scanRequestOptionDto->scriptScanRequest && $this->isScanRequest();
    }

    public function isStyleScanRequest(): bool
    {
        return $this->scanRequestOptionDto->styleScanRequest && $this->isScanRequest();
    }

    public function noBorlabsCookie(): bool
    {
        return $this->scanRequestOptionDto->noBorlabsCookie && $this->isScanRequest();
    }

    public function noCompatibilityPatches(): bool
    {
        return $this->scanRequestOptionDto->noCompatibilityPatches && $this->isScanRequest();
    }

    // TODO implement
    public function noConsentDialog(): bool
    {
        return $this->scanRequestOptionDto->noConsentDialog && $this->isScanRequest();
    }

    public function noContentBlockers(): bool
    {
        return $this->scanRequestOptionDto->noContentBlockers && $this->isScanRequest();
    }

    public function noDefaultContentBlocker(): bool
    {
        return $this->scanRequestOptionDto->noDefaultContentBlocker && $this->isScanRequest();
    }

    public function noScriptBlockers(): bool
    {
        return $this->scanRequestOptionDto->noScriptBlockers && $this->isScanRequest();
    }

    public function noStyleBlockers(): bool
    {
        return $this->scanRequestOptionDto->noStyleBlockers && $this->isScanRequest();
    }

    public function registerScanRequest(
        string $url,
        ScanRequestOptionDto $scanRequestOptionDto,
        ?KeyValueDtoList $scanPhrases = null
    ): ScanRequestResponseDto {
        $scanRequestId = Randomizer::randomString(12);
        $scanSalt = Randomizer::randomString();
        $salt = $this->license->get()->siteSalt ?? $scanSalt;
        $query = parse_url($url, PHP_URL_QUERY);
        $data = [
            '__borlabsCookieScannerConfig' => [
                'expires' => (string) (time() + 300),
                'noBorlabsCookie' => (string) (int) $scanRequestOptionDto->noBorlabsCookie,
                'noCompatibilityPatches' => (string) (int) $scanRequestOptionDto->noCompatibilityPatches,
                'noConsentDialog' => (string) (int) $scanRequestOptionDto->noConsentDialog,
                'noContentBlockers' => (string) (int) $scanRequestOptionDto->noContentBlockers,
                'noDefaultContentBlocker' => (string) (int) $scanRequestOptionDto->noDefaultContentBlocker,
                'noScriptBlockers' => (string) (int) $scanRequestOptionDto->noScriptBlockers,
                'noStyleBlockers' => (string) (int) $scanRequestOptionDto->noStyleBlockers,
                'withDebugConsole' => (string) (int) $scanRequestOptionDto->withDebugConsole,
                'scanRequestId' => $scanRequestId,
                'scriptScanRequest' => (string) (int) $scanRequestOptionDto->scriptScanRequest,
                'styleScanRequest' => (string) (int) $scanRequestOptionDto->styleScanRequest,
            ],
        ];
        $hash = hash_hmac('sha256', json_encode($data['__borlabsCookieScannerConfig']), $salt);
        $this->transient->set(
            'ScanRequestId' . $scanRequestId,
            new KeyValueDto('scanRequestId', $scanSalt),
            3600,
        );

        if (isset($scanPhrases)) {
            $this->transient->set(
                'ScanPhrases' . $scanRequestId,
                new KeyValueDto('scanPhrases', $scanPhrases),
                3600,
            );
        }

        return new ScanRequestResponseDto(
            $scanRequestId,
            $url . (!isset($query) ? '?' : '') . '__borlabsCookieSignature=' . $hash . '&' . http_build_query($data),
        );
    }

    public function withDebugConsole(): bool
    {
        return $this->scanRequestOptionDto->withDebugConsole && $this->isScanRequest();
    }
}
