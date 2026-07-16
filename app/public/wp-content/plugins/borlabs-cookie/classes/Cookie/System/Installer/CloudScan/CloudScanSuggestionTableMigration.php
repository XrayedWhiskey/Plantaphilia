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
use Borlabs\Cookie\System\Log\Log;

final class CloudScanSuggestionTableMigration
{
    public const TABLE = 'borlabs_cookie_cloud_scan_suggests';

    private CloudScanSuggestionInstall $cloudScanSuggestionInstall;

    private CloudScanSuggestionUpgrade $cloudScanSuggestionUpgrade;

    private Log $log;

    private WpDb $wpdb;

    public function __construct(
        CloudScanSuggestionInstall $cloudScanSuggestionInstall,
        CloudScanSuggestionUpgrade $cloudScanSuggestionUpgrade,
        Log $log,
        WpDb $wpdb
    ) {
        $this->cloudScanSuggestionInstall = $cloudScanSuggestionInstall;
        $this->cloudScanSuggestionUpgrade = $cloudScanSuggestionUpgrade;
        $this->log = $log;
        $this->wpdb = $wpdb;
    }

    /**
     * @param string $prefix optional; Default: `$wpdb->prefix`; Default prefix for the table name
     */
    public function run(string $prefix = ''): AuditDto
    {
        if (empty($prefix)) {
            $prefix = $this->wpdb->prefix;
        }

        // Rename table
        if (Database::tableExists($prefix . 'borlabs_cookie_cloud_scan_suggestions') === true && Database::tableExists($prefix . self::TABLE) === false) {
            $renameStatus = $this->wpdb->query('
                RENAME TABLE
                    `' . $prefix . 'borlabs_cookie_cloud_scan_suggestions`
                TO
                    `' . $prefix . self::TABLE . '`
            ');

            if ($renameStatus === false) {
                $this->log->error(
                    'Failed to rename table of CloudScanSuggestionRepository',
                    [
                        'error' => $this->wpdb->last_error,
                    ],
                );

                return new AuditDto(false, $this->wpdb->last_error);
            }
            $this->log->info('Renamed table of CloudScanSuggestionRepository successfully');
        }

        if (Database::tableExists($prefix . self::TABLE) === false) {
            $createStatus = $this->cloudScanSuggestionInstall->createTable($prefix);

            if ($createStatus === false) {
                return new AuditDto(false, $this->wpdb->last_error);
            }
        }

        $upgradeStatus = $this->cloudScanSuggestionUpgrade->upgrade($prefix);

        return new AuditDto($upgradeStatus);
    }
}
