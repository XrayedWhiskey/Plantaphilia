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

namespace Borlabs\Cookie\Repository;

use Borlabs\Cookie\Adapter\WpDb;
use Borlabs\Cookie\Container\Container;
use Borlabs\Cookie\Dto\Repository\PageDto;
use Borlabs\Cookie\Dto\Repository\PaginationResultDto;
use Borlabs\Cookie\Dto\Repository\PropertyMapDto;
use Borlabs\Cookie\Dto\Repository\PropertyMapRelationItemDto;
use Borlabs\Cookie\DtoList\Repository\PageDtoList;
use Borlabs\Cookie\Exception\UnexpectedRepositoryOperationException;
use Borlabs\Cookie\Model\AbstractModel;
use Borlabs\Cookie\Repository\Expression\AbstractExpression;
use Borlabs\Cookie\Repository\Expression\AssignmentExpression;
use Borlabs\Cookie\Repository\Expression\BinaryOperatorExpression;
use Borlabs\Cookie\Repository\Expression\DirectionAscExpression;
use Borlabs\Cookie\Repository\Expression\DirectionDescExpression;
use Borlabs\Cookie\Repository\Expression\DirectionExpression;
use Borlabs\Cookie\Repository\Expression\FunctionExpression;
use Borlabs\Cookie\Repository\Expression\LiteralExpression;
use Borlabs\Cookie\Repository\Expression\ModelFieldNameExpression;
use Borlabs\Cookie\Repository\Expression\NullExpression;
use Borlabs\Cookie\Repository\Expression\SelectAliasExpression;
use Borlabs\Cookie\Repository\Expression\SelectStarExpression;
use InvalidArgumentException;

/**
 * The **Repository** handles all model-database related actions like delete, find, insert and update. The
 * methods delete, insert and update require a model that extended the **AbstractModel**.
 *
 * @see \Borlabs\Cookie\Repository\AbstractRepository::delete() This method deletes a model in the database.
 * @see \Borlabs\Cookie\Repository\AbstractRepository::find() This method returns the models that matches the optional
 *     $where argument. If $where is empty, all models are returned.
 * @see \Borlabs\Cookie\Repository\AbstractRepository::insert() This method inserts the data of a model into the
 *     database.
 * @see \Borlabs\Cookie\Repository\AbstractRepository::update() This method updates the data of a model in the
 *     database.
 * @see \Borlabs\Cookie\Repository\AbstractRepository::getTableSize()
 * @see \Borlabs\Cookie\Repository\AbstractRepository::getTotal()
 * @see \Borlabs\Cookie\Model\AbstractModel
 *
 * @template TModel of AbstractModel
 *
 * @implements RepositoryInterface<TModel>
 */
abstract class AbstractRepository implements RepositoryInterface
{
    protected const UNDELETABLE = null;

    protected Container $container;

    protected ?string $overwrittenTablePrefix = null;

    protected WpDb $wpdb;

    public function __construct(
        Container $container,
        WpDb $wpdb
    ) {
        $this->container = $container;
        $this->wpdb = $wpdb;
    }

    /**
     * This method deletes a model in the database.
     *
     * @param TModel $model the model that is an abstraction of the AbstractModel
     *
     * @throws \Borlabs\Cookie\Exception\UnexpectedRepositoryOperationException
     *
     * @return int The number of entries that have been deleted. Since only single models can be passed, the return
     *             value on success is always 1.
     */
    final public function delete(AbstractModel $model): int
    {
        return $this->_delete($model, true);
    }

    /**
     * This method returns the models that matches the optional `$where` argument. If `$where` is empty, all models are
     * returned.
     *
     * @param array<AbstractExpression>|array<string, null|bool|int|string> $where         Optional; Example: ['id' => 123]
     * @param array<string, string>                                         $orderBy       Optional; Example: ['id' => 'DESC']
     * @param array<int, int>                                               $limit         Optional; Example: [0, 5]; First value: offset, second value: row count
     * @param array<string, callable|string>                                $withRelations Optional; Example: ['myRelation'] or ['myRelation' =>
     *                                                                                     function (RepositoryQueryBuilderWithRelations $queryBuilder) {
     *                                                                                     $queryBuilder->addWith('subRelation of myRelation');
     *                                                                                     }];
     *
     * @return array<TModel>
     */
    final public function find(
        array $where = [],
        array $orderBy = [],
        array $limit = [],
        array $withRelations = []
    ): array {
        $queryBuilder = $this->getQueryBuilderWithRelations();
        $queryBuilder->addSelectColumn(new SelectStarExpression());
        $this->addGenericWhereClauses($queryBuilder, $where);

        foreach ($orderBy as $fieldName => $direction) {
            if ($direction === 'ASC') {
                $direction = new DirectionAscExpression();
            } elseif ($direction === 'DESC') {
                $direction = new DirectionDescExpression();
            } else {
                throw new InvalidArgumentException();
            }
            $queryBuilder->addOrderBy(new DirectionExpression(new ModelFieldNameExpression($fieldName), $direction));
        }
        $queryBuilder->limit($limit[0] ?? 0, $limit[1] ?? null);

        foreach ($withRelations as $key => $value) {
            if (is_string($value)) {
                $queryBuilder->addWith($value);
            } elseif (is_callable($value)) {
                $queryBuilder->addWith($key, $value);
            } else {
                throw new InvalidArgumentException('Expected string or callable');
            }
        }

        $query = $queryBuilder->getWpSelectQuery();

        return $query->getResults();
    }

    /**
     * @return null|TModel
     */
    final public function findById(int $id, array $withRelations = []): ?AbstractModel
    {
        $result = $this->find(['id' => $id,], [], [], $withRelations);

        return $result[0] ?? null;
    }

    /**
     * @throws \Borlabs\Cookie\Exception\UnexpectedRepositoryOperationException
     *
     * @return TModel
     */
    final public function findByIdOrFail(int $id, array $withRelations = []): AbstractModel
    {
        $model = $this->findById($id, $withRelations);

        if ($model === null) {
            throw new UnexpectedRepositoryOperationException('notFound');
        }

        return $model;
    }

    final public function findWithQueryBuilder(
        RepositoryQueryBuilder $queryBuilder
    ): array {
        $query = $queryBuilder->getWpSelectQuery();

        return $query->getResults();
    }

    /**
     * @param TModel $model
     *
     * @throws \Borlabs\Cookie\Exception\UnexpectedRepositoryOperationException
     *
     * @return int The number of entries that have been deleted. Since only single models can be passed, the return
     *             value on success is always 1.
     */
    final public function forceDelete(AbstractModel $model): int
    {
        return $this->_delete($model, false);
    }

    /**
     * @internal this method is not intended to be used in repositories
     *
     * @see getQueryBuilderWithRelations() Use this method instead.
     *
     * TODO: Delete this method
     */
    final public function getModelQueryBuilder(): RepositoryModelQueryBuilder
    {
        return new RepositoryModelQueryBuilder(
            $this->wpdb,
            $this,
            $this->getTablePrefix(),
        );
    }

    /**
     * @internal this method is not intended to be used in repositories
     *
     * @see getQueryBuilderWithRelations() Use this method instead.
     *
     * TODO: Rename method to `getRawQueryBuilder`
     */
    final public function getQueryBuilder(): RepositoryQueryBuilder
    {
        return new RepositoryQueryBuilder(
            $this->wpdb,
            $this,
            $this->getTablePrefix(),
        );
    }

    /**
     * Only use this method in repositories to receive models.
     *
     * TODO: Rename method to `getModelQueryBuilder`
     */
    final public function getQueryBuilderWithRelations(): RepositoryQueryBuilderWithRelations
    {
        return new RepositoryQueryBuilderWithRelations(
            $this->wpdb,
            $this,
            $this->getTablePrefix(),
        );
    }

    /**
     * This method finds all objects of `AbstractRelationInfoDto` in a property map.
     *
     * @return array<\Borlabs\Cookie\Dto\Repository\AbstractRelationInfoDto>
     */
    final public function getRelationInfoProperties(PropertyMapDto $propertyMap): array
    {
        $data = [];
        $mapItems = $propertyMap->map;

        foreach ($mapItems as $propertyMapItem) {
            if (!$propertyMapItem instanceof PropertyMapRelationItemDto) {
                continue;
            }
            $data[$propertyMapItem->reference] = $propertyMapItem->relationInfo;
        }

        return $data;
    }

    final public function getRepositoryForFqn(string $repositoryFqn): ?RepositoryInterface
    {
        return $this->container->get($repositoryFqn);
    }

    public function getTable(): string
    {
        return static::TABLE;
    }

    /**
     * Returns the table size in mebibyte.
     */
    final public function getTableSize(): int
    {
        $dbName = $this->wpdb->dbname;
        // HyperDB workaround
        if (empty($dbName) && defined('DB_NAME')) {
            $dbName = DB_NAME;
        }

        $row = $this->wpdb->get_row(
            $this->wpdb->prepare(
                '
            SELECT
                round(((`data_length` + `index_length`) / 1024 / 1024), 2) `size_in_mib`
            FROM
                `information_schema`.`TABLES`
            WHERE
                `TABLE_SCHEMA` = %s
                AND
                `TABLE_NAME` = %s
            ',
                [
                    $dbName,
                    $this->getTablePrefix() . (static::class)::TABLE,
                ],
            ),
        );

        return (int) $row->size_in_mib;
    }

    /**
     * Returns the total count of entries found that match the $where argument.
     *
     * @param array<AbstractExpression>|array<string, string> $where AND is used for all conditions
     */
    final public function getTotal(array $where = []): int
    {
        $queryBuilder = $this->getQueryBuilder();
        $queryBuilder->addSelectColumn(
            new SelectAliasExpression(
                new FunctionExpression('COUNT', new SelectStarExpression()),
                'total',
            ),
        );
        $this->addGenericWhereClauses($queryBuilder, $where);

        $query = $queryBuilder->getWpSelectQuery();

        return (int) $query->getResults()[0]['total'];
    }

    /**
     * This method inserts the data of a model into the database.
     *
     * @param TModel $model the model that is an abstraction of the AbstractModel
     *
     * @throws \Borlabs\Cookie\Exception\UnexpectedRepositoryOperationException
     *
     * @return TModel
     */
    final public function insert(AbstractModel $model): AbstractModel
    {
        $model = clone $model;

        if (!is_a($model, (static::class)::MODEL)) {
            throw new InvalidArgumentException('[ABSTRACT_REPOSITORY:INSERT][' . static::class . '] The passed in model ' . get_class($model) . ' is not a type of this repository ' . static::class);
        }

        $queryBuilder = $this->getModelQueryBuilder();
        $queryBuilder->setModel($model);
        $query = $queryBuilder->getWpInsertQuery();
        $status = $query->execute();

        if ($status === false) {
            throw new UnexpectedRepositoryOperationException('[ABSTRACT_REPOSITORY:INSERT][' . static::class . '] ' . $this->wpdb->last_error);
        }

        $model->id = $this->wpdb->insert_id;

        return $model;
    }

    /**
     * @param TModel                 $model
     * @param AssignmentExpression[] $updateAssignmentsForInsertOrUpdate
     *
     * @throws \Borlabs\Cookie\Exception\UnexpectedRepositoryOperationException
     *
     * @return TModel
     */
    final public function insertOrUpdate(AbstractModel $model, array $updateAssignmentsForInsertOrUpdate): AbstractModel
    {
        if (!is_a($model, (static::class)::MODEL)) {
            throw new InvalidArgumentException('[ABSTRACT_REPOSITORY:INSERT_OR_UPDATE][' . static::class . '] The passed in model ' . get_class($model) . ' is not a type of this repository ' . static::class);
        }

        $id = empty($model->id) ? -1 : $model->id;

        $queryBuilder = $this->getModelQueryBuilder();
        $queryBuilder->setModel($model);

        foreach ($updateAssignmentsForInsertOrUpdate as $assignmentExpression) {
            if (!$assignmentExpression instanceof AssignmentExpression) {
                throw new InvalidArgumentException('Second parameter array should consist of ' . AssignmentExpression::class);
            }
            $queryBuilder->addUpdateAssignmentForInsertOrUpdate($assignmentExpression);
        }
        $query = $queryBuilder->getWpInsertOrUpdateQuery();
        $status = $query->execute();

        $id = $id === -1 ? $this->wpdb->insert_id : $id;

        if ($status === false) {
            throw new UnexpectedRepositoryOperationException('[ABSTRACT_REPOSITORY:INSERT_OR_UPDATE][' . static::class . '] ' . $this->wpdb->last_error);
        }

        $model = $this->findById($id);

        if ($model === null) {
            throw new UnexpectedRepositoryOperationException('notFound');
        }

        return $model;
    }

    final public function optimizeTable(): void
    {
        $this->wpdb->query('OPTIMIZE TABLE `' . $this->getTablePrefix() . (static::class)::TABLE . '`');
    }

    /**
     * Use this method to overwrite the default table prefix.
     *
     * **ATTENTION** Already instantiated `QueryBuilder` objects will not take this change into account.
     * You have to construct new `QueryBuilder` objects if you want them to access another table.
     */
    final public function overwriteTablePrefix(?string $prefix = null): void
    {
        $this->overwrittenTablePrefix = $prefix;
    }

    final public function paginate(
        int $currentPage = 1,
        array $where = [],
        array $orderBy = [],
        array $withRelations = [],
        int $itemsPerPage = 25,
        ?array $query = null
    ): PaginationResultDto {
        $paginationResult = new PaginationResultDto();
        $paginationResult->total = $this->getTotal($where);
        $paginationResult->perPage = $itemsPerPage;
        $paginationResult->lastPage = (int) ceil($paginationResult->total / $itemsPerPage);
        $paginationResult->lastPage = $paginationResult->lastPage === 0 ? 1 : $paginationResult->lastPage;

        // Current page must be in valid range
        if ($currentPage < 1 || $currentPage > $paginationResult->lastPage) {
            $currentPage = 1;
        }

        $paginationResult->currentPage = $currentPage;
        $paginationResult->from = $currentPage * $itemsPerPage - $itemsPerPage + 1;
        $paginationResult->to = $paginationResult->from + $itemsPerPage - 1;

        if ($paginationResult->to > $paginationResult->total) {
            $paginationResult->to = $paginationResult->total;
        }

        $paginationResult->firstPageQueryParameter = $paginationResult->currentPage !== 1 ? 'borlabs-page=1' : null;
        $paginationResult->nextPageQueryParameter = $paginationResult->lastPage !== $paginationResult->currentPage ? 'borlabs-page=' . ($paginationResult->currentPage + 1) : null;
        $paginationResult->previousPageQueryParameter = $paginationResult->currentPage !== 1 ? 'borlabs-page=' . ($paginationResult->currentPage - 1) : null;
        $paginationResult->lastPageQueryParameter = $paginationResult->lastPage !== $paginationResult->currentPage ? 'borlabs-page=' . $paginationResult->lastPage : null;

        $queryParameters = '';

        if ($query) {
            $queryParameters = http_build_query($query);
            $paginationResult->firstPageQueryParameter = $paginationResult->firstPageQueryParameter ? $queryParameters . '&' . $paginationResult->firstPageQueryParameter : null;
            $paginationResult->nextPageQueryParameter = $paginationResult->nextPageQueryParameter ? $queryParameters . '&' . $paginationResult->nextPageQueryParameter : null;
            $paginationResult->previousPageQueryParameter = $paginationResult->previousPageQueryParameter ? $queryParameters . '&' . $paginationResult->previousPageQueryParameter : null;
            $paginationResult->lastPageQueryParameter = $paginationResult->lastPageQueryParameter ? $queryParameters . '&' . $paginationResult->lastPageQueryParameter : null;
        }

        // Show the previous and next three pages from the current page.
        $paginationResult->pages = new PageDtoList(
            array_map(
                fn ($page) => new PageDto($page, ($queryParameters ? $queryParameters . '&' : '') . 'borlabs-page=' . $page, $page === $currentPage),
                range(
                    max(1, $currentPage - 3),
                    min($paginationResult->lastPage, $currentPage + 3),
                ),
            ),
        );

        $paginationResult->data = $this->find(
            $where,
            $orderBy,
            [
                $paginationResult->from - 1,
                $itemsPerPage,
            ],
            $withRelations,
        );

        // Modify `from` and `to` value when `total` is 0
        if ($paginationResult->total === 0) {
            $paginationResult->from = 0;
            $paginationResult->to = 0;
        }

        return $paginationResult;
    }

    final public function truncateTable(): void
    {
        $this->wpdb->query('TRUNCATE TABLE `' . $this->getTablePrefix() . (static::class)::TABLE . '`');
    }

    /**
     * This method updates the data of a model in the database.
     *
     * @param TModel $model the model that is an abstraction of the AbstractModel
     *
     * @throws \Borlabs\Cookie\Exception\UnexpectedRepositoryOperationException
     *
     * @return bool returns true on successful update
     */
    final public function update(AbstractModel $model): bool
    {
        if (!is_a($model, (static::class)::MODEL)) {
            throw new InvalidArgumentException('[ABSTRACT_REPOSITORY:UPDATE][' . static::class . '] The passed in model ' . get_class($model) . ' is not a type of this repository');
        }

        $queryBuilder = $this->getModelQueryBuilder();
        $queryBuilder->setModel($model);
        $query = $queryBuilder->getWpUpdateQuery();
        $status = $query->execute();

        if ($status === false) {
            throw new UnexpectedRepositoryOperationException('[ABSTRACT_REPOSITORY:UPDATE][' . static::class . '] ' . $this->wpdb->last_error);
        }

        return true;
    }

    /**
     * Overwrite this method in inherited repositories, if another prefix should be used by default.
     */
    protected function getDefaultTablePrefix(): string
    {
        return $this->wpdb->prefix;
    }

    /**
     * Use this method to access the current table prefix.
     */
    final protected function getTablePrefix(): string
    {
        if ($this->overwrittenTablePrefix !== null) {
            return $this->overwrittenTablePrefix;
        }

        return $this->getDefaultTablePrefix();
    }

    /**
     * @param TModel $model
     *
     * @throws \Borlabs\Cookie\Exception\UnexpectedRepositoryOperationException
     */
    private function _delete(
        AbstractModel $model,
        bool $obeyUndeletable
    ): int {
        if (!is_a($model, (static::class)::MODEL)) {
            throw new InvalidArgumentException('The passed in model is not a type of this repository');
        }

        $queryBuilder = $this->getQueryBuilder();
        $queryBuilder->andWhere(new BinaryOperatorExpression(new ModelFieldNameExpression('id'), '=', new LiteralExpression($model->id)));

        if ($obeyUndeletable === true) {
            if ((static::class)::UNDELETABLE) {
                $queryBuilder->andWhere(new BinaryOperatorExpression(new ModelFieldNameExpression('undeletable'), '=', new LiteralExpression(0)));
            }
        }

        $query = $queryBuilder->getWpDeleteQuery();
        $status = $query->execute();

        if ($status === false) {
            throw new UnexpectedRepositoryOperationException('[ABSTRACT_REPOSITORY:DELETE][' . static::class . '] ' . $this->wpdb->last_error);
        }

        unset($model);

        return $status;
    }

    /**
     * @param array<AbstractExpression>|array<string, string> $where
     */
    private function addGenericWhereClauses(
        RepositoryQueryBuilder $queryBuilder,
        array $where
    ): void {
        foreach ($where as $fieldName => $value) {
            if ($value instanceof AbstractExpression) {
                if (is_string($fieldName)) {
                    throw new InvalidArgumentException('If an AbstractExpression is given, it must include the affected column');
                }
                $queryBuilder->andWhere($value);
            } elseif ($value !== null) {
                $queryBuilder->andWhere(new BinaryOperatorExpression(new ModelFieldNameExpression($fieldName), '=', new LiteralExpression($value)));
            } else {
                $queryBuilder->andWhere(new BinaryOperatorExpression(new ModelFieldNameExpression($fieldName), 'IS', new NullExpression()));
            }
        }
    }

    private function findWithFirstRelations(
        array $where = [],
        array $orderBy = [],
        array $limit = []
    ): array {
        $with = [];

        foreach ($this::propertyMap()->map as $property) {
            if (is_a($property, PropertyMapRelationItemDto::class)) {
                $with[] = $property->reference;
            }
        }

        return $this->find($where, $orderBy, $limit, $with);
    }

    abstract public static function propertyMap(): PropertyMapDto;
}
