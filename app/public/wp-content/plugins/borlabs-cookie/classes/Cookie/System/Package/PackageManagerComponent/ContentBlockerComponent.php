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

use Borlabs\Cookie\ApiClient\Transformer\ContentBlockerTransformer;
use Borlabs\Cookie\Dto\Package\InstallationStatusDto;
use Borlabs\Cookie\DtoList\Package\InstallationStatusDtoList;
use Borlabs\Cookie\Enum\Package\ComponentTypeEnum;
use Borlabs\Cookie\Enum\Package\InstallationStatusEnum;
use Borlabs\Cookie\Exception\GenericException;
use Borlabs\Cookie\Exception\TranslatedException;
use Borlabs\Cookie\Localization\System\ModelLocalizationStrings;
use Borlabs\Cookie\Localization\System\PackageManagerComponentLocalizationStrings;
use Borlabs\Cookie\Model\ContentBlocker\ContentBlockerLocationModel;
use Borlabs\Cookie\Model\ContentBlocker\ContentBlockerModel;
use Borlabs\Cookie\Model\Package\PackageModel;
use Borlabs\Cookie\Repository\ContentBlocker\ContentBlockerLocationRepository;
use Borlabs\Cookie\Repository\ContentBlocker\ContentBlockerRepository;
use Borlabs\Cookie\Repository\Service\ServiceRepository;
use Borlabs\Cookie\Support\Formatter;
use Borlabs\Cookie\System\Log\Log;
use Borlabs\Cookie\System\Package\Traits\SettingsFieldListTrait;

final class ContentBlockerComponent
{
    use SettingsFieldListTrait;

    private ContentBlockerLocationRepository $contentBlockerLocationRepository;

    private ContentBlockerRepository $contentBlockerRepository;

    private ContentBlockerTransformer $contentBlockerTransformer;

    private Log $log;

    private ModelLocalizationStrings $modelLocalizationStrings;

    private PackageManagerComponentLocalizationStrings $packageManagerComponentLocalizationStrings;

    private ProviderComponent $providerComponent;

    private ServiceRepository $serviceRepository;

    public function __construct(
        ContentBlockerLocationRepository $contentBlockerLocationRepository,
        ContentBlockerRepository $contentBlockerRepository,
        ContentBlockerTransformer $contentBlockerTransformer,
        Log $log,
        ProviderComponent $providerComponent,
        ServiceRepository $serviceRepository,
        PackageManagerComponentLocalizationStrings $packageManagerComponentLocalizationStrings,
        ModelLocalizationStrings $modelLocalizationStrings
    ) {
        $this->contentBlockerLocationRepository = $contentBlockerLocationRepository;
        $this->contentBlockerRepository = $contentBlockerRepository;
        $this->contentBlockerTransformer = $contentBlockerTransformer;
        $this->log = $log;
        $this->providerComponent = $providerComponent;
        $this->serviceRepository = $serviceRepository;
        $this->packageManagerComponentLocalizationStrings = $packageManagerComponentLocalizationStrings;
        $this->modelLocalizationStrings = $modelLocalizationStrings;
    }

    /**
     * @param array<PackageModel> $packages
     *
     * @return array<PackageModel>
     */
    public function checkUsage(ContentBlockerModel $contentBlockerModel, PackageModel $ignorePackage, array $packages): array
    {
        $resourcesInUse = [];

        foreach ($packages as $package) {
            if ($package->installedAt === null || $package->id === $ignorePackage->id) {
                continue;
            }

            foreach ($package->components->contentBlockers->list as $contentBlocker) {
                if ($contentBlocker->key === $contentBlockerModel->key) {
                    $resourcesInUse[] = $package;

                    break;
                }
            }
        }

        return $resourcesInUse;
    }

    public function install(
        object $contentBlockerData,
        string $borlabsServicePackageKey,
        string $languageCode,
        ?array $componentSettings = null
    ): InstallationStatusDto {
        $contentBlockerModel = $this->contentBlockerTransformer->toModel($contentBlockerData, $borlabsServicePackageKey, $languageCode);
        $contentBlocker = $this->contentBlockerRepository->getByKey($contentBlockerData->key, $languageCode);

        if ($contentBlocker !== null && $contentBlocker->borlabsServicePackageKey === null) {
            return $this->getFailureInstallationStatus(
                $contentBlocker,
                Formatter::interpolate($this->packageManagerComponentLocalizationStrings::get()['alert']['keyAlreadyInUse'], [
                    'key' => $contentBlockerData->key,
                    'resource' => $this->modelLocalizationStrings::get()['models'][ContentBlockerModel::class],
                ]),
            );
        }

        $providerInstallStatus = $this->providerComponent->install($contentBlockerData->provider, $borlabsServicePackageKey, $languageCode, $componentSettings);

        if ($providerInstallStatus->status->is(InstallationStatusEnum::FAILURE())) {
            return $providerInstallStatus;
        }

        $contentBlockerModel->providerId = $providerInstallStatus->id;

        // If the content blocker already exists, update the values and perhaps the text.
        if ($contentBlocker !== null) {
            $contentBlockerModel->id = $contentBlocker->id;
            $contentBlockerModel->status = $contentBlocker->status;
            // Repeatable settings fields only have the visibility "edit-only".
            $contentBlockerModel->repeatableSettingsFields = $contentBlocker->repeatableSettingsFields;
            // Set current values in our model. This includes fields with the visibility "edit-only".
            $contentBlockerModel->settingsFields = $this->migrateSettingsFieldValues($contentBlockerModel->settingsFields, $contentBlocker->settingsFields);

            if (isset($componentSettings['overwrite-code']) && $componentSettings['overwrite-code'] === '0') {
                $contentBlockerModel->javaScriptGlobal = $contentBlocker->javaScriptGlobal;
                $contentBlockerModel->javaScriptInitialization = $contentBlocker->javaScriptInitialization;
                $contentBlockerModel->previewCss = $contentBlocker->previewCss;
                $contentBlockerModel->previewImage = $contentBlocker->previewImage;
                $contentBlockerModel->previewHtml = $contentBlocker->previewHtml;
            }

            if (isset($componentSettings['overwrite-translation']) && $componentSettings['overwrite-translation'] === '0') {
                $contentBlockerModel->languageStrings = $contentBlocker->languageStrings;
                $contentBlockerModel->name = $contentBlocker->name;
            }
        }

        // Set values from the install/update form fields. This are fields with the visibility "edit-and-setup" and "setup-only".
        if ($componentSettings !== null) {
            $contentBlockerModel->settingsFields = $this->updateSettingsValuesFromFormFields($contentBlockerModel->settingsFields, $componentSettings);
        }

        if ($contentBlocker !== null) {
            $this->contentBlockerRepository->update($contentBlockerModel);
        } else {
            $contentBlockerModel = $this->contentBlockerRepository->insert($contentBlockerModel);
        }

        if ($contentBlockerModel->id === -1) {
            $this->log->error(
                'Content Blocker could not be installed',
                [
                    'componentSettings' => $componentSettings,
                    'contentBlockerData' => $contentBlockerData,
                    'languageCode' => $languageCode,
                    'packageKey' => $borlabsServicePackageKey,
                ],
            );

            return new InstallationStatusDto(
                InstallationStatusEnum::FAILURE(),
                ComponentTypeEnum::CONTENT_BLOCKER(),
                $contentBlockerModel->key,
                $contentBlockerModel->name . '(' . $contentBlockerModel->language . ')',
                $contentBlockerModel->id,
                new InstallationStatusDtoList([$providerInstallStatus,]),
            );
        }

        if ($contentBlocker === null || isset($componentSettings['overwrite-code']) && $componentSettings['overwrite-code'] === '1') {
            // Delete all old locations
            $locations = $this->contentBlockerLocationRepository->find(
                ['contentBlockerId' => $contentBlockerModel->id],
            );

            foreach ($locations as $location) {
                $this->contentBlockerLocationRepository->delete($location);
            }

            // Add new locations
            foreach ($contentBlockerData->config->locations as $location) {
                $hostModel = new ContentBlockerLocationModel();
                $hostModel->hostname = $location->hostname;
                $hostModel->path = $location->path;
                $hostModel->contentBlockerId = $contentBlockerModel->id;
                $this->contentBlockerLocationRepository->insert($hostModel);
            }
        }

        return new InstallationStatusDto(
            InstallationStatusEnum::SUCCESS(),
            ComponentTypeEnum::CONTENT_BLOCKER(),
            $contentBlockerModel->key,
            $contentBlockerModel->name . ' (' . $contentBlockerModel->language . ')',
            $contentBlockerModel->id,
            new InstallationStatusDtoList([$providerInstallStatus,]),
        );
    }

    public function reassignToOtherPackage(PackageModel $packageModel, ContentBlockerModel $contentBlockerModel): InstallationStatusDto
    {
        $contentBlockerModel->borlabsServicePackageKey = $packageModel->borlabsServicePackageKey;
        $success = $this->contentBlockerRepository->update($contentBlockerModel);

        if ($success) {
            return $this->getSuccessInstallationStatus($contentBlockerModel);
        }

        return $this->getFailureInstallationStatus($contentBlockerModel);
    }

    public function setServiceAssociationIfRequired(
        object $contentBlockerData,
        string $languageCode
    ): void {
        if ($contentBlockerData->serviceKey === '') {
            return;
        }

        $contentBlocker = $this->contentBlockerRepository->getByKey($contentBlockerData->key, $languageCode);
        $service = $this->serviceRepository->getByKey($contentBlockerData->serviceKey, $languageCode);

        if (!isset($contentBlocker, $service)) {
            return;
        }
        $contentBlocker->serviceId = $service->id;
        $this->contentBlockerRepository->update($contentBlocker);
    }

    /**
     * @param array<PackageModel> $packages
     *
     * @return array<InstallationStatusDto>
     */
    public function uninstall(PackageModel $packageModel, ContentBlockerModel $contentBlockerModel, array $packages, bool $uninstallProvider): array
    {
        $installationStatusEntries = [];

        $usage = $this->checkUsage($contentBlockerModel, $packageModel, $packages);

        if (count($usage) === 0) {
            try {
                $this->contentBlockerRepository->deleteWithRelations($contentBlockerModel->id);
                $installationStatusEntries[] = $this->getSuccessInstallationStatus($contentBlockerModel);

                if ($uninstallProvider) {
                    $installationStatusEntries[] = $this->providerComponent->uninstall($packageModel, $contentBlockerModel->providerId);
                }
            } catch (TranslatedException $e) {
                $installationStatusEntries[] = $this->getFailureInstallationStatus($contentBlockerModel, $e->getTranslatedMessage());
            } catch (GenericException $e) {
                $this->log->error('Service uninstall failed', [
                    'exceptionMessage' => $e->getMessage(),
                ]);
                $installationStatusEntries[] = $this->getFailureInstallationStatus($contentBlockerModel);
            }
        } else {
            $installationStatusEntries[] = $this->reassignToOtherPackage($usage[0], $contentBlockerModel);
        }

        return $installationStatusEntries;
    }

    private function getFailureInstallationStatus(ContentBlockerModel $contentBlockerModel, ?string $message = null): InstallationStatusDto
    {
        return new InstallationStatusDto(
            InstallationStatusEnum::FAILURE(),
            ComponentTypeEnum::CONTENT_BLOCKER(),
            $contentBlockerModel->key,
            $contentBlockerModel->name,
            $contentBlockerModel->id,
            null,
            $message,
        );
    }

    private function getSuccessInstallationStatus(ContentBlockerModel $contentBlockerModel): InstallationStatusDto
    {
        return new InstallationStatusDto(
            InstallationStatusEnum::SUCCESS(),
            ComponentTypeEnum::CONTENT_BLOCKER(),
            $contentBlockerModel->key,
            $contentBlockerModel->name,
            $contentBlockerModel->id,
        );
    }
}
