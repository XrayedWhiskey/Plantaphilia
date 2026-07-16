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

namespace Borlabs\Cookie\Enum\LocalScanner;

use Borlabs\Cookie\Enum\AbstractEnum;

/**
 * @method static TagTypeEnum LINK()
 * @method static TagTypeEnum SCRIPT()
 * @method static TagTypeEnum STYLE()
 * @method static TagTypeEnum UNKNOWN()
 */
class TagTypeEnum extends AbstractEnum
{
    public const LINK = 'link';

    public const SCRIPT = 'script';

    public const STYLE = 'style';

    public const UNKNOWN = 'unknown';
}
