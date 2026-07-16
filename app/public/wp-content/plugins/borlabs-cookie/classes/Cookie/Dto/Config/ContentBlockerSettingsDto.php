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
 * The **GeneralDto** class is used as a typed object that is passed within the system.
 *
 * The object contains technical configuration properties related to the Borlabs Cookie plugin and its cookie.
 *
 * @see \Borlabs\Cookie\System\Config\GeneralConfig
 */
final class ContentBlockerSettingsDto extends AbstractConfigDto
{
    /**
     * @var array list of hosts whose iframes are not automatically blocked by the Content Blocker
     */
    public array $excludedHostnames = [];

    /**
     * @var bool default: `true`; `true`: Iframe tags are removed within feeds
     */
    public bool $removeIframesInFeeds = true;
}
