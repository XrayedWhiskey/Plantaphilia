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

namespace BorlabsCookie\Cookie;

use DirectoryIterator;

class Uninstall
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
     * deleteFilesInDirectory function.
     *
     * @param mixed $dir
     */
    public function deleteFilesInDirectory($dir)
    {
        if (file_exists($dir)) {
            foreach (new DirectoryIterator($dir) as $fileInfo) {
                // Ignore . and ..
                if (!$fileInfo->isDot()) {
                    // If folder, delete files in folder
                    if (!$fileInfo->isDir()) {
                        unlink($fileInfo->getPathname());
                    }
                }
            }
        }
    }

    /**
     * isDirectoryEmpty function.
     *
     * @param mixed $dir
     */
    public function isDirectoryEmpty($dir)
    {
        $isEmpty = true;

        if (file_exists($dir)) {
            foreach (new DirectoryIterator($dir) as $fileInfo) {
                if (!$fileInfo->isDot()) {
                    $isEmpty = false;
                }
            }
        } else {
            // In case something bad happens
            $isEmpty = false;
        }

        return $isEmpty;
    }

    /**
     * uninstallPlugin function.
     */
    public function uninstallPlugin()
    {
        global $wpdb;

        // Remove cached files. This has to be executed before the tables are removed, otherwise we get an error due the code in __construct() of ContentBlocker
        $cacheFolder = Frontend\ContentBlocker::getInstance()->getCacheFolder();

        $this->deleteFilesInDirectory($cacheFolder);

        if ($this->isDirectoryEmpty($cacheFolder)) {
            rmdir($cacheFolder);
        }

        if (is_multisite()) {
            $allBlogs = $wpdb->get_results(
                '
                SELECT
                    `blog_id`
                FROM
                    `' . $wpdb->prefix . 'blogs`
            '
            );

            if (!empty($allBlogs)) {
                $originalBlogId = get_current_blog_id();

                foreach ($allBlogs as $blogData) {
                    $tableNameCookies = $wpdb->base_prefix . $blogData->blog_id . '_borlabs_cookie_legacy_cookies';
                    $tableNameCookieGroups = $wpdb->base_prefix . $blogData->blog_id . '_borlabs_cookie_legacy_groups';
                    $tableNameCookieConsentLog = $wpdb->base_prefix . $blogData->blog_id . '_borlabs_cookie_legacy_consent_log';
                    $tableNameContentBlocker = $wpdb->base_prefix . $blogData->blog_id . '_borlabs_cookie_legacy_content_blocker';
                    $tableNameScriptBlocker = $wpdb->base_prefix . $blogData->blog_id . '_borlabs_cookie_legacy_script_blocker';
                    $tableNameStatistics = $wpdb->base_prefix . $blogData->blog_id . '_borlabs_cookie_legacy_statistics';

                    $wpdb->query('DROP TABLE IF EXISTS `' . $tableNameCookies . '`');
                    $wpdb->query('DROP TABLE IF EXISTS `' . $tableNameCookieGroups . '`');
                    $wpdb->query('DROP TABLE IF EXISTS `' . $tableNameCookieConsentLog . '`');
                    $wpdb->query('DROP TABLE IF EXISTS `' . $tableNameContentBlocker . '`');
                    $wpdb->query('DROP TABLE IF EXISTS `' . $tableNameScriptBlocker . '`');
                    $wpdb->query('DROP TABLE IF EXISTS `' . $tableNameStatistics . '`');

                    switch_to_blog($blogData->blog_id);

                    // Find Borlabs Cookie Options and delete them
                    $borlabsCookieOptions = $wpdb->get_results(
                        '
                        SELECT
                            `option_name`
                        FROM
                            `' . $wpdb->options . "`
                        WHERE
                            `option_name` LIKE 'BorlabsCookieLegacy%'
                    "
                    );

                    if (!empty($borlabsCookieOptions)) {
                        foreach ($borlabsCookieOptions as $optionData) {
                            delete_option($optionData->option_name);
                        }
                    }

                    // Remove Cron
                    wp_clear_scheduled_hook('borlabsCookieCron');

                    // Remove user capabilities
                    Install::getInstance()->removeUserCapabilities();
                }

                switch_to_blog($originalBlogId);
            }

            // Find Borlabs Cookie Options in sitemeta and delete them
            $borlabsCookieOptions = $wpdb->get_results(
                '
                SELECT
                    `meta_key`
                FROM
                    `' . $wpdb->sitemeta . "`
                WHERE
                    `meta_key` LIKE 'BorlabsCookieLegacy%'
            "
            );

            if (!empty($borlabsCookieOptions)) {
                foreach ($borlabsCookieOptions as $optionData) {
                    delete_site_option($optionData->meta_key);
                }
            }
        } else {
            // Find Borlabs Cookie Options and delete them
            $borlabsCookieOptions = $wpdb->get_results(
                '
                SELECT
                    `option_name`
                FROM
                    `' . $wpdb->options . "`
                WHERE
                    `option_name` LIKE 'BorlabsCookieLegacy%'
            "
            );

            if (!empty($borlabsCookieOptions)) {
                foreach ($borlabsCookieOptions as $optionData) {
                    delete_option($optionData->option_name);
                }
            }

            // Remove Cron
            wp_clear_scheduled_hook('borlabsCookieCron');

            // Remove user capabilities
            Install::getInstance()->removeUserCapabilities();
        }

        $tableNameCookies = $wpdb->prefix . 'borlabs_cookie_legacy_cookies';
        $tableNameCookieGroups = $wpdb->prefix . 'borlabs_cookie_legacy_groups';
        $tableNameCookieConsentLog = $wpdb->prefix . 'borlabs_cookie_legacy_consent_log';
        $tableNameContentBlocker = $wpdb->prefix . 'borlabs_cookie_legacy_content_blocker';
        $tableNameScriptBlocker = $wpdb->prefix . 'borlabs_cookie_legacy_script_blocker';
        $tableNameStatistics = $wpdb->prefix . 'borlabs_cookie_legacy_statistics';

        $wpdb->query('DROP TABLE IF EXISTS `' . $tableNameCookies . '`');
        $wpdb->query('DROP TABLE IF EXISTS `' . $tableNameCookieGroups . '`');
        $wpdb->query('DROP TABLE IF EXISTS `' . $tableNameCookieConsentLog . '`');
        $wpdb->query('DROP TABLE IF EXISTS `' . $tableNameContentBlocker . '`');
        $wpdb->query('DROP TABLE IF EXISTS `' . $tableNameScriptBlocker . '`');
        $wpdb->query('DROP TABLE IF EXISTS `' . $tableNameStatistics . '`');
    }
}
