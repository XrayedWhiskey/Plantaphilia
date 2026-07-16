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
use InvalidArgumentException;

class DirectionExpression extends AbstractExpression
{
    /**
     * @var DirectionAscExpression|DirectionDescExpression
     */
    protected object $direction;

    protected AbstractExpression $expression;

    public function __construct(
        AbstractExpression $expression,
        ?object $direction = null
    ) {
        $this->expression = $expression;

        if ($direction === null) {
            $direction = new DirectionDescExpression();
        }

        if (!$direction instanceof DirectionDescExpression && !$direction instanceof DirectionAscExpression) {
            throw new InvalidArgumentException('Direction must be either ' . DirectionDescExpression::class . ' or ' . DirectionAscExpression::class);
        }
        $this->direction = $direction;
    }

    public function getExpressionChildren(): array
    {
        return [$this->expression];
    }

    public function toWpSqlQueryPart(): RepositoryQueryPart
    {
        $expressionWpSql = $this->expression->toWpSqlQueryPart();

        return new RepositoryQueryPart(
            $expressionWpSql->wpSqlQuery . ' ' . $this->direction->toWpSqlQueryPart()->wpSqlQuery,
            $expressionWpSql->parameters,
        );
    }
}
