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

class BinaryOperatorExpression extends AbstractExpression
{
    protected AbstractExpression $leftOperand;

    protected string $operator;

    protected AbstractExpression $rightOperand;

    private array $allowedOperators = [
        '=',
        '!=',
        '>',
        '<',
        '>=',
        '<=',
        '+',
        '-',
        '*',
        '/',
        'AND',
        'OR',
        'IS',
        'IS NOT',
        'IN',
        'LIKE',
    ];

    private array $expectsListAsRhs = [
        'IN',
    ];

    public function __construct(
        AbstractExpression $leftOperand,
        string $operator,
        AbstractExpression $rightOperand
    ) {
        if (!in_array($operator, $this->allowedOperators, true)) {
            throw new InvalidArgumentException('Invalid operator passed in: ' . $operator);
        }

        if (in_array($operator, $this->expectsListAsRhs, true)) {
            if (!$rightOperand instanceof ListExpression) {
                throw new InvalidArgumentException('Operator expects ' . ListExpression::class . ' as RHS');
            }
        }

        if ($operator === 'LIKE') {
            if (!$rightOperand instanceof AbstractLikeLiteralExpression) {
                throw new InvalidArgumentException('LIKE operator expects ' . AbstractLikeLiteralExpression::class . ' as RHS');
            }
        }

        $this->leftOperand = $leftOperand;
        $this->operator = $operator;
        $this->rightOperand = $rightOperand;
    }

    public function getExpressionChildren(): array
    {
        return [$this->leftOperand, $this->rightOperand];
    }

    public function toWpSqlQueryPart(): RepositoryQueryPart
    {
        $leftOperandWpSqlQuery = $this->leftOperand->toWpSqlQueryPart();
        $rightOperandWpSqlQuery = $this->rightOperand->toWpSqlQueryPart();

        if ($this->operator === 'IN') {
            $rightOperandWpSqlQuery->wpSqlQuery = '(' . $rightOperandWpSqlQuery->wpSqlQuery . ')';
        }

        return new RepositoryQueryPart(
            '(' . $leftOperandWpSqlQuery->wpSqlQuery . ' ' . $this->operator . ' ' . $rightOperandWpSqlQuery->wpSqlQuery . ')',
            array_merge($leftOperandWpSqlQuery->parameters, $rightOperandWpSqlQuery->parameters),
        );
    }
}
