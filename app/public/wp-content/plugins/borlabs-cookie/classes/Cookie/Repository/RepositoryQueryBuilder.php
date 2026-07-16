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
use Borlabs\Cookie\Dto\Repository\PropertyMapDto;
use Borlabs\Cookie\Dto\Repository\PropertyMapRelationItemDto;
use Borlabs\Cookie\Exception\PropertyDoesNotExistException;
use Borlabs\Cookie\Repository\Expression\AbstractExpression;
use Borlabs\Cookie\Repository\Expression\AssignmentExpression;
use Borlabs\Cookie\Repository\Expression\BinaryOperatorExpression;
use Borlabs\Cookie\Repository\Expression\DirectionExpression;
use Borlabs\Cookie\Repository\Expression\ListExpression;
use Borlabs\Cookie\Repository\Expression\ModelFieldNameExpression;
use Borlabs\Cookie\Repository\Expression\SelectStarExpression;

/**
 * This class can be used to construct a `RepositoryQuery` object by providing methods to add parts of the query
 * pea-a-peu. It utilizes `AbstractExpression` nodes such that on the one hand field names of the model
 * objects (properties) can be used (which are later transformed to DB column names); and on the other hand complex
 * queries involving operators and MySQL/MariaDB functions can be used.
 */
class RepositoryQueryBuilder
{
    protected AbstractRepository $modelRepository;

    protected PropertyMapDto $propertyMapDto;

    protected WpDb $wpdb;

    private int $_firstResult = 0;

    private ?int $_maxResults = null;

    /**
     * @var AssignmentExpression[]
     */
    private array $assignments = [];

    /**
     * @var AbstractExpression[]
     */
    private array $columns = [];

    /**
     * @var DirectionExpression[]
     */
    private array $orderBy = [];

    /**
     * Default prefix for the table name.
     */
    private string $prefix;

    /**
     * @var AssignmentExpression[]
     */
    private array $updateAssignmentForInsertOrUpdate = [];

    /**
     * @var AbstractExpression[]
     */
    private array $where = [];

    public function __construct(
        WpDb $wpdb,
        AbstractRepository $modelRepository,
        string $prefix
    ) {
        $this->wpdb = $wpdb;
        $this->modelRepository = $modelRepository;
        $this->prefix = $prefix;

        $this->propertyMapDto = $this->modelRepository::propertyMap();
    }

    public function addAssignment(
        AssignmentExpression $assignmentExpression
    ): void {
        $this->assignments[] = $assignmentExpression;
    }

    public function addOrderBy(
        DirectionExpression $expr
    ): void {
        $this->orderBy[] = $expr;
    }

    public function addSelectColumn(
        AbstractExpression $expr
    ): void {
        $this->columns[] = $expr;
    }

    public function addUpdateAssignmentForInsertOrUpdate(
        AssignmentExpression $assignmentExpression
    ): void {
        $this->updateAssignmentForInsertOrUpdate[] = $assignmentExpression;
    }

    public function andWhere(AbstractExpression $expr): void
    {
        $this->where[] = $expr;
    }

    public function getWpDeleteQuery(): RepositoryQuery
    {
        $this->augmentAllExpressionsWithDbColumnNames();

        $queryString = $this->getWpDeleteQueryStringAndParameters();

        return new RepositoryQuery(
            $this->getWpQueryExecutor(),
            $queryString->wpSqlQuery,
            $queryString->parameters,
        );
    }

    public function getWpInsertOrUpdateQuery(): RepositoryQuery
    {
        $this->augmentAllExpressionsWithDbColumnNames();

        $queryString = $this->getWpInsertOrUpdateQueryStringAndParameters();

        return new RepositoryQuery(
            $this->getWpQueryExecutor(),
            $queryString->wpSqlQuery,
            $queryString->parameters,
        );
    }

    public function getWpInsertQuery(): RepositoryQuery
    {
        $this->augmentAllExpressionsWithDbColumnNames();

        $queryString = $this->getWpInsertQueryStringAndParameters();

        return new RepositoryQuery(
            $this->getWpQueryExecutor(),
            $queryString->wpSqlQuery,
            $queryString->parameters,
        );
    }

    public function getWpSelectQuery(): RepositoryQuery
    {
        if (count($this->columns) === 0) {
            $this->addSelectColumn(new SelectStarExpression());
        }

        $this->augmentAllExpressionsWithDbColumnNames();

        $queryString = $this->getWpSelectQueryStringAndParameters();

        return new RepositoryQuery(
            $this->getWpQueryExecutor(),
            $queryString->wpSqlQuery,
            $queryString->parameters,
        );
    }

    public function getWpUpdateQuery(): RepositoryQuery
    {
        $this->augmentAllExpressionsWithDbColumnNames();

        $queryString = $this->getWpUpdateQueryStringAndParameters();

        return new RepositoryQuery(
            $this->getWpQueryExecutor(),
            $queryString->wpSqlQuery,
            $queryString->parameters,
        );
    }

    public function limit($firstResult = 0, $maxResults = null): void
    {
        $this->_firstResult = $firstResult;
        $this->_maxResults = $maxResults;
    }

    protected function getWpDeleteQueryStringAndParameters(): RepositoryQueryPart
    {
        $queryPart = new RepositoryQueryPart('DELETE');

        $queryPart->append($this->constructFromClause());
        $queryPart->append($this->constructWhereClause());
        $queryPart->append($this->constructOrderByClause());
        $queryPart->append($this->constructLimitClause());

        return $queryPart;
    }

    protected function getWpInsertOrUpdateQueryStringAndParameters(): RepositoryQueryPart
    {
        $queryPart = new RepositoryQueryPart();

        $queryPart->append($this->constructInsertIntoClause());
        $queryPart->append($this->constructValuesClause());
        $queryPart->append($this->constructOnDuplicateKeyUpdateClause());

        return $queryPart;
    }

    protected function getWpInsertQueryStringAndParameters(): RepositoryQueryPart
    {
        $queryPart = new RepositoryQueryPart();

        $queryPart->append($this->constructInsertIntoClause());
        $queryPart->append($this->constructValuesClause());

        return $queryPart;
    }

    protected function getWpQueryExecutor(): RepositoryQueryExecutor
    {
        return new RepositoryQueryExecutor(
            $this->wpdb,
            new ArrayHydrator(),
        );
    }

    protected function getWpSelectQueryStringAndParameters(): RepositoryQueryPart
    {
        $queryPart = new RepositoryQueryPart();

        $queryPart->append($this->constructSelectClause());
        $queryPart->append($this->constructFromClause());
        $queryPart->append($this->constructWhereClause());
        $queryPart->append($this->constructOrderByClause());
        $queryPart->append($this->constructLimitClause());

        return $queryPart;
    }

    protected function getWpUpdateQueryStringAndParameters(): RepositoryQueryPart
    {
        $queryPart = new RepositoryQueryPart();

        $queryPart->append($this->constructUpdateClause());
        $queryPart->append($this->constructSetClause());
        $queryPart->append($this->constructWhereClause());

        return $queryPart;
    }

    /**
     * The `ModelFieldNameExpression` nodes must be augmented with db column names, otherwise
     * we cannot construct a query.
     */
    private function augmentAllExpressionsWithDbColumnNames(): void
    {
        foreach ($this->columns as $columnExpression) {
            $this->augmentExpressionWithDbColumnNames($columnExpression);
        }

        foreach ($this->assignments as $assignmentExpression) {
            $this->augmentExpressionWithDbColumnNames($assignmentExpression);
        }

        foreach ($this->updateAssignmentForInsertOrUpdate as $assignmentExpression) {
            $this->augmentExpressionWithDbColumnNames($assignmentExpression);
        }

        foreach ($this->where as $whereExpression) {
            $this->augmentExpressionWithDbColumnNames($whereExpression);
        }

        foreach ($this->orderBy as $orderByExpression) {
            $this->augmentExpressionWithDbColumnNames($orderByExpression);
        }
    }

    /**
     * Augment the given `ModelFieldNameExpression` nodes of the node tree given via `$expression`
     * with db column names, otherwise we cannot construct a query.
     */
    private function augmentExpressionWithDbColumnNames(AbstractExpression $expression): void
    {
        $queue = $expression->getExpressionChildren();
        while (count($queue) > 0) {
            /** @var AbstractExpression $current */
            $current = array_shift($queue);
            $queue = array_merge($queue, $current->getExpressionChildren());

            if (!$current instanceof ModelFieldNameExpression) {
                continue;
            }

            $fieldName = $current->getModelFieldName();

            $fieldInfo = current(
                array_filter(
                    $this->propertyMapDto->map,
                    static function ($propertyMapItemDto) use ($fieldName) {
                        return $propertyMapItemDto->reference === $fieldName;
                    },
                ),
            );

            if ($fieldInfo === false) {
                throw new PropertyDoesNotExistException('[REPOSITORY_QUERY_BUILDER:AUGMENT_EXPRESSION_WITH_DB_COLUMN_NAMES] Property does not exist in model: ' . $fieldName, E_USER_ERROR);
            }

            if ($fieldInfo instanceof PropertyMapRelationItemDto) {
                continue;
            }
            $current->setDbColumnName($fieldInfo->target);
        }
    }

    private function constructFromClause(): RepositoryQueryPart
    {
        $queryPart = new RepositoryQueryPart();

        $queryPart->wpSqlQuery .= ' FROM ' . '`' . str_replace('`', '``', $this->prefix . $this->modelRepository->getTable()) . '`';

        return $queryPart;
    }

    private function constructInsertIntoClause(): RepositoryQueryPart
    {
        $queryPart = new RepositoryQueryPart('INSERT INTO ');

        $queryPart->wpSqlQuery .= '`' . str_replace('`', '``', $this->prefix . $this->modelRepository->getTable()) . '`';

        return $queryPart;
    }

    private function constructLimitClause(): RepositoryQueryPart
    {
        $queryPart = new RepositoryQueryPart();

        if ($this->_maxResults !== null) {
            $queryPart->wpSqlQuery .= ' LIMIT ' . $this->_firstResult . ', ' . $this->_maxResults;
        }

        return $queryPart;
    }

    private function constructOnDuplicateKeyUpdateClause(): RepositoryQueryPart
    {
        $queryPart = new RepositoryQueryPart(' ON DUPLICATE KEY UPDATE ');

        $queryParts = new ListExpression();

        if (count($this->updateAssignmentForInsertOrUpdate) > 0) {
            foreach ($this->updateAssignmentForInsertOrUpdate as $assignmentExpression) {
                $queryParts->addExpressionChildren($assignmentExpression);
            }
        } else {
            foreach ($this->assignments as $assignmentExpression) {
                $queryParts->addExpressionChildren($assignmentExpression);
            }
        }
        $queryPart->append($queryParts->toWpSqlQueryPart());

        return $queryPart;
    }

    private function constructOrderByClause(): RepositoryQueryPart
    {
        $queryPart = new RepositoryQueryPart();

        if (count($this->orderBy) > 0) {
            $queryPart->wpSqlQuery .= ' ORDER BY ';
            $queryParts = new ListExpression();

            foreach ($this->orderBy as $orderByExpression) {
                $queryParts->addExpressionChildren($orderByExpression);
            }
            $queryPart->append($queryParts->toWpSqlQueryPart());
        }

        return $queryPart;
    }

    private function constructSelectClause(): RepositoryQueryPart
    {
        $queryPart = new RepositoryQueryPart();

        $queryPart->wpSqlQuery .= 'SELECT ';
        $queryParts = new ListExpression();

        foreach ($this->columns as $columnExpression) {
            $queryParts->addExpressionChildren($columnExpression);
        }
        $queryPart->append($queryParts->toWpSqlQueryPart());

        return $queryPart;
    }

    private function constructSetClause(): RepositoryQueryPart
    {
        $queryPart = new RepositoryQueryPart(' SET ');

        $queryParts = new ListExpression();

        foreach ($this->assignments as $assignmentExpression) {
            $queryParts->addExpressionChildren($assignmentExpression);
        }
        $queryPart->append($queryParts->toWpSqlQueryPart());

        return $queryPart;
    }

    private function constructUpdateClause(): RepositoryQueryPart
    {
        $queryPart = new RepositoryQueryPart('UPDATE ');

        $queryPart->wpSqlQuery .= '`' . str_replace('`', '``', $this->prefix . $this->modelRepository->getTable()) . '`';

        return $queryPart;
    }

    private function constructValuesClause(): RepositoryQueryPart
    {
        $queryPart = new RepositoryQueryPart(' (');

        $columns = new ListExpression();
        $values = new ListExpression();

        foreach ($this->assignments as $assignmentExpression) {
            $columns->addExpressionChildren($assignmentExpression->getFieldNameExpression());
            $values->addExpressionChildren($assignmentExpression->getValueExpression());
        }

        $queryPart->append($columns->toWpSqlQueryPart());
        $queryPart->wpSqlQuery .= ') VALUES (';
        $queryPart->append($values->toWpSqlQueryPart());
        $queryPart->wpSqlQuery .= ')';

        return $queryPart;
    }

    private function constructWhereClause(): RepositoryQueryPart
    {
        $queryPart = new RepositoryQueryPart();

        if (count($this->where) > 0) {
            $queryPart->wpSqlQuery .= ' WHERE ';

            $combinedWhereExpression = null;

            foreach ($this->where as $whereExpression) {
                if ($combinedWhereExpression === null) {
                    $combinedWhereExpression = $whereExpression;
                } else {
                    $combinedWhereExpression = new BinaryOperatorExpression(
                        $combinedWhereExpression,
                        'AND',
                        $whereExpression,
                    );
                }
            }

            $queryPart->append($combinedWhereExpression->toWpSqlQueryPart());
        }

        return $queryPart;
    }
}
