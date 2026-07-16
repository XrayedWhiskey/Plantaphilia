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

namespace Borlabs\Cookie\System\Installer\ServiceGroup;

use Borlabs\Cookie\Adapter\WpDb;

final class ServiceGroupInstall
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
            CREATE TABLE IF NOT EXISTS ' . $prefix . ServiceGroupTableMigration::TABLE . ' (
            `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
            `key` varchar(64) NOT NULL,
            `description` text NOT NULL,
            `language` varchar(16) NOT NULL,
            `name` varchar(255) NOT NULL,
            `position` int(11) unsigned NOT NULL DEFAULT \'1\',
            `pre_selected` tinyint(1) NOT NULL DEFAULT \'0\',
            `status` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
            `undeletable` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
            PRIMARY KEY (`id`),
            UNIQUE KEY `key_language` (`key`,`language`)
        ) ' . $this->wpdb->get_charset_collate() . ' ENGINE=INNODB
        ',
        );

        return !($createResult === false);
    }
}
