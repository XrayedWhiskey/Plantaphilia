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

namespace Borlabs\Cookie\Dto\Config;

/**
 * The **BackwardsCompatibilityDto** class is used as a typed object that is passed within the system.
 *
 * @see \Borlabs\Cookie\System\Config\BackwardsCompatibilityConfig
 */
final class BackwardsCompatibilityDto extends AbstractConfigDto
{
    /**
     * @var bool default: `false`; `true`: The JavaScript for Borlabs Cookie Legacy API support is loaded to ensure backwards compatibility
     */
    public bool $loadBackwardsCompatibilityJavaScript = false;
}
