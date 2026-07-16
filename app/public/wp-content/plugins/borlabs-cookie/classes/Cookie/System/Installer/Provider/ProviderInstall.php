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

final class ProviderInstall
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
            CREATE TABLE IF NOT EXISTS ' . $prefix . ProviderTableMigration::TABLE . ' (
                `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `borlabs_service_package_key` varchar(64) NULL,
                `borlabs_service_provider_key` varchar(64) NULL,
                `iab_vendor_id` int(11) unsigned NULL,
                `key` varchar(64) NOT NULL,
                `address` varchar(255) NOT NULL DEFAULT \'\',
                `cookie_url` varchar(255) NOT NULL DEFAULT \'\',
                `description` text NOT NULL,
                `language` varchar(16) NOT NULL,
                `name` varchar(255) NOT NULL,
                `opt_out_url` varchar(255) NOT NULL DEFAULT \'\',
                `partners` text NULL,
                `privacy_url` varchar(255) NOT NULL DEFAULT \'\',
                `undeletable` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
                PRIMARY KEY (`id`),
                INDEX (`borlabs_service_package_key`),
                UNIQUE KEY `key_language` (`key`, `language`),
                UNIQUE KEY `iab_vendor_id_language` (`iab_vendor_id`, `language`)
            ) ' . $this->wpdb->get_charset_collate() . ' ENGINE=INNODB
        ',
        );

        return !($createResult === false);
    }
}
