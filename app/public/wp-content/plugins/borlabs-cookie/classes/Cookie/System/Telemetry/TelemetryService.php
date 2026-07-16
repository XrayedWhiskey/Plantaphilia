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

namespace Borlabs\Cookie\System\Telemetry;

use Borlabs\Cookie\Adapter\WpDb;
use Borlabs\Cookie\Adapter\WpFunction;
use Borlabs\Cookie\ApiClient\TelemetryApiClient;
use Borlabs\Cookie\Dto\Telemetry\ContentBlockerDto;
use Borlabs\Cookie\Dto\Telemetry\LegalLinkDto;
use Borlabs\Cookie\Dto\Telemetry\LegalLinksPerLanguageListItemDto;
use Borlabs\Cookie\Dto\Telemetry\PackageDto;
use Borlabs\Cookie\Dto\Telemetry\PluginDto;
use Borlabs\Cookie\Dto\Telemetry\ScriptBlockerDto;
use Borlabs\Cookie\Dto\Telemetry\ServiceDto;
use Borlabs\Cookie\Dto\Telemetry\SettingsDto;
use Borlabs\Cookie\Dto\Telemetry\SetupAssistantUsageDto;
use Borlabs\Cookie\Dto\Telemetry\StyleBlockerDto;
use Borlabs\Cookie\Dto\Telemetry\SystemInformationDto;
use Borlabs\Cookie\Dto\Telemetry\TelemetryDto;
use Borlabs\Cookie\Dto\Telemetry\ThemeDto;
use Borlabs\Cookie\DtoList\Telemetry\ContentBlockerDtoList;
use Borlabs\Cookie\DtoList\Telemetry\LegalLinkDtoList;
use Borlabs\Cookie\DtoList\Telemetry\LegalLinksPerLanguageDtoList;
use Borlabs\Cookie\DtoList\Telemetry\PackageDtoList;
use Borlabs\Cookie\DtoList\Telemetry\PluginDtoList;
use Borlabs\Cookie\DtoList\Telemetry\ScriptBlockerDtoList;
use Borlabs\Cookie\DtoList\Telemetry\ServiceDtoList;
use Borlabs\Cookie\DtoList\Telemetry\StyleBlockerDtoList;
use Borlabs\Cookie\DtoList\Telemetry\ThemeDtoList;
use Borlabs\Cookie\Enum\System\AutomaticUpdateEnum;
use Borlabs\Cookie\Enum\Telemetry\LinkTypeEnum;
use Borlabs\Cookie\Model\Package\PackageModel;
use Borlabs\Cookie\Repository\ContentBlocker\ContentBlockerRepository;
use Borlabs\Cookie\Repository\Package\PackageRepository;
use Borlabs\Cookie\Repository\ScriptBlocker\ScriptBlockerRepository;
use Borlabs\Cookie\Repository\Service\ServiceRepository;
use Borlabs\Cookie\Repository\StyleBlocker\StyleBlockerRepository;
use Borlabs\Cookie\Support\Database;
use Borlabs\Cookie\System\Config\DialogSettingsConfig;
use Borlabs\Cookie\System\Config\GeneralConfig;
use Borlabs\Cookie\System\Config\IabTcfConfig;
use Borlabs\Cookie\System\Config\LibraryConfig;
use Borlabs\Cookie\System\Config\PluginConfig;
use Borlabs\Cookie\System\Config\WidgetConfig;
use Borlabs\Cookie\System\Language\Language;
use Borlabs\Cookie\System\Option\Option;

class TelemetryService
{
    private ContentBlockerRepository $contentBlockerRepository;

    private DialogSettingsConfig $dialogSettingsConfig;

    private GeneralConfig $generalConfig;

    private IabTcfConfig $iabTcfConfig;

    private Language $language;

    private LibraryConfig $libraryConfig;

    private Option $option;

    private PackageRepository $packageRepository;

    private PluginConfig $pluginConfig;

    private ScriptBlockerRepository $scriptBlockerRepository;

    private ServiceRepository $serviceRepository;

    private StyleBlockerRepository $styleBlockerRepository;

    private TelemetryApiClient $telemetryApiClient;

    private WidgetConfig $widgetConfig;

    private WpDb $wpdb;

    private WpFunction $wpFunction;

    public function __construct(
        ContentBlockerRepository $contentBlockerRepository,
        DialogSettingsConfig $dialogSettingsConfig,
        GeneralConfig $generalConfig,
        IabTcfConfig $iabTcfConfig,
        Language $language,
        LibraryConfig $libraryConfig,
        Option $option,
        PackageRepository $packageRepository,
        PluginConfig $pluginConfig,
        ScriptBlockerRepository $scriptBlockerRepository,
        ServiceRepository $serviceRepository,
        StyleBlockerRepository $styleBlockerRepository,
        TelemetryApiClient $telemetryApiClient,
        WidgetConfig $widgetConfig,
        WpDb $wpdb,
        WpFunction $wpFunction
    ) {
        $this->contentBlockerRepository = $contentBlockerRepository;
        $this->dialogSettingsConfig = $dialogSettingsConfig;
        $this->generalConfig = $generalConfig;
        $this->iabTcfConfig = $iabTcfConfig;
        $this->language = $language;
        $this->libraryConfig = $libraryConfig;
        $this->option = $option;
        $this->packageRepository = $packageRepository;
        $this->pluginConfig = $pluginConfig;
        $this->scriptBlockerRepository = $scriptBlockerRepository;
        $this->styleBlockerRepository = $styleBlockerRepository;
        $this->serviceRepository = $serviceRepository;
        $this->telemetryApiClient = $telemetryApiClient;
        $this->widgetConfig = $widgetConfig;
        $this->wpdb = $wpdb;
        $this->wpFunction = $wpFunction;
    }

    public function getInstalledPackages(): PackageDtoList
    {
        $installedPackages = $this->packageRepository->getInstalledPackages();
        $packages = new PackageDtoList();

        foreach ($installedPackages as $packageModel) {
            $packages->add($this->getPackageTelemetryData($packageModel));
        }

        return $packages;
    }

    public function getPackageTelemetryData(PackageModel $packageModel): PackageDto
    {
        $packageDto = new PackageDto();
        $packageDto->key = $packageModel->borlabsServicePackageKey;
        $packageDto->name = $packageModel->name;
        $packageDto->version = $packageModel->version->major
            . '.' . $packageModel->version->minor
            . '.' . $packageModel->version->patch;

        return $packageDto;
    }

    public function getTelemetry(): ?TelemetryDto
    {
        $telemetryDto = new TelemetryDto();
        // The following data is crucial for the development of the plugin and the maintenance of Borlabs Cookie packages.
        $telemetryDto->borlabsCookiePackages = $this->getInstalledPackages();
        // To monitor compliance with the IAB's TCF guidelines. In addition, settings data is constantly required as browser behaviour changes in relation to third-party cookies.
        $telemetryDto->borlabsCookieSettings = $this->getSettings($this->hasCustomCode());
        $telemetryDto->systemInformation = $this->getSystemInformation();
        $telemetryDto->borlabsCookieContentBlockers = $this->getContentBlockers();
        $telemetryDto->borlabsCookieLegalLinksPerLanguageList = $this->collectLegalLinks();
        $telemetryDto->borlabsCookieScriptBlockers = $this->getScriptBlockers();
        $telemetryDto->borlabsCookieServices = $this->getServices();
        $telemetryDto->borlabsCookieStyleBlockers = $this->getStyleBlockers();
        $telemetryDto->plugins = $this->getPlugins();
        $telemetryDto->themes = $this->getThemes();

        return $telemetryDto;
    }

    /**
     * @throws \Borlabs\Cookie\Exception\HttpClient\ConnectionErrorException
     * @throws \Borlabs\Cookie\Exception\HttpClient\ServerErrorException
     */
    public function sendTelemetryData()
    {
        $telemetryDto = $this->getTelemetry();

        if (is_null($telemetryDto)) {
            return;
        }

        $this->telemetryApiClient->sendTelemetryData($telemetryDto);
    }

    /**
     * @throws \Borlabs\Cookie\Exception\HttpClient\ConnectionErrorException
     * @throws \Borlabs\Cookie\Exception\HttpClient\ServerErrorException
     */
    public function sendTelemetryDataForPackage(PackageModel $packageModel): void
    {
        $this->telemetryApiClient->sendPackageTelemetryData(
            $this->getPackageTelemetryData($packageModel),
        );
    }

    /**
     * @throws \Borlabs\Cookie\Exception\HttpClient\ConnectionErrorException
     * @throws \Borlabs\Cookie\Exception\HttpClient\ServerErrorException
     */
    public function sendTelemetryDataForSetupAssistantUsage(SetupAssistantUsageDto $setupAssistantUsage): void
    {
        $this->telemetryApiClient->sendSetupAssistantUsageTelemetryData($setupAssistantUsage);
    }

    private function collectLegalLinks(): LegalLinksPerLanguageDtoList
    {
        $availableLanguages = $this->language->getLanguageList();
        $legalLinksPerLanguage = new LegalLinksPerLanguageDtoList();

        foreach ($availableLanguages->list as $language) {
            $languageCode = $language->key;

            if (!$this->dialogSettingsConfig->hasConfig($languageCode)) {
                continue;
            }

            $dialogSettingsConfig = $this->dialogSettingsConfig->load($languageCode);

            if (empty($dialogSettingsConfig->imprintPageUrl) && empty($dialogSettingsConfig->privacyPageUrl)) {
                continue;
            }

            $links = new LegalLinkDtoList();

            if (!empty($dialogSettingsConfig->imprintPageUrl)) {
                $links->add(new LegalLinkDto(LinkTypeEnum::IMPRINT(), $dialogSettingsConfig->imprintPageUrl));
            }

            if (!empty($dialogSettingsConfig->privacyPageUrl)) {
                $links->add(new LegalLinkDto(LinkTypeEnum::PRIVACY(), $dialogSettingsConfig->privacyPageUrl));
            }

            if (count($links->list) > 0) {
                $legalLinksPerLanguage->add(new LegalLinksPerLanguageListItemDto($languageCode, $links));
            }
        }

        return $legalLinksPerLanguage;
    }

    private function getContentBlockers(): ContentBlockerDtoList
    {
        $contentBlockerModels = $this->contentBlockerRepository->getAllOfSelectedLanguage();
        $contentBlockers = new ContentBlockerDtoList();

        foreach ($contentBlockerModels as $contentBlockerModel) {
            $contentBlockerDto = new ContentBlockerDto();
            $contentBlockerDto->borlabsServicePackageKey = $contentBlockerModel->borlabsServicePackageKey;
            $contentBlockerDto->key = $contentBlockerModel->key;
            $contentBlockerDto->name = $contentBlockerModel->name;
            $contentBlockerDto->status = $contentBlockerModel->status;
            $contentBlockers->add($contentBlockerDto);
        }

        return $contentBlockers;
    }

    private function getNonEmptyStringOrDefault($value = null, $defaultValue = 'Unknown')
    {
        return !empty($value) && is_string($value) ? $value : $defaultValue;
    }

    private function getPlugins(): PluginDtoList
    {
        $activePlugins = $this->option->getThirdPartyOption('active_plugins')->value;
        $installedPlugins = $this->wpFunction->getPlugins();
        $plugins = new PluginDtoList();

        foreach ($installedPlugins as $slug => $plugin) {
            $pluginDto = new PluginDto();
            $pluginDto->author = $this->getNonEmptyStringOrDefault($plugin['Author']);
            $pluginDto->isEnabled = in_array($slug, $activePlugins, true);
            $pluginDto->name = $this->getNonEmptyStringOrDefault($plugin['Name']);
            $pluginDto->pluginUrl = $this->getNonEmptyStringOrDefault($plugin['PluginURI'], 'https://unknown-plugin.internal');
            $pluginDto->slug = $this->getNonEmptyStringOrDefault($slug, 'unknown');
            $pluginDto->textDomain = $this->getNonEmptyStringOrDefault($plugin['TextDomain'], 'unknown');
            $pluginDto->version = $this->getNonEmptyStringOrDefault($plugin['Version'], '0.0.0');
            $plugins->add($pluginDto);
        }

        return $plugins;
    }

    private function getScriptBlockers(): ScriptBlockerDtoList
    {
        $scriptBlockerModels = $this->scriptBlockerRepository->getAll();
        $scriptBlockers = new ScriptBlockerDtoList();

        foreach ($scriptBlockerModels as $scriptBlockerModel) {
            $scriptBlockerDto = new ScriptBlockerDto();
            $scriptBlockerDto->borlabsServicePackageKey = $scriptBlockerModel->borlabsServicePackageKey;
            $scriptBlockerDto->handles = $scriptBlockerModel->handles;
            $scriptBlockerDto->key = $scriptBlockerModel->key;
            $scriptBlockerDto->name = $scriptBlockerModel->name;
            $scriptBlockerDto->onExist = $scriptBlockerModel->onExist;
            $scriptBlockerDto->phrases = $scriptBlockerModel->phrases;
            $scriptBlockerDto->status = $scriptBlockerModel->status;
            $scriptBlockers->add($scriptBlockerDto);
        }

        return $scriptBlockers;
    }

    private function getServices(): ServiceDtoList
    {
        $serviceModels = $this->serviceRepository->getAllOfSelectedLanguage(true);
        $services = new ServiceDtoList();

        foreach ($serviceModels as $serviceModel) {
            $serviceDto = new ServiceDto();
            $serviceDto->borlabsServicePackageKey = $serviceModel->borlabsServicePackageKey;
            $serviceDto->key = $serviceModel->key;
            $serviceDto->name = $serviceModel->name;
            $serviceDto->status = $serviceModel->status;
            $services->add($serviceDto);
        }

        return $services;
    }

    private function getSettings(bool $hasCustomCode = false): SettingsDto
    {
        $settingsDto = new SettingsDto();
        $settingsDto->hasCustomCodeUsage = $hasCustomCode;
        $settingsDto->animation = $this->dialogSettingsConfig->get()->animation;
        $settingsDto->animationDelay = $this->dialogSettingsConfig->get()->animationDelay;
        $settingsDto->crossCookieDomains = $this->generalConfig->get()->crossCookieDomains;
        $settingsDto->defaultPluginUrl = $this->generalConfig->defaultConfig()->pluginUrl;
        $settingsDto->displayModeSettings = $this->pluginConfig->get()->displayModeSettings;
        $settingsDto->geoIpActive = $this->dialogSettingsConfig->get()->geoIpActive;
        $settingsDto->hasCustomCodeUsage = $this->hasCustomCode();
        $settingsDto->iabTcfStatus = $this->iabTcfConfig->get()->iabTcfStatus;
        $settingsDto->isLanguageSwitcherConfigured = (bool) count($this->dialogSettingsConfig->get()->languageOptions->list ?? []);
        $settingsDto->isLogoIdentical = ($this->dialogSettingsConfig->get()->logo === $this->dialogSettingsConfig->get()->logoHd)
            || $this->dialogSettingsConfig->get()->showLogo === false; // If the logo is not displayed, we consider it to be identical, as we are only interested in websites that actually use and display different logos for this value.
        $settingsDto->layout = $this->dialogSettingsConfig->get()->layout;
        $settingsDto->packageAutoUpdateTime = $this->libraryConfig->get()->packageAutoUpdateTime;
        $settingsDto->packageAutoUpdateTimeSpan = $this->libraryConfig->get()->packageAutoUpdateTimeSpan;
        $settingsDto->pluginUrl = $this->generalConfig->get()->pluginUrl;
        $settingsDto->position = $this->dialogSettingsConfig->get()->position;
        $settingsDto->showAcceptAllButton = $this->dialogSettingsConfig->get()->showAcceptAllButton;
        $settingsDto->showAcceptOnlyEssentialButton = $this->dialogSettingsConfig->get()->showAcceptOnlyEssentialButton;
        $settingsDto->showDialogAfterUserInteraction = $this->dialogSettingsConfig->get()->showDialogAfterUserInteraction;
        $settingsDto->showSaveButton = $this->dialogSettingsConfig->get()->showSaveButton;
        $settingsDto->showWidget = $this->widgetConfig->get()->show;
        $settingsDto->wordPressAdminSidebarMenuMode = $this->pluginConfig->get()->wordPressAdminSidebarMenuMode;

        return $settingsDto;
    }

    private function getStyleBlockers(): StyleBlockerDtoList
    {
        $styleBlockerModels = $this->styleBlockerRepository->getAll();
        $styleBlockers = new StyleBlockerDtoList();

        foreach ($styleBlockerModels as $styleBlockerModel) {
            $styleBlockerDto = new StyleBlockerDto();
            $styleBlockerDto->borlabsServicePackageKey = $styleBlockerModel->borlabsServicePackageKey;
            $styleBlockerDto->handles = $styleBlockerModel->handles;
            $styleBlockerDto->key = $styleBlockerModel->key;
            $styleBlockerDto->name = $styleBlockerModel->name;
            $styleBlockerDto->phrases = $styleBlockerModel->phrases;
            $styleBlockerDto->status = $styleBlockerModel->status;
            $styleBlockers->add($styleBlockerDto);
        }

        return $styleBlockers;
    }

    private function getSystemInformation(): SystemInformationDto
    {
        $systemInformationDto = new SystemInformationDto();
        $systemInformationDto->databaseVersion = Database::getDbVersion();
        $systemInformationDto->phpVersion = phpversion();
        $systemInformationDto->product = 'borlabs-cookie';
        $systemInformationDto->productAutomaticUpdate = $this->pluginConfig->get()->automaticUpdate ?? AutomaticUpdateEnum::AUTO_UPDATE_ALL();
        $systemInformationDto->productDebugLoggingEnabled = $this->pluginConfig->get()->enableDebugLogging;
        $systemInformationDto->productVersion = BORLABS_COOKIE_VERSION;
        $systemInformationDto->wordPressContentUrl = defined('WP_CONTENT_URL') ? (string) constant('WP_CONTENT_URL') : null;
        $systemInformationDto->wordPressDatabasePrefix = $this->wpdb->prefix;
        $systemInformationDto->wordPressDebugDisplayEnabled = defined('WP_DEBUG_DISPLAY') && (bool) constant('WP_DEBUG_DISPLAY') === true;
        $systemInformationDto->wordPressDebugEnabled = defined('WP_DEBUG') && (bool) constant('WP_DEBUG') === true;
        $systemInformationDto->wordPressDebugLogEnabled = defined('WP_DEBUG_LOG') && (bool) constant('WP_DEBUG_LOG') === true;
        $systemInformationDto->wordPressDefaultLanguage = $this->language->getDefaultLanguage();
        $systemInformationDto->wordPressHasMultilanguagePluginActive = $this->language->isMultilanguagePluginActive();
        $systemInformationDto->wordPressIsMultisite = defined('MULTISITE') && constant('MULTISITE') === true;
        $systemInformationDto->wordPressIsMultisiteSubdomainInstall = defined('SUBDOMAIN_INSTALL') && constant('SUBDOMAIN_INSTALL') === true;
        $systemInformationDto->wordPressMultilanguagePluginLanguages = $this->language->getLanguageList();
        $systemInformationDto->wordPressMultisiteMainSite = defined('DOMAIN_CURRENT_SITE') && defined('PATH_CURRENT_SITE') ? constant('DOMAIN_CURRENT_SITE') . constant('PATH_CURRENT_SITE') : null;
        $systemInformationDto->wordPressVersion = $this->wpFunction->getBlogInfo('version');

        return $systemInformationDto;
    }

    private function getThemes(): ThemeDtoList
    {
        $activeTheme = $this->wpFunction->getWpTheme();
        $installedThemes = $this->wpFunction->getWpThemes();
        $themes = new ThemeDtoList();

        foreach ($installedThemes as $theme) {
            $themeDto = new ThemeDto();
            $themeDto->author = $this->getNonEmptyStringOrDefault($theme->get('Author'));
            $themeDto->isChildtheme = strlen((string) $theme->get('Template')) ? true : false;
            $themeDto->isEnabled = $activeTheme->get_template() === $theme->get_template();
            $themeDto->name = $this->getNonEmptyStringOrDefault($theme->get('Name'));
            $themeDto->template = $this->getNonEmptyStringOrDefault($theme->get_template(), 'unknown');
            $themeDto->textDomain = $this->getNonEmptyStringOrDefault($theme->get('TextDomain'), 'unknown');
            $themeDto->themeUrl = $this->getNonEmptyStringOrDefault($theme->get('ThemeURI'), 'https://unknown-theme.internal');
            $themeDto->version = $this->getNonEmptyStringOrDefault($theme->get('Version'), '0.0.0');
            $themes->add($themeDto);
        }

        return $themes;
    }

    private function hasCustomCode(): bool
    {
        $result = $this->wpdb->get_var(
            $this->wpdb->prepare(
                '
                SELECT
                    `meta_id`
                FROM
                    `' . $this->wpdb->postmeta . '`
                WHERE
                    `meta_key` = %s
                    AND
                    `meta_value` != \'\'
                LIMIT 1
                ',
                [
                    'borlabsCookieCustomCode',
                ],
            ),
        );

        return !empty($result);
    }
}
