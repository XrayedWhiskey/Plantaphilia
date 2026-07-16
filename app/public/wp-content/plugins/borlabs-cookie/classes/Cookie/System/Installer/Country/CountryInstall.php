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

namespace Borlabs\Cookie\System\Installer\Country;

use Borlabs\Cookie\Adapter\WpDb;

final class CountryInstall
{
    private WpDb $wpdb;

    public function __construct(
        WpDb $wpdb
    ) {
        $this->wpdb = $wpdb;
    }

    public function createTable(string $prefix = ''): bool
    {
        if (empty($prefix)) {
            $prefix = $this->wpdb->prefix;
        }

        $createResult = $this->wpdb->query(
            '
            CREATE TABLE ' . $prefix . CountryTableMigration::TABLE . ' (
                `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `country_code` varchar(3) NOT NULL,
                `is_european_union` tinyint(1) unsigned NOT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `country_code` (`country_code`)
            ) ' . $this->wpdb->get_charset_collate() . ' ENGINE=INNODB
        ',
        );

        return !($createResult === false);
    }
}
