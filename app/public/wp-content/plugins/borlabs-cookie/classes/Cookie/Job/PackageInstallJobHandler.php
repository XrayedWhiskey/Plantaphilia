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

namespace Borlabs\Cookie\Job;

use Borlabs\Cookie\Container\Container;
use Borlabs\Cookie\Exception\GenericException;
use Borlabs\Cookie\Exception\HttpClient\ConnectionErrorException;
use Borlabs\Cookie\Exception\HttpClient\ServerErrorException;
use Borlabs\Cookie\Exception\TranslatedException;
use Borlabs\Cookie\Model\Job\JobModel;
use Borlabs\Cookie\Repository\Package\PackageRepository;
use Borlabs\Cookie\System\Log\Log;
use Borlabs\Cookie\System\Package\PackageInstallFailedMailJobService;
use Borlabs\Cookie\System\Package\PackageManager;
use Borlabs\Cookie\System\Package\Traits\InstallationStatusAnalyzerTrait;

class PackageInstallJobHandler implements JobHandler
{
    use InstallationStatusAnalyzerTrait;

    public const JOB_TYPE = 'packageInstall';

    public Log $log;

    private Container $container;

    private PackageInstallFailedMailJobService $packageInstallFailedMailJobService;

    private PackageRepository $packageRepository;

    public function __construct(
        Container $container,
        Log $log,
        PackageInstallFailedMailJobService $packageInstallFailedMailJobService,
        PackageRepository $packageRepository
    ) {
        $this->container = $container;
        $this->log = $log;
        $this->packageInstallFailedMailJobService = $packageInstallFailedMailJobService;
        $this->packageRepository = $packageRepository;
    }

    public function handle(JobModel $job): void
    {
        // Ensure the package exists and is not installed
        $package = $this->packageRepository->getByPackageKey($job->payload['borlabsServicePackageKey']);

        if ($package === null || $package->installedAt !== null) {
            return;
        }

        $packageManager = $this->container->get(PackageManager::class);
        $componentSettings = $packageManager->getDefaultComponentSettings($package);

        $this->log->info('PackageInstallJobHandler: Installing package: ' . $package->name, [
            'componentSettings' => $componentSettings,
            'package' => $package,
        ]);

        $hasConnectionErrorException = false;
        $installationFailed = true;
        $installationStatus = null;

        try {
            $installationStatus = $packageManager->installWithCleanupAndTelemetry($package, $componentSettings);
            $installationFailed = !$installationStatus || !$this->isInstallationCompletelySuccessful($installationStatus);
        } catch (ConnectionErrorException $e) {
            $hasConnectionErrorException = true;
            $this->log->error('Exception in PackageAutoUpdateJobHandler', [
                'exceptionMessage' => $e->getTranslatedMessage(),
            ]);
        } catch (ServerErrorException $e) {
            $hasConnectionErrorException = true;
            $this->log->error('Exception in PackageAutoUpdateJobHandler', [
                'exceptionMessage' => $e->getMessage(),
            ]);
        } catch (TranslatedException $e) {
            $this->log->error('Exception in PackageInstallJobHandler', [
                'exceptionMessage' => $e->getTranslatedMessage(),
            ]);
        } catch (GenericException $e) {
            $this->log->error('Generic exception in PackageInstallJobHandler', [
                'exceptionMessage' => $e->getMessage(),
            ]);
        }

        $this->log->info('PackageInstallJobHandler: Installation status: {{ status }}', [
            'installationStatus' => $installationStatus,
            'status' => $installationFailed ? 'FAILED' : 'SUCCESS',
        ]);

        if ($installationFailed && !$hasConnectionErrorException) {
            $this->packageInstallFailedMailJobService->updateJob([
                'borlabsServicePackageKey' => $job->payload['borlabsServicePackageKey'],
                'borlabsServicePackageVersion' => $package->borlabsServicePackageVersion,
                'processId' => $this->log->getProcessId(),
                'version' => $package->version,
            ]);
        }
    }
}
