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

class SelectAliasExpression extends AbstractExpression
{
    private ?string $alias;

    private AbstractExpression $selection;

    public function __construct(
        AbstractExpression $selection,
        ?string $alias = null
    ) {
        $this->selection = $selection;
        $this->alias = $alias;
    }

    public function toWpSqlQueryPart(): RepositoryQueryPart
    {
        $return = $this->selection->toWpSqlQueryPart();

        if ($this->alias !== null) {
            $return->wpSqlQuery .= ' AS ' . '`' . str_replace('`', '``', $this->alias) . '`';
        }

        return $return;
    }
}
