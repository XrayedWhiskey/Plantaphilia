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

namespace Borlabs\Cookie\Dto\Telemetry;

use Borlabs\Cookie\Dto\AbstractDto;
use Borlabs\Cookie\Enum\System\DisplayModeSettingsEnum;
use Borlabs\Cookie\Enum\System\WordPressAdminSidebarMenuModeEnum;

class SettingsDto extends AbstractDto
{
    public bool $animation;

    public bool $animationDelay;

    public array $crossCookieDomains;

    public string $defaultPluginUrl;

    public DisplayModeSettingsEnum $displayModeSettings;

    public bool $geoIpActive;

    public bool $hasCustomCodeUsage;

    public bool $iabTcfStatus;

    public bool $isLanguageSwitcherConfigured;

    public bool $isLogoIdentical;

    public string $layout;

    public string $packageAutoUpdateTime;

    public string $packageAutoUpdateTimeSpan;

    public string $pluginUrl;

    public string $position;

    public bool $showAcceptAllButton;

    public bool $showAcceptOnlyEssentialButton;

    public bool $showDialogAfterUserInteraction;

    public bool $showSaveButton;

    public bool $showWidget;

    public WordPressAdminSidebarMenuModeEnum $wordPressAdminSidebarMenuMode;
}
