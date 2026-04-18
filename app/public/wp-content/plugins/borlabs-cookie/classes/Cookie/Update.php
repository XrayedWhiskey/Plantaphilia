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

use Plugin_Upgrader;
use stdClass;

class Update
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

    public function handleAutomaticUpdateStatus()
    {
        $upgradeStatus = (bool) (is_multisite() ? get_site_option('BorlabsCookieLegacyAutomaticImport', false) : get_option('BorlabsCookieLegacyAutomaticImport', false));
        $autoUpdatePluginsList = get_option('auto_update_plugins', []);
        $shouldUpdateOption = false;

        if (!is_array($autoUpdatePluginsList)) {
            $autoUpdatePluginsList = [];
        }

        // Remove from auto_update_plugins list
        if ($upgradeStatus === false) {
            if (in_array(BORLABS_COOKIE_BASENAME, $autoUpdatePluginsList, true)) {
                $index = array_search(BORLABS_COOKIE_BASENAME, $autoUpdatePluginsList, true);

                if ($index !== false) {
                    unset($autoUpdatePluginsList[$index]);
                    sort($autoUpdatePluginsList);
                    $shouldUpdateOption = true;
                }
            }
        } else {
            if (!in_array(BORLABS_COOKIE_BASENAME, $autoUpdatePluginsList, true)) {
                $autoUpdatePluginsList[] = BORLABS_COOKIE_BASENAME;
                $shouldUpdateOption = true;
            }
        }

        // Update WordPress auto_update_plugins option
        if ($shouldUpdateOption) {
            update_option('auto_update_plugins', $autoUpdatePluginsList);
        }
    }

    /**
     * handlePluginAPI function.
     *
     * @param mixed  $result Default is false
     * @param string $action Type of information
     * @param object $args   Plugin API arguments
     */
    public function handlePluginAPI($result, $action, $args)
    {
        if (!empty($action) && $action == 'plugin_information' && !empty($args->slug)) {
            if ($args->slug == BORLABS_COOKIE_SLUG) {
                // Return alternative API URL
                $upgradeStatus = (bool) (is_multisite() ? get_site_option('BorlabsCookieLegacyAutomaticImport', false) : get_option('BorlabsCookieLegacyAutomaticImport', false));
                $borlabsCookieVersion = $upgradeStatus ? '3.1.999' : BORLABS_COOKIE_VERSION;

                $result = API::getInstance()->getPluginInformation($borlabsCookieVersion);

                // Fallback if 3.2 is not available but the website requires a 3.2 upgrade
                if ($upgradeStatus && !empty($result) && version_compare('3.2', $result->version, '>')) {
                    $result = API::getInstance()->getPluginInformation();
                }
            }
        }

        return $result;
    }

    /**
     * handleTransientUpdatePlugins function.
     *
     * @param mixed $transient
     */
    public function handleTransientUpdatePlugins($transient)
    {
        // This happens during the upgrade process from  >=2.3.0 to >=3.2.0
        if (!class_exists('\BorlabsCookie\Cookie\API')) {
            return $transient;
        }

        // If info is already available
        if (isset($transient->response[BORLABS_COOKIE_BASENAME])) {
            return $transient;
        }

        // Check for updates
        $upgradeStatus = (bool) (is_multisite() ? get_site_option('BorlabsCookieLegacyAutomaticImport', false) : get_option('BorlabsCookieLegacyAutomaticImport', false));
        $borlabsCookieVersion = $upgradeStatus ? '3.1.999' : BORLABS_COOKIE_VERSION;

        $updateInformation = API::getInstance()->getLatestVersion($borlabsCookieVersion);

        // Fallback if 3.2.0 is not available but the website requires a 3.2.0 upgrade
        if ($upgradeStatus && !empty($updateInformation) && version_compare('3.2', $updateInformation->new_version, '>')) {
            $updateInformation = API::getInstance()->getLatestVersion();
        }

        if (!empty($updateInformation)) {
            if (version_compare(BORLABS_COOKIE_VERSION, $updateInformation->new_version, '<')) {
                // $transient can be null if third party plugins force a plugin refresh an kill the object
                if (!is_object($transient) && !isset($transient->response)) {
                    $transient = new stdClass();
                    $transient->response = [];
                }
                $transient->response[BORLABS_COOKIE_BASENAME] = $updateInformation;
            }
        }

        return $transient;
    }

    /**
     * processUpgrade function.
     */
    public function processUpgrade()
    {
        global $wpdb;

        $lastVersion = get_option('BorlabsCookieLegacyVersion', false);

        if (!$lastVersion) {
            $lastVersion = get_option('BorlabsCookieVersion', false);
        }

        if (is_multisite()) {
            $allBlogs = $wpdb->get_results(
                '
                SELECT
                    `blog_id`
                FROM
                    `' . $wpdb->base_prefix . 'blogs`
            '
            );
        }

        $versionUpgrades = Upgrade::getInstance()->getVersionUpgrades();

        if (!empty($lastVersion)) {
            foreach ($versionUpgrades as $upgradeFunction => $version) {
                if (version_compare($lastVersion, $version, '<')) {
                    if (method_exists(Upgrade::getInstance(), $upgradeFunction)) {
                        // Call upgrade function
                        call_user_func([Upgrade::getInstance(), $upgradeFunction]);

                        // Upgrade multisites
                        if (is_multisite() && !empty($allBlogs)) {
                            $originalBlogId = get_current_blog_id();

                            foreach ($allBlogs as $blogData) {
                                if ($blogData->blog_id != 1) {
                                    switch_to_blog($blogData->blog_id);

                                    $this->currentBlogId = $blogData->blog_id;

                                    call_user_func([Upgrade::getInstance(), $upgradeFunction]);

                                    switch_to_blog($originalBlogId);
                                }
                            }

                            // Just in case we use this value at some later point
                            $this->currentBlogId = $originalBlogId;
                        }
                    }
                }
            }
        }
    }

    /**
     * upgradeComplete function.
     *
     * @param mixed $upgraderObject
     * @param mixed $options
     */
    public function upgradeComplete($upgraderObject, $options)
    {
        if ($upgraderObject instanceof Plugin_Upgrader === false) {
            return;
        }

        if (!isset($upgraderObject->result['source_files']) || !in_array(basename(BORLABS_COOKIE_BASENAME), $upgraderObject->result['source_files'], true)) {
            return;
        }

        $this->processUpgrade();
    }
}
