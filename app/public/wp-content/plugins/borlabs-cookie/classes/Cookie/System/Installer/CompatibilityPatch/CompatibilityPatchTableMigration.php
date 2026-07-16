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

namespace Borlabs\Cookie\System\Installer\CompatibilityPatch;

use Borlabs\Cookie\Adapter\WpDb;
use Borlabs\Cookie\Dto\System\AuditDto;
use Borlabs\Cookie\Support\Database;

final class CompatibilityPatchTableMigration
{
    public const TABLE = 'borlabs_cookie_compatibility_patches';

    private CompatibilityPatchInstall $compatibilityPatchInstall;

    private CompatibilityPatchUpgrade $compatibilityPatchUpgrade;

    private WpDb $wpdb;

    public function __construct(
        CompatibilityPatchInstall $compatibilityPatchInstall,
        CompatibilityPatchUpgrade $compatibilityPatchUpgrade,
        WpDb $wpdb
    ) {
        $this->compatibilityPatchInstall = $compatibilityPatchInstall;
        $this->compatibilityPatchUpgrade = $compatibilityPatchUpgrade;
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
            $createStatus = $this->compatibilityPatchInstall->createTable($prefix);

            if ($createStatus === false) {
                return new AuditDto(false, $this->wpdb->last_error);
            }
        }

        $upgradeStatus = $this->compatibilityPatchUpgrade->upgrade($prefix);

        return new AuditDto($upgradeStatus);
    }
}
