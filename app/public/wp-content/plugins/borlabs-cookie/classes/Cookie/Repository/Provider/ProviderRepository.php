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

namespace Borlabs\Cookie\Repository\Provider;

use Borlabs\Cookie\Dto\Repository\HasManyRelationDto;
use Borlabs\Cookie\Dto\Repository\PropertyMapDto;
use Borlabs\Cookie\Dto\Repository\PropertyMapItemDto;
use Borlabs\Cookie\Dto\Repository\PropertyMapRelationItemDto;
use Borlabs\Cookie\Exception\ModelDeletionException;
use Borlabs\Cookie\Exception\StillInUseModelDeletionException;
use Borlabs\Cookie\Model\Provider\ProviderModel;
use Borlabs\Cookie\Repository\AbstractRepositoryWithLanguage;
use Borlabs\Cookie\Repository\ContentBlocker\ContentBlockerRepository;
use Borlabs\Cookie\Repository\RepositoryInterface;
use Borlabs\Cookie\Repository\Service\ServiceRepository;

/**
 * @extends AbstractRepositoryWithLanguage<ProviderModel>
 */
final class ProviderRepository extends AbstractRepositoryWithLanguage implements RepositoryInterface
{
    public const MODEL = ProviderModel::class;

    public const TABLE = 'borlabs_cookie_providers';

    protected const UNDELETABLE = true;

    public static function propertyMap(): PropertyMapDto
    {
        return new PropertyMapDto([
            new PropertyMapItemDto('id', 'id'),
            new PropertyMapItemDto('borlabsServicePackageKey', 'borlabs_service_package_key'),
            new PropertyMapItemDto('borlabsServiceProviderKey', 'borlabs_service_provider_key'),
            new PropertyMapItemDto('address', 'address'),
            new PropertyMapItemDto('cookieUrl', 'cookie_url'),
            new PropertyMapItemDto('description', 'description'),
            new PropertyMapItemDto('iabVendorId', 'iab_vendor_id'),
            new PropertyMapItemDto('key', 'key'),
            new PropertyMapItemDto('language', 'language'),
            new PropertyMapItemDto('name', 'name'),
            new PropertyMapItemDto('optOutUrl', 'opt_out_url'),
            new PropertyMapItemDto('partners', 'partners'),
            new PropertyMapItemDto('privacyUrl', 'privacy_url'),
            new PropertyMapItemDto('undeletable', 'undeletable'),
            new PropertyMapRelationItemDto(
                'contentBlockers',
                new HasManyRelationDto(
                    ContentBlockerRepository::class,
                    'id',
                    'providerId',
                    'provider',
                ),
            ),
            new PropertyMapRelationItemDto(
                'services',
                new HasManyRelationDto(
                    ServiceRepository::class,
                    'id',
                    'providerId',
                    'provider',
                ),
            ),
        ]);
    }

    /**
     * @throws \Borlabs\Cookie\Exception\UnexpectedRepositoryOperationException
     * @throws \Borlabs\Cookie\Exception\StillInUseModelDeletionException
     * @throws \Borlabs\Cookie\Exception\ModelDeletionException
     */
    public function deleteWithRelationChecks(ProviderModel $provider, bool $relationsLoaded = false): void
    {
        if ($relationsLoaded) {
            $model = $provider;
        } else {
            $model = $this->findById($provider->id, [
                'contentBlockers',
                'services',
            ]);
        }

        if (count($model->services) > 0) {
            throw new StillInUseModelDeletionException($provider, $provider->services[0], $provider->name, $provider->services[0]->name);
        }

        if (count($model->contentBlockers) > 0) {
            throw new StillInUseModelDeletionException($provider, $provider->contentBlockers[0], $provider->name, $provider->contentBlockers[0]->name);
        }

        $result = $this->forceDelete($model);

        if ($result !== 1) {
            throw new ModelDeletionException($model, $model->name);
        }
    }

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
     * @return ProviderModel[]
     */
    public function getAllByKey(string $key): ?array
    {
        $list = $this->find(
            [
                'key' => $key,
            ],
            [
                'name' => 'ASC',
            ],
        );

        if (count($list)) {
            return $list;
        }

        return null;
    }

    public function getAllOfSelectedLanguage(bool $withRelatedData = false): array
    {
        $relatedData = [];

        if ($withRelatedData) {
            $relatedData = [
                'contentBlockers',
                'services',
            ];
        }

        return $this->find(
            [
                'language' => $this->language->getSelectedLanguageCode(),
            ],
            [
                'name' => 'ASC',
            ],
            [],
            $relatedData,
        );
    }

    public function getByBorlabsServiceProviderKey(string $borlabsServiceProviderKey, ?string $languageCode = null): ?ProviderModel
    {
        if (!isset($languageCode)) {
            $languageCode = $this->language->getSelectedLanguageCode();
        }

        $data = $this->find(
            [
                'borlabsServiceProviderKey' => $borlabsServiceProviderKey,
                'language' => $languageCode,
            ],
        );

        if (isset($data[0]->id) === false) {
            return null;
        }

        return $data[0];
    }

    public function getByKey(string $key, ?string $languageCode = null): ?ProviderModel
    {
        if (!isset($languageCode)) {
            $languageCode = $this->language->getSelectedLanguageCode();
        }

        $data = $this->find(
            [
                'key' => $key,
                'language' => $languageCode,
            ],
            [
                'name' => 'ASC',
            ],
        );

        if (isset($data[0]->id) === false) {
            return null;
        }

        return $data[0];
    }
}
