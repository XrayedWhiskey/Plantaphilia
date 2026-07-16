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

use Borlabs\Cookie\ApiClient\Transformer\ProviderTransformer;
use Borlabs\Cookie\Dto\Package\InstallationStatusDto;
use Borlabs\Cookie\Enum\Package\ComponentTypeEnum;
use Borlabs\Cookie\Enum\Package\InstallationStatusEnum;
use Borlabs\Cookie\Exception\GenericException;
use Borlabs\Cookie\Exception\TranslatedException;
use Borlabs\Cookie\Localization\System\ModelLocalizationStrings;
use Borlabs\Cookie\Localization\System\PackageManagerComponentLocalizationStrings;
use Borlabs\Cookie\Model\Package\PackageModel;
use Borlabs\Cookie\Model\Provider\ProviderModel;
use Borlabs\Cookie\Repository\Expression\BinaryOperatorExpression;
use Borlabs\Cookie\Repository\Expression\ListExpression;
use Borlabs\Cookie\Repository\Expression\LiteralExpression;
use Borlabs\Cookie\Repository\Expression\ModelFieldNameExpression;
use Borlabs\Cookie\Repository\Package\PackageRepository;
use Borlabs\Cookie\Repository\Provider\ProviderRepository;
use Borlabs\Cookie\Support\Formatter;
use Borlabs\Cookie\System\Log\Log;

final class ProviderComponent
{
    private Log $log;

    private ModelLocalizationStrings $modelLocalizationStrings;

    private PackageManagerComponentLocalizationStrings $packageManagerComponentLocalizationStrings;

    private PackageRepository $packageRepository;

    private ProviderRepository $providerRepository;

    private ProviderTransformer $providerTransformer;

    public function __construct(
        Log $log,
        PackageRepository $packageRepository,
        ProviderRepository $providerRepository,
        ProviderTransformer $providerTransformer,
        PackageManagerComponentLocalizationStrings $packageManagerComponentLocalizationStrings,
        ModelLocalizationStrings $modelLocalizationStrings
    ) {
        $this->log = $log;
        $this->packageRepository = $packageRepository;
        $this->providerRepository = $providerRepository;
        $this->providerTransformer = $providerTransformer;
        $this->packageManagerComponentLocalizationStrings = $packageManagerComponentLocalizationStrings;
        $this->modelLocalizationStrings = $modelLocalizationStrings;
    }

    /**
     * Precondition: Provider needs relations "services" and "contentBlockers" loaded.
     *
     * @return array<PackageModel>
     */
    public function checkUsage(ProviderModel $providerModel, PackageModel $ignorePackage): array
    {
        $packageKeysInUse = [];

        foreach ($providerModel->services as $service) {
            if ($service->borlabsServicePackageKey !== null && $service->borlabsServicePackageKey !== $ignorePackage->borlabsServicePackageKey) {
                $packageKeysInUse[] = $service->borlabsServicePackageKey;
            }
        }

        foreach ($providerModel->contentBlockers as $contentBlocker) {
            if ($contentBlocker->borlabsServicePackageKey !== null && $contentBlocker->borlabsServicePackageKey !== $ignorePackage->borlabsServicePackageKey) {
                $packageKeysInUse[] = $contentBlocker->borlabsServicePackageKey;
            }
        }

        if (count($packageKeysInUse) === 0) {
            return [];
        }

        return $this->packageRepository->find([
            new BinaryOperatorExpression(
                new ModelFieldNameExpression('borlabsServicePackageKey'),
                'IN',
                new ListExpression(
                    array_map(
                        fn ($packageKey) => new LiteralExpression($packageKey),
                        $packageKeysInUse,
                    ),
                ),
            ),
        ]);
    }

    public function install(
        object $providerData,
        string $borlabsServicePackageKey,
        string $languageCode,
        ?array $componentSettings = null
    ): InstallationStatusDto {
        $providerModel = $this->providerTransformer->toModel($providerData, $borlabsServicePackageKey, $languageCode);
        $providerByNormalKey = $this->providerRepository->getByKey($providerData->key, $languageCode);

        if ($providerByNormalKey !== null
            && $providerByNormalKey->borlabsServicePackageKey === null
            && $providerByNormalKey->borlabsServiceProviderKey !== 'default'
            && $providerByNormalKey->borlabsServiceProviderKey !== 'unknown'
        ) {
            return $this->getFailureInstallationStatus(
                $providerByNormalKey,
                Formatter::interpolate($this->packageManagerComponentLocalizationStrings::get()['alert']['keyAlreadyInUse'], [
                    'key' => $providerByNormalKey->key,
                    'resource' => $this->modelLocalizationStrings::get()['models'][ProviderModel::class],
                ]),
            );
        }

        $provider = $this->providerRepository->getByBorlabsServiceProviderKey($providerData->key, $languageCode);

        if ($provider !== null) {
            $providerModel->id = $provider->id;

            if (
                isset($componentSettings['overwrite-translation'])
                && $componentSettings['overwrite-translation'] === '0'
            ) {
                $providerModel->address = $provider->address;
                $providerModel->cookieUrl = $provider->cookieUrl;
                $providerModel->description = $provider->description;
                $providerModel->name = $provider->name;
                $providerModel->optOutUrl = $provider->optOutUrl;
                $providerModel->privacyUrl = $provider->privacyUrl;
            }

            // Do not overwrite the default provider (owner of the website & unknown)
            if ($providerModel->borlabsServiceProviderKey !== 'default' && $providerModel->borlabsServiceProviderKey !== 'unknown') {
                $this->providerRepository->update($providerModel);
            }
        } else {
            $providerModel = $this->providerRepository->insert($providerModel);
        }

        if ($providerModel->id === -1) {
            $this->log->error(
                'Provider could not be installed.',
                [
                    'componentSettings' => $componentSettings,
                    'languageCode' => $languageCode,
                    'providerData' => $providerData,
                ],
            );
        }

        return new InstallationStatusDto(
            $providerModel->id !== -1 ? InstallationStatusEnum::SUCCESS() : InstallationStatusEnum::FAILURE(),
            ComponentTypeEnum::PROVIDER(),
            $providerModel->borlabsServiceProviderKey,
            $providerModel->name . ' (' . $providerModel->language . ')',
            $providerModel->id,
        );
    }

    public function reassignToOtherPackage(PackageModel $packageModel, ProviderModel $providerModel): InstallationStatusDto
    {
        $providerModel->borlabsServicePackageKey = $packageModel->borlabsServicePackageKey;
        $success = $this->providerRepository->update($providerModel);

        if ($success) {
            return $this->getSuccessInstallationStatus($providerModel);
        }

        return $this->getFailureInstallationStatus($providerModel);
    }

    public function uninstall(PackageModel $packageModel, int $providerId): InstallationStatusDto
    {
        $providerModel = $this->providerRepository->findById($providerId, ['services', 'contentBlockers']);

        $usage = $this->checkUsage($providerModel, $packageModel);

        if (count($usage) === 0) {
            try {
                $this->providerRepository->deleteWithRelationChecks($providerModel, true);

                return $this->getSuccessInstallationStatus($providerModel);
            } catch (TranslatedException $e) {
                $this->log->debug('Provider uninstallation failed', [
                    'exception' => $e,
                ]);

                return $this->getFailureInstallationStatus($providerModel, $e->getTranslatedMessage());
            } catch (GenericException $e) {
                $this->log->error('Provider uninstallation failed', [
                    'exceptionMessage' => $e->getMessage(),
                ]);

                return $this->getFailureInstallationStatus($providerModel);
            }
        } else {
            return $this->reassignToOtherPackage($usage[0], $providerModel);
        }
    }

    private function getFailureInstallationStatus(
        ProviderModel $providerModel,
        ?string $message = null
    ): InstallationStatusDto {
        return new InstallationStatusDto(
            InstallationStatusEnum::FAILURE(),
            ComponentTypeEnum::PROVIDER(),
            $providerModel->key,
            $providerModel->name,
            $providerModel->id,
            null,
            $message,
        );
    }

    private function getSuccessInstallationStatus(ProviderModel $providerModel): InstallationStatusDto
    {
        return new InstallationStatusDto(
            InstallationStatusEnum::SUCCESS(),
            ComponentTypeEnum::PROVIDER(),
            $providerModel->key,
            $providerModel->name,
            $providerModel->id,
        );
    }
}
