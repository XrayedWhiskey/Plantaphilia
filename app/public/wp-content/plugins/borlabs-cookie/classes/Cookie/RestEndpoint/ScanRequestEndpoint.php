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

namespace Borlabs\Cookie\RestEndpoint;

use Borlabs\Cookie\Adapter\WpFunction;
use Borlabs\Cookie\Dto\LocalScanner\ScanRequestOptionDto;
use Borlabs\Cookie\Dto\LocalScanner\ScanRequestResponseDto;
use Borlabs\Cookie\Dto\System\KeyValueDto;
use Borlabs\Cookie\DtoList\System\KeyValueDtoList;
use Borlabs\Cookie\Support\Transformer;
use Borlabs\Cookie\System\LocalScanner\ScanRequestService;
use WP_REST_Request;

final class ScanRequestEndpoint implements RestEndpointInterface
{
    private ScanRequestService $scanRequestService;

    private WpFunction $wpFunction;

    public function __construct(
        ScanRequestService $scanRequestService,
        WpFunction $wpFunction
    ) {
        $this->scanRequestService = $scanRequestService;
        $this->wpFunction = $wpFunction;
    }

    public function register(): void
    {
        $this->wpFunction->registerRestRoute(
            RestEndpointManager::NAMESPACE . '/v1',
            '/scan-request/',
            [
                'methods' => 'POST',
                'callback' => [$this, 'registerScanRequestAndGetSignedUrl'],
                'permission_callback' => function () {
                    return $this->wpFunction->currentUserCan('manage_borlabs_cookie');
                },
            ],
        );
    }

    public function registerScanRequestAndGetSignedUrl(WP_REST_Request $request): ?ScanRequestResponseDto
    {
        $requestData = Transformer::buildNestedArray($request->get_body_params());

        if (!isset($requestData['searchPhrase'], $requestData['scanRequestOption'], $requestData['url'])) {
            return null;
        }

        $scanRequestOption = new ScanRequestOptionDto();
        $scanRequestOption = $scanRequestOption::fromJson((object) $requestData['scanRequestOption']);

        $searchPhraseList = new KeyValueDtoList();
        $phrases = explode(',', $requestData['searchPhrase']);

        foreach ($phrases as $phrase) {
            $phrase = trim($phrase);

            if (strlen($phrase)) {
                $searchPhraseList->add(new KeyValueDto($phrase, $phrase));
            }
        }

        return $this->scanRequestService->registerScanRequest(
            $requestData['url'],
            $scanRequestOption,
            $searchPhraseList,
        );
    }
}
