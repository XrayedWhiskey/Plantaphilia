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
use BorlabsCookie\Cookie\Multilanguage;
use BorlabsCookie\Cookie\Tools;

/*
Elaine: What does he do?
George: He's an importer.
Elaine: Just imports? No exports?
George: (getting irritated) He's an importer-exporter. Okay?
 */

class ImportExport
{
    private static $instance;

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * tableContentBlocker.
     *
     * @var mixed
     */
    private $tableContentBlocker;

    /**
     * tableCookie.
     *
     * @var mixed
     */
    private $tableCookie;

    /**
     * tableCookieGroup.
     *
     * @var mixed
     */
    private $tableCookieGroup;

    /**
     * tableScriptBlocker.
     *
     * @var mixed
     */
    private $tableScriptBlocker;

    public function __construct()
    {
        global $wpdb;

        $this->tableContentBlocker = $wpdb->prefix . 'borlabs_cookie_legacy_content_blocker';
        $this->tableCookie = $wpdb->prefix . 'borlabs_cookie_legacy_cookies';
        $this->tableCookieGroup = $wpdb->prefix . 'borlabs_cookie_legacy_groups';
        $this->tableScriptBlocker = $wpdb->prefix . 'borlabs_cookie_legacy_script_blocker';
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
     * display function.
     */
    public function display()
    {
        $action = false;

        if (!empty($_POST['action'])) {
            $action = $_POST['action'];
        }

        if ($action !== false) {
            // Import Settings
            if ($action === 'import' && check_admin_referer('borlabs_cookie_import')) {
                $importStatus = $this->import($_POST);

                if ($importStatus['config'] === true) {
                    Messages::getInstance()->add(
                        _x(
                            'Import <strong>General Settings & Appearance Settings</strong> successfully.',
                            'Backend / Import Export / Alert Message',
                            'borlabs-cookie'
                        ),
                        'success'
                    );
                } elseif ($importStatus['config'] === false) {
                    Messages::getInstance()->add(
                        _x(
                            'Import <strong>General Settings & Appearance Settings</strong> failed. Invalid data.',
                            'Backend / Import Export / Alert Message',
                            'borlabs-cookie'
                        ),
                        'error'
                    );
                }

                if ($importStatus['cookiesAndGroups'] === true) {
                    Messages::getInstance()->add(
                        _x(
                            'Import <strong>Cookies & Cookie Groups</strong> successfully.',
                            'Backend / Import Export / Alert Message',
                            'borlabs-cookie'
                        ),
                        'success'
                    );
                } elseif ($importStatus['cookiesAndGroups'] === false) {
                    Messages::getInstance()->add(
                        _x(
                            'Import <strong>Cookies & Cookie Groups</strong> failed. Invalid data.',
                            'Backend / Import Export / Alert Message',
                            'borlabs-cookie'
                        ),
                        'error'
                    );
                }

                if ($importStatus['contentBlocker'] === true) {
                    Messages::getInstance()->add(
                        _x(
                            'Import <strong>Content Blocker</strong> successfully.',
                            'Backend / Import Export / Alert Message',
                            'borlabs-cookie'
                        ),
                        'success'
                    );
                } elseif ($importStatus['contentBlocker'] === false) {
                    Messages::getInstance()->add(
                        _x(
                            'Import <strong>Content Blocker</strong> failed. Invalid data.',
                            'Backend / Import Export / Alert Message',
                            'borlabs-cookie'
                        ),
                        'error'
                    );
                }

                if ($importStatus['scriptBlocker'] === true) {
                    Messages::getInstance()->add(
                        _x(
                            'Import <strong>Script Blocker</strong> successfully.',
                            'Backend / Import Export / Alert Message',
                            'borlabs-cookie'
                        ),
                        'success'
                    );
                } elseif ($importStatus['contentBlocker'] === false) {
                    Messages::getInstance()->add(
                        _x(
                            'Import <strong>Script Blocker</strong> failed. Invalid data.',
                            'Backend / Import Export / Alert Message',
                            'borlabs-cookie'
                        ),
                        'error'
                    );
                }
            }
        }

        $this->displayOverview();
    }

    public function displayOverview()
    {
        $textareaConfig = esc_textarea(json_encode(['config' => Config::getInstance()->get()]));
        $textareaCookiesAndGroups = esc_textarea(json_encode($this->getAllCookiesAndGroups()));
        $textareaContentBlocker = esc_textarea(json_encode($this->getAllContentBlocker()));
        $textareaScriptBlocker = esc_textarea(json_encode($this->getAllScriptBlocker()));

        include Backend::getInstance()->templatePath . '/import-export.html.php';
    }

    /**
     * getAllContentBlocker function.
     */
    public function getAllContentBlocker()
    {
        global $wpdb;

        $data = [];

        $contentBlockerData = $wpdb->get_results(
            '
            SELECT
                `id`,
                `content_blocker_id`,
                `language`,
                `name`,
                `description`,
                `privacy_policy_url`,
                `hosts`,
                `preview_html`,
                `preview_css`,
                `global_js`,
                `init_js`,
                `settings`,
                `status`,
                `undeletable`
            FROM
                `' . $this->tableContentBlocker . "`
            WHERE
                `language` = '" . esc_sql(Multilanguage::getInstance()->getCurrentLanguageCode()) . "'
        "
        );

        $data['contentBlocker'] = $contentBlockerData;

        return $data;
    }

    /**
     * getAllCookiesAndGroups function.
     */
    public function getAllCookiesAndGroups()
    {
        global $wpdb;

        $data = [];

        $cookieGroupsData = $wpdb->get_results(
            '
            SELECT
                `id`,
                `group_id`,
                `language`,
                `name`,
                `description`,
                `pre_selected`,
                `position`,
                `status`,
                `undeletable`
            FROM
                `' . $this->tableCookieGroup . "`
            WHERE
                `language` = '" . esc_sql(Multilanguage::getInstance()->getCurrentLanguageCode()) . "'
        "
        );

        $data['cookieGroups'] = $cookieGroupsData;

        $cookiesData = $wpdb->get_results(
            '
            SELECT
                `cookie_id`,
                `language`,
                `cookie_group_id`,
                `service`,
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
                `settings`,
                `position`,
                `status`,
                `undeletable`
            FROM
                `' . $this->tableCookie . "`
            WHERE
                `language` = '" . esc_sql(Multilanguage::getInstance()->getCurrentLanguageCode()) . "'
        "
        );

        $data['cookies'] = $cookiesData;

        return $data;
    }

    /**
     * getAllScriptBlocker function.
     */
    public function getAllScriptBlocker()
    {
        global $wpdb;

        $data = [];

        $scriptBlockerData = $wpdb->get_results(
            '
            SELECT
                `id`,
                `script_blocker_id`,
                `name`,
                `handles`,
                `js_block_phrases`,
                `status`,
                `undeletable`
            FROM
                `' . $this->tableScriptBlocker . '`
        '
        );

        $data['scriptBlocker'] = $scriptBlockerData;

        return $data;
    }

    /**
     * import function.
     *
     * @param mixed $formData
     */
    public function import($formData)
    {
        $importStatus = [
            'config' => null,
            'cookiesAndGroups' => null,
            'contentBlocker' => null,
            'scriptBlocker' => null,
        ];

        // Test import config data
        if (!empty($formData['importConfig'])) {
            $formData['importConfig'] = stripslashes($formData['importConfig']);

            if (Tools::getInstance()->isStringJSON($formData['importConfig'])) {
                $importConfig = json_decode($formData['importConfig'], true);

                // Check what kind of import data was sent
                if (!empty($importConfig['config'])) {
                    $this->importConfig($importConfig['config']);

                    $importStatus['config'] = true;
                } else {
                    $importStatus['config'] = false;
                }
            } else {
                $importStatus['config'] = false;
            }
        }

        // Test import cookies and groups data
        if (!empty($formData['importCookiesAndGroups'])) {
            $formData['importCookiesAndGroups'] = stripslashes($formData['importCookiesAndGroups']);

            if (Tools::getInstance()->isStringJSON($formData['importCookiesAndGroups'])) {
                $importCookiesAndGroups = json_decode($formData['importCookiesAndGroups'], true);

                // Check what kind of import data was sent
                if (!empty($importCookiesAndGroups['cookieGroups']) && !empty($importCookiesAndGroups['cookies'])) {
                    $this->importCookiesAndGroups($importCookiesAndGroups);

                    $importStatus['cookiesAndGroups'] = true;
                } else {
                    $importStatus['cookiesAndGroups'] = false;
                }
            } else {
                $importStatus['cookiesAndGroups'] = false;
            }
        }

        // Test import content blocker data
        if (!empty($formData['importContentBlocker'])) {
            $formData['importContentBlocker'] = stripslashes($formData['importContentBlocker']);

            if (Tools::getInstance()->isStringJSON($formData['importContentBlocker'])) {
                $importContentBlocker = json_decode($formData['importContentBlocker'], true);

                // Check what kind of import data was sent
                if (!empty($importContentBlocker['contentBlocker'])) {
                    $this->importContentBlocker($importContentBlocker['contentBlocker']);

                    $importStatus['contentBlocker'] = true;
                } else {
                    $importStatus['contentBlocker'] = false;
                }
            } else {
                $importStatus['contentBlocker'] = false;
            }
        }

        // Test import script blocker data
        if (!empty($formData['importScriptBlocker'])) {
            $formData['importScriptBlocker'] = stripslashes($formData['importScriptBlocker']);

            if (Tools::getInstance()->isStringJSON($formData['importScriptBlocker'])) {
                $importScriptBlocker = json_decode($formData['importScriptBlocker'], true);

                // Check what kind of import data was sent
                if (!empty($importScriptBlocker['scriptBlocker'])) {
                    $this->importScriptBlocker($importScriptBlocker['scriptBlocker']);

                    $importStatus['scriptBlocker'] = true;
                } else {
                    $importStatus['scriptBlocker'] = false;
                }
            } else {
                $importStatus['scriptBlocker'] = false;
            }
        }

        return $importStatus;
    }

    /**
     * importConfig function.
     *
     * @param mixed $data
     */
    public function importConfig($data)
    {
        // Obtain data type
        $data['cookieStatus'] = !empty($data['cookieStatus']) ? true : false;
        $data['setupMode'] = !empty($data['setupMode']) ? true : false;
        $data['cookieBeforeConsent'] = !empty($data['cookieBeforeConsent']) ? true : false;
        $data['aggregateCookieConsent'] = !empty($data['aggregateCookieConsent']) ? true : false;
        $data['cookiesForBots'] = !empty($data['cookiesForBots']) ? true : false;
        $data['respectDoNotTrack'] = !empty($data['respectDoNotTrack']) ? true : false;
        $data['reloadAfterConsent'] = !empty($data['reloadAfterConsent']) ? true : false;
        $data['automaticCookieDomainAndPath'] = !empty($data['automaticCookieDomainAndPath']) ? true : false;
        $data['cookieLifetime'] = (int) ($data['cookieLifetime']);
        $data['cookieLifetimeEssentialOnly'] = (int) ($data['cookieLifetimeEssentialOnly']);
        $data['showCookieBox'] = !empty($data['showCookieBox']) ? true : false;
        $data['showCookieBoxOnLoginPage'] = !empty($data['showCookieBoxOnLoginPage']) ? true : false;
        $data['cookieBoxBlocksContent'] = !empty($data['cookieBoxBlocksContent']) ? true : false;
        $data['cookieBoxHideRefuseOption'] = !empty($data['cookieBoxHideRefuseOption']) ? true : false;
        $data['privacyPageId'] = (int) ($data['privacyPageId']);
        $data['imprintPageId'] = (int) ($data['imprintPageId']);
        $data['supportBorlabsCookie'] = !empty($data['supportBorlabsCookie']) ? true : false;
        $data['cookieBoxShowAcceptAllButton'] = !empty($data['cookieBoxShowAcceptAllButton']) ? true : false;
        $data['cookieBoxIgnorePreSelectStatus'] = !empty($data['cookieBoxIgnorePreSelectStatus']) ? true : false;
        $data['cookieBoxAnimation'] = !empty($data['cookieBoxAnimation']) ? true : false;
        $data['cookieBoxAnimationDelay'] = !empty($data['cookieBoxAnimationDelay']) ? true : false;
        $data['cookieBoxShowLogo'] = !empty($data['cookieBoxShowLogo']) ? true : false;
        $data['cookieBoxFontSize'] = (int) ($data['cookieBoxFontSize']);
        $data['cookieBoxBorderRadius'] = (int) ($data['cookieBoxBorderRadius']);
        $data['cookieBoxBtnBorderRadius'] = (int) ($data['cookieBoxBtnBorderRadius']);
        $data['cookieBoxAccordionBorderRadius'] = (int) ($data['cookieBoxAccordionBorderRadius']);
        $data['cookieBoxTableBorderRadius'] = (int) ($data['cookieBoxTableBorderRadius']);
        $data['cookieBoxBtnFullWidth'] = !empty($data['cookieBoxBtnFullWidth']) ? true : false;
        $data['cookieBoxBtnSwitchRound'] = !empty($data['cookieBoxBtnSwitchRound']) ? true : false;
        $data['removeIframesInFeeds'] = !empty($data['removeIframesInFeeds']) ? true : false;
        $data['contentBlockerFontSize'] = (int) ($data['contentBlockerFontSize']);
        $data['contentBlockerBgOpacity'] = (int) ($data['contentBlockerBgOpacity']);
        $data['contentBlockerBtnBorderRadius'] = (int) ($data['contentBlockerBtnBorderRadius']);
        $data['testEnvironment'] = !empty($data['testEnvironment']) ? true : false;

        // Save config
        Config::getInstance()->saveConfig($data);

        // Update CSS File
        CSS::getInstance()->save();
    }

    /**
     * importContentBlocker function.
     *
     * @param mixed $data
     */
    public function importContentBlocker($data)
    {
        global $wpdb;

        $language = Multilanguage::getInstance()->getCurrentLanguageCode();

        foreach ($data as $contentBlockerData) {
            $wpdb->query(
                '
                INSERT INTO
                    `' . $this->tableContentBlocker . "`
                    (
                        `content_blocker_id`,
                        `language`,
                        `name`,
                        `description`,
                        `privacy_policy_url`,
                        `hosts`,
                        `preview_html`,
                        `preview_css`,
                        `global_js`,
                        `init_js`,
                        `settings`,
                        `status`,
                        `undeletable`
                    )
                VALUES
                    (
                        '" . esc_sql($contentBlockerData['content_blocker_id']) . "',
                        '" . esc_sql($language) . "',
                        '" . esc_sql($contentBlockerData['name']) . "',
                        '" . esc_sql($contentBlockerData['description']) . "',
                        '" . esc_sql($contentBlockerData['privacy_policy_url']) . "',
                        '" . esc_sql($contentBlockerData['hosts']) . "',
                        '" . esc_sql($contentBlockerData['preview_html']) . "',
                        '" . esc_sql($contentBlockerData['preview_css']) . "',
                        '" . esc_sql($contentBlockerData['global_js']) . "',
                        '" . esc_sql($contentBlockerData['init_js']) . "',
                        '" . esc_sql($contentBlockerData['settings']) . "',
                        '" . ((int) ($contentBlockerData['status']) ? 1 : 0) . "',
                        '" . ((int) ($contentBlockerData['undeletable']) ? 1 : 0) . "'
                    )
                ON DUPLICATE KEY UPDATE
                    `name` = VALUES(`name`),
                    `description` = VALUES(`description`),
                    `privacy_policy_url` = VALUES(`privacy_policy_url`),
                    `hosts` = VALUES(`hosts`),
                    `preview_html` = VALUES(`preview_html`),
                    `preview_css` = VALUES(`preview_css`),
                    `global_js` = VALUES(`global_js`),
                    `init_js` = VALUES(`init_js`),
                    `settings` = VALUES(`settings`),
                    `status` = VALUES(`status`),
                    `undeletable` = VALUES(`undeletable`)
            "
            );
        }
    }

    /**
     * importCookiesAndGroups function.
     *
     * @param mixed $data
     */
    public function importCookiesAndGroups($data)
    {
        global $wpdb;

        $language = Multilanguage::getInstance()->getCurrentLanguageCode();

        $importedCookieGroupIds = [];

        // Import cookie groups
        foreach ($data['cookieGroups'] as $groupData) {
            $importedCookieGroupIds[$groupData['id']] = $groupData['group_id'];

            $wpdb->query(
                '
                INSERT INTO
                    `' . $this->tableCookieGroup . "`
                    (
                        `group_id`,
                        `language`,
                        `name`,
                        `description`,
                        `pre_selected`,
                        `position`,
                        `status`,
                        `undeletable`
                    )
                VALUES
                    (
                        '" . esc_sql($groupData['group_id']) . "',
                        '" . esc_sql($language) . "',
                        '" . esc_sql($groupData['name']) . "',
                        '" . esc_sql($groupData['description']) . "',
                        '" . ((int) ($groupData['pre_selected']) ? 1 : 0) . "',
                        '" . (int) ($groupData['position']) . "',
                        '" . ((int) ($groupData['status']) ? 1 : 0) . "',
                        '" . ((int) ($groupData['undeletable']) ? 1 : 0) . "'
                    )
                ON DUPLICATE KEY UPDATE
                    `name` = VALUES(`name`),
                    `description` = VALUES(`description`),
                    `pre_selected` = VALUES(`pre_selected`),
                    `position` = VALUES(`position`),
                    `status` = VALUES(`status`),
                    `undeletable` = VALUES(`undeletable`)
            "
            );
        }

        // Get all group ids of current language
        $currentCookieGroupIds = [];

        $cookieGroups = $wpdb->get_results(
            '
            SELECT
                `id`,
                `group_id`
            FROM
                `' . $this->tableCookieGroup . "`
            WHERE
                `language` = '" . esc_sql(Multilanguage::getInstance()->getCurrentLanguageCode()) . "'
        "
        );

        foreach ($cookieGroups as $groupData) {
            $currentCookieGroupIds[$groupData->group_id] = $groupData->id;
        }

        // Import cookies
        foreach ($data['cookies'] as $cookieData) {
            $newCookieGroupId = $currentCookieGroupIds[$importedCookieGroupIds[$cookieData['cookie_group_id']]];

            $wpdb->query(
                '
                INSERT INTO
                    `' . $this->tableCookie . "`
                    (
                        `cookie_id`,
                        `language`,
                        `cookie_group_id`,
                        `service`,
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
                        `settings`,
                        `position`,
                        `status`,
                        `undeletable`
                    )
                VALUES
                    (
                        '" . esc_sql($cookieData['cookie_id']) . "',
                        '" . esc_sql($language) . "',
                        '" . (int) $newCookieGroupId . "',
                        '" . esc_sql($cookieData['service']) . "',
                        '" . esc_sql($cookieData['name']) . "',
                        '" . esc_sql($cookieData['provider']) . "',
                        '" . esc_sql($cookieData['purpose']) . "',
                        '" . esc_sql($cookieData['privacy_policy_url']) . "',
                        '" . esc_sql($cookieData['hosts']) . "',
                        '" . esc_sql($cookieData['cookie_name']) . "',
                        '" . esc_sql($cookieData['cookie_expiry']) . "',
                        '" . esc_sql($cookieData['opt_in_js']) . "',
                        '" . esc_sql($cookieData['opt_out_js']) . "',
                        '" . esc_sql($cookieData['fallback_js']) . "',
                        '" . esc_sql($cookieData['settings']) . "',
                        '" . (int) ($cookieData['position']) . "',
                        '" . ((int) ($cookieData['status']) ? 1 : 0) . "',
                        '" . ((int) ($cookieData['undeletable']) ? 1 : 0) . "'
                    )
                ON DUPLICATE KEY UPDATE
                    `cookie_group_id` = VALUES(`cookie_group_id`),
                    `service` = VALUES(`service`),
                    `name` = VALUES(`name`),
                    `provider` = VALUES(`provider`),
                    `purpose` = VALUES(`purpose`),
                    `privacy_policy_url` = VALUES(`privacy_policy_url`),
                    `hosts` = VALUES(`hosts`),
                    `cookie_name` = VALUES(`cookie_name`),
                    `cookie_expiry` = VALUES(`cookie_expiry`),
                    `opt_in_js` = VALUES(`opt_in_js`),
                    `opt_out_js` = VALUES(`opt_out_js`),
                    `fallback_js` = VALUES(`fallback_js`),
                    `settings` = VALUES(`settings`),
                    `position` = VALUES(`position`),
                    `status` = VALUES(`status`),
                    `undeletable` = VALUES(`undeletable`)
            "
            );
        }
    }

    /**
     * importScriptBlocker function.
     *
     * @param mixed $data
     */
    public function importScriptBlocker($data)
    {
        global $wpdb;

        foreach ($data as $scriptBlockerData) {
            $wpdb->query(
                '
                INSERT INTO
                    `' . $this->tableScriptBlocker . "`
                    (
                        `script_blocker_id`,
                        `name`,
                        `handles`,
                        `js_block_phrases`,
                        `status`,
                        `undeletable`
                    )
                VALUES
                    (
                        '" . esc_sql($scriptBlockerData['script_blocker_id']) . "',
                        '" . esc_sql($scriptBlockerData['name']) . "',
                        '" . esc_sql($scriptBlockerData['handles']) . "',
                        '" . esc_sql($scriptBlockerData['js_block_phrases']) . "',
                        '" . ((int) ($scriptBlockerData['status']) ? 1 : 0) . "',
                        '" . ((int) ($scriptBlockerData['undeletable']) ? 1 : 0) . "'
                    )
                ON DUPLICATE KEY UPDATE
                    `name` = VALUES(`name`),
                    `handles` = VALUES(`handles`),
                    `js_block_phrases` = VALUES(`js_block_phrases`),
                    `status` = VALUES(`status`),
                    `undeletable` = VALUES(`undeletable`)
            "
            );
        }
    }
}
