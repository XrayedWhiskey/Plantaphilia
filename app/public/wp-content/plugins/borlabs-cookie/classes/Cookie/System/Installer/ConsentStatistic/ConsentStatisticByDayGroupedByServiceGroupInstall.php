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

final class ConsentStatisticByDayGroupedByServiceGroupInstall
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
            CREATE TABLE IF NOT EXISTS ' . $prefix . ConsentStatisticByDayGroupedByServiceGroupTableMigration::TABLE . ' (
                `id` bigint(11) unsigned NOT NULL AUTO_INCREMENT,
                `service_group_key` varchar(64) NOT NULL,
                `cookie_version` int(11) unsigned NOT NULL,
                `date` date NOT NULL,
                `is_anonymous` tinyint(1) unsigned NOT NULL,
                `count` int(11) unsigned NOT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `service_group_key_date_grouped_by_service_group` (`service_group_key`, `cookie_version`, `date`, `is_anonymous`)
            ) ' . $this->wpdb->get_charset_collate() . ' ENGINE=INNODB
        ',
        );

        return !($createResult === false);
    }
}
