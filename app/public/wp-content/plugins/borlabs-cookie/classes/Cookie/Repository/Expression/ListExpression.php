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

namespace Borlabs\Cookie\Repository\Expression;

use Borlabs\Cookie\Repository\RepositoryQueryPart;

class ListExpression extends AbstractExpression
{
    /**
     * @var AbstractExpression[]
     */
    protected array $values = [];

    /**
     * @param AbstractExpression[] $values
     */
    public function __construct(
        array $values = []
    ) {
        $this->values = $values;
    }

    public function addExpressionChildren(AbstractExpression $expr): void
    {
        $this->values[] = $expr;
    }

    public function getExpressionChildren(): array
    {
        return $this->values;
    }

    public function toWpSqlQueryPart(): RepositoryQueryPart
    {
        $returnStrings = [];
        $returnParameters = [];

        foreach ($this->values as $value) {
            $wpQuery = $value->toWpSqlQueryPart();
            $returnStrings[] = $wpQuery->wpSqlQuery;
            $returnParameters[] = $wpQuery->parameters;
        }

        return new RepositoryQueryPart(
            join(', ', $returnStrings),
            array_merge(...$returnParameters),
        );
    }
}
