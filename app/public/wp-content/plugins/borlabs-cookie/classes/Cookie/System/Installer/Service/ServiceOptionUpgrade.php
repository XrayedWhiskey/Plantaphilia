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

final class ServiceOptionUpgrade
{
    private ServiceOptionInstall $serviceOptionInstall;

    private WpDb $wpdb;

    public function __construct(ServiceOptionInstall $serviceOptionInstall, WpDb $wpdb)
    {
        $this->serviceOptionInstall = $serviceOptionInstall;
        $this->wpdb = $wpdb;
    }

    public function upgrade(string $prefix = ''): bool
    {
        if (empty($prefix)) {
            $prefix = $this->wpdb->prefix;
        }

        $tableName = $prefix . ServiceOptionTableMigration::TABLE;

        if (Database::columnExists('description', $tableName) === false) {
            $this->wpdb->query('DROP TABLE IF EXISTS ' . $tableName);
            $this->serviceOptionInstall->createTable();
        }

        return true;
    }
}
