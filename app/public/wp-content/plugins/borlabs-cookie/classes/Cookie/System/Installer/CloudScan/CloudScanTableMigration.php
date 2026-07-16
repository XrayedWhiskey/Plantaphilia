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
use Borlabs\Cookie\Dto\System\AuditDto;
use Borlabs\Cookie\Support\Database;

final class CloudScanTableMigration
{
    public const TABLE = 'borlabs_cookie_cloud_scans';

    private CloudScanInstall $cloudScanInstall;

    private CloudScanUpgrade $cloudScanUpgrade;

    private WpDb $wpdb;

    public function __construct(
        CloudScanInstall $cloudScanInstall,
        CloudScanUpgrade $cloudScanUpgrade,
        WpDb $wpdb
    ) {
        $this->wpdb = $wpdb;
        $this->cloudScanInstall = $cloudScanInstall;
        $this->cloudScanUpgrade = $cloudScanUpgrade;
    }

    /**
     * @param string $prefix optional; Default: `$wpdb->prefix`; Default prefix for the table name
     */
    public function run(string $prefix = ''): AuditDto
    {
        if (empty($prefix)) {
            $prefix = $this->wpdb->prefix;
        }
        $tableName = $prefix . self::TABLE;

        if (!Database::tableExists($tableName)) {
            $createStatus = $this->cloudScanInstall->createTable($prefix);

            if ($createStatus === false) {
                return new AuditDto(false, $this->wpdb->last_error);
            }
        }

        $upgradeStatus = $this->cloudScanUpgrade->upgrade($prefix);

        return new AuditDto($upgradeStatus);
    }
}
