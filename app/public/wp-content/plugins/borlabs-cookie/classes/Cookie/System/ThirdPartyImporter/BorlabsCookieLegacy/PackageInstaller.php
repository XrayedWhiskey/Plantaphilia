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

namespace Borlabs\Cookie\System\ThirdPartyImporter\BorlabsCookieLegacy;

use Borlabs\Cookie\ApiClient\PackageApiClient;
use Borlabs\Cookie\DtoList\Package\SuggestedPackageDtoList;
use Borlabs\Cookie\Exception\ApiClient\PackageApiClientException;
use Borlabs\Cookie\Exception\HttpClient\ConnectionErrorException;
use Borlabs\Cookie\Exception\HttpClient\ServerErrorException;
use Borlabs\Cookie\Job\PackageInstallJobHandler;
use Borlabs\Cookie\Repository\Package\PackageRepository;
use Borlabs\Cookie\System\Log\Log;
use Borlabs\Cookie\System\Package\PackageInstallJobService;

final class PackageInstaller
{
    private Log $log;

    private PackageApiClient $packageApiClient;

    private PackageInstallJobHandler $packageInstallJobHandler;

    private PackageInstallJobService $packageInstallJobService;

    private PackageRepository $packageRepository;

    public function __construct(
        Log $log,
        PackageApiClient $packageApiClient,
        PackageInstallJobHandler $packageInstallJobHandler,
        PackageInstallJobService $packageInstallJobService,
        PackageRepository $packageRepository
    ) {
        $this->log = $log;
        $this->packageApiClient = $packageApiClient;
        $this->packageInstallJobHandler = $packageInstallJobHandler;
        $this->packageInstallJobService = $packageInstallJobService;
        $this->packageRepository = $packageRepository;
    }

    public function createOrUpdateInstallJobs(SuggestedPackageDtoList $suggestions): void
    {
        foreach ($suggestions->list as $suggestedPackage) {
            $package = $this->packageRepository->getByPackageKey($suggestedPackage->key);

            if ($package !== null) {
                $this->packageInstallJobService->updateJob($package);
            }
        }
    }

    public function getPackageSuggestions(): SuggestedPackageDtoList
    {
        $suggestions = new SuggestedPackageDtoList([]);

        try {
            $suggestions = $this->packageApiClient->requestPackageSuggestions(true, true);
        } catch (ConnectionErrorException $e) {
            $this->log->error($e->getTranslatedMessage());
        } catch (ServerErrorException $e) {
            $this->log->error($e->getMessage());
        } catch (PackageApiClientException $e) {
            $this->log->error($e->getTranslatedMessage());
        }

        return $suggestions;
    }

    public function install(): void
    {
        $suggestions = $this->getPackageSuggestions();
        $this->createOrUpdateInstallJobs($suggestions);

        foreach ($suggestions->list as $package) {
            $packageModel = $this->packageRepository->getByPackageKey($package->key);

            if ($packageModel === null) {
                $this->log->error(
                    '[Import] Package "{{ borlabsServicePackageKey }}" not found',
                    [
                        'borlabsServicePackageKey' => $package->key,
                    ],
                );

                continue;
            }

            $job = $this->packageInstallJobService->getJob($packageModel);

            if ($job !== null) {
                $this->packageInstallJobHandler->handle($job);
            }
        }
    }
}
