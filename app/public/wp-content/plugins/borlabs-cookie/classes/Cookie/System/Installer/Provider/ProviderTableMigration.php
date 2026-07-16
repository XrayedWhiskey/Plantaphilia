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

namespace Borlabs\Cookie\System\Installer\Provider;

use Borlabs\Cookie\Adapter\WpDb;
use Borlabs\Cookie\Dto\System\AuditDto;
use Borlabs\Cookie\Support\Database;

final class ProviderTableMigration
{
    public const TABLE = 'borlabs_cookie_providers';

    private ProviderInstall $providerInstall;

    private ProviderUpgrade $providerUpgrade;

    private WpDb $wpdb;

    public function __construct(
        ProviderInstall $providerInstall,
        ProviderUpgrade $providerUpgrade,
        WpDb $wpdb
    ) {
        $this->providerInstall = $providerInstall;
        $this->providerUpgrade = $providerUpgrade;
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
            $createStatus = $this->providerInstall->createTable($prefix);

            if ($createStatus === false) {
                return new AuditDto(false, $this->wpdb->last_error);
            }
        }

        $upgradeStatus = $this->providerUpgrade->upgrade($prefix);

        return new AuditDto($upgradeStatus);
    }
}
