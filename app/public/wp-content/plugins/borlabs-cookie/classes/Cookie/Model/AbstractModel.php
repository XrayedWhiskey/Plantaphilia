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

namespace Borlabs\Cookie\Model;

/**
 * Class AbstractModel.
 *
 * Each model MUST extend the **AbstractModel** to work with the **AbstractRepository**.
 *
 * @see \Borlabs\Cookie\Repository\AbstractRepository
 */
abstract class AbstractModel
{
    /**
     * @var int primary key. If is -1, the model is not yet saved
     */
    public int $id = -1;
}
