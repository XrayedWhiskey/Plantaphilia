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

abstract class AbstractExpression
{
    /**
     * @return AbstractExpression[] This method should return all `AbstractExpression` child nodes. This is currently
     *                              used to find all `ModelFieldNameExpression` nodes and to augment them with db column names.
     */
    public function getExpressionChildren(): array
    {
        return [];
    }

    abstract public function toWpSqlQueryPart(): RepositoryQueryPart;
}
