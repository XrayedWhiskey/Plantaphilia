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

namespace Borlabs\Cookie\Repository\ContentBlocker;

use Borlabs\Cookie\Dto\Repository\BelongsToRelationDto;
use Borlabs\Cookie\Dto\Repository\HasManyRelationDto;
use Borlabs\Cookie\Dto\Repository\PropertyMapDto;
use Borlabs\Cookie\Dto\Repository\PropertyMapItemDto;
use Borlabs\Cookie\Dto\Repository\PropertyMapRelationItemDto;
use Borlabs\Cookie\Exception\ModelDeletionException;
use Borlabs\Cookie\Model\ContentBlocker\ContentBlockerModel;
use Borlabs\Cookie\Repository\AbstractRepositoryWithLanguage;
use Borlabs\Cookie\Repository\Package\PackageRepository;
use Borlabs\Cookie\Repository\Provider\ProviderRepository;
use Borlabs\Cookie\Repository\RepositoryInterface;
use Borlabs\Cookie\Repository\Service\ServiceRepository;

/**
 * @extends AbstractRepositoryWithLanguage<ContentBlockerModel>
 */
final class ContentBlockerRepository extends AbstractRepositoryWithLanguage implements RepositoryInterface
{
    public const MODEL = ContentBlockerModel::class;

    public const TABLE = 'borlabs_cookie_content_blockers';

    protected const UNDELETABLE = true;

    public static function propertyMap(): PropertyMapDto
    {
        return new PropertyMapDto([
            new PropertyMapItemDto('id', 'id'),
            new PropertyMapItemDto('providerId', 'provider_id'),
            new PropertyMapItemDto('serviceId', 'service_id'),
            new PropertyMapItemDto('borlabsServicePackageKey', 'borlabs_service_package_key'),
            new PropertyMapItemDto('key', 'key'),
            new PropertyMapItemDto('description', 'description'),
            new PropertyMapItemDto('javaScriptGlobal', 'javascript_global'),
            new PropertyMapItemDto('javaScriptInitialization', 'javascript_initialization'),
            new PropertyMapItemDto('language', 'language'),
            new PropertyMapItemDto('languageStrings', 'language_strings'),
            new PropertyMapItemDto('name', 'name'),
            new PropertyMapItemDto('previewCss', 'preview_css'),
            new PropertyMapItemDto('previewHtml', 'preview_html'),
            new PropertyMapItemDto('previewImage', 'preview_image'),
            new PropertyMapItemDto('repeatableSettingsFields', 'repeatable_settings_fields'),
            new PropertyMapItemDto('settingsFields', 'settings_fields'),
            new PropertyMapItemDto('status', 'status'),
            new PropertyMapItemDto('undeletable', 'undeletable'),
            new PropertyMapRelationItemDto(
                'provider',
                new BelongsToRelationDto(
                    ProviderRepository::class,
                    'providerId',
                    'id',
                    'contentBlockers',
                ),
            ),
            new PropertyMapRelationItemDto(
                'service',
                new BelongsToRelationDto(
                    ServiceRepository::class,
                    'serviceId',
                    'id',
                ),
            ),
            new PropertyMapRelationItemDto(
                'package',
                new BelongsToRelationDto(
                    PackageRepository::class,
                    'borlabsServicePackageKey',
                    'borlabsServicePackageKey',
                ),
            ),
            new PropertyMapRelationItemDto(
                'contentBlockerLocations',
                new HasManyRelationDto(
                    ContentBlockerLocationRepository::class,
                    'id',
                    'contentBlockerId',
                    'contentBlocker',
                ),
            ),
        ]);
    }

    /**
     * @throws \Borlabs\Cookie\Exception\UnexpectedRepositoryOperationException
     * @throws \Borlabs\Cookie\Exception\ModelDeletionException
     */
    public function deleteWithRelations(int $id): void
    {
        $contentBlocker = $this->findById($id, ['contentBlockerLocations']);

        if ($contentBlocker === null) {
            return;
        }

        if ($contentBlocker->contentBlockerLocations !== null) {
            foreach ($contentBlocker->contentBlockerLocations as $contentBlockerLocation) {
                $result = $this->container->get(ContentBlockerLocationRepository::class)->delete($contentBlockerLocation);

                if ($result !== 1) {
                    throw new ModelDeletionException($contentBlockerLocation, (string) $contentBlockerLocation->id);
                }
            }
        }

        $result = $this->forceDelete($contentBlocker);

        if ($result !== 1) {
            throw new ModelDeletionException($contentBlocker, $contentBlocker->name);
        }
    }

    /**
     * TODO: near identical to getAllOfCurrentLanguage which cannot be used in frontend.
     */
    public function getAll(): array
    {
        return $this->find([], [
            'name' => 'ASC',
        ]);
    }

    public function getAllActiveOfLanguage(string $languageCode): array
    {
        return $this->find(['language' => $languageCode, 'status' => true]);
    }

    /**
     * @return null|ContentBlockerModel[]
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
     * @return ContentBlockerModel[]
     */
    public function getAllOfSelectedLanguage(bool $withRelatedData = false, ?bool $status = null): array
    {
        $relatedData = [];

        if ($withRelatedData) {
            $relatedData = [
                'contentBlockerLocations',
            ];
        }

        $where = [
            'language' => $this->language->getSelectedLanguageCode(),
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

    public function getByKey(string $key, ?string $languageCode = null, bool $withRelatedData = false): ?ContentBlockerModel
    {
        if (!isset($languageCode)) {
            $languageCode = $this->language->getSelectedLanguageCode();
        }

        $relatedData = [];

        if ($withRelatedData) {
            $relatedData = [
                'provider',
                'service',
                'contentBlockerLocations',
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
     * @throws \Borlabs\Cookie\Exception\UnexpectedRepositoryOperationException
     */
    public function switchStatus(int $id): void
    {
        $model = $this->findByIdOrFail($id);
        $model->status = !$model->status;
        $this->update($model);
    }
}
