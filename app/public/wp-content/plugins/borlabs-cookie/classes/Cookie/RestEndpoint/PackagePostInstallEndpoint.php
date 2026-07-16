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
use Borlabs\Cookie\Exception\GenericException;
use Borlabs\Cookie\Exception\TranslatedException;
use Borlabs\Cookie\System\Log\Log;
use Borlabs\Cookie\System\Telemetry\TelemetryService;
use Borlabs\Cookie\System\ThirdPartyCacheClearer\ThirdPartyCacheClearerManager;

final class PackagePostInstallEndpoint implements RestEndpointInterface
{
    private Log $log;

    private TelemetryService $telemetryService;

    private ThirdPartyCacheClearerManager $thirdPartyCacheClearerManager;

    private WpFunction $wpFunction;

    public function __construct(
        Log $log,
        TelemetryService $telemetryService,
        ThirdPartyCacheClearerManager $thirdPartyCacheClearerManager,
        WpFunction $wpFunction
    ) {
        $this->log = $log;
        $this->telemetryService = $telemetryService;
        $this->thirdPartyCacheClearerManager = $thirdPartyCacheClearerManager;
        $this->wpFunction = $wpFunction;
    }

    public function postInstall(): bool
    {
        try {
            $this->thirdPartyCacheClearerManager->clearCache();
            $this->telemetryService->sendTelemetryData();
        } catch (TranslatedException $e) {
            $this->log->error('Post install process', [
                'exceptionMessage' => $e->getTranslatedMessage(),
            ]);
        } catch (GenericException $e) {
            $this->log->error('Post install process', [
                'exceptionMessage' => $e->getMessage(),
                'exceptionStackTrace' => $e->getTraceAsString(),
                'exceptionType' => get_class($e),
            ]);
        }

        return true;
    }

    public function register(): void
    {
        $this->wpFunction->registerRestRoute(
            RestEndpointManager::NAMESPACE . '/v1',
            '/package/post-install',
            [
                'methods' => 'POST',
                'callback' => [$this, 'postInstall'],
                'permission_callback' => function () {
                    return $this->wpFunction->currentUserCan('manage_borlabs_cookie');
                },
            ],
        );
    }
}
