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

/**
 * This class is used to collect parts to later build a complete `RepositoryQuery` object.
 *
 * @internal
 */
class RepositoryQueryPart
{
    public array $parameters;

    public string $wpSqlQuery;

    public function __construct(
        string $wpSqlQuery = '',
        array $parameters = []
    ) {
        $this->wpSqlQuery = $wpSqlQuery;
        $this->parameters = $parameters;
    }

    public function append(self $toAppendPart): void
    {
        $this->wpSqlQuery .= $toAppendPart->wpSqlQuery;
        $this->parameters = array_merge($this->parameters, $toAppendPart->parameters);
    }
}
