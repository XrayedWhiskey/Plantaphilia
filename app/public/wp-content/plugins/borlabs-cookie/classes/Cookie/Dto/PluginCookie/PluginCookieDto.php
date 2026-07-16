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

namespace Borlabs\Cookie\Dto\PluginCookie;

use Borlabs\Cookie\Dto\AbstractDto;

class PluginCookieDto extends AbstractDto
{
    /**
     * Example:
     * <code>
     *     {
     *         "essential" => ["borlabs-cookie"],
     *     }
     * </code>.
     */
    public object $consents;

    public string $domainPath;

    public string $expires;

    public string $uid;

    public bool $v3;

    public int $version;
}
