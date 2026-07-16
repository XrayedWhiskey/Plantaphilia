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

namespace Borlabs\Cookie\Container;

use Psr\Container\ContainerExceptionInterface;

/**
 * Describes the PSR-11 interface of a container.
 */
interface ContainerInterface
{
    /**
     * This method finds an entry by its identifier and returns it.
     *
     * @param string $id identifier of the entry to search
     *
     * @throws ContainerExceptionInterface general error
     *
     * @return mixed entry
     */
    public function get(string $id);

    /**
     * Returns true if the container has an entry for the given id, false otherwise.
     *
     * @param string $id identifier of the entry to search
     */
    public function has(string $id): bool;
}
