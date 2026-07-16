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

final class ServiceTableMigration
{
    public const TABLE = 'borlabs_cookie_services';

    private ServiceInstall $serviceInstall;

    private ServiceUpgrade $serviceUpgrade;

    private WpDb $wpdb;

    public function __construct(
        ServiceInstall $serviceInstall,
        ServiceUpgrade $serviceUpgrade,
        WpDb $wpdb
    ) {
        $this->serviceInstall = $serviceInstall;
        $this->serviceUpgrade = $serviceUpgrade;
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
            $createStatus = $this->serviceInstall->createTable($prefix);

            if ($createStatus === false) {
                return new AuditDto(false, $this->wpdb->last_error);
            }
        }

        $upgradeStatus = $this->serviceUpgrade->upgrade($prefix);

        return new AuditDto($upgradeStatus);
    }
}
