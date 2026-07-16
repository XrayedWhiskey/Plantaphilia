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

final class VendorInstall
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
            CREATE TABLE IF NOT EXISTS ' . $prefix . VendorTableMigration::TABLE . ' (
                `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `vendor_id` int(11) unsigned NOT NULL,
                `cookie_max_age_seconds` int(11) unsigned NOT NULL,
                `data_declaration` text NOT NULL,
                `data_retention` text NOT NULL,
                `device_storage_disclosure_url` varchar(255) NOT NULL DEFAULT \'\',
                `features` text NOT NULL,
                `leg_int_purposes` text NOT NULL,
                `name` varchar(255) NOT NULL,
                `purposes` text NOT NULL,
                `special_features` text NOT NULL,
                `special_purposes` text NOT NULL,
                `urls` text NOT NULL,
                `uses_cookies` tinyint(1) unsigned NOT NULL,
                `uses_non_cookie_access` tinyint(1) unsigned NOT NULL,
                `status` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
                PRIMARY KEY (`id`),
                UNIQUE KEY `vendor_id` (`vendor_id`)
            ) ' . $this->wpdb->get_charset_collate() . ' ENGINE=INNODB
        ',
        );

        return !($createResult === false);
    }
}
