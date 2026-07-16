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

class FunctionExpression extends AbstractExpression
{
    /**
     * @var AbstractExpression[]
     */
    private array $parameters;

    private string $sqlFunctionName;

    public function __construct(
        string $sqlFunctionName
    ) {
        $this->sqlFunctionName = $sqlFunctionName;

        $args = array_slice(func_get_args(), 1, null, true);

        foreach ($args as $key => $arg) {
            if (!$arg instanceof AbstractExpression) {
                throw new InvalidArgumentException('Argument ' . $key . ' is not of type ' . AbstractExpression::class);
            }
        }
        $this->parameters = $args;
    }

    public function getExpressionChildren(): array
    {
        return $this->parameters;
    }

    public function toWpSqlQueryPart(): RepositoryQueryPart
    {
        $wpSqlString = $this->sqlFunctionName . '(';
        $parameters = [];

        $wpSqlStringParameters = [];

        foreach ($this->parameters as $parameter) {
            $wpQuery = $parameter->toWpSqlQueryPart();
            $wpSqlStringParameters[] .= $wpQuery->wpSqlQuery;
            $parameters = array_merge($parameters, $wpQuery->parameters);
        }

        $wpSqlString .= join(', ', $wpSqlStringParameters);
        $wpSqlString .= ')';

        return new RepositoryQueryPart(
            $wpSqlString,
            $parameters,
        );
    }
}
