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
 * This is the most-basic hydrator, doing nothing and just returning an array of rows and each row
 * is an associative array.
 */
class ArrayHydrator implements ResultHydratorInterface
{
    public function hydrate(array $result): array
    {
        return $result;
    }
}
