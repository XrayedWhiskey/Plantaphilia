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

namespace Borlabs\Cookie\System\Installer\Package;

use Borlabs\Cookie\Adapter\WpDb;

final class PackageInstall
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
            CREATE TABLE IF NOT EXISTS ' . $prefix . PackageTableMigration::TABLE . ' (
                `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `borlabs_service_package_key` varchar(64) NOT NULL,
                `borlabs_service_package_successor_key` varchar(64) NOT NULL DEFAULT \'\',
                `borlabs_service_package_version` varchar(64) NOT NULL,
                `borlabs_service_updated_at` datetime DEFAULT NULL,
                `auto_update_enabled` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
                `auto_update_overwrite_code` tinyint(1) unsigned NOT NULL DEFAULT \'1\',
                `auto_update_overwrite_translation` tinyint(1) unsigned NOT NULL DEFAULT \'1\',
                `components` mediumtext NOT NULL,
                `installed_at` datetime DEFAULT NULL,
                `is_deprecated` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
                `is_featured` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
                `name` varchar(255) NOT NULL,
                `required_borlabs_cookie_version` varchar(64) NOT NULL DEFAULT \'{"major":3,"minor":1,"patch":9,"hotfix":0}\',
                `thumbnail` varchar(255) NOT NULL DEFAULT \'\',
                `translations` mediumtext NOT NULL,
                `type` varchar(32) NOT NULL,
                `updated_at` datetime DEFAULT NULL,
                `version` varchar(64) NOT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `borlabs_service_package_key` (`borlabs_service_package_key`)
            ) ' . $this->wpdb->get_charset_collate() . ' ENGINE=INNODB
        ',
        );

        return !($createResult === false);
    }
}
