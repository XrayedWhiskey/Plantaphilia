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

use Borlabs\Cookie\Adapter\WpFunction;
use Borlabs\Cookie\Controller\Admin\CloudScan\CloudScanController;
use Borlabs\Cookie\Controller\Admin\CompatibilityPatches\CompatibilityPatchesController;
use Borlabs\Cookie\Controller\Admin\ConsentLog\ConsentLogController;
use Borlabs\Cookie\Controller\Admin\ContentBlocker\ContentBlockerAppearanceController;
use Borlabs\Cookie\Controller\Admin\ContentBlocker\ContentBlockerController;
use Borlabs\Cookie\Controller\Admin\ContentBlocker\ContentBlockerSettingsController;
use Borlabs\Cookie\Controller\Admin\Dashboard\DashboardController;
use Borlabs\Cookie\Controller\Admin\Debug\DebugController;
use Borlabs\Cookie\Controller\Admin\Dialog\DialogAppearanceController;
use Borlabs\Cookie\Controller\Admin\Dialog\DialogLocalizationController;
use Borlabs\Cookie\Controller\Admin\Dialog\DialogSettingsController;
use Borlabs\Cookie\Controller\Admin\IabTcf\IabTcfSettingsController;
use Borlabs\Cookie\Controller\Admin\IabTcf\IabTcfVendorController;
use Borlabs\Cookie\Controller\Admin\ImportExport\ImportExportController;
use Borlabs\Cookie\Controller\Admin\Job\JobController;
use Borlabs\Cookie\Controller\Admin\LegacyImporter\LegacyImporterController;
use Borlabs\Cookie\Controller\Admin\Library\LibraryController;
use Borlabs\Cookie\Controller\Admin\Library\LibrarySettingsController;
use Borlabs\Cookie\Controller\Admin\License\LicenseController;
use Borlabs\Cookie\Controller\Admin\LocalizationChecker\LocalizationCheckerController;
use Borlabs\Cookie\Controller\Admin\Log\LogController;
use Borlabs\Cookie\Controller\Admin\Plugin\PluginSettingsController;
use Borlabs\Cookie\Controller\Admin\Provider\ProviderController;
use Borlabs\Cookie\Controller\Admin\ScriptBlocker\ScriptBlockerController;
use Borlabs\Cookie\Controller\Admin\Service\ServiceController;
use Borlabs\Cookie\Controller\Admin\ServiceGroup\ServiceGroupController;
use Borlabs\Cookie\Controller\Admin\Settings\SettingsController;
use Borlabs\Cookie\Controller\Admin\SetupAssistant\SetupAssistantController;
use Borlabs\Cookie\Controller\Admin\StyleBlocker\StyleBlockerController;
use Borlabs\Cookie\Controller\Admin\Widget\WidgetController;
use Borlabs\Cookie\Enum\System\WordPressAdminSidebarMenuModeEnum;
use Borlabs\Cookie\Localization\System\WordPressPageManagerLocalizationStrings;
use Borlabs\Cookie\System\Config\PluginConfig;
use Borlabs\Cookie\System\Package\PackageManager;

final class WordPressPageManager
{
    private const CAPABILITY = 'manage_borlabs_cookie';

    private string $mainMenuSlug;

    private PackageManager $packageManager;

    private PluginConfig $pluginConfig;

    private WordPressControllerBridge $wordPressControllerBridge;

    private WordPressPageManagerLocalizationStrings $wordPressPageManagerLocalizationStrings;

    private WpFunction $wpFunction;

    public function __construct(
        PackageManager $packageManager,
        PluginConfig $pluginConfig,
        WordPressControllerBridge $wordPressControllerBridge,
        WordPressPageManagerLocalizationStrings $wordPressPageManagerLocalizationStrings,
        WpFunction $wpFunction
    ) {
        $this->packageManager = $packageManager;
        $this->pluginConfig = $pluginConfig;
        $this->wordPressControllerBridge = $wordPressControllerBridge;
        $this->wordPressPageManagerLocalizationStrings = $wordPressPageManagerLocalizationStrings;
        $this->wpFunction = $wpFunction;
        $this->mainMenuSlug = DashboardController::CONTROLLER_ID;
    }

    public function register(): void
    {
        $this->registerBorlabsCookieMenu();
        $this->registerSubMenuDashboard();
        $this->registerSubMenuLibrary();
        $this->registerSubMenuScanner();
        $this->registerSubMenuSettings();
        $this->registerSubMenuDialog();
        $this->registerSubMenuWidget();
        $this->registerSubMenuConsentManagement();
        $this->registerSubMenuContentBlocker();
        $this->registerSubMenuScriptBlocker();
        $this->registerSubMenuStyleBlocker();
        $this->registerSubMenuSystem();

        if (defined('BORLABS_COOKIE_DEV_MODE_ENABLE_DEBUG_INTERFACE') && constant('BORLABS_COOKIE_DEV_MODE_ENABLE_DEBUG_INTERFACE')) {
            $this->registerSubMenuDebug();
        }

        if (defined('BORLABS_COOKIE_DEV_MODE_ENABLE_LOCALIZATION_CHECKER_INTERFACE') && constant('BORLABS_COOKIE_DEV_MODE_ENABLE_LOCALIZATION_CHECKER_INTERFACE')) {
            $this->registerSubMenuLocalizationChecker();
        }
    }

    private function isBorlabsCookiePage(): bool
    {
        return isset($_GET['page']) && strpos($_GET['page'], 'borlabs-cookie') !== false;
    }

    private function packageUpdateCount(): string
    {
        $count = $this->packageManager->getPackageUpdateCount();

        if ($count === 0) {
            return '';
        }

        return '<span
            class="menu-counter"
            style="display: inline-block; vertical-align: top; box-sizing: border-box; padding: 0 5px; min-width: 18px; height: 18px; border-radius: 9px; font-size: 11px; line-height: 1.6; text-align: center; z-index: 26; background: #9333ea; color: #fff; letter-spacing: 0; margin: 0 0 0 2px">'
            . $count
            . '</span>';
    }

    private function registerBorlabsCookieMenu(): void
    {
        $menuTitle = $this->wordPressPageManagerLocalizationStrings->get()['menuTitle']['borlabsCookie'];
        $packageUpdateCount = $this->packageUpdateCount();

        if (!$this->isBorlabsCookiePage() && $packageUpdateCount !== '') {
            $menuTitle = '<span style="letter-spacing: -1px;">' . $menuTitle . '</span> ' . $packageUpdateCount;
        }

        $this->wpFunction->addMenuPage(
            $this->wordPressPageManagerLocalizationStrings->get()['siteTitle']['borlabsCookie'],
            $menuTitle,
            self::CAPABILITY,
            DashboardController::CONTROLLER_ID,
            [$this->wordPressControllerBridge, DashboardController::class],
            $this->svgIcon(),
        );
    }

    private function registerSubMenuConsentManagement(): void
    {
        $this->wpFunction->addSubMenuPage(
            $this->mainMenuSlug,
            $this->wordPressPageManagerLocalizationStrings->get()['siteTitle']['consentManagement'],
            $this->wordPressPageManagerLocalizationStrings->get()['menuTitle']['consentManagement'],
            self::CAPABILITY,
            ServiceController::CONTROLLER_ID,
            [$this->wordPressControllerBridge, ServiceController::class],
        );
        $this->wpFunction->addSubMenuPage(
            $this->mainMenuSlug,
            $this->wordPressPageManagerLocalizationStrings->get()['siteTitle']['services'],
            $this->wordPressPageManagerLocalizationStrings->get()['menuTitle']['services'],
            self::CAPABILITY,
            ServiceController::CONTROLLER_ID,
            [$this->wordPressControllerBridge, ServiceController::class],
        );
        $this->wpFunction->addSubMenuPage(
            $this->mainMenuSlug,
            $this->wordPressPageManagerLocalizationStrings->get()['siteTitle']['serviceGroups'],
            $this->wordPressPageManagerLocalizationStrings->get()['menuTitle']['serviceGroups'],
            self::CAPABILITY,
            ServiceGroupController::CONTROLLER_ID,
            [$this->wordPressControllerBridge, ServiceGroupController::class],
        );
        $this->wpFunction->addSubMenuPage(
            $this->mainMenuSlug,
            $this->wordPressPageManagerLocalizationStrings->get()['siteTitle']['iabTcf'],
            $this->wordPressPageManagerLocalizationStrings->get()['menuTitle']['iabTcf'],
            self::CAPABILITY,
            IabTcfVendorController::CONTROLLER_ID,
            [$this->wordPressControllerBridge, IabTcfVendorController::class],
        );
        $this->wpFunction->addSubMenuPage(
            $this->mainMenuSlug,
            $this->wordPressPageManagerLocalizationStrings->get()['siteTitle']['iabTcfManageVendors'],
            $this->wordPressPageManagerLocalizationStrings->get()['menuTitle']['iabTcfManageVendors'],
            self::CAPABILITY,
            IabTcfVendorController::CONTROLLER_ID,
            [$this->wordPressControllerBridge, IabTcfVendorController::class],
        );
        $this->wpFunction->addSubMenuPage(
            $this->mainMenuSlug,
            $this->wordPressPageManagerLocalizationStrings->get()['siteTitle']['iabTcfSettings'],
            $this->wordPressPageManagerLocalizationStrings->get()['menuTitle']['iabTcfSettings'],
            self::CAPABILITY,
            IabTcfSettingsController::CONTROLLER_ID,
            [$this->wordPressControllerBridge, IabTcfSettingsController::class],
        );
        $this->wpFunction->addSubMenuPage(
            $this->mainMenuSlug,
            $this->wordPressPageManagerLocalizationStrings->get()['siteTitle']['providers'],
            $this->wordPressPageManagerLocalizationStrings->get()['menuTitle']['providers'],
            self::CAPABILITY,
            ProviderController::CONTROLLER_ID,
            [$this->wordPressControllerBridge, ProviderController::class],
        );
        $this->wpFunction->addSubMenuPage(
            $this->mainMenuSlug,
            $this->wordPressPageManagerLocalizationStrings->get()['siteTitle']['consentLogs'],
            $this->wordPressPageManagerLocalizationStrings->get()['menuTitle']['consentLogs'],
            self::CAPABILITY,
            ConsentLogController::CONTROLLER_ID,
            [$this->wordPressControllerBridge, ConsentLogController::class],
        );
    }

    private function registerSubMenuContentBlocker(): void
    {
        $this->wpFunction->addSubMenuPage(
            $this->mainMenuSlug,
            $this->wordPressPageManagerLocalizationStrings->get()['siteTitle']['contentBlockers'],
            $this->wordPressPageManagerLocalizationStrings->get()['menuTitle']['contentBlockers'],
            self::CAPABILITY,
            ContentBlockerController::CONTROLLER_ID,
            [$this->wordPressControllerBridge, ContentBlockerController::class],
        );

        if ($this->pluginConfig->get()->wordPressAdminSidebarMenuMode == WordPressAdminSidebarMenuModeEnum::EXPANDED()) {
            $this->wpFunction->addSubMenuPage(
                $this->mainMenuSlug,
                $this->wordPressPageManagerLocalizationStrings->get()['siteTitle']['contentBlockersManage'],
                $this->wordPressPageManagerLocalizationStrings->get()['menuTitle']['contentBlockersManage'],
                self::CAPABILITY,
                ContentBlockerController::CONTROLLER_ID,
                [$this->wordPressControllerBridge, ContentBlockerController::class],
            );
        }
        $this->wpFunction->addSubMenuPage(
            $this->mainMenuSlug,
            $this->wordPressPageManagerLocalizationStrings->get()['siteTitle']['contentBlockersSettings'],
            $this->wordPressPageManagerLocalizationStrings->get()['menuTitle']['contentBlockersSettings'],
            self::CAPABILITY,
            ContentBlockerSettingsController::CONTROLLER_ID,
            [$this->wordPressControllerBridge, ContentBlockerSettingsController::class],
        );
        $this->wpFunction->addSubMenuPage(
            $this->mainMenuSlug,
            $this->wordPressPageManagerLocalizationStrings->get()['siteTitle']['contentBlockersAppearance'],
            $this->wordPressPageManagerLocalizationStrings->get()['menuTitle']['contentBlockersAppearance'],
            self::CAPABILITY,
            ContentBlockerAppearanceController::CONTROLLER_ID,
            [$this->wordPressControllerBridge, ContentBlockerAppearanceController::class],
        );
    }

    private function registerSubMenuDashboard(): void
    {
        $this->wpFunction->addSubMenuPage(
            $this->mainMenuSlug,
            $this->wordPressPageManagerLocalizationStrings->get()['siteTitle']['dashboard'],
            $this->wordPressPageManagerLocalizationStrings->get()['menuTitle']['dashboard'],
            self::CAPABILITY,
            DashboardController::CONTROLLER_ID,
            [$this->wordPressControllerBridge, DashboardController::class],
        );
    }

    private function registerSubMenuDebug(): void
    {
        $this->wpFunction->addSubMenuPage(
            $this->mainMenuSlug,
            'Debug',
            'Debug',
            self::CAPABILITY,
            DebugController::CONTROLLER_ID,
            [$this->wordPressControllerBridge, DebugController::class],
        );
    }

    private function registerSubMenuDialog(): void
    {
        $this->wpFunction->addSubMenuPage(
            $this->mainMenuSlug,
            $this->wordPressPageManagerLocalizationStrings->get()['siteTitle']['dialog'],
            $this->wordPressPageManagerLocalizationStrings->get()['menuTitle']['dialog'],
            self::CAPABILITY,
            DialogSettingsController::CONTROLLER_ID,
            [$this->wordPressControllerBridge, DialogSettingsController::class],
        );

        if ($this->pluginConfig->get()->wordPressAdminSidebarMenuMode != WordPressAdminSidebarMenuModeEnum::SIMPLIFIED()) {
            $this->wpFunction->addSubMenuPage(
                $this->mainMenuSlug,
                $this->wordPressPageManagerLocalizationStrings->get()['siteTitle']['dialogSettings'],
                $this->wordPressPageManagerLocalizationStrings->get()['menuTitle']['dialogSettings'],
                self::CAPABILITY,
                DialogSettingsController::CONTROLLER_ID,
                [$this->wordPressControllerBridge, DialogSettingsController::class],
            );
        }

        $this->wpFunction->addSubMenuPage(
            $this->mainMenuSlug,
            $this->wordPressPageManagerLocalizationStrings->get()['siteTitle']['dialogAppearance'],
            $this->wordPressPageManagerLocalizationStrings->get()['menuTitle']['dialogAppearance'],
            self::CAPABILITY,
            DialogAppearanceController::CONTROLLER_ID,
            [$this->wordPressControllerBridge, DialogAppearanceController::class],
        );
        $this->wpFunction->addSubMenuPage(
            $this->mainMenuSlug,
            $this->wordPressPageManagerLocalizationStrings->get()['siteTitle']['dialogLocalization'],
            $this->wordPressPageManagerLocalizationStrings->get()['menuTitle']['dialogLocalization'],
            self::CAPABILITY,
            DialogLocalizationController::CONTROLLER_ID,
            [$this->wordPressControllerBridge, DialogLocalizationController::class],
        );
    }

    private function registerSubMenuLibrary(): void
    {
        $this->wpFunction->addSubMenuPage(
            $this->mainMenuSlug,
            $this->wordPressPageManagerLocalizationStrings->get()['siteTitle']['library'],
            $this->wordPressPageManagerLocalizationStrings->get()['menuTitle']['library'],
            self::CAPABILITY,
            LibraryController::CONTROLLER_ID,
            [$this->wordPressControllerBridge, LibraryController::class],
        );

        if ($this->pluginConfig->get()->wordPressAdminSidebarMenuMode == WordPressAdminSidebarMenuModeEnum::EXPANDED()) {
            $this->wpFunction->addSubMenuPage(
                $this->mainMenuSlug,
                $this->wordPressPageManagerLocalizationStrings->get()['siteTitle']['libraryManage'],
                $this->wordPressPageManagerLocalizationStrings->get()['menuTitle']['libraryManage'],
                self::CAPABILITY,
                LibraryController::CONTROLLER_ID,
                [$this->wordPressControllerBridge, LibraryController::class],
            );
        }

        $this->wpFunction->addSubMenuPage(
            $this->mainMenuSlug,
            $this->wordPressPageManagerLocalizationStrings->get()['siteTitle']['librarySettings'],
            $this->wordPressPageManagerLocalizationStrings->get()['menuTitle']['librarySettings'],
            self::CAPABILITY,
            LibrarySettingsController::CONTROLLER_ID,
            [$this->wordPressControllerBridge, LibrarySettingsController::class],
        );
    }

    private function registerSubMenuLocalizationChecker(): void
    {
        $this->wpFunction->addSubMenuPage(
            $this->mainMenuSlug,
            'Localization Checker',
            'Localization Checker',
            self::CAPABILITY,
            LocalizationCheckerController::CONTROLLER_ID,
            [$this->wordPressControllerBridge, LocalizationCheckerController::class],
        );
    }

    private function registerSubMenuScanner(): void
    {
        $this->wpFunction->addSubMenuPage(
            $this->mainMenuSlug,
            $this->wordPressPageManagerLocalizationStrings->get()['siteTitle']['scanner'],
            $this->wordPressPageManagerLocalizationStrings->get()['menuTitle']['scanner'],
            self::CAPABILITY,
            CloudScanController::CONTROLLER_ID,
            [$this->wordPressControllerBridge, CloudScanController::class],
        );
    }

    private function registerSubMenuScriptBlocker(): void
    {
        $this->wpFunction->addSubMenuPage(
            $this->mainMenuSlug,
            $this->wordPressPageManagerLocalizationStrings->get()['siteTitle']['scriptBlockers'],
            $this->wordPressPageManagerLocalizationStrings->get()['menuTitle']['scriptBlockers'],
            self::CAPABILITY,
            ScriptBlockerController::CONTROLLER_ID,
            [$this->wordPressControllerBridge, ScriptBlockerController::class],
        );
    }

    private function registerSubMenuSettings(): void
    {
        $this->wpFunction->addSubMenuPage(
            $this->mainMenuSlug,
            $this->wordPressPageManagerLocalizationStrings->get()['siteTitle']['settings'],
            $this->wordPressPageManagerLocalizationStrings->get()['menuTitle']['settings'],
            self::CAPABILITY,
            SettingsController::CONTROLLER_ID,
            [$this->wordPressControllerBridge, SettingsController::class],
        );

        if ($this->pluginConfig->get()->wordPressAdminSidebarMenuMode == WordPressAdminSidebarMenuModeEnum::EXPANDED()) {
            $this->wpFunction->addSubMenuPage(
                $this->mainMenuSlug,
                $this->wordPressPageManagerLocalizationStrings->get()['siteTitle']['generalSettings'],
                $this->wordPressPageManagerLocalizationStrings->get()['menuTitle']['generalSettings'],
                self::CAPABILITY,
                SettingsController::CONTROLLER_ID,
                [$this->wordPressControllerBridge, SettingsController::class],
            );
        }

        $this->wpFunction->addSubMenuPage(
            $this->mainMenuSlug,
            $this->wordPressPageManagerLocalizationStrings->get()['siteTitle']['adminAndPluginSettings'],
            $this->wordPressPageManagerLocalizationStrings->get()['menuTitle']['adminAndPluginSettings'],
            self::CAPABILITY,
            PluginSettingsController::CONTROLLER_ID,
            [$this->wordPressControllerBridge, PluginSettingsController::class],
        );
    }

    private function registerSubMenuStyleBlocker(): void
    {
        $this->wpFunction->addSubMenuPage(
            $this->mainMenuSlug,
            $this->wordPressPageManagerLocalizationStrings->get()['siteTitle']['styleBlockers'],
            $this->wordPressPageManagerLocalizationStrings->get()['menuTitle']['styleBlockers'],
            self::CAPABILITY,
            StyleBlockerController::CONTROLLER_ID,
            [$this->wordPressControllerBridge, StyleBlockerController::class],
        );
    }

    private function registerSubMenuSystem(): void
    {
        if ($this->pluginConfig->get()->wordPressAdminSidebarMenuMode == WordPressAdminSidebarMenuModeEnum::EXPANDED()) {
            $this->wpFunction->addSubMenuPage(
                $this->mainMenuSlug,
                $this->wordPressPageManagerLocalizationStrings->get()['siteTitle']['system'],
                $this->wordPressPageManagerLocalizationStrings->get()['menuTitle']['system'],
                self::CAPABILITY,
                DashboardController::CONTROLLER_ID,
                [$this->wordPressControllerBridge, DashboardController::class],
            );
        }

        $this->wpFunction->addSubMenuPage(
            $this->mainMenuSlug,
            $this->wordPressPageManagerLocalizationStrings->get()['siteTitle']['compatibilityPatches'],
            $this->wordPressPageManagerLocalizationStrings->get()['menuTitle']['compatibilityPatches'],
            self::CAPABILITY,
            CompatibilityPatchesController::CONTROLLER_ID,
            [$this->wordPressControllerBridge, CompatibilityPatchesController::class],
        );
        $this->wpFunction->addSubMenuPage(
            $this->mainMenuSlug,
            $this->wordPressPageManagerLocalizationStrings->get()['siteTitle']['importExport'],
            $this->wordPressPageManagerLocalizationStrings->get()['menuTitle']['importExport'],
            self::CAPABILITY,
            ImportExportController::CONTROLLER_ID,
            [$this->wordPressControllerBridge, ImportExportController::class],
        );
        $this->wpFunction->addSubMenuPage(
            $this->mainMenuSlug,
            $this->wordPressPageManagerLocalizationStrings->get()['siteTitle']['legacyImporter'],
            $this->wordPressPageManagerLocalizationStrings->get()['menuTitle']['legacyImporter'],
            self::CAPABILITY,
            LegacyImporterController::CONTROLLER_ID,
            [$this->wordPressControllerBridge, LegacyImporterController::class],
        );
        $this->wpFunction->addSubMenuPage(
            $this->mainMenuSlug,
            $this->wordPressPageManagerLocalizationStrings->get()['siteTitle']['jobs'],
            $this->wordPressPageManagerLocalizationStrings->get()['menuTitle']['jobs'],
            self::CAPABILITY,
            JobController::CONTROLLER_ID,
            [$this->wordPressControllerBridge, JobController::class],
        );
        $this->wpFunction->addSubMenuPage(
            $this->mainMenuSlug,
            $this->wordPressPageManagerLocalizationStrings->get()['siteTitle']['license'],
            $this->wordPressPageManagerLocalizationStrings->get()['menuTitle']['license'],
            self::CAPABILITY,
            LicenseController::CONTROLLER_ID,
            [$this->wordPressControllerBridge, LicenseController::class],
        );
        $this->wpFunction->addSubMenuPage(
            $this->mainMenuSlug,
            $this->wordPressPageManagerLocalizationStrings->get()['siteTitle']['logs'],
            $this->wordPressPageManagerLocalizationStrings->get()['menuTitle']['logs'],
            self::CAPABILITY,
            LogController::CONTROLLER_ID,
            [$this->wordPressControllerBridge, LogController::class],
        );
        $this->wpFunction->addSubMenuPage(
            $this->mainMenuSlug,
            $this->wordPressPageManagerLocalizationStrings->get()['siteTitle']['setupAssistant'],
            $this->wordPressPageManagerLocalizationStrings->get()['menuTitle']['setupAssistant'],
            self::CAPABILITY,
            SetupAssistantController::CONTROLLER_ID,
            [$this->wordPressControllerBridge, SetupAssistantController::class],
        );
    }

    private function registerSubMenuWidget(): void
    {
        $this->wpFunction->addSubMenuPage(
            $this->mainMenuSlug,
            $this->wordPressPageManagerLocalizationStrings->get()['siteTitle']['widget'],
            $this->wordPressPageManagerLocalizationStrings->get()['menuTitle']['widget'],
            self::CAPABILITY,
            WidgetController::CONTROLLER_ID,
            [$this->wordPressControllerBridge, WidgetController::class],
        );
    }

    private function svgIcon(): string
    {
        return 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiPz4KPCFET0NUWVBFIHN2ZyBQVUJMSUMgIi0vL1czQy8vRFREIFNWRyAxLjEvL0VOIiAiaHR0cDovL3d3dy53My5vcmcvR3JhcGhpY3MvU1ZHLzEuMS9EVEQvc3ZnMTEuZHRkIj4KPHN2ZyB2ZXJzaW9uPSIxLjEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHg9IjAiIHk9IjAiIHdpZHRoPSIyMCIgaGVpZ2h0PSIyMCIgdmlld0JveD0iMCwgMCwgMjAsIDIwIj4KICA8ZyBpZD0iTGF5ZXJfMSI+CiAgICA8Zz4KICAgICAgPHBhdGggZD0iTTcuNzIzLDEuMTIxIEM1LjM4OSwwLjQzOSAyLjgyOSwyLjE0OCAyLjAxOSw0LjkzNiBDMi4wMTksNC45ODIgMS45OTYsNS4wMjkgMS45ODIsNS4wNzUgQzIuMzE4LDQuODUyIDIuNjg4LDQuNjg3IDMuMDc4LDQuNTg3IEMzLjc0Niw0LjQxOCA0LjQ0NCw0LjQwNSA1LjExNiw0LjU1IEM1LjQ5Miw0LjYyNSA1Ljg1OSw0Ljc0IDYuMjEsNC44OTIgQzYuNjg5LDUuMSA3LjEzOSw1LjM2NyA3LjU1LDUuNjg4IEM3LjkyMiw1LjQ3NyA4LjMxOSw1LjMxNCA4LjczMyw1LjIwNSBDOC44MDcsNC42ODMgOC45MzEsNC4xNjkgOS4xMDIsMy42NyBMOS4xMTcsMy42MzQgQzkuNDEsMy41OTQgOS43MDYsMy41NzQgMTAuMDAzLDMuNTc0IEwxMC4zMjQsMy41NzQgQzkuODgsMi40IDguOTIxLDEuNDk2IDcuNzIzLDEuMTIxIHoiIGZpbGw9IiNGRkZGRkYiLz4KICAgICAgPHBhdGggZD0iTTIuMTAyLDUuMzM2IEMtMC4wMzEsNi40OTMgLTAuNjI4LDkuNTE3IDAuNzY3LDEyLjA2NiBDMC43OTEsMTIuMTA4IDAuODE0LDEyLjE0OSAwLjgzOSwxMi4xOTEgQzAuOTE5LDExLjc5NyAxLjA2NCwxMS40MTggMS4yNjcsMTEuMDcxIEMxLjYyMSwxMC40NzkgMi4xMDQsOS45NzcgMi42ODIsOS42MDIgQzMuMDAyLDkuMzkxIDMuMzQyLDkuMjEzIDMuNjk5LDkuMDcyIEM0LjE4Myw4Ljg3OSA0LjY5MSw4Ljc1IDUuMjA5LDguNjg2IEM1LjMyMSw4LjI3NCA1LjQ4Nyw3Ljg3OSA1LjcwMiw3LjUxMSBDNS4zODgsNy4wODggNS4xMTMsNi42MzcgNC44OCw2LjE2NSBMNC44NjQsNi4xMjcgQzUuMDQzLDUuODkxIDUuMjM3LDUuNjY4IDUuNDQ3LDUuNDU5IEM1LjUyMiw1LjM4NSA1LjYsNS4zMTEgNS42NzksNS4yMjggQzQuNTMzLDQuNzE0IDMuMjE1LDQuNzU0IDIuMTAyLDUuMzM2IHoiIGZpbGw9IiNGRkZGRkYiLz4KICAgICAgPHBhdGggZD0iTTEuMTA4LDEyLjI5IEMwLjQyNSwxNC42MjUgMi4xMzUsMTcuMTg1IDQuOTIyLDE3Ljk5NSBMNS4wNjEsMTguMDMyIEM0LjgzOSwxNy42OTYgNC42NzQsMTcuMzI2IDQuNTczLDE2LjkzNSBDNC40MDQsMTYuMjY4IDQuMzkxLDE1LjU3MSA0LjUzNiwxNC44OTcgQzQuNjExLDE0LjUyMiA0LjcyNiwxNC4xNTUgNC44NzgsMTMuODAzIEM1LjA4NSwxMy4zMjUgNS4zNTMsMTIuODc1IDUuNjc0LDEyLjQ2MyBDNS40NjMsMTIuMDkyIDUuMzAxLDExLjY5NSA1LjE5LDExLjI4MiBDNC42NjgsMTEuMjA3IDQuMTU2LDExLjA4MyAzLjY1NywxMC45MTIgTDMuNjE5LDEwLjg5OCBDMy41NzksMTAuNjA0IDMuNTU5LDEwLjMwOCAzLjU1OSwxMC4wMTIgQzMuNTU5LDkuOTA3IDMuNTU5LDkuNzk5IDMuNTU5LDkuNjkgQzIuMzg3LDEwLjEzNSAxLjQ4MywxMS4wOTMgMS4xMDgsMTIuMjkgeiIgZmlsbD0iI0ZGRkZGRiIvPgogICAgICA8cGF0aCBkPSJNNS4zMjIsMTcuOTEyIEM2LjQ3OSwyMC4wNDUgOS41MDMsMjAuNjQxIDEyLjA1MiwxOS4yNDcgTDEyLjE3NywxOS4xNzUgQzExLjc4MiwxOS4wOTUgMTEuNDA0LDE4Ljk1IDExLjA1NywxOC43NDcgQzEwLjQ2NiwxOC4zOTQgOS45NjMsMTcuOTA5IDkuNTg4LDE3LjMzMSBDOS4zNzcsMTcuMDExIDkuMiwxNi42NzEgOS4wNTksMTYuMzE2IEM4Ljg2NiwxNS44MyA4LjczNiwxNS4zMjMgOC42NzIsMTQuODA1IEM4LjI2LDE0LjY5MyA3Ljg2NSwxNC41MjcgNy40OTcsMTQuMzEyIEM3LjA3NCwxNC42MjYgNi42MjMsMTQuOTAxIDYuMTUxLDE1LjEzMyBMNi4xMTQsMTUuMTUgQzUuODc3LDE0Ljk3MSA1LjY1NCwxNC43NzYgNS40NDUsMTQuNTY3IEM1LjM3MSwxNC40OTMgNS4yOTcsMTQuNDE0IDUuMjE0LDE0LjMzNSBDNC42OTksMTUuNDgxIDQuNzM5LDE2Ljc5OSA1LjMyMiwxNy45MTIgeiIgZmlsbD0iI0ZGRkZGRiIvPgogICAgICA8cGF0aCBkPSJNMTIuMjc3LDE4LjkwNyBDMTQuNjExLDE5LjU4OSAxNy4xNzEsMTcuODc5IDE3Ljk4MSwxNS4xMDMgQzE3Ljk5MywxNS4wNTcgMTguMDA0LDE1LjAxMSAxOC4wMTgsMTQuOTY0IEMxNy42ODIsMTUuMTg3IDE3LjMxMiwxNS4zNTIgMTYuOTIyLDE1LjQ1MyBDMTYuMjU0LDE1LjYyMiAxNS41NTYsMTUuNjM0IDE0Ljg4NCwxNS40ODkgQzE0LjUwOCwxNS40MTUgMTQuMTQxLDE1LjI5OSAxMy43OSwxNS4xNDcgQzEzLjMxMSwxNC45NCAxMi44NjEsMTQuNjcyIDEyLjQ1LDE0LjM1MSBDMTIuMDc4LDE0LjU2MyAxMS42ODEsMTQuNzI1IDExLjI2NywxNC44MzUgQzExLjE5MywxNS4zNTcgMTEuMDY5LDE1Ljg3IDEwLjg5OCwxNi4zNjkgTDEwLjg4MywxNi40MDYgQzEwLjU5LDE2LjQ0NiAxMC4yOTQsMTYuNDY2IDkuOTk3LDE2LjQ2NiBMOS42NzYsMTYuNDY2IEMxMC4xMjQsMTcuNjM1IDExLjA4MiwxOC41MzQgMTIuMjc3LDE4LjkwNyB6IiBmaWxsPSIjRkZGRkZGIi8+CiAgICAgIDxwYXRoIGQ9Ik0xNy44OTgsMTQuNjkyIEMyMC4wMzEsMTMuNTM1IDIwLjYyOCwxMC41MTEgMTkuMjMzLDcuOTYyIEMxOS4yMDksNy45MiAxOS4xODYsNy44NzkgMTkuMTYxLDcuODM3IEMxOS4wODEsOC4yMzEgMTguOTM2LDguNjA5IDE4LjczMyw4Ljk1NyBDMTguMzc5LDkuNTQ4IDE3Ljg5NiwxMC4wNTEgMTcuMzE4LDEwLjQyNiBDMTYuOTk4LDEwLjYzNyAxNi42NTgsMTAuODE0IDE2LjMwMSwxMC45NTYgQzE1LjgxNywxMS4xNDggMTUuMzA5LDExLjI3OCAxNC43OTEsMTEuMzQyIEMxNC42NzksMTEuNzUzIDE0LjUxMywxMi4xNDggMTQuMjk4LDEyLjUxNyBDMTQuNjEyLDEyLjk0IDE0Ljg4NywxMy4zOTEgMTUuMTIsMTMuODYzIEwxNS4xMzYsMTMuOSBDMTQuOTU3LDE0LjEzNyAxNC43NjMsMTQuMzYgMTQuNTUzLDE0LjU2OSBDMTQuNDc4LDE0LjY0MyAxNC40LDE0LjcxNyAxNC4zMjEsMTQuOCBDMTUuNDY3LDE1LjMxNCAxNi43ODUsMTUuMjc0IDE3Ljg5OCwxNC42OTIgeiIgZmlsbD0iI0ZGRkZGRiIvPgogICAgICA8cGF0aCBkPSJNMTguODUzLDcuNzM4IEMxOS41MzUsNS40MDMgMTcuODI2LDIuODQyIDE1LjA1LDIuMDMzIEwxNC45MTEsMS45OTYgQzE1LjEzNCwyLjMzMSAxNS4yOTksMi43MDIgMTUuMzk5LDMuMDkyIEMxNS41NjgsMy43NiAxNS41ODEsNC40NTcgMTUuNDM3LDUuMTMgQzE1LjM2MSw1LjUwNiAxNS4yNDcsNS44NzMgMTUuMDk0LDYuMjI1IEMxNC44ODcsNi43MDMgMTQuNjIsNy4xNTMgMTQuMjk4LDcuNTY0IEMxNC41MDksNy45MzYgMTQuNjcyLDguMzMzIDE0Ljc4Miw4Ljc0NiBDMTUuMzAzLDguODIxIDE1LjgxNyw4Ljk0NSAxNi4zMTYsOS4xMTcgTDE2LjM1Miw5LjEzIEMxNi4zOTMsOS40MjQgMTYuNDEzLDkuNzIgMTYuNDEzLDEwLjAxNiBDMTYuNDEzLDEwLjEyIDE2LjQxMywxMC4yMjkgMTYuNDEzLDEwLjMzOCBDMTcuNTgxLDkuODkgMTguNDgxLDguOTMyIDE4Ljg1Myw3LjczOCB6IiBmaWxsPSIjRkZGRkZGIi8+CiAgICAgIDxwYXRoIGQ9Ik0xNC42MDEsMi4wODggQzEzLjQ0NCwtMC4wNDUgMTAuNDIsLTAuNjQxIDcuODcxLDAuNzUzIEw3Ljc0NiwwLjgyNSBDOC4xNCwwLjkwNSA4LjUxOSwxLjA1IDguODY2LDEuMjUzIEM5LjQ1NywxLjYwNiA5Ljk2LDIuMDkxIDEwLjMzNCwyLjY2OSBDMTAuNTQ2LDIuOTg5IDEwLjcyNCwzLjMyOSAxMC44NjUsMy42ODQgQzExLjA1Nyw0LjE3IDExLjE4Nyw0LjY3NyAxMS4yNTEsNS4xOTUgQzExLjY2Miw1LjMwNyAxMi4wNTcsNS40NzMgMTIuNDI2LDUuNjg4IEMxMi44NDksNS4zNzQgMTMuMyw1LjA5OSAxMy43NzIsNC44NjcgTDEzLjgxLDQuODUgQzE0LjA0NSw1LjAyOSAxNC4yNjksNS4yMjQgMTQuNDc4LDUuNDMzIEMxNC41NTIsNS41MDcgMTQuNjI2LDUuNTg2IDE0LjcwOSw1LjY2NSBDMTUuMjIzLDQuNTE5IDE1LjE4MywzLjIwMSAxNC42MDEsMi4wODggeiIgZmlsbD0iI0ZGRkZGRiIvPgogICAgPC9nPgogIDwvZz4KPC9zdmc+Cg==';
    }
}
