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

namespace Borlabs\Cookie\System\WordPressAdminDriver;

use Borlabs\Cookie\Controller\Admin\CloudScan\CloudScanController;
use Borlabs\Cookie\Controller\Admin\ContentBlocker\ContentBlockerAppearanceController;
use Borlabs\Cookie\Controller\Admin\ContentBlocker\ContentBlockerController;
use Borlabs\Cookie\Controller\Admin\Dashboard\DashboardController;
use Borlabs\Cookie\Controller\Admin\Dialog\DialogAppearanceController;
use Borlabs\Cookie\Controller\Admin\Dialog\DialogLocalizationController;
use Borlabs\Cookie\Controller\Admin\Dialog\DialogSettingsController;
use Borlabs\Cookie\Controller\Admin\Library\LibraryController;
use Borlabs\Cookie\Controller\Admin\License\LicenseController;
use Borlabs\Cookie\Controller\Admin\Provider\ProviderController;
use Borlabs\Cookie\Controller\Admin\ScriptBlocker\ScriptBlockerController;
use Borlabs\Cookie\Controller\Admin\Service\ServiceController;
use Borlabs\Cookie\Controller\Admin\ServiceGroup\ServiceGroupController;
use Borlabs\Cookie\Controller\Admin\Settings\SettingsController;
use Borlabs\Cookie\Controller\Admin\SetupAssistant\SetupAssistantController;
use Borlabs\Cookie\Enum\System\WordPressAdminSidebarMenuModeEnum;
use Borlabs\Cookie\System\Config\PluginConfig;
use Borlabs\Cookie\System\License\License;
use Borlabs\Cookie\System\ResourceEnqueuer\ResourceEnqueuer;

final class WordPressSidebarMenuModeManager
{
    private License $license;

    private PluginConfig $pluginConfig;

    private ResourceEnqueuer $resourceEnqueuer;

    public function __construct(
        License $license,
        PluginConfig $pluginConfig,
        ResourceEnqueuer $resourceEnqueuer
    ) {
        $this->license = $license;
        $this->pluginConfig = $pluginConfig;
        $this->resourceEnqueuer = $resourceEnqueuer;
    }

    public function register()
    {
        $this->resourceEnqueuer->enqueueInlineStyle('admin-sidebar', $this->buildInlineCss());
    }

    private function buildInlineCss(): string
    {
        $css = '#toplevel_page_borlabs-cookie ul li {display: none;}';

        if ($this->pluginConfig->get()->wordPressAdminSidebarMenuMode->is(WordPressAdminSidebarMenuModeEnum::EXPANDED()) && $this->license->isPluginUnlocked()) {
            return '';
        }

        if ($this->pluginConfig->get()->wordPressAdminSidebarMenuMode->is(WordPressAdminSidebarMenuModeEnum::SIMPLIFIED())) {
            $controllerList = $this->getControllerIdsForSimplifiedMode();
        }

        if ($this->pluginConfig->get()->wordPressAdminSidebarMenuMode->is(WordPressAdminSidebarMenuModeEnum::STANDARD())) {
            $controllerList = array_merge($this->getControllerIdsForStandardMode(), $this->getControllerIdsForSimplifiedMode());
        }

        if (!$this->license->isPluginUnlocked()) {
            $controllerList = $this->getControllerIdsForPluginLockedMode();
        }

        $selectors = [];

        foreach ($controllerList as $controllerId) {
            $selectors[] = $this->getSelector($controllerId);
        }

        $css .= implode(', ', $selectors) . ' {display: block;}';

        return $css;
    }

    private function getControllerIdsForPluginLockedMode(): array
    {
        return [
            DashboardController::CONTROLLER_ID,
            LicenseController::CONTROLLER_ID,
            SetupAssistantController::CONTROLLER_ID,
        ];
    }

    private function getControllerIdsForSimplifiedMode(): array
    {
        return [
            CloudScanController::CONTROLLER_ID,
            DashboardController::CONTROLLER_ID,
            DialogAppearanceController::CONTROLLER_ID,
            DialogSettingsController::CONTROLLER_ID,
            LibraryController::CONTROLLER_ID,
            SettingsController::CONTROLLER_ID,
        ];
    }

    private function getControllerIdsForStandardMode(): array
    {
        return [
            ContentBlockerController::CONTROLLER_ID,
            ContentBlockerAppearanceController::CONTROLLER_ID,
            DialogLocalizationController::CONTROLLER_ID,
            ProviderController::CONTROLLER_ID,
            ScriptBlockerController::CONTROLLER_ID,
            ServiceController::CONTROLLER_ID,
            ServiceGroupController::CONTROLLER_ID,
        ];
    }

    private function getSelector(string $controllerId): string
    {
        //return sprintf('#toplevel_page_borlabs-cookie li:has(a[href="admin.php?page=%s"]):not(li:has(a[href="admin.php?page=%s"]) ~ *)', $controllerId, $controllerId); // This selects only the first occurrence of the menu item.
        return sprintf('#toplevel_page_borlabs-cookie li:has(a[href="admin.php?page=%s"])', $controllerId);
    }
}
