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

namespace Borlabs\Cookie\System\Installer\ContentBlocker;

use Borlabs\Cookie\Adapter\WpDb;
use Borlabs\Cookie\Dto\System\AuditDto;
use Borlabs\Cookie\Support\Database;

final class ContentBlockerLocationTableMigration
{
    public const TABLE = 'borlabs_cookie_content_blocker_locations';

    private ContentBlockerLocationInstall $contentBlockerLocationInstall;

    private ContentBlockerLocationUpgrade $contentBlockerLocationUpgrade;

    private WpDb $wpdb;

    public function __construct(
        ContentBlockerLocationInstall $contentBlockerLocationInstall,
        ContentBlockerLocationUpgrade $contentBlockerLocationUpgrade,
        WpDb $wpdb
    ) {
        $this->contentBlockerLocationInstall = $contentBlockerLocationInstall;
        $this->contentBlockerLocationUpgrade = $contentBlockerLocationUpgrade;
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
            $createStatus = $this->contentBlockerLocationInstall->createTable($prefix);

            if ($createStatus === false) {
                return new AuditDto(false, $this->wpdb->last_error);
            }
        }

        $upgradeStatus = $this->contentBlockerLocationUpgrade->upgrade($prefix);

        return new AuditDto($upgradeStatus);
    }
}
