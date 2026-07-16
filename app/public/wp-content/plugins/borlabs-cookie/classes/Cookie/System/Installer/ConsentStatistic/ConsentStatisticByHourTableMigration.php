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

namespace Borlabs\Cookie\System\Installer\ConsentStatistic;

use Borlabs\Cookie\Adapter\WpDb;
use Borlabs\Cookie\Dto\System\AuditDto;
use Borlabs\Cookie\Support\Database;
use Borlabs\Cookie\System\Log\Log;

final class ConsentStatisticByHourTableMigration
{
    public const TABLE = 'borlabs_cookie_consent_stats_hour';

    private ConsentStatisticByHourInstall $consentStatisticByHourInstall;

    private ConsentStatisticByHourUpgrade $consentStatisticByHourUpgrade;

    private Log $log;

    private WpDb $wpdb;

    public function __construct(
        ConsentStatisticByHourInstall $consentStatisticByHourInstall,
        ConsentStatisticByHourUpgrade $consentStatisticByHourUpgrade,
        Log $log,
        WpDb $wpdb
    ) {
        $this->consentStatisticByHourInstall = $consentStatisticByHourInstall;
        $this->consentStatisticByHourUpgrade = $consentStatisticByHourUpgrade;
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
        if (Database::tableExists($prefix . 'borlabs_cookie_consent_statistic_by_hour') === true && Database::tableExists($prefix . self::TABLE) === false) {
            $renameStatus = $this->wpdb->query('
                RENAME TABLE
                    `' . $prefix . 'borlabs_cookie_consent_statistic_by_hour`
                TO
                    `' . $prefix . self::TABLE . '`
            ');

            if ($renameStatus === false) {
                $this->log->error(
                    'Failed to rename table of ConsentStatisticByHourRepository',
                    [
                        'error' => $this->wpdb->last_error,
                    ],
                );

                return new AuditDto(false, $this->wpdb->last_error);
            }
            $this->log->info('Renamed table of ConsentStatisticByHourRepository successfully');
        }

        if (Database::tableExists($prefix . self::TABLE) === false) {
            $createStatus = $this->consentStatisticByHourInstall->createTable($prefix);

            if ($createStatus === false) {
                return new AuditDto(false, $this->wpdb->last_error);
            }
        }

        $upgradeStatus = $this->consentStatisticByHourUpgrade->upgrade($prefix);

        return new AuditDto($upgradeStatus);
    }
}
