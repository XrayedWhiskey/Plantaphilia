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
use Borlabs\Cookie\Dto\System\AuditDto;
use Borlabs\Cookie\Support\Database;

final class ServiceLocationTableMigration
{
    public const TABLE = 'borlabs_cookie_service_locations';

    private ServiceLocationInstall $serviceLocationInstall;

    private ServiceLocationUpgrade $serviceLocationUpgrade;

    private WpDb $wpdb;

    public function __construct(
        ServiceLocationInstall $serviceLocationInstall,
        ServiceLocationUpgrade $serviceLocationUpgrade,
        WpDb $wpdb
    ) {
        $this->serviceLocationInstall = $serviceLocationInstall;
        $this->serviceLocationUpgrade = $serviceLocationUpgrade;
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
            $createStatus = $this->serviceLocationInstall->createTable($prefix);

            if ($createStatus === false) {
                return new AuditDto(false, $this->wpdb->last_error);
            }
        }

        $upgradeStatus = $this->serviceLocationUpgrade->upgrade($prefix);

        return new AuditDto($upgradeStatus);
    }
}
