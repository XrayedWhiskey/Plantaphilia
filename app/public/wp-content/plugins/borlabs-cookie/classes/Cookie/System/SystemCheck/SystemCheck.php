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

namespace Borlabs\Cookie\System\SystemCheck;

// TODO: save all file to wp-content/uploads/borlabs-cookie - copy files to wp-content/cache/borlabs-cookie if missing. Make cache folder configurable.
use Borlabs\Cookie\Adapter\WpDb;
use Borlabs\Cookie\Adapter\WpFunction;
use Borlabs\Cookie\ApiClient\TestWordPressRestApiApiClient;
use Borlabs\Cookie\Container\Container;
use Borlabs\Cookie\Dto\System\AuditDto;
use Borlabs\Cookie\Exception\HttpClient\ConnectionErrorException;
use Borlabs\Cookie\Exception\HttpClient\ServerErrorException;
use Borlabs\Cookie\Localization\SystemCheck\SystemCheckLocalizationStrings;
use Borlabs\Cookie\Repository\ConsentLog\ConsentLogRepository;
use Borlabs\Cookie\ScheduleEvent\ScheduleEventManager;
use Borlabs\Cookie\Support\Formatter;
use Borlabs\Cookie\System\Config\GeneralConfig;
use Borlabs\Cookie\System\GeoIp\GeoIp;
use Borlabs\Cookie\System\IabTcf\IabTcfService;
use Borlabs\Cookie\System\Installer\FileSystem\CacheFolder;
use Borlabs\Cookie\System\Installer\FileSystem\StorageFolder;
use Borlabs\Cookie\System\Language\Language;
use Borlabs\Cookie\System\Package\PackageManager;
use Borlabs\Cookie\System\SafeMode\SafeMode;
use Borlabs\Cookie\System\Template\Template;

class SystemCheck
{
    private CacheFolder $cacheFolder;

    private ConsentLogRepository $consentLogRepository;

    private Container $container;

    private GeneralConfig $generalConfig;

    private GeoIp $geoIp;

    private IabTcfService $iabTcfService;

    private Language $language;

    private PackageManager $packageManager;

    private SafeMode $safeMode;

    private ScheduleEventManager $scheduleEventManager;

    private StorageFolder $storageFolder;

    private SystemCheckLocalizationStrings $systemCheckLocalizationStrings;

    private Template $template;

    private TestWordPressRestApiApiClient $testWordPressRestApiApiClient;

    private WpDb $wpdb;

    private WpFunction $wpFunction;

    public function __construct(
        CacheFolder $cacheFolder,
        ConsentLogRepository $consentLogRepository,
        Container $container,
        GeneralConfig $generalConfig,
        GeoIp $geoIp,
        IabTcfService $iabTcfService,
        Language $language,
        PackageManager $packageManager,
        SafeMode $safeMode,
        ScheduleEventManager $scheduleEventManager,
        StorageFolder $storageFolder,
        SystemCheckLocalizationStrings $systemCheckLocalizationStrings,
        Template $template,
        TestWordPressRestApiApiClient $testWordPressRestApiApiClient,
        WpDb $wpdb,
        WpFunction $wpFunction
    ) {
        $this->cacheFolder = $cacheFolder;
        $this->consentLogRepository = $consentLogRepository;
        $this->container = $container;
        $this->generalConfig = $generalConfig;
        $this->geoIp = $geoIp;
        $this->iabTcfService = $iabTcfService;
        $this->language = $language;
        $this->packageManager = $packageManager;
        $this->safeMode = $safeMode;
        $this->scheduleEventManager = $scheduleEventManager;
        $this->storageFolder = $storageFolder;
        $this->systemCheckLocalizationStrings = $systemCheckLocalizationStrings;
        $this->template = $template;
        $this->testWordPressRestApiApiClient = $testWordPressRestApiApiClient;
        $this->wpdb = $wpdb;
        $this->wpFunction = $wpFunction;
    }

    public function compatibilityPatches(): AuditDto
    {
        if ($this->safeMode->isEnabledByConstant()) {
            return new AuditDto(false, $this->systemCheckLocalizationStrings::get()['alert']['compatibilityPatchesDisabledByConstant']);
        }

        if ($this->safeMode->isEnabledByCookie()) {
            return new AuditDto(false, $this->systemCheckLocalizationStrings::get()['alert']['compatibilityPatchesDisabledByCookie']);
        }

        return new AuditDto(true);
    }

    public function cronjobSetting(): AuditDto
    {
        $eventsSchedule = $this->scheduleEventManager->getStatus();

        $notRegisteredEvents = array_keys(
            array_filter($eventsSchedule, function ($eventSchedule) {
                return !$eventSchedule['registered'];
            }),
        );
        $overdueEvents = array_keys(
            array_filter($eventsSchedule, function ($eventSchedule) {
                return $eventSchedule['overdue'];
            }),
        );
        $message = '';
        $success = true;

        if (count($notRegisteredEvents) > 0) {
            $success = false;
            $message .= Formatter::interpolate($this->systemCheckLocalizationStrings::get()['alert']['notScheduled'], [
                'events' => implode(', ', $notRegisteredEvents),
            ]);
        }

        if (count($overdueEvents) > 0) {
            $success = false;
            $message .= Formatter::interpolate($this->systemCheckLocalizationStrings::get()['alert']['overdueEvents'], [
                'events' => implode(', ', $overdueEvents),
            ]);
        }

        return new AuditDto($success, $message);
    }

    public function getDbVersion(): string
    {
        $dbVersion = $this->wpdb->get_var('SELECT VERSION()');

        return $dbVersion ?? '-';
    }

    public function getPhpVersion(): string
    {
        return phpversion();
    }

    public function languageSetting(): AuditDto
    {
        $language = $this->language->getSelectedLanguageCode();

        if (empty($language)) {
            return new AuditDto(false, $this->systemCheckLocalizationStrings::get()['alert']['languageConfigurationIsBroken']);
        }

        if ($this->language->isMultilanguagePluginActive() && count($this->language->getLanguageList()->list) === 0) {
            return new AuditDto(false, $this->systemCheckLocalizationStrings::get()['alert']['multilanguageConfigurationIncorrect']);
        }

        return new AuditDto(true);
    }

    /**
     * @return array<string, array<string, AuditDto>>
     */
    public function report(): array
    {
        $report = [];
        $prefix = $this->wpdb->prefix;

        // Table checks
        $report['table'] = $this->container->get('Borlabs\Cookie\System\Installer\MigrationService')->runDatabaseTableMigrations($prefix);

        // Entry checks
        $report['languageSpecificEntry'] = $this->container->get('Borlabs\Cookie\System\Installer\MigrationService')->runLanguageSpecificSeeder($prefix);
        $report['nonLanguageSpecificEntry'] = $this->container->get('Borlabs\Cookie\System\Installer\MigrationService')->runNonLanguageSpecificSeeder($prefix);

        $languages = $this->language->getLanguageList()->list;

        if ($this->language->isMultilanguagePluginActive() && count($languages) > 0) {
            foreach ($languages as $language) {
                $languageSpecificComponentAudits = $this->container->get('Borlabs\Cookie\System\Installer\MigrationService')->runLanguageSpecificSeeder($prefix, $language->key);

                foreach ($languageSpecificComponentAudits as $component => $languageSpecificAudit) {
                    foreach ($languageSpecificAudit as $languageCode => $audit) {
                        $report['languageSpecificEntry'][$component][$languageCode] = $audit;
                    }
                }
            }
        }

        // Run migrations
        $this->container->get('Borlabs\Cookie\System\Installer\MigrationService')->runMigrations();

        // File checks
        $report['fileSystem']['cacheFolder'] = $this->cacheFolder->run();
        $report['fileSystem']['storageFolder'] = $this->storageFolder->run();

        // Settings checks
        $report['system']['compatibilityPatches'] = $this->compatibilityPatches();
        $report['system']['cronjob'] = $this->cronjobSetting();
        $report['system']['language'] = $this->languageSetting();
        $report['system']['ssl'] = $this->sslSetting();

        return $report;
    }

    public function sslSetting(): AuditDto
    {
        // Check if HTTPS settings are correct
        $contentUrl = parse_url(WP_CONTENT_URL);

        if (isset($contentUrl['scheme']) && $contentUrl['scheme'] === 'https') {
            return new AuditDto(true);
        }

        // No SSL certificate
        // TODO: or?
        if (
            empty($_SERVER['SERVER_PORT']) || empty($_SERVER['HTTPS'])
            || ($_SERVER['SERVER_PORT'] !== '443'
                && !isset($_SERVER['HTTP_X_FORWARDED_PORT']))
        ) {
            return new AuditDto(false, $this->systemCheckLocalizationStrings::get()['alert']['noSSLCertification']);
        }

        // Broken configuration
        return new AuditDto(
            false,
            Formatter::interpolate($this->systemCheckLocalizationStrings::get()['alert']['sslConfigurationIsNotCorrect'], [
                'wp_content_url' => WP_CONTENT_URL,
                'https' => $_SERVER['HTTPS'],
                'server_port' => $_SERVER['SERVER_PORT'],
            ]),
        );
    }

    /**
     * @throws \Borlabs\Cookie\Dependencies\Twig\Error\Error
     */
    public function systemCheckView(): ?string
    {
        $templateData = [];
        $templateData['localized'] = $this->systemCheckLocalizationStrings::get();
        $templateData['language'] = $this->language->getSelectedLanguageCode();
        $templateData['defaultLanguage'] = $this->language->getDefaultLanguage();
        $templateData['data']['borlabsCookieStatus'] = $this->generalConfig->get()->borlabsCookieStatus;

        $lastSuccessfulCheckWithApiTimestamp = $this->geoIp->getLastSuccessfulCheckWithApiTimestamp();
        $templateData['data']['geoIpLastSuccessfulCheckWithApiFormattedTime']
            = $lastSuccessfulCheckWithApiTimestamp === null
            ? '-' : Formatter::timestamp($lastSuccessfulCheckWithApiTimestamp);

        $lastSuccessfulCheckWithApiTimestamp = $this->iabTcfService->getLastSuccessfulCheckWithApiTimestamp();
        $templateData['data']['gvlLastSuccessfulCheckWithApiFormattedTime']
            = $lastSuccessfulCheckWithApiTimestamp === null
            ? '-' : Formatter::timestamp($lastSuccessfulCheckWithApiTimestamp);

        $lastSuccessfulCheckWithApiTimestamp = $this->packageManager->getLastSuccessfulCheckWithApiTimestamp();
        $templateData['data']['packageListSuccessfulCheckWithApiFormattedTime']
            = $lastSuccessfulCheckWithApiTimestamp === 0
            ? '-' : Formatter::timestamp($lastSuccessfulCheckWithApiTimestamp);

        $templateData['data']['consentLogTableSize'] = $this->wpFunction->numberFormatI18n(
            $this->consentLogRepository->getTableSize(),
            2,
        );

        $templateData['data']['dbVersion'] = $this->getDbVersion();
        $templateData['data']['phpVersion'] = $this->getPhpVersion();
        $templateData['data']['status'] = $this->report();
        $templateData['data']['totalConsentLogs'] = $this->wpFunction->numberFormatI18n($this->consentLogRepository->getTotal());
        $templateData['data']['wordPressRestApiStatus'] = $this->wordPressRestApiStatus();

        return $this->template->getEngine()->render(
            'system-check/system-check.html.twig',
            $templateData,
        );
    }

    public function wordPressRestApiStatus(): AuditDto
    {
        try {
            $response = $this->testWordPressRestApiApiClient->requestTest();

            return new AuditDto($response->success, $response->messageCode);
        } catch (ConnectionErrorException $e) {
            return new AuditDto(false, $e->getTranslatedMessage());
        } catch (ServerErrorException $e) {
            return new AuditDto(false, $e->getMessage());
        }
    }
}
