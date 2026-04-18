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

use autoptimizeCache;
use BorlabsCookie\Cookie\Backend\CSS;
use GlobIterator;

class Upgrade
{
    private static $instance;

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private $currentBlogId = '';

    private $versionUpgrades
        = [
            'upgradeVersion_2_3_0' => '2.3.0',
        ];

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
     * clearCache function.
     */
    public function clearCache()
    {
        Log::getInstance()->info(__METHOD__, 'Clear cache after upgrade');

        // Borlabs Cookie - CSS
        if (file_exists(WP_CONTENT_DIR . '/cache/borlabs-cookie/')) {
            $iterator = new GlobIterator(WP_CONTENT_DIR . '/cache/borlabs-cookie/borlabs-cookie_*.css');

            if ($iterator->count()) {
                foreach ($iterator as $fileInfo) {
                    if (is_writable($fileInfo->getPathname())) {
                        unlink($fileInfo->getPathname());
                    }
                }
            }
        }

        // Autoptimize
        if (class_exists('\autoptimizeCache')) {
            Log::getInstance()->info(__METHOD__, 'Clear cache of Autoptimize');

            autoptimizeCache::clearall();
        }

        // Borlabs Cache
        if (class_exists('\Borlabs\Cache\Frontend\Garbage')) {
            Log::getInstance()->info(__METHOD__, 'Clear cache of Borlabs Cache');

            \Borlabs\Cache\Frontend\Garbage::getInstance()->clearStylesPreCacheFiles();

            \Borlabs\Cache\Frontend\Garbage::getInstance()->clearCache();
        }

        // WP Fastest Cache
        if (function_exists('wpfc_clear_all_cache')) {
            Log::getInstance()->info(__METHOD__, 'Clear cache of WP Fastest Cache');

            wpfc_clear_all_cache(true);
        }

        // WP Rocket
        if (function_exists('rocket_clean_domain')) {
            Log::getInstance()->info(__METHOD__, 'Clear cache of WP Rocket');

            rocket_clean_domain();
        }

        // WP Super Cache
        if (function_exists('wp_cache_clean_cache')) {
            global $file_prefix;

            if (isset($file_prefix)) {
                Log::getInstance()->info(__METHOD__, 'Clear cache of WP Super Cache');

                wp_cache_clean_cache($file_prefix);
            }
        }

        update_option('BorlabsCookieLegacyClearCache', false, 'no');

        Log::getInstance()->info(__METHOD__, 'Cache cleared');
    }

    /**
     * getVersionUpgrades function.
     */
    public function getVersionUpgrades()
    {
        return $this->versionUpgrades;
    }

    public function upgradeVersion_2_3_0()
    {
        global $wpdb;

        if (!defined('BORLABS_COOKIE_DEBUG')) {
            define('BORLABS_COOKIE_DEBUG', true);
        }

        $options = [];
        $allBorlabsCookieOptions = $wpdb->get_results('
            SELECT
                `option_id`,
                `option_name`
            FROM
                `' . $wpdb->options . '`
            WHERE
                `option_name` LIKE \'BorlabsCookie%\'
        ');

        foreach ($allBorlabsCookieOptions as $optionData) {
            if (strpos($optionData->option_name, 'Legacy') !== false) {
                continue;
            }

            $wpdb->query(
                'UPDATE
                `' . $wpdb->options . '`
                SET
                    `option_name` = REPLACE(`option_name`, \'BorlabsCookie\', \'BorlabsCookieLegacy\')
                WHERE
                `option_id` = ' . $optionData->option_id
            );
        }

        $oldNewTableMapping = [
            $wpdb->prefix . 'borlabs_cookie_consent_log' => $wpdb->prefix . 'borlabs_cookie_legacy_consent_log',
            $wpdb->prefix . 'borlabs_cookie_content_blocker' => $wpdb->prefix . 'borlabs_cookie_legacy_content_blocker',
            $wpdb->prefix . 'borlabs_cookie_cookies' => $wpdb->prefix . 'borlabs_cookie_legacy_cookies',
            $wpdb->prefix . 'borlabs_cookie_groups' => $wpdb->prefix . 'borlabs_cookie_legacy_groups',
            $wpdb->prefix . 'borlabs_cookie_script_blocker' => $wpdb->prefix . 'borlabs_cookie_legacy_script_blocker',
            $wpdb->prefix . 'borlabs_cookie_statistics' => $wpdb->prefix . 'borlabs_cookie_legacy_statistics',
        ];

        foreach ($oldNewTableMapping as $oldTableName => $newTableName) {
            if (Install::getInstance()->checkIfTableExists($oldTableName) === true && Install::getInstance()->checkIfTableExists($newTableName) === false) {
                $wpdb->query('RENAME TABLE ' . $oldTableName . ' TO ' . $newTableName);
            }
        }

        update_option('BorlabsCookieLegacyClearCache', true, 'yes');
        update_option('BorlabsCookieLegacyVersion', '2.3.0', 'yes');
        Log::getInstance()->info(__METHOD__, 'Upgrade complete: 2.3.0');
    }

    private function getConfigs()
    {
        global $wpdb;

        $configs = [];
        $allConfigs = $wpdb->get_results('
            SELECT
                `option_name`
            FROM
                `' . $wpdb->options . '`
            WHERE
                `option_name` LIKE \'BorlabsCookieConfig_%\'
        ');

        foreach ($allConfigs as $optionData) {
            $configs[$optionData->option_name] = str_replace('BorlabsCookieLegacyConfig_', '', $optionData->option_name);
        }

        return $configs;
    }

    private function getLanguageCodes()
    {
        $languageCodes = [];

        // Polylang
        if (defined('POLYLANG_VERSION')) {
            $polylangLanguages = get_terms('language', ['hide_empty' => false]);

            if (!empty($polylangLanguages)) {
                foreach ($polylangLanguages as $languageData) {
                    if (!empty($languageData->slug) && is_string($languageData->slug)) {
                        $languageCodes[$languageData->slug] = $languageData->slug;
                    }
                }
            }
        }

        // WPML
        if (defined('ICL_LANGUAGE_CODE')) {
            $wpmlLanguages = apply_filters('wpml_active_languages', null, []);

            if (!empty($wpmlLanguages)) {
                foreach ($wpmlLanguages as $languageData) {
                    if (!empty($languageData['code'])) {
                        $languageCodes[$languageData['code']] = $languageData['code'];
                    }
                }
            }
        }

        // Weglot
        if (function_exists('weglot_get_original_language') && function_exists('weglot_get_destination_languages')) {
            $originalLanguageCode = weglot_get_original_language();
            $languageCodes = array_merge($languageCodes, [
                $originalLanguageCode => $originalLanguageCode,
            ]);

            foreach (weglot_get_destination_languages() as $destination) {
                $languageCodes = array_merge($languageCodes, [
                    $destination['language_to'] => $destination['language_to'],
                ]);
            }
        }

        return $languageCodes;
    }
}
