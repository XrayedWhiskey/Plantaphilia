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

namespace Borlabs\Cookie\System\Uninstaller\CloudScan;

use Borlabs\Cookie\Adapter\WpDb;
use Borlabs\Cookie\Repository\CloudScan\CloudScanCookieRepository;
use Borlabs\Cookie\Repository\CloudScan\CloudScanExternalResourceRepository;
use Borlabs\Cookie\Repository\CloudScan\CloudScanRepository;
use Borlabs\Cookie\Repository\CloudScan\CloudScanSuggestionRepository;

final class CloudScanUninstall
{
    private WpDb $wpdb;

    public function __construct(
        WpDb $wpdb
    ) {
        $this->wpdb = $wpdb;
    }

    public function uninstall(string $prefix = ''): bool
    {
        if (empty($prefix)) {
            $prefix = $this->wpdb->prefix;
        }

        return
            $this->wpdb->query('DROP TABLE IF EXISTS ' . $prefix . CloudScanCookieRepository::TABLE)
            && $this->wpdb->query('DROP TABLE IF EXISTS ' . $prefix . CloudScanExternalResourceRepository::TABLE)
            && $this->wpdb->query('DROP TABLE IF EXISTS ' . $prefix . CloudScanSuggestionRepository::TABLE)
            && $this->wpdb->query('DROP TABLE IF EXISTS ' . $prefix . CloudScanRepository::TABLE);
    }
}
