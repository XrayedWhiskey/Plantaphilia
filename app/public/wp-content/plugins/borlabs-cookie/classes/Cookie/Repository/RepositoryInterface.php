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

use Borlabs\Cookie\Dto\Repository\PropertyMapDto;
use Borlabs\Cookie\Model\AbstractModel;

/**
 * @template TModel of AbstractModel
 */
interface RepositoryInterface
{
    public static function propertyMap(): PropertyMapDto;

    /**
     * @param array<string, string> $where
     * @param array<string,string>  $orderBy
     * @param array<int, int>       $limit
     * @param array<int, int>       $withRelations
     *
     * @return array<TModel>
     */
    public function find(
        array $where = [],
        array $orderBy = [],
        array $limit = [],
        array $withRelations = []
    ): array;

    /**
     * @internal this should not be used outside a Repository, but must be public because `RepositoryQuery` is using it
     */
    public function getQueryBuilder(): RepositoryQueryBuilder;

    /**
     * This method initializes the Repository.
     * During the installation routine this method is called several times with different prefixes.
     *
     * @param ?string $prefix
     */
    public function overwriteTablePrefix(?string $prefix = null): void;
}
