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

use Borlabs\Cookie\Adapter\WpDb;
use Borlabs\Cookie\Adapter\WpFunction;
use Borlabs\Cookie\Container\Container;
use Borlabs\Cookie\Exception\GenericException;
use Borlabs\Cookie\System\Installer\FileSystem\CacheFolder;
use Borlabs\Cookie\System\Installer\FileSystem\StorageFolder;
use Borlabs\Cookie\System\Language\Language;
use Borlabs\Cookie\System\Option\Option;
use Borlabs\Cookie\System\Package\PackageManager;

class Install
{
    private CacheFolder $cacheFolder;

    private Capabilities $capabilities;

    private Container $container;

    private Language $language;

    private Option $option;

    private PackageManager $packageManager;

    private StorageFolder $storageFolder;

    private WpDb $wpdb;

    private WpFunction $wpFunction;

    public function __construct(
        Container $container,
        CacheFolder $cacheFolder,
        Capabilities $capabilities,
        Language $language,
        Option $option,
        PackageManager $packageManager,
        StorageFolder $storageFolder,
        WpDb $wpdb,
        WpFunction $wpFunction
    ) {
        $this->container = $container;
        $this->cacheFolder = $cacheFolder;
        $this->capabilities = $capabilities;
        $this->language = $language;
        $this->option = $option;
        $this->packageManager = $packageManager;
        $this->storageFolder = $storageFolder;
        $this->wpdb = $wpdb;
        $this->wpFunction = $wpFunction;
    }

    public function pluginActivated(): void
    {
        $report = [];
        $this->language->setInitializationSignal();
        // TODO Check if WPML / Polylang is available at this point
        $this->language->init();
        $this->language->loadTextDomain();
        $blogId = $this->wpFunction->getCurrentBlogId();
        $prefix = $this->wpdb->prefix;
        $report[$blogId] = $this->setup($prefix);

        // Only on main site execute package list update
        try {
            $this->packageManager->updatePackageList();
        } catch (GenericException $e) {
            // Ignore
        }

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
                $report[$site->blog_id] = $this->setup($prefix);
            }
            $this->wpFunction->switchToBlog($blogId);
        }
    }

    private function setup($prefix): array
    {
        $audits = [];
        $audits['table'] = $this->container->get('Borlabs\Cookie\System\Installer\MigrationService')->runDatabaseTableMigrations($prefix);

        // File system
        $audits['filesystem']['cacheFolder'] = $this->cacheFolder->run();
        $audits['filesystem']['storageFolder'] = $this->storageFolder->run();
        // Seeders
        $audits['languageSpecificEntry'] = $this->container->get('Borlabs\Cookie\System\Installer\MigrationService')->runLanguageSpecificSeeder($prefix);
        $audits['nonLanguageSpecificEntry'] = $this->container->get('Borlabs\Cookie\System\Installer\MigrationService')->runNonLanguageSpecificSeeder($prefix);
        // Add capabilities to administrator
        $this->capabilities->add();
        // Update version
        $this->option->set('Version', BORLABS_COOKIE_VERSION);

        return $audits;
    }
}
