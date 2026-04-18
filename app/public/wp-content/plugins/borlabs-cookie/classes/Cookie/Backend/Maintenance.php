<?php
/*
 * ----------------------------------------------------------------------
 *
 *                          Borlabs Cookie
 *                    developed by Borlabs GmbH
 *
 * ----------------------------------------------------------------------
 *
 * Copyright 2018-2022 Borlabs GmbH. All rights reserved.
 * This file may not be redistributed in whole or significant part.
 * Content of this file is protected by international copyright laws.
 *
 * ----------------- Borlabs Cookie IS NOT FREE SOFTWARE -----------------
 *
 * @copyright Borlabs GmbH, https://borlabs.io
 * @author Benjamin A. Bornschein
 *
 */

namespace BorlabsCookie\Cookie\Backend;

use BorlabsCookie\Cookie\Config;

class Maintenance
{
    private static $instance;

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function __construct()
    {
    }

    public function __clone()
    {
        trigger_error('Cloning is not allowed.', E_USER_ERROR);
    }

    public function __wakeup()
    {
        trigger_error('Unserialize is forbidden.', E_USER_ERROR);
    }

    /**
     * cleanUp function.
     *
     * @param bool $optimizeTable (default: false)
     */
    public function cleanUp($optimizeTable = false)
    {
        global $wpdb;

        $table = (Config::getInstance()->get('aggregateCookieConsent') ? $wpdb->base_prefix : $wpdb->prefix)
            . 'borlabs_cookie_legacy_consent_log';
        $cookieLifetime = Config::getInstance()->get('cookieLifetime');

        // Delete old entries
        $wpdb->query(
            '
            DELETE FROM
                `' . $table . '`
            WHERE
                `stamp` < NOW() - INTERVAL ' . (int) $cookieLifetime . ' DAY
        '
        );

        // Delete old statistic entries
        $tableStatistics = $wpdb->prefix . 'borlabs_cookie_legacy_statistics';
        $wpdb->query('
            DELETE FROM
                `' . $tableStatistics . '`
            WHERE
                `stamp` < NOW() - INTERVAL 60 DAY
        ');

        // Optimize
        if ($optimizeTable === true) {
            $wpdb->query('OPTIMIZE TABLE `' . $table . '`');
            $wpdb->query('OPTIMIZE TABLE `' . $tableStatistics . '`');
        }
    }
}
