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

namespace Borlabs\Cookie\System\Installer\ServiceGroup;

use Borlabs\Cookie\Adapter\WpDb;
use Borlabs\Cookie\Dto\System\AuditDto;
use Borlabs\Cookie\Support\Database;

final class ServiceGroupTableMigration
{
    public const TABLE = 'borlabs_cookie_service_groups';

    private ServiceGroupInstall $serviceGroupInstall;

    private ServiceGroupUpgrade $serviceGroupUpgrade;

    private WpDb $wpdb;

    public function __construct(
        ServiceGroupInstall $serviceGroupInstall,
        ServiceGroupUpgrade $serviceGroupUpgrade,
        WpDb $wpdb
    ) {
        $this->serviceGroupInstall = $serviceGroupInstall;
        $this->serviceGroupUpgrade = $serviceGroupUpgrade;
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

        if (Database::tableExists($prefix . self::TABLE) === false) {
            $createStatus = $this->serviceGroupInstall->createTable($prefix);

            if ($createStatus === false) {
                return new AuditDto(false, $this->wpdb->last_error);
            }
        }

        $upgradeStatus = $this->serviceGroupUpgrade->upgrade($prefix);

        return new AuditDto($upgradeStatus);
    }
}
