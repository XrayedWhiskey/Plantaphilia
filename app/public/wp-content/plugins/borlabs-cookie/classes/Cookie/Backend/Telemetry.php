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

class Telemetry
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

    public function getContentBlocker()
    {
        global $wpdb;

        $activeContentBlocker = $wpdb->get_results(
            'SELECT
                `content_blocker_id`,
                `name`,
                `privacy_policy_url`,
                `hosts`
            FROM
                `' . $wpdb->prefix . 'borlabs_cookie_legacy_content_blocker' . '`
            WHERE
                `status` = 1
            GROUP BY
                `content_blocker_id`
                '
        );
        $contentBlocker = [];

        foreach ($activeContentBlocker as $contentBlockerData) {
            if (in_array($contentBlockerData->content_blocker_id, ['default', 'facebook', 'googlemaps', 'instagram', 'openstreetmap', 'twitter', 'vimeo', 'youtube'], true)) {
                continue;
            }

            $contentBlocker[] = [
                'content_blocker_id' => $contentBlockerData->content_blocker_id,
                'hosts' => unserialize($contentBlockerData->hosts),
                'name' => $contentBlockerData->name,
                'privacy_policy_url' => $contentBlockerData->privacy_policy_url,
            ];
        }

        return $contentBlocker;
    }

    public function getPlugins()
    {
        if (!function_exists('get_plugins')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        $activePlugins = get_option('active_plugins');
        $installedPlugins = get_plugins();
        $plugins = [];

        foreach ($installedPlugins as $slug => $plugin) {
            $plugins[] = [
                'author' => $plugin['Author'],
                'is_enabled' => in_array($slug, $activePlugins, true),
                'name' => $plugin['Name'],
                'plugin_url' => $plugin['PluginURI'],
                'slug' => $slug,
                'text_domain' => $plugin['TextDomain'],
                'version' => $plugin['Version'],
            ];
        }

        return $plugins;
    }

    public function getServices()
    {
        global $wpdb;

        $activeServices = $wpdb->get_results(
            'SELECT
                `cookie_id`,
                `name`,
                `provider`,
                `privacy_policy_url`,
                `hosts`,
                `cookie_name`
            FROM
                `' . $wpdb->prefix . 'borlabs_cookie_legacy_cookies' . '`
            WHERE
                `status` = 1
            GROUP BY
                `cookie_id`
                '
        );
        $services = [];

        foreach ($activeServices as $service) {
            if (in_array($service->cookie_id, ['borlabs-cookie', 'facebook', 'googlemaps', 'instagram', 'openstreetmap', 'twitter', 'vimeo', 'youtube'], true)) {
                continue;
            }

            $cookies = explode(',', $service->cookie_name);
            $services[] = [
                'cookies' => array_filter(
                    array_map(
                        function ($value) {
                            return trim($value);
                        },
                        $cookies
                    ),
                    function ($value) {
                        return strlen($value);
                    }
                ),
                'hosts' => unserialize($service->hosts),
                'name' => $service->name,
                'privacy_policy_url' => $service->privacy_policy_url,
                'provider' => $service->provider,
                'service_id' => $service->cookie_id,
            ];
        }

        return $services;
    }

    public function getTelemetry()
    {
        if (get_option('BorlabsCookieLegacyTelemetryStatus', false) === false) {
            return;
        }

        return [
            'borlabs-cookie-content-blocker' => $this->getContentBlocker(),
            'borlabs-cookie-services' => $this->getServices(),
            'plugins' => $this->getPlugins(),
            'themes' => $this->getThemes(),
        ];
    }

    public function getThemes()
    {
        $activeTheme = wp_get_theme();
        $installedThemes = wp_get_themes();
        $themes = [];

        foreach ($installedThemes as $theme) {
            $themes[] = [
                'author' => $theme->get('Author'),
                'is_childtheme' => strlen((string) $theme->get('Template')) ? true : false,
                'is_enabled' => $activeTheme->template === $theme->template,
                'name' => $theme->get('Name'),
                'template' => $theme->template,
                'text_domain' => $theme->get('TextDomain'),
                'theme_url' => $theme->get('ThemeURI'),
                'version' => $theme->get('Version'),
            ];
        }

        return $themes;
    }
}
