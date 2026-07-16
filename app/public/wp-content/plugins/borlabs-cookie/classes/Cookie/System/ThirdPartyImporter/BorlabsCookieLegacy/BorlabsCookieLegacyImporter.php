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

namespace Borlabs\Cookie\System\ThirdPartyImporter\BorlabsCookieLegacy;

use Borlabs\Cookie\Adapter\WpDb;
use Borlabs\Cookie\Adapter\WpFunction;
use Borlabs\Cookie\Dto\ThirdPartyImporter\ImportReportDto;
use Borlabs\Cookie\Localization\Library\SharedMailLocalizationStrings;
use Borlabs\Cookie\Localization\ThirdPartyImporter\ThirdPartyImporterLocalizationStrings;
use Borlabs\Cookie\Support\Database;
use Borlabs\Cookie\Support\Formatter;
use Borlabs\Cookie\System\Config\BackwardsCompatibilityConfig;
use Borlabs\Cookie\System\Localization\LocalizationForMailTemplates;
use Borlabs\Cookie\System\Log\Log;
use Borlabs\Cookie\System\Mail\MailService;
use Borlabs\Cookie\System\Option\Option;
use Borlabs\Cookie\System\Package\PackageInstallJobService;
use Borlabs\Cookie\System\Package\PackageManager;
use Borlabs\Cookie\System\Package\Traits\InstallationStatusAnalyzerTrait;
use Borlabs\Cookie\System\Template\Template;
use Borlabs\Cookie\System\ThirdPartyImporter\ThirdPartyImporterInterface;

final class BorlabsCookieLegacyImporter implements ThirdPartyImporterInterface
{
    use InstallationStatusAnalyzerTrait;

    private BackwardsCompatibilityConfig $backwardsCompatibilityConfig;

    private BorlabsCookieLegacyConfigImporter $borlabsCookieLegacyConfigImporter;

    private ContentBlockerImporter $contentBlockerImporter;

    private LanguageInitializer $languageInitializer;

    private LicenseImporter $licenseImporter;

    private LocalizationForMailTemplates $localizationForMailTemplates;

    private Log $log;

    private MailService $mailService;

    private Option $option;

    private PackageInstaller $packageInstaller;

    private PackageInstallJobService $packageInstallJobService;

    private PackageManager $packageManager;

    private ScriptBlockerImporter $scriptBlockerImporter;

    private ServiceGroupImporter $serviceGroupImporter;

    private ServiceImporter $serviceImporter;

    private Template $template;

    private WpDb $wpdb;

    private WpFunction $wpFunction;

    public function __construct(
        BackwardsCompatibilityConfig $backwardsCompatibilityConfig,
        BorlabsCookieLegacyConfigImporter $borlabsCookieLegacyConfigImporter,
        ContentBlockerImporter $contentBlockerImporter,
        LanguageInitializer $languageInitializer,
        LicenseImporter $licenseImporter,
        LocalizationForMailTemplates $localizationForMailTemplates,
        Log $log,
        MailService $mailService,
        Option $option,
        PackageInstaller $packageInstaller,
        PackageInstallJobService $packageInstallJobService,
        PackageManager $packageManager,
        ScriptBlockerImporter $scriptBlockerImporter,
        ServiceGroupImporter $serviceGroupImporter,
        ServiceImporter $serviceImporter,
        Template $template,
        WpDb $wpdb,
        WpFunction $wpFunction
    ) {
        $this->backwardsCompatibilityConfig = $backwardsCompatibilityConfig;
        $this->borlabsCookieLegacyConfigImporter = $borlabsCookieLegacyConfigImporter;
        $this->contentBlockerImporter = $contentBlockerImporter;
        $this->languageInitializer = $languageInitializer;
        $this->licenseImporter = $licenseImporter;
        $this->localizationForMailTemplates = $localizationForMailTemplates;
        $this->log = $log;
        $this->mailService = $mailService;
        $this->option = $option;
        $this->packageInstaller = $packageInstaller;
        $this->packageInstallJobService = $packageInstallJobService;
        $this->packageManager = $packageManager;
        $this->scriptBlockerImporter = $scriptBlockerImporter;
        $this->serviceGroupImporter = $serviceGroupImporter;
        $this->serviceImporter = $serviceImporter;
        $this->template = $template;
        $this->wpdb = $wpdb;
        $this->wpFunction = $wpFunction;
    }

    public function getImporterName(): string
    {
        return 'Borlabs Cookie Legacy';
    }

    public function import(): ?ImportReportDto
    {
        if (!$this->isImportDataAvailable()) {
            return null;
        }

        $this->log->info(
            '[Import] Start importing data of: {{ importerName }}',
            [
                'importerName' => $this->getImporterName(),
            ],
        );

        $licenseImporStatus = $this->licenseImporter->import();

        if ($licenseImporStatus === false) {
            $this->log->info(
                '[Import] Import of license failed. Skipping import of legacy data.',
            );

            return null;
        }

        $importReport = new ImportReportDto();
        // Configs
        $importReport->configImported = $this->borlabsCookieLegacyConfigImporter->import();
        // Update package library to allow import of Content Blockers and Services
        $this->packageManager->updatePackageList();
        $this->languageInitializer->executeSeedersBasedOnLegacyConfig();
        // Service Groups
        $importReport->presetServiceGroupsImported = $this->serviceGroupImporter->importPreset();
        $importReport->customServiceGroupsImported = $this->serviceGroupImporter->importCustom();
        // Content Blockers
        $importReport->presetContentBlockersImported = $this->contentBlockerImporter->importPreset();
        $importReport->customContentBlockersImported = $this->contentBlockerImporter->importCustom();
        // Services
        $importReport->presetServicesImported = $this->serviceImporter->importPreset();
        $importReport->customServicesImported = $this->serviceImporter->importCustom();
        // Script Blockers
        $importReport->customScriptBlockersImported = $this->scriptBlockerImporter->importCustom();
        // Sort lists
        $importReport->customContentBlockersImported->sortListByPropertyNaturally('name');
        $importReport->customScriptBlockersImported->sortListByPropertyNaturally('name');
        $importReport->customServicesImported->sortListByPropertyNaturally('name');
        $importReport->presetContentBlockersImported->sortListByPropertyNaturally('name');
        $importReport->presetServicesImported->sortListByPropertyNaturally('name');

        $this->packageInstaller->install();
        // Enable Backwards Compatibility JavaScript
        $backwardsCompatibilityConfig = $this->backwardsCompatibilityConfig->get();
        $backwardsCompatibilityConfig->loadBackwardsCompatibilityJavaScript = true;
        $this->backwardsCompatibilityConfig->save($backwardsCompatibilityConfig);
        $this->sendReport($importReport);
        $this->option->set('LegacyImportCompleted', true);

        return $importReport;
    }

    public function isImportCompleted(): bool
    {
        return $this->option->get('LegacyImportCompleted', false)->value === true;
    }

    public function isImportDataAvailable(): bool
    {
        return Database::tableExists($this->wpdb->prefix . $this->contentBlockerImporter::TABLE_NAME)
            && Database::tableExists($this->wpdb->prefix . $this->serviceImporter::TABLE_NAME)
            && Database::tableExists($this->wpdb->prefix . $this->serviceGroupImporter::TABLE_NAME)
            && Database::tableExists($this->wpdb->prefix . $this->scriptBlockerImporter::TABLE_NAME);
    }

    public function shouldImport(): bool
    {
        return (
            ($this->wpFunction->isMultisite() && (bool) $this->option->getGlobal('LegacyAutomaticImport', false)->value === true)
            || (!$this->wpFunction->isMultisite() && (bool) $this->option->get('LegacyAutomaticImport', false)->value === true)
        )
            && !$this->isImportCompleted();
    }

    private function sendReport(ImportReportDto $importReport): void
    {
        $mailRecipient = $this->option->getThirdPartyOption('admin_email', '')->value;
        $templateData = $this->localizationForMailTemplates->replaceLocalizationTags(
            function () use ($importReport) {
                $templateData = [];
                $templateData['localized'] = ThirdPartyImporterLocalizationStrings::get();
                $templateData['localized']['shared'] = SharedMailLocalizationStrings::get();
                $templateData['data']['importStatus'] = $this->isInstallationCompletelySuccessful($importReport->customContentBlockersImported)
                    && $this->isInstallationCompletelySuccessful($importReport->customScriptBlockersImported)
                    && $this->isInstallationCompletelySuccessful($importReport->customServicesImported)
                    && $this->isInstallationCompletelySuccessful($importReport->presetContentBlockersImported)
                    && $this->isInstallationCompletelySuccessful($importReport->presetServicesImported)
                    && $importReport->presetServiceGroupsImported
                    && ($importReport->customServiceGroupsImported ?? true);
                $templateData['data']['importReport'] = $importReport;
                $templateData['localized']['shared']['text']['mailInformation'] = Formatter::interpolate(
                    $templateData['localized']['shared']['text']['mailInformation'],
                    [
                        'websiteName' => $this->wpFunction->getBloginfo('name'),
                        'websiteUrl' => $this->wpFunction->getSiteUrl(),
                    ],
                );
                $templateData['localized']['text']['importStatus'] =
                    Formatter::interpolate(
                        $templateData['localized']['text']['importStatus'],
                        [
                            'importStatus' => $templateData['data']['importStatus'] ? $templateData['localized']['text']['importStatusSuccessful'] : $templateData['localized']['text']['importStatusUnsuccessful'],
                        ],
                    );

                return $templateData;
            },
        );
        $mailBody = $this->template->getEngine()->render(
            'mail/library/third-party-importer-report.html.twig',
            $templateData,
        );
        $this->mailService->sendMail(
            $mailRecipient,
            $templateData['localized']['text']['subject'],
            $mailBody,
        );
    }
}
