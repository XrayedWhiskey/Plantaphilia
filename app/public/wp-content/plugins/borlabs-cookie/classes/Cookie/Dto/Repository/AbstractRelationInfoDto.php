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

namespace Borlabs\Cookie\Dto\Repository;

use Borlabs\Cookie\Repository\RepositoryInterface;
use InvalidArgumentException;

/**
 * The **AbstractRelationInfoDto** class is used as a typed object that is passed within the system.
 *
 * The object contains information to help **RepositoryManager** to load the related data of a model.
 *
 * @see \Borlabs\Cookie\Repository\RepositoryManager RepositoryManager
 */
abstract class AbstractRelationInfoDto
{
    /**
     * @var class-string FQN of the target repository
     */
    public string $repository;

    /**
     * @param class-string $repository FQN of the target repository
     */
    public function __construct(
        string $repository
    ) {
        if (!is_a($repository, RepositoryInterface::class, true)) {
            throw new InvalidArgumentException('[RELATION_INFO_DTO:__CONSTRUCT] ' . $repository . ' is not a RepositoryInterface');
        }

        $this->repository = $repository;
    }
}
