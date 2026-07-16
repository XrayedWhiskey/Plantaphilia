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

use Borlabs\Cookie\ApiClient\Transformer\ServiceCookieTransformer;
use Borlabs\Cookie\ApiClient\Transformer\ServiceOptionTransformer;
use Borlabs\Cookie\ApiClient\Transformer\ServiceTransformer;
use Borlabs\Cookie\Dto\Package\InstallationStatusDto;
use Borlabs\Cookie\DtoList\Package\InstallationStatusDtoList;
use Borlabs\Cookie\DtoList\System\SettingsFieldDtoList;
use Borlabs\Cookie\Enum\Package\ComponentTypeEnum;
use Borlabs\Cookie\Enum\Package\InstallationStatusEnum;
use Borlabs\Cookie\Enum\System\SettingsFieldDataTypeEnum;
use Borlabs\Cookie\Exception\GenericException;
use Borlabs\Cookie\Exception\TranslatedException;
use Borlabs\Cookie\Exception\UnexpectedRepositoryOperationException;
use Borlabs\Cookie\Localization\System\ModelLocalizationStrings;
use Borlabs\Cookie\Localization\System\PackageManagerComponentLocalizationStrings;
use Borlabs\Cookie\Model\Package\PackageModel;
use Borlabs\Cookie\Model\Service\ServiceLocationModel;
use Borlabs\Cookie\Model\Service\ServiceModel;
use Borlabs\Cookie\Model\ServiceGroup\ServiceGroupModel;
use Borlabs\Cookie\Repository\Service\ServiceCookieRepository;
use Borlabs\Cookie\Repository\Service\ServiceLocationRepository;
use Borlabs\Cookie\Repository\Service\ServiceOptionRepository;
use Borlabs\Cookie\Repository\Service\ServiceRepository;
use Borlabs\Cookie\Repository\ServiceGroup\ServiceGroupRepository;
use Borlabs\Cookie\Support\Formatter;
use Borlabs\Cookie\Support\Searcher;
use Borlabs\Cookie\System\Log\Log;
use Borlabs\Cookie\System\Package\Traits\SettingsFieldListTrait;
use Borlabs\Cookie\System\ServiceGroup\ServiceGroupService;

final class ServiceComponent
{
    use SettingsFieldListTrait;

    private Log $log;

    private ModelLocalizationStrings $modelLocalizationStrings;

    private PackageManagerComponentLocalizationStrings $packageManagerComponentLocalizationStrings;

    private ProviderComponent $providerComponent;

    private ServiceCookieRepository $serviceCookieRepository;

    private ServiceCookieTransformer $serviceCookieTransformer;

    private ServiceGroupRepository $serviceGroupRepository;

    private ServiceGroupService $serviceGroupService;

    private ServiceLocationRepository $serviceLocationRepository;

    private ServiceOptionRepository $serviceOptionRepository;

    private ServiceOptionTransformer $serviceOptionTransformer;

    private ServiceRepository $serviceRepository;

    private ServiceTransformer $serviceTransformer;

    public function __construct(
        Log $log,
        ModelLocalizationStrings $modelLocalizationStrings,
        PackageManagerComponentLocalizationStrings $packageManagerComponentLocalizationStrings,
        ProviderComponent $providerComponent,
        ServiceCookieRepository $serviceCookieRepository,
        ServiceCookieTransformer $serviceCookieTransformer,
        ServiceGroupRepository $serviceGroupRepository,
        ServiceGroupService $serviceGroupService,
        ServiceLocationRepository $serviceLocationRepository,
        ServiceOptionRepository $serviceOptionRepository,
        ServiceOptionTransformer $serviceOptionTransformer,
        ServiceRepository $serviceRepository,
        ServiceTransformer $serviceTransformer
    ) {
        $this->log = $log;
        $this->modelLocalizationStrings = $modelLocalizationStrings;
        $this->packageManagerComponentLocalizationStrings = $packageManagerComponentLocalizationStrings;
        $this->providerComponent = $providerComponent;
        $this->serviceCookieRepository = $serviceCookieRepository;
        $this->serviceCookieTransformer = $serviceCookieTransformer;
        $this->serviceGroupRepository = $serviceGroupRepository;
        $this->serviceGroupService = $serviceGroupService;
        $this->serviceLocationRepository = $serviceLocationRepository;
        $this->serviceOptionRepository = $serviceOptionRepository;
        $this->serviceOptionTransformer = $serviceOptionTransformer;
        $this->serviceRepository = $serviceRepository;
        $this->serviceTransformer = $serviceTransformer;
    }

    /**
     * @param array<PackageModel> $packages
     *
     * @return array<PackageModel>
     */
    public function checkUsage(ServiceModel $serviceModel, PackageModel $ignorePackage, array $packages): array
    {
        $resourcesInUse = [];

        foreach ($packages as $package) {
            if ($package->installedAt === null || $package->id === $ignorePackage->id) {
                continue;
            }

            foreach ($package->components->services->list as $service) {
                if ($service->key === $serviceModel->key) {
                    $resourcesInUse[] = $package;

                    break;
                }
            }
        }

        return $resourcesInUse;
    }

    public function install(
        object $serviceData,
        string $borlabsServicePackageKey,
        string $languageCode,
        bool $isInitialInstall = true,
        ?array $componentSettings = null
    ): InstallationStatusDto {
        $serviceModel = $this->serviceTransformer->toModel($serviceData, $borlabsServicePackageKey, $languageCode);
        $service = $this->serviceRepository->getByKey($serviceData->key, $languageCode);

        if ($service !== null && $service->borlabsServicePackageKey === null) {
            return $this->getFailureInstallationStatus(
                $serviceModel,
                Formatter::interpolate($this->packageManagerComponentLocalizationStrings::get()['alert']['keyAlreadyInUse'], [
                    'key' => $serviceData->key,
                    'resource' => $this->modelLocalizationStrings::get()['models'][ServiceModel::class],
                ]),
            );
        }

        $providerInstallStatus = $this->providerComponent->install($serviceData->provider, $borlabsServicePackageKey, $languageCode, $componentSettings);

        if ($providerInstallStatus->status->is(InstallationStatusEnum::FAILURE())) {
            return $providerInstallStatus;
        }

        $serviceModel->providerId = $providerInstallStatus->id;
        $serviceGroup = null;

        if ($componentSettings !== null) {
            try {
                $serviceGroup = $this->getServiceGroup($serviceModel->settingsFields, $componentSettings, $languageCode);
            } catch (UnexpectedRepositoryOperationException $e) {
                return new InstallationStatusDto(
                    InstallationStatusEnum::FAILURE(),
                    ComponentTypeEnum::SERVICE(),
                    $serviceModel->key,
                    $serviceModel->name . ' (' . $serviceModel->language . ')',
                    $serviceModel->id,
                    new InstallationStatusDtoList([$providerInstallStatus,]),
                    $e->getMessage(),
                );
            }
        }

        if ($serviceGroup === null) {
            $this->log->error(
                'Service could not be installed, service group not found.',
                [
                    'componentSettings' => $componentSettings,
                    'languageCode' => $languageCode,
                    'packageKey' => $borlabsServicePackageKey,
                    'serviceData' => $serviceData,
                ],
            );

            return new InstallationStatusDto(
                InstallationStatusEnum::FAILURE(),
                ComponentTypeEnum::SERVICE(),
                $serviceModel->key,
                $serviceModel->name . ' (' . $serviceModel->language . ')',
                $serviceModel->id,
            );
        }

        // If the service already exists, update the values and perhaps the text.
        if ($service !== null) {
            $serviceModel->id = $service->id;
            $serviceModel->position = $service->position;
            $serviceModel->serviceGroupId = $service->serviceGroupId;
            $serviceModel->status = $service->status;
            // Repeatable settings fields only have the visibility "edit-only".
            $serviceModel->repeatableSettingsFields = $service->repeatableSettingsFields;
            // Set current values in our model. This includes fields with the visibility "edit-only".
            $serviceModel->settingsFields = $this->migrateSettingsFieldValues($serviceModel->settingsFields, $service->settingsFields);

            if (isset($componentSettings['overwrite-code']) && $componentSettings['overwrite-code'] === '0') {
                $serviceModel->fallbackCode = $service->fallbackCode;
                $serviceModel->optInCode = $service->optInCode;
                $serviceModel->optOutCode = $service->optOutCode;
            }

            if (isset($componentSettings['overwrite-translation']) && $componentSettings['overwrite-translation'] === '0') {
                $serviceModel->description = $service->description;
                $serviceModel->name = $service->name;
            }
        } else {
            $serviceModel->serviceGroupId = $serviceGroup->id;
            // Enable the status only on initial install.
            // For compliance reasons, newly added services should remain disabled by default.
            $serviceModel->status = $isInitialInstall;
        }

        // Set values from the install/update form fields. This are fields with the visibility "edit-and-setup" and "setup-only".
        if ($componentSettings !== null) {
            $serviceModel->settingsFields = $this->updateSettingsValuesFromFormFields($serviceModel->settingsFields, $componentSettings);
        }

        if ($service !== null) {
            $this->serviceRepository->update($serviceModel);
        } else {
            $serviceModel = $this->serviceRepository->insert($serviceModel);
        }

        if ($serviceModel->id === -1) {
            $this->log->error(
                'Service could not be installed.',
                [
                    'componentSettings' => $componentSettings,
                    'languageCode' => $languageCode,
                    'packageKey' => $borlabsServicePackageKey,
                    'serviceData' => $serviceData,
                ],
            );

            return new InstallationStatusDto(
                InstallationStatusEnum::FAILURE(),
                ComponentTypeEnum::SERVICE(),
                $serviceModel->key,
                $serviceModel->name . ' (' . $serviceModel->language . ')',
                $serviceModel->id,
                new InstallationStatusDtoList([$providerInstallStatus,]),
            );
        }

        // If the service is null, $serviceModel was just inserted.
        if ($service === null || isset($componentSettings['overwrite-translation']) && $componentSettings['overwrite-translation'] === '1') {
            $this->handleCookies($serviceModel, $serviceData->config->cookies, $languageCode);
            $this->handleLocations($serviceModel, $serviceData->config->locations);
            $this->handleOptions($serviceModel, $serviceData->config->options, $languageCode);
        }

        return new InstallationStatusDto(
            InstallationStatusEnum::SUCCESS(),
            ComponentTypeEnum::SERVICE(),
            $serviceModel->key,
            $serviceModel->name . ' (' . $serviceModel->language . ')',
            $serviceModel->id,
            new InstallationStatusDtoList([$providerInstallStatus,]),
        );
    }

    public function reassignToOtherPackage(PackageModel $packageModel, ServiceModel $serviceModel): InstallationStatusDto
    {
        $serviceModel->borlabsServicePackageKey = $packageModel->borlabsServicePackageKey;
        $success = $this->serviceRepository->update($serviceModel);

        if ($success) {
            return $this->getSuccessInstallationStatus($serviceModel);
        }

        $this->log->error(
            'Reassignment of the "{{ serviceName }}" service failed.',
            [
                'packageKey' => $packageModel->borlabsServicePackageKey,
                'serviceKey' => $serviceModel->key,
                'serviceName' => $serviceModel->name,
            ],
        );

        return $this->getFailureInstallationStatus($serviceModel);
    }

    /**
     * @param array<PackageModel> $packages
     *
     * @return array<InstallationStatusDto>
     */
    public function uninstall(PackageModel $packageModel, ServiceModel $serviceModel, array $packages, bool $uninstallProvider): array
    {
        $response = [];

        $usage = $this->checkUsage($serviceModel, $packageModel, $packages);

        if (count($usage) === 0) {
            try {
                $this->serviceRepository->deleteWithRelations($serviceModel->id);
                $response[] = $this->getSuccessInstallationStatus($serviceModel);

                if ($uninstallProvider) {
                    $response[] = $this->providerComponent->uninstall($packageModel, $serviceModel->providerId);
                }
            } catch (TranslatedException $e) {
                $this->log->debug('Service uninstallation failed', [
                    'exception' => $e,
                ]);
                $response[] = $this->getFailureInstallationStatus($serviceModel, $e->getTranslatedMessage());
            } catch (GenericException $e) {
                $this->log->error('Service uninstallation failed', [
                    'exceptionMessage' => $e->getMessage(),
                ]);
                $response[] = $this->getFailureInstallationStatus($serviceModel);
            }
        } else {
            $response[] = $this->reassignToOtherPackage($usage[0], $serviceModel);
        }

        return $response;
    }

    private function getFailureInstallationStatus(ServiceModel $serviceModel, ?string $message = null): InstallationStatusDto
    {
        return new InstallationStatusDto(
            InstallationStatusEnum::FAILURE(),
            ComponentTypeEnum::SERVICE(),
            $serviceModel->key,
            $serviceModel->name,
            $serviceModel->id,
            null,
            $message,
        );
    }

    private function getOriginalServiceGroup(string $serviceGroupKey): ?ServiceGroupModel
    {
        $originalServiceGroup = null;
        $serviceGroups = $this->serviceGroupRepository->getAllByKey($serviceGroupKey);

        if ($serviceGroups !== null) {
            foreach ($serviceGroups as $serviceGroup) {
                if ($originalServiceGroup === null || $serviceGroup->id < $originalServiceGroup->id) {
                    $originalServiceGroup = $serviceGroup;
                }
            }
        }

        return $originalServiceGroup;
    }

    /**
     * @throws \Borlabs\Cookie\Exception\UnexpectedRepositoryOperationException
     */
    private function getServiceGroup(
        SettingsFieldDtoList $settingsFieldList,
        array $componentSettings,
        string $languageCode
    ): ?ServiceGroupModel {
        $serviceGroup = null;
        $serviceGroupKey = null;

        // Find field of type SYSTEM_SERVICE_GROUP. Each service MUST contain this field.
        foreach ($settingsFieldList->list as $settingsField) {
            if ($settingsField->dataType->is(SettingsFieldDataTypeEnum::SYSTEM_SERVICE_GROUP())) {
                if (isset($componentSettings[$settingsField->key])) {
                    $serviceGroupKey = $componentSettings[$settingsField->key];
                    $serviceGroup = $this->serviceGroupRepository->getByKey($serviceGroupKey, $languageCode);
                }

                break;
            }
        }

        if ($serviceGroup !== null) {
            return $serviceGroup;
        }

        // Fallback if the service group for the requested language does not exist.
        if ($serviceGroup === null && $serviceGroupKey !== null) {
            $originalServiceGroup = $this->getOriginalServiceGroup($serviceGroupKey);
            // Create Service Group for requested language
            $serviceGroupList = $this->serviceGroupService->handleAdditionalLanguages(
                $originalServiceGroup->id,
                [
                    'description' => $originalServiceGroup->description,
                    'key' => $originalServiceGroup->key,
                    'name' => $originalServiceGroup->name,
                    'position' => (string) $originalServiceGroup->position,
                    'preSelected' => (string) $originalServiceGroup->preSelected,
                    'status' => (string) $originalServiceGroup->status,
                ],
                [$languageCode],
                [$languageCode],
            );

            return $this->serviceGroupRepository->findByIdOrFail(
                (int) Searcher::findObject($serviceGroupList->list, 'key', $languageCode)->value,
            );
        }

        $this->log->error(
            'Service group "{{ serviceGroupKey }}" not found.',
            [
                'languageCode' => $languageCode,
                'serviceGroupKey' => $serviceGroupKey ?? 'MISSING',
            ],
        );

        return null;
    }

    private function getSuccessInstallationStatus(ServiceModel $serviceModel): InstallationStatusDto
    {
        return new InstallationStatusDto(
            InstallationStatusEnum::SUCCESS(),
            ComponentTypeEnum::SERVICE(),
            $serviceModel->key,
            $serviceModel->name,
            $serviceModel->id,
        );
    }

    private function handleCookies(ServiceModel $service, array $serviceCookies, string $languageCode)
    {
        // Delete all old cookies
        $cookies = $this->serviceCookieRepository->find(['serviceId' => $service->id]);

        foreach ($cookies as $cookie) {
            $this->serviceCookieRepository->delete($cookie);
        }

        // Add new cookies
        foreach ($serviceCookies as $cookie) {
            $cookieModel = $this->serviceCookieTransformer->toModel($cookie, $languageCode);
            $cookieModel->serviceId = $service->id;
            $this->serviceCookieRepository->insert($cookieModel);
        }
    }

    private function handleLocations(ServiceModel $service, array $serviceLocations)
    {
        // Delete all old locations
        $locations = $this->serviceLocationRepository->find(['serviceId' => $service->id]);

        foreach ($locations as $location) {
            $this->serviceLocationRepository->delete($location);
        }

        // Add new locations
        foreach ($serviceLocations as $location) {
            $locationModel = new ServiceLocationModel();
            $locationModel->hostname = $location->hostname;
            $locationModel->path = $location->path;
            $locationModel->serviceId = $service->id;
            $this->serviceLocationRepository->insert($locationModel);
        }
    }

    private function handleOptions(ServiceModel $service, array $serviceOptions, string $languageCode)
    {
        // Delete all old options
        $options = $this->serviceOptionRepository->find(['serviceId' => $service->id]);

        foreach ($options as $option) {
            $this->serviceOptionRepository->delete($option);
        }

        // Add new options
        foreach ($serviceOptions as $option) {
            $optionModel = $this->serviceOptionTransformer->toModel($option, $languageCode);
            $optionModel->serviceId = $service->id;
            $this->serviceOptionRepository->insert($optionModel);
        }
    }
}
