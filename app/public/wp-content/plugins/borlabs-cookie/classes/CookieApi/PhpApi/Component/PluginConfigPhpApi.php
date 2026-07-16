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

namespace Borlabs\CookieApi\PhpApi\Component;

use Borlabs\Cookie\Support\Transformer;
use Borlabs\Cookie\System\Config\PluginConfig;
use stdClass;

final class PluginConfigPhpApi
{
    private PluginConfig $pluginConfig;

    public function __construct(PluginConfig $pluginConfig)
    {
        $this->pluginConfig = $pluginConfig;
    }

    /**
     * Example:
     * <code>
     * {
     *  "automaticUpdate": "auto-update-all", // "auto-update-all"|"auto-update-minor"|"auto-update-none"|"auto-update-patch"
     *  "enableDebugConsole": true,
     *  "enableDebugLogging": false,
     *  "enableEmailNotificationsForUpdatablePackagesWithAutoUpdateDisabled": false,
     *  "enableEmailNotificationsForUpdatablePackagesWithAutoUpdateEnabled": true,
     *  "packageAutoUpdateEmailAddresses": ["mail@example.internal"],
     *  "packageAutoUpdateInterval": "after-24-hours", // "after-24-hours"|"monday"|"tuesday"|"wednesday"|"thursday"|"friday"|"saturday"|"sunday"
     *  "packageAutoUpdateTime": "08:30",
     *  "packageAutoUpdateTimeSpan": "08:00-09:59",
     * }
     * </code>.
     *
     * @return stdClass{
     *     automaticUpdate: string,
     *     enableDebugConsole: bool,
     *     enableEmailNotificationsForUpdatablePackagesWithAutoUpdateDisabled: bool,
     *     enableEmailNotificationsForUpdatablePackagesWithAutoUpdateEnabled: bool,
     *     packageAutoUpdateEmailAddresses: array,
     *     packageAutoUpdateInterval: string,
     *     packageAutoUpdateTime: string,
     *     packageAutoUpdateTimeSpan: string
     * }
     */
    public function get(): stdClass
    {
        return Transformer::objectToStdClass($this->pluginConfig->get());
    }
}
