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
use Borlabs\Cookie\DtoList\CloudScan\PackageSuggestionDtoList;
use Borlabs\Cookie\System\CloudScan\CloudScanService;
use Borlabs\Cookie\System\Log\Log;
use WP_REST_Request;

final class CloudScanSuggestionEndpoint
{
    private CloudScanService $cloudScanService;

    private Log $log;

    private WpFunction $wpFunction;

    public function __construct(
        CloudScanService $cloudScanService,
        Log $log,
        WpFunction $wpFunction
    ) {
        $this->cloudScanService = $cloudScanService;
        $this->log = $log;
        $this->wpFunction = $wpFunction;
    }

    public function getSuggestedPackages(WP_REST_Request $request): ?PackageSuggestionDtoList
    {
        $scanIdParam = $request->get_param('scanId');

        if ($scanIdParam === null) {
            $this->log->error('CloudScanEndpoint: Parameter "scanId" missing', [
                'value' => (string) $scanIdParam,
            ]);

            return null;
        }

        return $this->cloudScanService->getNotInstalledSuggestedPackages((int) $scanIdParam);
    }

    public function register(): void
    {
        $this->wpFunction->registerRestRoute(
            RestEndpointManager::NAMESPACE . '/v1',
            '/cloud-scan/suggested-packages/(?P<scanId>[0-9]{1,})',
            [
                'methods' => 'GET',
                'callback' => [$this, 'getSuggestedPackages'],
                'permission_callback' => function () {
                    return $this->wpFunction->currentUserCan('manage_borlabs_cookie');
                },
            ],
        );
    }
}
