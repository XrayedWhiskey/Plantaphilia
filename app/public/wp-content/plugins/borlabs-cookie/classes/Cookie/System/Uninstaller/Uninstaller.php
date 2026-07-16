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

namespace Borlabs\Cookie\System\Uninstaller;

use Borlabs\Cookie\Adapter\WpDb;
use Borlabs\Cookie\Adapter\WpFunction;
use Borlabs\Cookie\System\FileSystem\FileManager;
use Borlabs\Cookie\System\Option\Option;
use Borlabs\Cookie\System\Uninstaller\CloudScan\CloudScanUninstall;
use Borlabs\Cookie\System\Uninstaller\CompatibilityPatch\CompatibilityPatchUninstall;
use Borlabs\Cookie\System\Uninstaller\ConsentLog\ConsentLogUninstall;
use Borlabs\Cookie\System\Uninstaller\ConsentStatistic\ConsentStatisticUninstall;
use Borlabs\Cookie\System\Uninstaller\ContentBlocker\ContentBlockerUninstall;
use Borlabs\Cookie\System\Uninstaller\Country\CountryUninstall;
use Borlabs\Cookie\System\Uninstaller\IabTcf\VendorUninstall;
use Borlabs\Cookie\System\Uninstaller\Job\JobUninstall;
use Borlabs\Cookie\System\Uninstaller\Log\LogUninstall;
use Borlabs\Cookie\System\Uninstaller\Package\PackageUninstall;
use Borlabs\Cookie\System\Uninstaller\Provider\ProviderUninstall;
use Borlabs\Cookie\System\Uninstaller\ScriptBlocker\ScriptBlockerUninstall;
use Borlabs\Cookie\System\Uninstaller\Service\ServiceUninstall;
use Borlabs\Cookie\System\Uninstaller\ServiceGroup\ServiceGroupUninstall;
use Borlabs\Cookie\System\Uninstaller\StyleBlocker\StyleBlockerUninstall;

class Uninstaller
{
    private CloudScanUninstall $cloudScanUninstall;

    private CompatibilityPatchUninstall $compatibilityPatchUninstall;

    private ConsentLogUninstall $consentLogUninstall;

    private ConsentStatisticUninstall $consentStatisticUninstall;

    private ContentBlockerUninstall $contentBlockerUninstall;

    private CountryUninstall $countryUninstall;

    private FileManager $fileManager;

    private JobUninstall $jobUninstall;

    private LogUninstall $logUninstall;

    private Option $option;

    private PackageUninstall $packageUninstall;

    private ProviderUninstall $providerUninstall;

    private ScriptBlockerUninstall $scriptBlockerUninstall;

    private ServiceGroupUninstall $serviceGroupUninstall;

    private ServiceUninstall $serviceUninstall;

    private StyleBlockerUninstall $styleBlockerUninstall;

    private VendorUninstall $vendorUninstall;

    private WpDb $wpdb;

    private WpFunction $wpFunction;

    public function __construct(
        CloudScanUninstall $cloudScanUninstall,
        CompatibilityPatchUninstall $compatibilityPatchUninstall,
        ConsentLogUninstall $consentLogUninstall,
        ConsentStatisticUninstall $consentStatisticUninstall,
        ContentBlockerUninstall $contentBlockerUninstall,
        CountryUninstall $countryUninstall,
        FileManager $fileManager,
        JobUninstall $jobUninstall,
        LogUninstall $logUninstall,
        Option $option,
        PackageUninstall $packageUninstall,
        ProviderUninstall $providerUninstall,
        ScriptBlockerUninstall $scriptBlockerUninstall,
        ServiceGroupUninstall $serviceGroupUninstall,
        ServiceUninstall $serviceUninstall,
        StyleBlockerUninstall $styleBlockerUninstall,
        VendorUninstall $vendorUninstall,
        WpDb $wpdb,
        WpFunction $wpFunction
    ) {
        $this->cloudScanUninstall = $cloudScanUninstall;
        $this->compatibilityPatchUninstall = $compatibilityPatchUninstall;
        $this->consentLogUninstall = $consentLogUninstall;
        $this->consentStatisticUninstall = $consentStatisticUninstall;
        $this->contentBlockerUninstall = $contentBlockerUninstall;
        $this->countryUninstall = $countryUninstall;
        $this->fileManager = $fileManager;
        $this->jobUninstall = $jobUninstall;
        $this->logUninstall = $logUninstall;
        $this->option = $option;
        $this->packageUninstall = $packageUninstall;
        $this->providerUninstall = $providerUninstall;
        $this->scriptBlockerUninstall = $scriptBlockerUninstall;
        $this->serviceGroupUninstall = $serviceGroupUninstall;
        $this->serviceUninstall = $serviceUninstall;
        $this->styleBlockerUninstall = $styleBlockerUninstall;
        $this->vendorUninstall = $vendorUninstall;
        $this->wpdb = $wpdb;
        $this->wpFunction = $wpFunction;
    }

    // Todo
    public function run()
    {
        $report = [];
        $blogId = $this->wpFunction->getCurrentBlogId();
        $prefix = $this->wpdb->prefix;

        $report[$blogId] = $this->remove($prefix);

        if (!$this->wpFunction->isMultisite()) {
            return;
        }

        $sites = $this->wpFunction->getSites();

        if (count($sites) === 0) {
            return;
        }

        foreach ($sites as $site) {
            if ($site->blog_id !== 1) {
                $this->wpFunction->switchToBlog((int) $site->blog_id);
                $prefix = $this->wpdb->prefix;
                $report[$site->blog_id] = $this->remove($prefix);
            }
            $this->wpFunction->switchToBlog($blogId);
        }
    }

    private function remove($prefix): array
    {
        $report = [];
        // With dependencies
        $report['component']['cloudScan'] = $this->cloudScanUninstall->uninstall($prefix);
        $report['component']['contentBlocker'] = $this->contentBlockerUninstall->uninstall($prefix);
        $report['component']['service'] = $this->serviceUninstall->uninstall($prefix);
        $report['component']['provider'] = $this->providerUninstall->uninstall($prefix);
        $report['component']['serviceGroup'] = $this->serviceGroupUninstall->uninstall($prefix);

        // Without dependencies
        $report['component']['compatibilityPatch'] = $this->compatibilityPatchUninstall->uninstall($prefix);
        $report['component']['consentLog'] = $this->consentLogUninstall->uninstall($prefix);
        $report['component']['consentStatistic'] = $this->consentStatisticUninstall->uninstall($prefix);
        $report['component']['country'] = $this->countryUninstall->uninstall($prefix);
        $report['component']['iabtcf'] = $this->vendorUninstall->uninstall($prefix);
        $report['component']['package'] = $this->packageUninstall->uninstall($prefix);
        $report['component']['scriptBlocker'] = $this->scriptBlockerUninstall->uninstall($prefix);
        $report['component']['styleBlocker'] = $this->styleBlockerUninstall->uninstall($prefix);
        $report['component']['job'] = $this->jobUninstall->uninstall($prefix);
        $report['component']['log'] = $this->logUninstall->uninstall($prefix);

        // Delete cache and storage folders
        $this->fileManager->deleteCacheFolder();
        $this->fileManager->deleteStorageFolder();
        $this->fileManager->deleteGlobalCacheFolder();
        $this->fileManager->deleteGlobalStorageFolder();

        // Delete all options
        $this->wpdb->query('
            DELETE FROM
                `' . $prefix . 'options`
            WHERE
                `option_name` LIKE \'' . $this->option::OPTION_PREFIX . '%\'
                AND
                `option_name` NOT LIKE \'' . $this->option::OPTION_PREFIX . 'Legacy%\'
        ');

        return $report;
    }
}
