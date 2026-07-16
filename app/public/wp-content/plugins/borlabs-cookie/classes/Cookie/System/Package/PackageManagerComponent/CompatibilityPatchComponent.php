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

use Borlabs\Cookie\Adapter\WpFunction;
use Borlabs\Cookie\Dto\Package\InstallationStatusDto;
use Borlabs\Cookie\Dto\System\ExternalFileDto;
use Borlabs\Cookie\Enum\Package\ComponentTypeEnum;
use Borlabs\Cookie\Enum\Package\InstallationStatusEnum;
use Borlabs\Cookie\Model\CompatibilityPatch\CompatibilityPatchModel;
use Borlabs\Cookie\Model\Package\PackageModel;
use Borlabs\Cookie\Repository\CompatibilityPatch\CompatibilityPatchRepository;
use Borlabs\Cookie\System\FileSystem\FileManager;
use Borlabs\Cookie\System\Log\Log;

final class CompatibilityPatchComponent
{
    private CompatibilityPatchRepository $compatibilityPatchRepository;

    private FileManager $fileManager;

    private Log $log;

    private WpFunction $wpFunction;

    public function __construct(
        CompatibilityPatchRepository $compatibilityPatchRepository,
        FileManager $fileManager,
        Log $log,
        WpFunction $wpFunction
    ) {
        $this->compatibilityPatchRepository = $compatibilityPatchRepository;
        $this->fileManager = $fileManager;
        $this->log = $log;
        $this->wpFunction = $wpFunction;
    }

    /**
     * @param array<PackageModel> $packages
     *
     * @return array<PackageModel>
     */
    public function checkUsage(CompatibilityPatchModel $compatibilityPatchModel, PackageModel $ignorePackage, array $packages): array
    {
        $resourcesInUse = [];

        foreach ($packages as $package) {
            if ($package->installedAt === null || $package->id === $ignorePackage->id) {
                continue;
            }

            foreach ($package->components->compatibilityPatches->list as $compatibilityPatch) {
                if ($compatibilityPatch->key === $compatibilityPatchModel->key) {
                    $resourcesInUse[] = $package;

                    break;
                }
            }
        }

        return $resourcesInUse;
    }

    public function install(object $compatibilityPatchData, string $borlabsServicePackageKey): InstallationStatusDto
    {
        // Save file locally
        $externalFile = new ExternalFileDto(
            $compatibilityPatchData->config->downloadUrl,
            $compatibilityPatchData->config->hash,
        );

        $file = $this->fileManager->storeExternalFile($externalFile);

        if ($file === null) {
            $this->log->error(
                'Compatibility patch file could not be stored',
                [
                    'compatibilityPatchData' => $compatibilityPatchData,
                    'packageKey' => $borlabsServicePackageKey,
                ],
            );

            return new InstallationStatusDto(
                InstallationStatusEnum::FAILURE(),
                ComponentTypeEnum::COMPATIBILITY_PATCH(),
                $compatibilityPatchData->key,
                $externalFile->url,
            );
        }

        // Check if compatibility patch already exists
        $compatibilityPatch = $this->compatibilityPatchRepository->getByKey($compatibilityPatchData->key);

        if ($compatibilityPatch !== null && $compatibilityPatch->fileName !== $file->fileName) {
            $this->fileManager->deleteStoredFile($compatibilityPatch->fileName);
        }

        if ($compatibilityPatch === null) {
            $compatibilityPatch = new CompatibilityPatchModel();
        }

        $compatibilityPatch->borlabsServicePackageKey = $borlabsServicePackageKey;
        $compatibilityPatch->key = $compatibilityPatchData->key;
        $compatibilityPatch->fileName = $file->fileName;
        $compatibilityPatch->hash = $file->hash;

        if ($compatibilityPatch->id !== -1) {
            $this->compatibilityPatchRepository->update($compatibilityPatch);
        } else {
            $compatibilityPatch = $this->compatibilityPatchRepository->insert($compatibilityPatch);
        }

        if ($compatibilityPatch->id === -1) {
            $this->log->error(
                'Compatibility patch could not be installed.',
                [
                    'compatibilityPatch' => $compatibilityPatch,
                    'packageKey' => $borlabsServicePackageKey,
                ],
            );
        }

        return new InstallationStatusDto(
            $compatibilityPatch->id !== -1 ? InstallationStatusEnum::SUCCESS() : InstallationStatusEnum::FAILURE(),
            ComponentTypeEnum::COMPATIBILITY_PATCH(),
            $compatibilityPatch->key,
            $file->fileName,
            $compatibilityPatch->id,
        );
    }

    public function reassignToOtherPackage(PackageModel $packageModel, CompatibilityPatchModel $compatibilityPatchModel): InstallationStatusDto
    {
        $compatibilityPatchModel->borlabsServicePackageKey = $packageModel->borlabsServicePackageKey;
        $success = $this->compatibilityPatchRepository->update($compatibilityPatchModel);

        if ($success) {
            return $this->getSuccessInstallationStatus($compatibilityPatchModel);
        }

        return $this->getFailureInstallationStatus($compatibilityPatchModel);
    }

    public function uninstall(PackageModel $packageModel, CompatibilityPatchModel $compatibilityPatchModel, array $packages): InstallationStatusDto
    {
        $usage = $this->checkUsage($compatibilityPatchModel, $packageModel, $packages);

        if (count($usage) === 0) {
            if (!$this->fileManager->isStoredFilePresent($compatibilityPatchModel->fileName)) {
                return $this->getSuccessInstallationStatus($compatibilityPatchModel);
            }

            $this->wpFunction->applyFilter(
                'borlabsCookie/compatibilityPatch/beforeFileDeletion/' . $compatibilityPatchModel->key,
                $compatibilityPatchModel,
            );

            $successDelete = $this->fileManager->deleteStoredFile($compatibilityPatchModel->fileName);

            if (!$successDelete) {
                return $this->getFailureInstallationStatus($compatibilityPatchModel);
            }

            $result = $this->compatibilityPatchRepository->forceDelete($compatibilityPatchModel);

            if ($result !== 1) {
                return $this->getFailureInstallationStatus($compatibilityPatchModel);
            }

            return $this->getSuccessInstallationStatus($compatibilityPatchModel);
        }

        return $this->reassignToOtherPackage(
            $usage[0],
            $compatibilityPatchModel,
        );
    }

    private function getFailureInstallationStatus(CompatibilityPatchModel $compatibilityPatchModel): InstallationStatusDto
    {
        return new InstallationStatusDto(
            InstallationStatusEnum::FAILURE(),
            ComponentTypeEnum::COMPATIBILITY_PATCH(),
            $compatibilityPatchModel->key,
            $compatibilityPatchModel->fileName,
            $compatibilityPatchModel->id,
        );
    }

    private function getSuccessInstallationStatus(CompatibilityPatchModel $compatibilityPatchModel): InstallationStatusDto
    {
        return new InstallationStatusDto(
            InstallationStatusEnum::SUCCESS(),
            ComponentTypeEnum::COMPATIBILITY_PATCH(),
            $compatibilityPatchModel->key,
            $compatibilityPatchModel->fileName,
            $compatibilityPatchModel->id,
        );
    }
}
