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

namespace Borlabs\Cookie\System\Installer\StyleBlocker;

use Borlabs\Cookie\Adapter\WpDb;

final class StyleBlockerInstall
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

        $createResult = $this->wpdb->query(
            '
            CREATE TABLE IF NOT EXISTS ' . $prefix . StyleBlockerTableMigration::TABLE . ' (
                `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `borlabs_service_package_key` varchar(64) NULL,
                `key` varchar(64) NOT NULL,
                `handles` text NULL,
                `name` varchar(255) NOT NULL,
                `phrases` text NULL,
                `status` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
                `undeletable` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
                PRIMARY KEY (`id`),
                INDEX `borlabs_service_package_key` (`borlabs_service_package_key`),
                UNIQUE KEY `key` (`key`)
            ) ' . $this->wpdb->get_charset_collate() . ' ENGINE=INNODB
        ',
        );

        return !($createResult === false);
    }
}
