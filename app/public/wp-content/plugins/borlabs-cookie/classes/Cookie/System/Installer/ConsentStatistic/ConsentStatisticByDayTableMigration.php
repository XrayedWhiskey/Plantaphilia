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

final class ConsentStatisticByDayTableMigration
{
    public const TABLE = 'borlabs_cookie_consent_stats_day';

    private ConsentStatisticByDayInstall $consentStatisticByDayInstall;

    private ConsentStatisticByDayUpgrade $consentStatisticByDayUpgrade;

    private Log $log;

    private WpDb $wpdb;

    public function __construct(
        ConsentStatisticByDayInstall $consentStatisticByDayInstall,
        ConsentStatisticByDayUpgrade $consentStatisticByDayUpgrade,
        Log $log,
        WpDb $wpdb
    ) {
        $this->consentStatisticByDayInstall = $consentStatisticByDayInstall;
        $this->consentStatisticByDayUpgrade = $consentStatisticByDayUpgrade;
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
        if (Database::tableExists($prefix . 'borlabs_cookie_consent_statistic_by_day') === true && Database::tableExists($prefix . self::TABLE) === false) {
            $renameStatus = $this->wpdb->query('
                RENAME TABLE
                    `' . $prefix . 'borlabs_cookie_consent_statistic_by_day`
                TO
                    `' . $prefix . self::TABLE . '`
            ');

            if ($renameStatus === false) {
                $this->log->error(
                    'Failed to rename table of ConsentStatisticByDayRepository',
                    [
                        'error' => $this->wpdb->last_error,
                    ],
                );

                return new AuditDto(false, $this->wpdb->last_error);
            }
            $this->log->info('Renamed table of ConsentStatisticByDayRepository successfully');
        }

        if (Database::tableExists($prefix . self::TABLE) === false) {
            $createStatus = $this->consentStatisticByDayInstall->createTable($prefix);

            if ($createStatus === false) {
                return new AuditDto(false, $this->wpdb->last_error);
            }
        }

        $upgradeStatus = $this->consentStatisticByDayUpgrade->upgrade($prefix);

        return new AuditDto($upgradeStatus);
    }
}
