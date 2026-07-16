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
use Borlabs\Cookie\Enum\CloudScan\CloudScanTypeEnum;
use Borlabs\Cookie\Exception\GenericException;
use Borlabs\Cookie\Exception\TranslatedException;
use Borlabs\Cookie\System\CloudScan\CloudScanService;
use Borlabs\Cookie\System\Log\Log;
use WP_REST_Request;

final class CloudScanEndpoint
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

    public function register(): void
    {
        $this->wpFunction->registerRestRoute(
            RestEndpointManager::NAMESPACE . '/v1',
            '/cloud-scan/',
            [
                'methods' => 'POST',
                'callback' => [$this, 'scan'],
                'permission_callback' => function () {
                    return $this->wpFunction->currentUserCan('manage_borlabs_cookie');
                },
            ],
        );
    }

    public function scan(WP_REST_Request $request): int
    {
        $scanType = $request->get_param('scanType');

        if ($scanType === null || CloudScanTypeEnum::hasValue($scanType) === false) {
            $this->log->error('CloudScanEndpoint: Parameter "scanType" missing or invalid');

            return -1;
        }

        try {
            $model = $this->cloudScanService->createScan(
                $this->cloudScanService->getListOfPagesByType(
                    'selection_of_sites_per_post_type',
                    false,
                ),
                CloudScanTypeEnum::fromValue($scanType),
            );
        } catch (TranslatedException $e) {
            $this->log->error('Cloud scan create process', [
                'exceptionMessage' => $e->getTranslatedMessage(),
            ]);
        } catch (GenericException $e) {
            $this->log->error('Cloud scan create process', [
                'exceptionMessage' => $e->getMessage(),
                'exceptionStackTrace' => $e->getTraceAsString(),
                'exceptionType' => get_class($e),
            ]);
        }

        return $model->id ?? -1;
    }
}
