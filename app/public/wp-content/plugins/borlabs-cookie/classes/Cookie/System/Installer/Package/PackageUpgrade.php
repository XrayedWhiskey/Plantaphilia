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
use Borlabs\Cookie\Support\Database;

final class PackageUpgrade
{
    private WpDb $wpdb;

    public function __construct(WpDb $wpdb)
    {
        $this->wpdb = $wpdb;
    }

    public function upgrade(string $prefix = ''): bool
    {
        if (empty($prefix)) {
            $prefix = $this->wpdb->prefix;
        }

        $tableName = $prefix . PackageTableMigration::TABLE;

        if (Database::columnExists('is_deprecated', $tableName) === false) {
            $this->wpdb->query('ALTER TABLE `' . $tableName . '` ADD `is_deprecated` tinyint(1) unsigned NOT NULL DEFAULT \'0\' AFTER `installed_at`');
        }

        if (Database::columnExists('borlabs_service_package_successor_key', $tableName) === false) {
            $this->wpdb->query('ALTER TABLE `' . $tableName . '` ADD `borlabs_service_package_successor_key` varchar(64) NOT NULL DEFAULT \'\' AFTER `borlabs_service_package_key`');
        }

        if (Database::columnExists('auto_update_overwrite_translation', $tableName) === false) {
            $this->wpdb->query('ALTER TABLE `' . $tableName . '` ADD `auto_update_overwrite_translation` tinyint(1) unsigned NOT NULL DEFAULT \'1\' AFTER `borlabs_service_updated_at`');
        }

        if (Database::columnExists('auto_update_overwrite_code', $tableName) === false) {
            $this->wpdb->query('ALTER TABLE `' . $tableName . '` ADD `auto_update_overwrite_code` tinyint(1) unsigned NOT NULL DEFAULT \'1\' AFTER `borlabs_service_updated_at`');
        }

        if (Database::columnExists('auto_update_enabled', $tableName) === false) {
            $this->wpdb->query('ALTER TABLE `' . $tableName . '` ADD `auto_update_enabled` tinyint(1) unsigned NOT NULL DEFAULT \'0\' AFTER `borlabs_service_updated_at`');
        }

        if (Database::columnExists('required_borlabs_cookie_version', $tableName) === false) {
            $this->wpdb->query('ALTER TABLE `' . $tableName . '` ADD `required_borlabs_cookie_version` varchar(64) NOT NULL DEFAULT \'{"major":3,"minor":1,"patch":9,"hotfix":0}\' AFTER `name`');
        }

        return true;
    }
}
