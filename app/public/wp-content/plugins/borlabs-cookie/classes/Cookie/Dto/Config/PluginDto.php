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

use Borlabs\Cookie\Enum\Library\AutoUpdateIntervalEnum;
use Borlabs\Cookie\Enum\System\AutomaticUpdateEnum;
use Borlabs\Cookie\Enum\System\DisplayModeSettingsEnum;
use Borlabs\Cookie\Enum\System\WordPressAdminSidebarMenuModeEnum;

/**
 * The **PluginDto** class is used as a typed object that is passed within the system.
 *
 * The object specifies the criteria for updating the plugin.
 *
 * @see \Borlabs\Cookie\System\Config\PluginConfig
 */
final class PluginDto extends AbstractConfigDto
{
    public AutomaticUpdateEnum $automaticUpdate;

    /**
     * @var bool default: `true`; `true`: Borlabs Cookie automatically clears the cache from third-party plugins following specific actions within Borlabs Cookie
     *
     * @since 3.4.0 The setting used to belong to GeneralDto.
     */
    public bool $clearThirdPartyCache = true;

    public DisplayModeSettingsEnum $displayModeSettings;

    public bool $enableDebugConsole = true;

    public bool $enableDebugLogging = false;

    /**
     * @deprecated 3.4.0 The setting is now part of LibraryDto.
     */
    public bool $enableEmailNotificationsForUpdatablePackagesWithAutoUpdateDisabled = false;

    /**
     * @deprecated 3.4.0 The setting is now part of LibraryDto.
     */
    public bool $enableEmailNotificationsForUpdatablePackagesWithAutoUpdateEnabled = true;

    /**
     * @var array list of post types where Borlabs Cookie meta box is available
     *
     * @since 3.4.0 The setting used to belong to GeneralDto.
     */
    public array $metaBox = [];

    /**
     * @deprecated 3.4.0 The setting is now part of LibraryDto.
     */
    public array $packageAutoUpdateEmailAddresses = [];

    /**
     * @deprecated 3.4.0 The setting is now part of LibraryDto.
     */
    public AutoUpdateIntervalEnum $packageAutoUpdateInterval;

    /**
     * @deprecated 3.4.0 The setting is now part of LibraryDto.
     */
    public string $packageAutoUpdateTime = '08:30';

    /**
     * @deprecated 3.4.0 The setting is now part of LibraryDto.
     */
    public string $packageAutoUpdateTimeSpan = '08:00-09:59';

    public WordPressAdminSidebarMenuModeEnum $wordPressAdminSidebarMenuMode;
}
