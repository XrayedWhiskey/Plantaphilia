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

namespace Borlabs\Cookie\Repository\ServiceGroup;

use Borlabs\Cookie\Adapter\WpDb;
use Borlabs\Cookie\Container\Container;
use Borlabs\Cookie\Dto\Repository\HasManyRelationDto;
use Borlabs\Cookie\Dto\Repository\PropertyMapDto;
use Borlabs\Cookie\Dto\Repository\PropertyMapItemDto;
use Borlabs\Cookie\Dto\Repository\PropertyMapRelationItemDto;
use Borlabs\Cookie\Model\ServiceGroup\ServiceGroupModel;
use Borlabs\Cookie\Repository\AbstractRepositoryWithLanguage;
use Borlabs\Cookie\Repository\Expression\BinaryOperatorExpression;
use Borlabs\Cookie\Repository\Expression\DirectionDescExpression;
use Borlabs\Cookie\Repository\Expression\DirectionExpression;
use Borlabs\Cookie\Repository\Expression\LiteralExpression;
use Borlabs\Cookie\Repository\Expression\ModelFieldNameExpression;
use Borlabs\Cookie\Repository\RepositoryInterface;
use Borlabs\Cookie\Repository\RepositoryQueryBuilderWithRelations;
use Borlabs\Cookie\Repository\Service\ServiceRepository;
use Borlabs\Cookie\System\Language\Language;

/**
 * @extends AbstractRepositoryWithLanguage<ServiceGroupModel>
 */
final class ServiceGroupRepository extends AbstractRepositoryWithLanguage implements RepositoryInterface
{
    public const MODEL = ServiceGroupModel::class;

    public const TABLE = 'borlabs_cookie_service_groups';

    protected const UNDELETABLE = true;

    public static function propertyMap(): PropertyMapDto
    {
        return new PropertyMapDto([
            new PropertyMapItemDto('id', 'id'),
            new PropertyMapItemDto('key', 'key'),
            new PropertyMapItemDto('description', 'description'),
            new PropertyMapItemDto('language', 'language'),
            new PropertyMapItemDto('name', 'name'),
            new PropertyMapItemDto('position', 'position'),
            new PropertyMapItemDto('preSelected', 'pre_selected'),
            new PropertyMapItemDto('status', 'status'),
            new PropertyMapItemDto('undeletable', 'undeletable'),
            new PropertyMapRelationItemDto(
                'services',
                new HasManyRelationDto(
                    ServiceRepository::class,
                    'id',
                    'serviceGroupId',
                    'serviceGroup',
                ),
            ),
        ]);
    }

    protected ServiceRepository $serviceRepository;

    public function __construct(
        Container $container,
        WpDb $wpdb,
        Language $language,
        ServiceRepository $serviceRepository
    ) {
        parent::__construct($container, $wpdb, $language);
        $this->serviceRepository = $serviceRepository;
    }

    /**
     * @return array<ServiceGroupModel>
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
     * @return array<ServiceGroupModel>
     */
    public function getAllActiveOfLanguage(?string $languageCode = null, bool $withRelatedData = false): array
    {
        $queryBuilder = $this->getQueryBuilderWithRelations();

        if ($withRelatedData) {
            $queryBuilder->addWith(
                'services',
                function (RepositoryQueryBuilderWithRelations $queryBuilder) use ($languageCode) {
                    $queryBuilder->andWhere(new BinaryOperatorExpression(
                        new ModelFieldNameExpression('status'),
                        '=',
                        new LiteralExpression(1),
                    ));
                    $queryBuilder->andWhere(new BinaryOperatorExpression(
                        new ModelFieldNameExpression('language'),
                        '=',
                        new LiteralExpression($languageCode),
                    ));
                },
            );
        }

        $queryBuilder->andWhere(new BinaryOperatorExpression(
            new ModelFieldNameExpression('status'),
            '=',
            new LiteralExpression(1),
        ));
        $queryBuilder->andWhere(new BinaryOperatorExpression(
            new ModelFieldNameExpression('language'),
            '=',
            new LiteralExpression($languageCode),
        ));
        $queryBuilder->addOrderBy(new DirectionExpression(
            new ModelFieldNameExpression('name'),
            new DirectionDescExpression(),
        ));

        return $queryBuilder->getWpSelectQuery()->getResults();
    }

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

    /**
     * @return array<ServiceGroupModel>
     */
    public function getAllOfSelectedLanguage(bool $withRelatedData = false): array
    {
        $relatedData = [];

        if ($withRelatedData) {
            $relatedData = [
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

    public function getByKey(string $key, ?string $languageCode = null, bool $withRelatedData = false): ?ServiceGroupModel
    {
        if (!isset($languageCode)) {
            $languageCode = $this->language->getSelectedLanguageCode();
        }

        $relatedData = [];

        if ($withRelatedData) {
            $relatedData = [
                'services',
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

    public function getServices(ServiceGroupModel $model): array
    {
        return $this->serviceRepository->find([
            'serviceGroupId' => $model->id,
        ]);
    }

    public function hasService(ServiceGroupModel $model): bool
    {
        // TODO: replace with exist query?
        $services = $this->getServices($model);

        return count($services) > 0;
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
