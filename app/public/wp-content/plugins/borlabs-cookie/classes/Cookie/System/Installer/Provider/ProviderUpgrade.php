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

namespace Borlabs\Cookie\System\Installer\Provider;

use Borlabs\Cookie\Adapter\WpDb;
use Borlabs\Cookie\Support\Database;

final class ProviderUpgrade
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

        $tableName = $prefix . ProviderTableMigration::TABLE;

        if (Database::columnExists('borlabs_service_package_key', $tableName) === false) {
            $this->wpdb->query('ALTER TABLE `' . $tableName . '` ADD `borlabs_service_package_key` varchar(64) NULL AFTER `id`');
            $this->wpdb->query('ALTER TABLE `' . $tableName . '` ADD INDEX `borlabs_service_package_key` (`borlabs_service_package_key`)');
        }

        return true;
    }
}
