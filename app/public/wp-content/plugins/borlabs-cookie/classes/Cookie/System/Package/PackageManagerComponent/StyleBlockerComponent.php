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

namespace Borlabs\Cookie\System\Package\PackageManagerComponent;

use Borlabs\Cookie\ApiClient\Transformer\StyleBlockerTransformer;
use Borlabs\Cookie\Dto\Package\InstallationStatusDto;
use Borlabs\Cookie\Enum\Package\ComponentTypeEnum;
use Borlabs\Cookie\Enum\Package\InstallationStatusEnum;
use Borlabs\Cookie\Localization\System\ModelLocalizationStrings;
use Borlabs\Cookie\Localization\System\PackageManagerComponentLocalizationStrings;
use Borlabs\Cookie\Model\Package\PackageModel;
use Borlabs\Cookie\Model\StyleBlocker\StyleBlockerModel;
use Borlabs\Cookie\Repository\StyleBlocker\StyleBlockerRepository;
use Borlabs\Cookie\Support\Formatter;
use Borlabs\Cookie\System\Log\Log;

final class StyleBlockerComponent
{
    private Log $log;

    private ModelLocalizationStrings $modelLocalizationStrings;

    private PackageManagerComponentLocalizationStrings $packageManagerComponentLocalizationStrings;

    private StyleBlockerRepository $styleBlockerRepository;

    private StyleBlockerTransformer $styleBlockerTransformer;

    public function __construct(
        Log $log,
        StyleBlockerRepository $styleBlockerRepository,
        StyleBlockerTransformer $styleBlockerTransformer,
        ModelLocalizationStrings $modelLocalizationStrings,
        PackageManagerComponentLocalizationStrings $packageManagerComponentLocalizationStrings
    ) {
        $this->log = $log;
        $this->styleBlockerRepository = $styleBlockerRepository;
        $this->styleBlockerTransformer = $styleBlockerTransformer;
        $this->modelLocalizationStrings = $modelLocalizationStrings;
        $this->packageManagerComponentLocalizationStrings = $packageManagerComponentLocalizationStrings;
    }

    /**
     * @param array<PackageModel> $packages
     *
     * @return array<PackageModel>
     */
    public function checkUsage(StyleBlockerModel $styleBlockerModel, PackageModel $ignorePackage, array $packages): array
    {
        $resourcesInUse = [];

        foreach ($packages as $package) {
            if ($package->installedAt === null || $package->id === $ignorePackage->id) {
                continue;
            }

            foreach ($package->components->styleBlockers->list as $styleBlocker) {
                if ($styleBlocker->key === $styleBlockerModel->key) {
                    $resourcesInUse[] = $package;

                    break;
                }
            }
        }

        return $resourcesInUse;
    }

    public function install(object $styleBlockerData, string $borlabsServicePackageKey): InstallationStatusDto
    {
        $styleBlockerModel = $this->styleBlockerTransformer->toModel($styleBlockerData, $borlabsServicePackageKey);
        $styleBlocker = $this->styleBlockerRepository->getByKey($styleBlockerData->key);

        if ($styleBlocker !== null && $styleBlocker->borlabsServicePackageKey === null) {
            return $this->getFailureInstallationStatus(
                $styleBlocker,
                Formatter::interpolate($this->packageManagerComponentLocalizationStrings::get()['alert']['keyAlreadyInUse'], [
                    'key' => $styleBlocker->key,
                    'resource' => $this->modelLocalizationStrings::get()['models'][StyleBlockerModel::class],
                ]),
            );
        }

        if ($styleBlocker !== null) {
            $styleBlockerModel->id = $styleBlocker->id;
            $styleBlockerModel->status = $styleBlocker->status;
            $this->styleBlockerRepository->update($styleBlockerModel);
        } else {
            $styleBlockerModel = $this->styleBlockerRepository->insert($styleBlockerModel);
        }

        if ($styleBlockerModel->id === -1) {
            $this->log->error(
                'Style Blocker could not be installed.',
                [
                    'packageKey' => $borlabsServicePackageKey,
                    'styleBlockerData' => $styleBlockerData,
                ],
            );
        }

        if ($styleBlockerModel->id !== -1) {
            return $this->getSuccessInstallationStatus($styleBlockerModel);
        }

        return $this->getFailureInstallationStatus($styleBlockerModel);
    }

    public function reassignToOtherPackage(PackageModel $packageModel, StyleBlockerModel $styleBlockerModel): InstallationStatusDto
    {
        $styleBlockerModel->borlabsServicePackageKey = $packageModel->borlabsServicePackageKey;
        $success = $this->styleBlockerRepository->update($styleBlockerModel);

        if ($success) {
            return $this->getSuccessInstallationStatus($styleBlockerModel);
        }

        return $this->getFailureInstallationStatus($styleBlockerModel);
    }

    public function uninstall(
        PackageModel $packageModel,
        StyleBlockerModel $styleBlockerModel,
        array $packages
    ): InstallationStatusDto {
        $usage = $this->checkUsage($styleBlockerModel, $packageModel, $packages);

        if (count($usage) === 0) {
            $result = $this->styleBlockerRepository->forceDelete($styleBlockerModel);

            if ($result !== 1) {
                $this->getFailureInstallationStatus($styleBlockerModel);
            }

            return $this->getSuccessInstallationStatus($styleBlockerModel);
        }

        return $this->reassignToOtherPackage($usage[0], $styleBlockerModel);
    }

    private function getFailureInstallationStatus(StyleBlockerModel $styleBlockerModel, ?string $message = null): InstallationStatusDto
    {
        return new InstallationStatusDto(
            InstallationStatusEnum::FAILURE(),
            ComponentTypeEnum::STYLE_BLOCKER(),
            $styleBlockerModel->key,
            $styleBlockerModel->name,
            $styleBlockerModel->id,
            null,
            $message,
        );
    }

    private function getSuccessInstallationStatus(StyleBlockerModel $styleBlockerModel): InstallationStatusDto
    {
        return new InstallationStatusDto(
            InstallationStatusEnum::SUCCESS(),
            ComponentTypeEnum::STYLE_BLOCKER(),
            $styleBlockerModel->key,
            $styleBlockerModel->name,
            $styleBlockerModel->id,
        );
    }
}
