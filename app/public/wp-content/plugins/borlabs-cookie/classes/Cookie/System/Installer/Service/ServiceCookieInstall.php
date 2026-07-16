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

namespace Borlabs\Cookie\System\Installer\Service;

use Borlabs\Cookie\Adapter\WpDb;
use Borlabs\Cookie\Support\Database;

final class ServiceCookieInstall
{
    private WpDb $wpdb;

    public function __construct(WpDb $wpdb)
    {
        $this->wpdb = $wpdb;
    }

    public function createTable(string $prefix = ''): bool
    {
        if (empty($prefix)) {
            $prefix = $this->wpdb->prefix;
        }

        $foreignKeyName = Database::generateForeignKeyName(
            $prefix . ServiceCookieTableMigration::TABLE,
            $prefix . ServiceTableMigration::TABLE,
            'service_id',
            $prefix,
        );

        $createResult = $this->wpdb->query(
            '
            CREATE TABLE IF NOT EXISTS ' . $prefix . ServiceCookieTableMigration::TABLE . ' (
                `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `service_id` int(11) unsigned NOT NULL,
                `description` text NULL,
                `hostname` varchar(255) NULL,
                `lifetime` varchar(255) NOT NULL DEFAULT \'\',
                `name` varchar(255) NOT NULL,
                `path` varchar(255) NULL,
                `purpose` varchar(64) NOT NULL,
                `type` varchar(64) NOT NULL,
                PRIMARY KEY (`id`),
                KEY `service_id` (`service_id`),
                FOREIGN KEY ' . $foreignKeyName . ' (`service_id`)
                    REFERENCES ' . $prefix . ServiceTableMigration::TABLE . ' (`id`)
            ) ' . $this->wpdb->get_charset_collate() . ' ENGINE=INNODB
        ',
        );

        return !($createResult === false);
    }
}
