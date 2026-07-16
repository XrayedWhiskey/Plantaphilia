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

namespace Borlabs\Cookie\System\Installer\IabTcf;

use Borlabs\Cookie\Adapter\WpDb;
use Borlabs\Cookie\Dto\System\AuditDto;
use Borlabs\Cookie\Support\Database;

class VendorTableMigration
{
    public const TABLE = 'borlabs_cookie_iab_tcf_vendors';

    private VendorInstall $vendorInstall;

    private VendorUpgrade $vendorUpgrade;

    private WpDb $wpdb;

    public function __construct(
        VendorInstall $vendorInstall,
        VendorUpgrade $vendorUpgrade,
        WpDb $wpdb
    ) {
        $this->vendorInstall = $vendorInstall;
        $this->vendorUpgrade = $vendorUpgrade;
        $this->wpdb = $wpdb;
    }

    public function run(string $prefix = ''): AuditDto
    {
        if (empty($prefix)) {
            $prefix = $this->wpdb->prefix;
        }

        if (Database::tableExists($prefix . self::TABLE) === false) {
            $createStatus = $this->vendorInstall->createTable($prefix);

            if ($createStatus === false) {
                return new AuditDto(false, $this->wpdb->last_error);
            }
        }

        $upgradeStatus = $this->vendorUpgrade->upgrade($prefix);

        return new AuditDto($upgradeStatus);
    }
}
