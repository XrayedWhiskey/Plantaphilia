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

namespace Borlabs\Cookie\Enum\Telemetry;

use Borlabs\Cookie\Enum\AbstractEnum;

/**
 * @method static LinkTypeEnum IMPRINT()
 * @method static LinkTypeEnum PRIVACY()
 */
class LinkTypeEnum extends AbstractEnum
{
    public const IMPRINT = 'imprint';

    public const PRIVACY = 'privacy';
}
