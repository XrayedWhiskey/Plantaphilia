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

namespace Borlabs\Cookie\System\Installer\ScriptBlocker;

use Borlabs\Cookie\Adapter\WpDb;
use Borlabs\Cookie\Dto\System\AuditDto;
use Borlabs\Cookie\Support\Database;

final class ScriptBlockerTableMigration
{
    public const TABLE = 'borlabs_cookie_script_blockers';

    private ScriptBlockerInstall $scriptBlockerInstall;

    private ScriptBlockerUpgrade $scriptBlockerUpgrade;

    private WpDb $wpdb;

    public function __construct(
        ScriptBlockerInstall $scriptBlockerInstall,
        ScriptBlockerUpgrade $scriptBlockerUpgrade,
        WpDb $wpdb
    ) {
        $this->scriptBlockerInstall = $scriptBlockerInstall;
        $this->scriptBlockerUpgrade = $scriptBlockerUpgrade;
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
            $createStatus = $this->scriptBlockerInstall->createTable($prefix);

            if ($createStatus === false) {
                return new AuditDto(false, $this->wpdb->last_error);
            }
        }

        $upgradeStatus = $this->scriptBlockerUpgrade->upgrade($prefix);

        return new AuditDto($upgradeStatus);
    }
}
