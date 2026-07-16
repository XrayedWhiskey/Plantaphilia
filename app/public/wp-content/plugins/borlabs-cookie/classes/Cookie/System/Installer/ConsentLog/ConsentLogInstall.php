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

namespace Borlabs\Cookie\System\Installer\ConsentLog;

use Borlabs\Cookie\Adapter\WpDb;

final class ConsentLogInstall
{
    private WpDb $wpdb;

    public function __construct(WpDb $wpdb)
    {
        $this->wpdb = $wpdb;
    }

    public function createTable(string $prefix = ''): bool
    {
        if (empty($prefix)) {
            $prefix = $this->wpdb->prefix;
        }

        $createResult = $this->wpdb->query(
            '
            CREATE TABLE IF NOT EXISTS ' . $prefix . ConsentLogTableMigration::TABLE . ' (
                `id` bigint(11) unsigned NOT NULL AUTO_INCREMENT,
                `uid` varchar(35) NOT NULL,
                `consents` text NOT NULL,
                `cookie_version` int(11) unsigned NOT NULL,
                `iab_tcf_t_c_string` text NULL,
                `is_latest` tinyint(1) unsigned NOT NULL,
                `stamp` datetime NOT NULL,
                PRIMARY KEY (`id`),
                KEY `uid` (`uid`, `is_latest`)
            ) ' . $this->wpdb->get_charset_collate() . ' ENGINE=INNODB
        ',
        );

        return !($createResult === false);
    }
}
