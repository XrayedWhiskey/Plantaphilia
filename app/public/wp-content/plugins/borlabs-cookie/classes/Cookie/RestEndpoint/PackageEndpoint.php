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
use Borlabs\Cookie\DtoList\Package\InstallationStatusDtoList;
use Borlabs\Cookie\Exception\GenericException;
use Borlabs\Cookie\Exception\TranslatedException;
use Borlabs\Cookie\Repository\Package\PackageRepository;
use Borlabs\Cookie\System\Log\Log;
use Borlabs\Cookie\System\Package\PackageManager;
use WP_REST_Request;

final class PackageEndpoint implements RestEndpointInterface
{
    private Log $log;

    private PackageManager $packageManager;

    private PackageRepository $packageRepository;

    private WpFunction $wpFunction;

    public function __construct(
        Log $log,
        PackageManager $packageManager,
        PackageRepository $packageRepository,
        WpFunction $wpFunction
    ) {
        $this->log = $log;
        $this->packageManager = $packageManager;
        $this->packageRepository = $packageRepository;
        $this->wpFunction = $wpFunction;
    }

    public function install(WP_REST_Request $request): ?InstallationStatusDtoList
    {
        $package = null;

        if ($request->get_param('id')) {
            $package = $this->packageRepository->findById((int) $request->get_param('id'), [
                'services',
                'contentBlockers',
                'compatibilityPatches',
                'scriptBlockers',
                'styleBlockers',
            ]);
        }

        if ($package !== null) {
            try {
                if ((bool) $request->get_param('useDefaults') === true) {
                    return $this->packageManager->install(
                        $package,
                        null,
                        true,
                    );
                }

                return $this->packageManager->installWithCleanupAndTelemetry(
                    $package,
                    $request->get_body_params(),
                );
            } catch (TranslatedException $e) {
                $this->log->error('Installation of package failed', [
                    'exceptionMessage' => $e->getTranslatedMessage(),
                    'packageKey' => $package->borlabsServicePackageKey,
                ]);

                return null;
            } catch (GenericException $e) {
                $this->log->error('Installation of package failed', [
                    'exceptionMessage' => $e->getMessage(),
                    'exceptionStackTrace' => $e->getTraceAsString(),
                    'exceptionType' => get_class($e),
                    'packageKey' => $package->borlabsServicePackageKey,
                ]);

                return null;
            }
        }

        return null;
    }

    public function register(): void
    {
        $this->wpFunction->registerRestRoute(
            RestEndpointManager::NAMESPACE . '/v1',
            '/package/install/(?P<id>\d+)',
            [
                'methods' => 'POST',
                'callback' => [$this, 'install'],
                'args' => [
                    'id' => [
                        'required' => true,
                        'sanitize_callback' => 'absint',
                    ],
                ],
                'permission_callback' => function () {
                    return $this->wpFunction->currentUserCan('manage_borlabs_cookie');
                },
            ],
        );
    }
}
