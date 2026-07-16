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

namespace Borlabs\Cookie\Repository\Service;

use Borlabs\Cookie\Dto\Repository\BelongsToRelationDto;
use Borlabs\Cookie\Dto\Repository\HasManyRelationDto;
use Borlabs\Cookie\Dto\Repository\PropertyMapDto;
use Borlabs\Cookie\Dto\Repository\PropertyMapItemDto;
use Borlabs\Cookie\Dto\Repository\PropertyMapRelationItemDto;
use Borlabs\Cookie\Exception\ModelDeletionException;
use Borlabs\Cookie\Exception\PropertyDoesNotExistException;
use Borlabs\Cookie\Exception\StillInUseModelDeletionException;
use Borlabs\Cookie\Exception\UnexpectedRepositoryOperationException;
use Borlabs\Cookie\Model\Service\ServiceModel;
use Borlabs\Cookie\Repository\AbstractRepositoryWithLanguage;
use Borlabs\Cookie\Repository\ContentBlocker\ContentBlockerRepository;
use Borlabs\Cookie\Repository\Provider\ProviderRepository;
use Borlabs\Cookie\Repository\RepositoryInterface;
use Borlabs\Cookie\Repository\ServiceGroup\ServiceGroupRepository;
use Borlabs\Cookie\Support\Searcher;

/**
 * @extends AbstractRepositoryWithLanguage<ServiceModel>
 *
 * @implements RepositoryInterface<ServiceModel>
 */
final class ServiceRepository extends AbstractRepositoryWithLanguage implements RepositoryInterface
{
    public const MODEL = ServiceModel::class;

    public const TABLE = 'borlabs_cookie_services';

    protected const UNDELETABLE = true;

    public static function propertyMap(): PropertyMapDto
    {
        return new PropertyMapDto([
            new PropertyMapItemDto('id', 'id'),
            new PropertyMapItemDto('providerId', 'provider_id'),
            new PropertyMapItemDto('serviceGroupId', 'service_group_id'),
            new PropertyMapItemDto('borlabsServicePackageKey', 'borlabs_service_package_key'),
            new PropertyMapItemDto('key', 'key'),
            new PropertyMapItemDto('description', 'description'),
            new PropertyMapItemDto('fallbackCode', 'fallback_code'),
            new PropertyMapItemDto('language', 'language'),
            new PropertyMapItemDto('name', 'name'),
            new PropertyMapItemDto('optInCode', 'opt_in_code'),
            new PropertyMapItemDto('optOutCode', 'opt_out_code'),
            new PropertyMapItemDto('position', 'position'),
            new PropertyMapItemDto('repeatableSettingsFields', 'repeatable_settings_fields'),
            new PropertyMapItemDto('settingsFields', 'settings_fields'),
            new PropertyMapItemDto('status', 'status'),
            new PropertyMapItemDto('undeletable', 'undeletable'),
            new PropertyMapRelationItemDto(
                'contentBlockers',
                new HasManyRelationDto(
                    ContentBlockerRepository::class,
                    'id',
                    'serviceId',
                    'service',
                ),
            ),
            new PropertyMapRelationItemDto(
                'serviceGroup',
                new BelongsToRelationDto(
                    ServiceGroupRepository::class,
                    'serviceGroupId',
                    'id',
                    'services',
                ),
            ),
            new PropertyMapRelationItemDto(
                'provider',
                new BelongsToRelationDto(
                    ProviderRepository::class,
                    'providerId',
                    'id',
                    'services',
                ),
            ),
            new PropertyMapRelationItemDto(
                'serviceCookies',
                new HasManyRelationDto(
                    ServiceCookieRepository::class,
                    'id',
                    'serviceId',
                    'service',
                ),
            ),
            new PropertyMapRelationItemDto(
                'serviceLocations',
                new HasManyRelationDto(
                    ServiceLocationRepository::class,
                    'id',
                    'serviceId',
                    'service',
                ),
            ),
            new PropertyMapRelationItemDto(
                'serviceOptions',
                new HasManyRelationDto(
                    ServiceOptionRepository::class,
                    'id',
                    'serviceId',
                    'service',
                ),
            ),
        ]);
    }

    /**
     * @throws \Borlabs\Cookie\Exception\StillInUseModelDeletionException
     * @throws \Borlabs\Cookie\Exception\UnexpectedRepositoryOperationException
     * @throws \Borlabs\Cookie\Exception\ModelDeletionException
     */
    public function deleteWithRelations(int $id): void
    {
        $service = $this->findById($id, [
            'serviceCookies',
            'serviceLocations',
            'serviceOptions',
            'contentBlockers',
        ]);

        if ($service === null) {
            return;
        }

        if ($service->contentBlockers !== null && count($service->contentBlockers) > 0) {
            throw new StillInUseModelDeletionException($service, $service->contentBlockers[0], $service->name, $service->contentBlockers[0]->name);
        }

        if ($service->serviceCookies !== null) {
            foreach ($service->serviceCookies as $serviceCookie) {
                $result = $this->container->get(ServiceCookieRepository::class)->delete($serviceCookie);

                if ($result !== 1) {
                    throw new ModelDeletionException($serviceCookie, $serviceCookie->name);
                }
            }
        }

        if ($service->serviceLocations !== null) {
            foreach ($service->serviceLocations as $serviceLocation) {
                $result = $this->container->get(ServiceLocationRepository::class)->delete($serviceLocation);

                if ($result !== 1) {
                    throw new ModelDeletionException($serviceLocation, $serviceLocation->hostname);
                }
            }
        }

        if ($service->serviceOptions !== null) {
            foreach ($service->serviceOptions as $serviceOption) {
                $result = $this->container->get(ServiceOptionRepository::class)->delete($serviceOption);

                if ($result !== 1) {
                    throw new ModelDeletionException($serviceOption, (string) $serviceOption->id);
                }
            }
        }

        $result = $this->forceDelete($service);

        if ($result !== 1) {
            throw new ModelDeletionException($service, $service->name);
        }
    }

    /**
     * @return ServiceModel[]
     */
    public function getAll(): array
    {
        return $this->find(
            [],
            [
                'name' => 'ASC',
            ],
        );
    }

    /**
     * @return null|ServiceModel[]
     */
    public function getAllByKey(string $key): ?array
    {
        $list = $this->find([
            'key' => $key,
        ]);

        if (count($list)) {
            return $list;
        }

        return null;
    }

    /**
     * @return ServiceModel[]
     */
    public function getAllOfCurrentLanguage(bool $withRelatedData = false, ?bool $status = null): array
    {
        $relatedData = [];

        if ($withRelatedData) {
            $relatedData = [
                'provider',
                'serviceCookies',
                'serviceGroup',
                'serviceLocations',
                'serviceOptions',
            ];
        }

        return $this->getAllOfLanguage($this->language->getCurrentLanguageCode(), $relatedData, $status);
    }

    public function getAllOfLanguage(string $languageCode, array $relatedData = [], ?bool $status = null): array
    {
        $where = [
            'language' => $languageCode,
        ];

        if ($status !== null) {
            $where['status'] = $status;
        }

        return $this->find(
            $where,
            [
                'name' => 'ASC',
            ],
            [],
            $relatedData,
        );
    }

    /**
     * @return ServiceModel[]
     */
    public function getAllOfSelectedLanguage(bool $withRelatedData = false, ?bool $status = null): array
    {
        $relatedData = [];

        if ($withRelatedData) {
            $relatedData = [
                'provider',
                'serviceCookies',
                'serviceGroup',
                'serviceLocations',
                'serviceOptions',
            ];
        }

        return $this->getAllOfLanguage($this->language->getSelectedLanguageCode(), $relatedData, $status);
    }

    public function getByKey(string $key, ?string $languageCode = null, bool $withRelatedData = false): ?ServiceModel
    {
        if (!isset($languageCode)) {
            $languageCode = $this->language->getSelectedLanguageCode();
        }

        $relatedData = [];

        if ($withRelatedData) {
            $relatedData = [
                'serviceCookies',
                'serviceGroup',
                'serviceLocations',
                'serviceOptions',
            ];
        }

        $data = $this->find(
            [
                'key' => $key,
                'language' => $languageCode,
            ],
            [],
            [],
            $relatedData,
        );

        if (isset($data[0]->id) === false) {
            return null;
        }

        return $data[0];
    }

    /**
     * @return ServiceModel[]
     */
    public function getPrioritizedServices(string $languageCode): array
    {
        $prioritizedServices = [];

        $data = $this->find(
            [
                'language' => $languageCode,
                'status' => true,
            ],
        );

        foreach ($data as $serviceModel) {
            if (isset($serviceModel->settingsFields->list)) {
                $isPrioritized = Searcher::findObject($serviceModel->settingsFields->list, 'key', 'prioritize');

                if ($isPrioritized !== null && (bool) $isPrioritized->value === true) {
                    $prioritizedServices[] = $serviceModel;
                }
            }
        }

        return $prioritizedServices;
    }

    /**
     * @throws PropertyDoesNotExistException
     * @throws UnexpectedRepositoryOperationException
     */
    public function switchStatus(int $id): void
    {
        $model = $this->findByIdOrFail($id);
        $model->status = !$model->status;
        $this->update($model);
    }
}
