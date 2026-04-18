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

namespace BorlabsCookie\Cookie\Frontend;

use BorlabsCookie\Cookie\Config;
use BorlabsCookie\Cookie\Multilanguage;
use BorlabsCookie\Cookie\Tools;

class Cookies
{
    private static $instance;

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private $allCookieGroupsByLanguage = [];

    private $allCookiesByLanguage = [];

    private $cookieGroups = [];

    private $loadedLanguage;

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
     * checkConsent function.
     *
     * @param mixed $cookieId
     */
    public function checkConsent($cookieId)
    {
        $consent = false;

        if (!empty($_COOKIE['borlabs-cookie'])) {
            $borlabsCookie = json_decode(stripslashes($_COOKIE['borlabs-cookie']));

            if (!empty($borlabsCookie->consents)) {
                foreach ($borlabsCookie->consents as $cookiesOfGroup) {
                    if (in_array($cookieId, $cookiesOfGroup, true)) {
                        $consent = true;

                        break;
                    }
                }
            }
        }

        return $consent;
    }

    /**
     * getAllCookieGroups function.
     */
    public function getAllCookieGroups()
    {
        global $wpdb;

        if (empty($this->cookieGroups) || $this->loadedLanguage !== Multilanguage::getInstance()->getCurrentLanguageCode()) {
            $this->cookieGroups = [];
            $this->loadedLanguage = Multilanguage::getInstance()->getCurrentLanguageCode();
            $cookieGroupData = $wpdb->get_results(
                '
                SELECT
                    `id`,
                    `group_id`,
                    `name`,
                    `description`,
                    `pre_selected`,
                    `position`
                FROM
                    `' . $wpdb->prefix . "borlabs_cookie_legacy_groups`
                WHERE
                    `language` = '" . esc_sql(Multilanguage::getInstance()->getCurrentLanguageCode()) . "'
                    AND
                    `status` = 1
                ORDER BY
                    `position` ASC
            "
            );

            if (!empty($cookieGroupData)) {
                foreach ($cookieGroupData as $groupData) {
                    $this->cookieGroups[$groupData->id] = $groupData;
                    $this->cookieGroups[$groupData->id]->cookies = $this->getAllCookiesOfGroup($groupData->id);
                }
            }
        }

        return $this->cookieGroups;
    }

    /**
     * getAllCookieGroups function.
     *
     * @param mixed $language
     */
    public function getAllCookieGroupsOfLanguage($language)
    {
        global $wpdb;

        if (empty($this->allCookieGroupsByLanguage[$language])) {
            $this->allCookieGroupsByLanguage[$language] = [];

            $cookieGroupData = $wpdb->get_results(
                '
                SELECT
                    `group_id`,
                    `name`
                FROM
                    `' . $wpdb->prefix . "borlabs_cookie_legacy_groups`
                WHERE
                    `language` = '" . esc_sql($language) . "'
                    AND
                    `status` = 1
            "
            );

            if (!empty($cookieGroupData)) {
                foreach ($cookieGroupData as $groupData) {
                    $this->allCookieGroupsByLanguage[$language][$groupData->group_id] = $groupData->name;
                }
            }
        }

        return $this->allCookieGroupsByLanguage[$language];
    }

    /**
     * getAllCookiesOfGroup function.
     *
     * @param mixed $id
     */
    public function getAllCookiesOfGroup($id)
    {
        global $wpdb;

        $data = [];

        $cookiesData = $wpdb->get_results(
            '
            SELECT
                `cookie_id`,
                `name`,
                `provider`,
                `purpose`,
                `privacy_policy_url`,
                `hosts`,
                `cookie_name`,
                `cookie_expiry`,
                `opt_in_js`,
                `opt_out_js`,
                `fallback_js`,
                `settings`
            FROM
                `' . $wpdb->prefix . "borlabs_cookie_legacy_cookies`
            WHERE
                `cookie_group_id` = '" . esc_sql($id) . "'
                AND
                `language` = '" . esc_sql(Multilanguage::getInstance()->getCurrentLanguageCode()) . "'
                AND
                `status` = 1
            ORDER BY
                `position` ASC
        "
        );

        if (!empty($cookiesData)) {
            foreach ($cookiesData as $cookieData) {
                $data[$cookieData->cookie_id] = $cookieData;

                if ($cookieData->cookie_id === 'borlabs-cookie') {
                    $cookieBoxImprintLink = '';

                    if (!empty(Config::getInstance()->get('imprintPageURL'))) {
                        $cookieBoxImprintLink = Config::getInstance()->get('imprintPageURL');
                    }

                    if (!empty(Config::getInstance()->get('imprintPageCustomURL'))) {
                        $cookieBoxImprintLink = Config::getInstance()->get('imprintPageCustomURL');
                    }

                    if (!empty($cookieBoxImprintLink)) {
                        $data[$cookieData->cookie_id]->provider .= sprintf(
                            '<span>, </span><a href="%s">%s</a>',
                            $cookieBoxImprintLink,
                            Config::getInstance()->get('cookieBoxTextImprintLink')
                        );
                    }
                }

                $data[$cookieData->cookie_id]->hosts = unserialize($cookieData->hosts);
                $data[$cookieData->cookie_id]->settings = unserialize($cookieData->settings);

                if (!empty($data[$cookieData->cookie_id]->settings)) {
                    $settings = Tools::getInstance()->arrayFlat($data[$cookieData->cookie_id]->settings);

                    $searchKeys = array_keys($settings);
                    array_walk($searchKeys, function (&$value, $key) {
                        $value = '%%' . $value . '%%';
                    });

                    $replaceValues = array_values($settings);

                    $data[$cookieData->cookie_id]->opt_in_js = str_replace(
                        $searchKeys,
                        $replaceValues,
                        $data[$cookieData->cookie_id]->opt_in_js
                    );
                    $data[$cookieData->cookie_id]->opt_out_js = str_replace(
                        $searchKeys,
                        $replaceValues,
                        $data[$cookieData->cookie_id]->opt_out_js
                    );
                    $data[$cookieData->cookie_id]->fallback_js = str_replace(
                        $searchKeys,
                        $replaceValues,
                        $data[$cookieData->cookie_id]->fallback_js
                    );
                }
            }
        }

        return $data;
    }

    /**
     * getAllCookies function.
     *
     * @param mixed $language
     */
    public function getAllCookiesOfLanguage($language)
    {
        global $wpdb;

        if (empty($this->allCookiesByLanguage[$language])) {
            $this->allCookiesByLanguage[$language] = [];

            $cookiesData = $wpdb->get_results(
                '
                SELECT
                    `cookie_id`,
                    `name`
                FROM
                    `' . $wpdb->prefix . "borlabs_cookie_legacy_cookies`
                WHERE
                    `language` = '" . esc_sql($language) . "'
                    AND
                    `status` = 1
            "
            );

            if (!empty($cookiesData)) {
                foreach ($cookiesData as $cookieData) {
                    $this->allCookiesByLanguage[$language][$cookieData->cookie_id] = $cookieData->name;
                }
            }
        }

        return $this->allCookiesByLanguage[$language];
    }

    /**
     * getCookieData function.
     *
     * @param mixed $cookieId
     */
    public function getCookieData($cookieId)
    {
        global $wpdb;

        $data = [];

        $cookieData = $wpdb->get_results(
            '
            SELECT
                c.`cookie_id`,
                c.`name`,
                cg.`group_id`,
                cg.`name` as `cookie_group`
            FROM
                `' . $wpdb->prefix . 'borlabs_cookie_legacy_cookies` as c
            INNER JOIN
                `' . $wpdb->prefix . "borlabs_cookie_legacy_groups` as cg
                ON
                (
                    c.`cookie_id` = '" . esc_sql($cookieId) . "'
                    AND
                    c.`language` = '" . esc_sql(Multilanguage::getInstance()->getCurrentLanguageCode()) . "'
                    AND
                    c.`status` = 1
                    AND
                    cg.`id` = c.`cookie_group_id`
                    AND
                    cg.`language` = '" . esc_sql(Multilanguage::getInstance()->getCurrentLanguageCode()) . "'
                )
        "
        );

        if (!empty($cookieData[0]->cookie_id)) {
            $data = $cookieData[0];
        }

        return $data;
    }
}
