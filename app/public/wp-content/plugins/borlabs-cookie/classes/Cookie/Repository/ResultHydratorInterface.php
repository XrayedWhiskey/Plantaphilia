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
 * This is an interface to transform the result set of a run WordPress query (using the `ARRAY_A` mode, see
 * https://developer.wordpress.org/reference/classes/wpdb/get_results/) into the desired format.
 */
interface ResultHydratorInterface
{
    public function hydrate(array $result);
}
