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
use LogicException;

class ModelFieldNameExpression extends AbstractExpression
{
    private ?string $dbColumnName = null;

    private string $modelFieldName;

    public function __construct(
        string $modelFieldName
    ) {
        $this->modelFieldName = $modelFieldName;
    }

    public function getModelFieldName(): string
    {
        return $this->modelFieldName;
    }

    public function setDbColumnName(string $dbColumnName): void
    {
        $this->dbColumnName = $dbColumnName;
    }

    public function toWpSqlQueryPart(): RepositoryQueryPart
    {
        if ($this->dbColumnName === null) {
            throw new LogicException('This node requires setting a dbColumnName. Please do that.');
        }

        return new RepositoryQueryPart(
            '`' . str_replace('`', '``', $this->dbColumnName) . '`',
            [],
        );
    }
}
