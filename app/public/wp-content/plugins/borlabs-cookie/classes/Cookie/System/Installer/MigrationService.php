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

namespace Borlabs\Cookie\System\Installer;

use Borlabs\Cookie\Container\Container;
use Borlabs\Cookie\Support\Traits\VersionNumberTrait;
use Borlabs\Cookie\System\Installer\CloudScan\CloudScanCookieTableMigration;
use Borlabs\Cookie\System\Installer\CloudScan\CloudScanExternalResourceTableMigration;
use Borlabs\Cookie\System\Installer\CloudScan\CloudScanSuggestionTableMigration;
use Borlabs\Cookie\System\Installer\CloudScan\CloudScanTableMigration;
use Borlabs\Cookie\System\Installer\CompatibilityPatch\CompatibilityPatchTableMigration;
use Borlabs\Cookie\System\Installer\ConsentLog\ConsentLogTableMigration;
use Borlabs\Cookie\System\Installer\ConsentStatistic\ConsentStatisticByDayGroupedByServiceGroupTableMigration;
use Borlabs\Cookie\System\Installer\ConsentStatistic\ConsentStatisticByDayTableMigration;
use Borlabs\Cookie\System\Installer\ConsentStatistic\ConsentStatisticByHourGroupedByServiceGroupTableMigration;
use Borlabs\Cookie\System\Installer\ConsentStatistic\ConsentStatisticByHourTableMigration;
use Borlabs\Cookie\System\Installer\ContentBlocker\ContentBlockerLocationTableMigration;
use Borlabs\Cookie\System\Installer\ContentBlocker\ContentBlockerSeeder;
use Borlabs\Cookie\System\Installer\ContentBlocker\ContentBlockerTableMigration;
use Borlabs\Cookie\System\Installer\Country\CountrySeeder;
use Borlabs\Cookie\System\Installer\Country\CountryTableMigration;
use Borlabs\Cookie\System\Installer\IabTcf\VendorTableMigration;
use Borlabs\Cookie\System\Installer\Job\JobTableMigration;
use Borlabs\Cookie\System\Installer\Log\LogTableMigration;
use Borlabs\Cookie\System\Installer\Package\PackageTableMigration;
use Borlabs\Cookie\System\Installer\Provider\ProviderSeeder;
use Borlabs\Cookie\System\Installer\Provider\ProviderTableMigration;
use Borlabs\Cookie\System\Installer\ScriptBlocker\ScriptBlockerTableMigration;
use Borlabs\Cookie\System\Installer\Service\ServiceCookieTableMigration;
use Borlabs\Cookie\System\Installer\Service\ServiceLocationTableMigration;
use Borlabs\Cookie\System\Installer\Service\ServiceOptionTableMigration;
use Borlabs\Cookie\System\Installer\Service\ServiceSeeder;
use Borlabs\Cookie\System\Installer\Service\ServiceTableMigration;
use Borlabs\Cookie\System\Installer\ServiceGroup\ServiceGroupSeeder;
use Borlabs\Cookie\System\Installer\ServiceGroup\ServiceGroupTableMigration;
use Borlabs\Cookie\System\Installer\StyleBlocker\StyleBlockerTableMigration;
use Borlabs\Cookie\System\Language\Language;
use Borlabs\Cookie\System\Log\Log;
use Borlabs\Cookie\System\Option\Option;
use Throwable;

/**
 * DO NOT LOAD THIS CLASS VIA DEPENDENCY INJECTION!
 * DO NOT LOAD THIS CLASS VIA DEPENDENCY INJECTION!
 * DO NOT LOAD THIS CLASS VIA DEPENDENCY INJECTION!
 * DO NOT LOAD THIS CLASS VIA DEPENDENCY INJECTION!
 * DO NOT LOAD THIS CLASS VIA DEPENDENCY INJECTION!
 */
class MigrationService
{
    use VersionNumberTrait;

    public const BORLABS_COOKIE_VERSION = '3.4.0';

    private CloudScanCookieTableMigration $cloudScanCookieTableMigration;

    private CloudScanExternalResourceTableMigration $cloudScanExternalResourceTableMigration;

    private CloudScanSuggestionTableMigration $cloudScanSuggestionTableMigration;

    private CloudScanTableMigration $cloudScanTableMigration;

    private CompatibilityPatchTableMigration $compatibilityPatchTableMigration;

    private ConsentLogTableMigration $consentLogTableMigration;

    private ConsentStatisticByDayGroupedByServiceGroupTableMigration $consentStatisticByDayGroupedByServiceGroupTableMigration;

    private ConsentStatisticByDayTableMigration $consentStatisticByDayTableMigration;

    private ConsentStatisticByHourGroupedByServiceGroupTableMigration $consentStatisticByHourGroupedByServiceGroupTableMigration;

    private ConsentStatisticByHourTableMigration $consentStatisticByHourTableMigration;

    private Container $container;

    private ContentBlockerLocationTableMigration $contentBlockerLocationTableMigration;

    private ContentBlockerSeeder $contentBlockerSeeder;

    private ContentBlockerTableMigration $contentBlockerTableMigration;

    private CountrySeeder $countrySeeder;

    private CountryTableMigration $countryTableMigration;

    private JobTableMigration $jobTableMigration;

    private LogTableMigration $logTableMigration;

    private Option $option;

    private PackageTableMigration $packageTableMigration;

    private ProviderSeeder $providerSeeder;

    private ProviderTableMigration $providerTableMigration;

    private ScriptBlockerTableMigration $scriptBlockerTableMigration;

    private ServiceCookieTableMigration $serviceCookieTableMigration;

    private ServiceGroupSeeder $serviceGroupSeeder;

    private ServiceGroupTableMigration $serviceGroupTableMigration;

    private ServiceLocationTableMigration $serviceLocationTableMigration;

    private ServiceOptionTableMigration $serviceOptionTableMigration;

    private ServiceSeeder $serviceSeeder;

    private ServiceTableMigration $serviceTableMigration;

    private StyleBlockerTableMigration $styleBlockerTableMigration;

    private VendorTableMigration $vendorTableMigration;

    public function __construct(
        CloudScanCookieTableMigration $cloudScanCookieTableMigration,
        CloudScanSuggestionTableMigration $cloudScanSuggestionTableMigration,
        CloudScanExternalResourceTableMigration $cloudScanExternalResourceTableMigration,
        CloudScanTableMigration $cloudScanTableMigration,
        CompatibilityPatchTableMigration $compatibilityPatchTableMigration,
        ConsentLogTableMigration $consentLogTableMigration,
        ConsentStatisticByDayGroupedByServiceGroupTableMigration $consentStatisticByDayGroupedByServiceGroupTableMigration,
        ConsentStatisticByDayTableMigration $consentStatisticByDayTableMigration,
        ConsentStatisticByHourGroupedByServiceGroupTableMigration $consentStatisticByHourGroupedByServiceGroupTableMigration,
        ConsentStatisticByHourTableMigration $consentStatisticByHourTableMigration,
        Container $container,
        ContentBlockerSeeder $contentBlockerSeeder,
        ContentBlockerTableMigration $contentBlockerTableMigration,
        ContentBlockerLocationTableMigration $contentBlockerLocationTableMigration,
        CountrySeeder $countrySeeder,
        CountryTableMigration $countryTableMigration,
        JobTableMigration $jobTableMigration,
        LogTableMigration $logTableMigration,
        Option $option,
        PackageTableMigration $packageTableMigration,
        ProviderSeeder $providerSeeder,
        ProviderTableMigration $providerTableMigration,
        ScriptBlockerTableMigration $scriptBlockerTableMigration,
        ServiceCookieTableMigration $serviceCookieTableMigration,
        ServiceGroupSeeder $serviceGroupSeeder,
        ServiceGroupTableMigration $serviceGroupTableMigration,
        ServiceLocationTableMigration $serviceLocationTableMigration,
        ServiceOptionTableMigration $serviceOptionTableMigration,
        ServiceSeeder $serviceSeeder,
        ServiceTableMigration $serviceTableMigration,
        StyleBlockerTableMigration $styleBlockerTableMigration,
        VendorTableMigration $vendorTableMigration
    ) {
        $this->cloudScanCookieTableMigration = $cloudScanCookieTableMigration;
        $this->cloudScanExternalResourceTableMigration = $cloudScanExternalResourceTableMigration;
        $this->cloudScanSuggestionTableMigration = $cloudScanSuggestionTableMigration;
        $this->cloudScanTableMigration = $cloudScanTableMigration;
        $this->compatibilityPatchTableMigration = $compatibilityPatchTableMigration;
        $this->consentLogTableMigration = $consentLogTableMigration;
        $this->consentStatisticByDayGroupedByServiceGroupTableMigration = $consentStatisticByDayGroupedByServiceGroupTableMigration;
        $this->consentStatisticByDayTableMigration = $consentStatisticByDayTableMigration;
        $this->consentStatisticByHourTableMigration = $consentStatisticByHourTableMigration;
        $this->consentStatisticByHourGroupedByServiceGroupTableMigration = $consentStatisticByHourGroupedByServiceGroupTableMigration;
        $this->container = $container;
        $this->contentBlockerSeeder = $contentBlockerSeeder;
        $this->contentBlockerLocationTableMigration = $contentBlockerLocationTableMigration;
        $this->contentBlockerTableMigration = $contentBlockerTableMigration;
        $this->countrySeeder = $countrySeeder;
        $this->countryTableMigration = $countryTableMigration;
        $this->jobTableMigration = $jobTableMigration;
        $this->logTableMigration = $logTableMigration;
        $this->option = $option;
        $this->packageTableMigration = $packageTableMigration;
        $this->providerSeeder = $providerSeeder;
        $this->providerTableMigration = $providerTableMigration;
        $this->scriptBlockerTableMigration = $scriptBlockerTableMigration;
        $this->serviceCookieTableMigration = $serviceCookieTableMigration;
        $this->serviceGroupSeeder = $serviceGroupSeeder;
        $this->serviceGroupTableMigration = $serviceGroupTableMigration;
        $this->serviceLocationTableMigration = $serviceLocationTableMigration;
        $this->serviceOptionTableMigration = $serviceOptionTableMigration;
        $this->serviceSeeder = $serviceSeeder;
        $this->serviceTableMigration = $serviceTableMigration;
        $this->styleBlockerTableMigration = $styleBlockerTableMigration;
        $this->vendorTableMigration = $vendorTableMigration;
    }

    public function run(string $prefix)
    {
        $this->runDatabaseTableMigrations($prefix);
        $this->runLanguageSpecificSeeder($prefix);
        $this->runNonLanguageSpecificSeeder($prefix);
        $this->runMigrations();
    }

    /**
     * @return array<string, \Borlabs\Cookie\Dto\System\AuditDto>
     */
    public function runDatabaseTableMigrations(string $prefix): array
    {
        $audits = [];
        // Table migration - Without dependencies
        $audits['job'] = $this->jobTableMigration->run($prefix);
        $audits['log'] = $this->logTableMigration->run($prefix);
        $audits['compatibilityPatch'] = $this->compatibilityPatchTableMigration->run($prefix);
        $audits['consentLog'] = $this->consentLogTableMigration->run($prefix);
        $audits['consentStatisticByDayGroupedByServiceGroup'] = $this->consentStatisticByDayGroupedByServiceGroupTableMigration->run($prefix);
        $audits['consentStatisticByDay'] = $this->consentStatisticByDayTableMigration->run($prefix);
        $audits['consentStatisticByHourGroupedByServiceGroup'] = $this->consentStatisticByHourGroupedByServiceGroupTableMigration->run($prefix);
        $audits['consentStatisticByHour'] = $this->consentStatisticByHourTableMigration->run($prefix);
        $audits['country'] = $this->countryTableMigration->run($prefix);
        $audits['package'] = $this->packageTableMigration->run($prefix);
        $audits['provider'] = $this->providerTableMigration->run($prefix);
        $audits['scriptBlocker'] = $this->scriptBlockerTableMigration->run($prefix);
        $audits['styleBlocker'] = $this->styleBlockerTableMigration->run($prefix);
        $audits['vendor'] = $this->vendorTableMigration->run($prefix);
        $audits['serviceGroup'] = $this->serviceGroupTableMigration->run($prefix);

        // Table migration - With dependencies
        $audits['cloudScan'] = $this->cloudScanTableMigration->run($prefix);
        $audits['cloudScanCookie'] = $this->cloudScanCookieTableMigration->run($prefix);
        $audits['cloudScanExternalResource'] = $this->cloudScanExternalResourceTableMigration->run($prefix);
        $audits['cloudScanSuggestion'] = $this->cloudScanSuggestionTableMigration->run($prefix);
        $audits['service'] = $this->serviceTableMigration->run($prefix);
        $audits['serviceCookie'] = $this->serviceCookieTableMigration->run($prefix);
        $audits['serviceLocation'] = $this->serviceLocationTableMigration->run($prefix);
        $audits['serviceOption'] = $this->serviceOptionTableMigration->run($prefix);
        $audits['contentBlocker'] = $this->contentBlockerTableMigration->run($prefix);
        $audits['contentBlockerLocation'] = $this->contentBlockerLocationTableMigration->run($prefix);

        return $audits;
    }

    /**
     * @return array<string, array<string, \Borlabs\Cookie\Dto\System\AuditDto>>
     */
    public function runLanguageSpecificSeeder(string $prefix, ?string $languageCode = null): array
    {
        $language = $this->container->get(Language::class);

        if ($languageCode === null) {
            $languageCode = $language->getSelectedLanguageCode();
        }

        // TODO: Replace with `$language->runInLanguageContext` in near future
        $language->loadTextDomain($languageCode);
        $audits = [];
        $audits['provider'][$languageCode] = $this->providerSeeder->run($prefix, $languageCode);
        $audits['serviceGroup'][$languageCode] = $this->serviceGroupSeeder->run($prefix, $languageCode);
        $audits['service'][$languageCode] = $this->serviceSeeder->run($prefix, $languageCode);
        $audits['contentBlocker'][$languageCode] = $this->contentBlockerSeeder->run($prefix, $languageCode);
        $language->unloadBlogLanguage();

        return $audits;
    }

    public function runMigrations()
    {
        // Run migration files
        $directory = __DIR__ . '/Migrations/';
        $files = glob($directory . '*.php');
        natsort($files);
        $lastVersion = $this->transformToVersionNumberWithHotfixDto(
            $this->option->get('Version', '0.0.0')->value,
        );
        $this->option->set('Version', self::BORLABS_COOKIE_VERSION);

        foreach ($files as $file) {
            $className = basename($file, '.php');
            $classNameWithNamespace = '\\' . __NAMESPACE__ . '\\Migrations\\' . $className;
            $version = preg_replace(
                '/_/',
                '.',
                preg_replace('/Migration_(.*)/', '$1', $className),
            );
            $migrationVersion = $this->transformToVersionNumberWithHotfixDto($version);

            if ($this->compareVersionNumber($lastVersion, $migrationVersion, '>=')) {
                continue;
            }

            if (!class_exists($classNameWithNamespace)) {
                // Fallback
                try {
                    $this->container->get(Log::class)->warning(
                        'Migration class not found, try to include the file',
                        [
                            'file' => $file,
                            'fileName' => basename($file),
                            'migrationVersion' => $version,
                            'version' => self::BORLABS_COOKIE_VERSION,
                        ],
                    );

                    include_once $file;
                } catch (Throwable $e) {
                    $this->container->get(Log::class)->error(
                        'Migration file could not be included',
                        [
                            'file' => $file,
                            'fileName' => basename($file),
                            'migrationVersion' => $version,
                            'version' => self::BORLABS_COOKIE_VERSION,
                        ],
                    );

                    continue;
                }

                if (!class_exists($classNameWithNamespace)) {
                    $this->container->get(Log::class)->error(
                        'Migration class not found',
                        [
                            'classNameWithNamespace' => $classNameWithNamespace,
                            'migrationVersion' => $version,
                            'version' => self::BORLABS_COOKIE_VERSION,
                        ],
                    );

                    continue;
                }
            }

            $instance = $this->container->get($classNameWithNamespace);

            if (!method_exists($instance, 'run')) {
                continue;
            }

            $this->container->get(Log::class)->info(
                'Run migration: {{ migrationVersion }}',
                [
                    'migrationFiles' => $files,
                    'migrationVersion' => $version,
                    'version' => self::BORLABS_COOKIE_VERSION,
                ],
            );
            $this->container->get($classNameWithNamespace)->run();
        }
    }

    /**
     * @return array<string, \Borlabs\Cookie\Dto\System\AuditDto>
     */
    public function runNonLanguageSpecificSeeder(string $prefix): array
    {
        $audits = [];
        $audits['country'] = $this->countrySeeder->run($prefix);

        return $audits;
    }
}
