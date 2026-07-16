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
use Borlabs\Cookie\Dto\LocalScanner\ScanResultDto;
use Borlabs\Cookie\System\LocalScanner\ScanResultService;
use WP_REST_Request;

final class ScanResultEndpoint implements RestEndpointInterface
{
    private ScanResultService $scanResultService;

    private WpFunction $wpFunction;

    public function __construct(
        ScanResultService $scanResultService,
        WpFunction $wpFunction
    ) {
        $this->scanResultService = $scanResultService;
        $this->wpFunction = $wpFunction;
    }

    public function getResult(WP_REST_Request $request): ?ScanResultDto
    {
        return $this->scanResultService->getScanResult($request->get_param('scanRequestId'));
    }

    public function register(): void
    {
        $this->wpFunction->registerRestRoute(
            RestEndpointManager::NAMESPACE . '/v1',
            '/scan-result/(?P<scanRequestId>[a-zA-Z]{12})',
            [
                'methods' => 'GET',
                'callback' => [$this, 'getResult'],
                'permission_callback' => function () {
                    return $this->wpFunction->currentUserCan('manage_borlabs_cookie');
                },
            ],
        );
    }
}
