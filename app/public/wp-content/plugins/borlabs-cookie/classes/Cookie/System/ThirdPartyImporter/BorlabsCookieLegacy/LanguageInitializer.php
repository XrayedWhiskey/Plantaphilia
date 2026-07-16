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
use Borlabs\Cookie\Container\Container;
use Borlabs\Cookie\Support\Database;
use Borlabs\Cookie\System\Log\Log;

final class LanguageInitializer
{
    private Container $container;

    private ContentBlockerImporter $contentBlockerImporter;

    private Log $log;

    private ServiceGroupImporter $serviceGroupImporter;

    private WpDb $wpdb;

    public function __construct(
        Container $container,
        ContentBlockerImporter $contentBlockerImporter,
        Log $log,
        ServiceGroupImporter $serviceGroupImporter,
        WpDb $wpdb
    ) {
        $this->container = $container;
        $this->contentBlockerImporter = $contentBlockerImporter;
        $this->log = $log;
        $this->serviceGroupImporter = $serviceGroupImporter;
        $this->wpdb = $wpdb;
    }

    public function executeSeedersBasedOnLegacyConfig(): void
    {
        $prefix = $this->wpdb->prefix;

        if (!Database::tableExists($this->wpdb->prefix . $this->contentBlockerImporter::TABLE_NAME) || !Database::tableExists($this->wpdb->prefix . $this->serviceGroupImporter::TABLE_NAME)) {
            return;
        }

        $contentBlockerLanguages = $this->wpdb->get_results('
            SELECT
                `language`
            FROM
                `' . $this->wpdb->prefix . $this->contentBlockerImporter::TABLE_NAME . '`
            GROUP BY
                `language`
       ');
        $serviceGroupsLanguages = $this->wpdb->get_results('
            SELECT
                `language`
            FROM
                `' . $this->wpdb->prefix . $this->serviceGroupImporter::TABLE_NAME . '`
            GROUP BY
                `language`
       ');

        $languages = array_merge(
            array_column($contentBlockerLanguages ?? [], 'language'),
            array_column($serviceGroupsLanguages ?? [], 'language'),
        );

        $this->log->info('Run Language Seeder', ['languages' => $languages]);

        foreach ($languages as $languageCode) {
            $this->container->get('Borlabs\Cookie\System\Installer\MigrationService')->runLanguageSpecificSeeder($prefix, $languageCode);
        }
    }
}
