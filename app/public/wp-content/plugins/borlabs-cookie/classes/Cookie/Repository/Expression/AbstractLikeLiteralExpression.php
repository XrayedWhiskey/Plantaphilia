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

use InvalidArgumentException;

abstract class AbstractLikeLiteralExpression extends AbstractExpression
{
    protected LiteralExpression $literalExpression;

    public function __construct(
        LiteralExpression $literalExpression
    ) {
        $this->literalExpression = $literalExpression;
    }

    protected function escapeLiteralExpression(LiteralExpression $literalExpression): string
    {
        $queryPart = $literalExpression->toWpSqlQueryPart();

        if ($queryPart->wpSqlQuery !== '%s' || count($queryPart->parameters) !== 1) {
            throw new InvalidArgumentException('Literal expression inside of AbstractLikeLiteralExpression must be a single string');
        }

        return str_replace(
            ['%', '_'],
            ['\%', '\_'],
            $queryPart->parameters[0],
        );
    }
}
