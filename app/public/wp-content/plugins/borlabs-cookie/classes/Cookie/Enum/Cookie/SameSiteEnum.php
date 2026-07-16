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

namespace Borlabs\Cookie\Enum\Cookie;

use Borlabs\Cookie\Enum\AbstractEnum;

/**
 * @method static SameSiteEnum LAX()
 * @method static SameSiteEnum NONE()
 */
class SameSiteEnum extends AbstractEnum
{
    public const LAX = 'Lax';

    public const NONE = 'None';
}
