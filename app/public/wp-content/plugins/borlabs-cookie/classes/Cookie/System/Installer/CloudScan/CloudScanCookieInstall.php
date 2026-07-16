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

namespace Borlabs\Cookie\System\Installer\CloudScan;

use Borlabs\Cookie\Adapter\WpDb;
use Borlabs\Cookie\Support\Database;

final class CloudScanCookieInstall
{
    private WpDb $wpdb;

    public function __construct(
        WpDb $wpdb
    ) {
        $this->wpdb = $wpdb;
    }

    public function createTable(string $prefix = ''): bool
    {
        if (empty($prefix)) {
            $prefix = $this->wpdb->prefix;
        }

        $foreignKeyName = Database::generateForeignKeyName(
            $prefix . CloudScanCookieTableMigration::TABLE,
            $prefix . CloudScanTableMigration::TABLE,
            'cloud_scan_id',
            $prefix,
        );

        $createResult = $this->wpdb->query(
            '
            CREATE TABLE IF NOT EXISTS ' . $prefix . CloudScanCookieTableMigration::TABLE . ' (
                `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `cloud_scan_id` int(11) unsigned NOT NULL,
                `borlabs_service_package_key` varchar(64) NULL,
                `examples` text NOT NULL,
                `hostname` varchar(255) NOT NULL,
                `lifetime` int(11) unsigned NULL,
                `name` varchar(255) NOT NULL,
                `path` varchar(255) NOT NULL,
                PRIMARY KEY (`id`),
                FOREIGN KEY ' . $foreignKeyName . ' (`cloud_scan_id`)
                    REFERENCES ' . $prefix . CloudScanTableMigration::TABLE . ' (`id`)
            ) ' . $this->wpdb->get_charset_collate() . ' ENGINE=INNODB
        ',
        );

        return !($createResult === false);
    }
}
