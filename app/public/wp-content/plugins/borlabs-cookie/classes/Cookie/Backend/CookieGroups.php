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
use BorlabsCookie\Cookie\Install;
use BorlabsCookie\Cookie\Multilanguage;
use stdClass;

class CookieGroups
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
     * tableCookie.
     *
     * (default value: '')
     *
     * @var string
     */
    private $tableCookie = '';

    /**
     * tableCookieGroup.
     *
     * (default value: '')
     *
     * @var string
     */
    private $tableCookieGroup = '';

    public function __construct()
    {
        global $wpdb;

        $this->tableCookie = $wpdb->prefix . 'borlabs_cookie_legacy_cookies';
        $this->tableCookieGroup = $wpdb->prefix . 'borlabs_cookie_legacy_groups';
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
     * add function.
     *
     * @param mixed $data
     */
    public function add($data)
    {
        global $wpdb;

        $default = [
            'groupId' => '',
            'language' => '',
            'name' => '',
            'description' => '',
            'preSelected' => false,
            'position' => 1,
            'status' => false,
            'undeletable' => false,
        ];

        $data = array_merge($default, $data);

        if (empty($data['language'])) {
            $data['language'] = Multilanguage::getInstance()->getCurrentLanguageCode();
        }

        if ($this->checkIdExists($data['groupId'], $data['language']) === false) {
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
                        '" . esc_sql($data['groupId']) . "',
                        '" . esc_sql($data['language']) . "',
                        '" . esc_sql(stripslashes($data['name'])) . "',
                        '" . esc_sql(stripslashes($data['description'])) . "',
                        '" . ((int) ($data['preSelected']) ? 1 : 0) . "',
                        '" . (int) ($data['position']) . "',
                        '" . ((int) ($data['status']) ? 1 : 0) . "',
                        '" . ((int) ($data['undeletable']) ? 1 : 0) . "'
                    )
            "
            );

            if (!empty($wpdb->insert_id)) {
                return $wpdb->insert_id;
            }
        }

        return false;
    }

    /**
     * checkIdExists function.
     *
     * @param mixed $groupId
     * @param mixed $language (default: null)
     */
    public function checkIdExists($groupId, $language = null)
    {
        global $wpdb;

        if (empty($language)) {
            $language = Multilanguage::getInstance()->getCurrentLanguageCode();
        }

        $checkId = $wpdb->get_results(
            '
            SELECT
                `group_id`
            FROM
                `' . $this->tableCookieGroup . "`
            WHERE
                `group_id` = '" . esc_sql($groupId) . "'
                AND
                `language` = '" . esc_sql($language) . "'
        "
        );

        return (bool) (!empty($checkId[0]->group_id));
    }

    /**
     * delete function.
     *
     * @param mixed $id
     */
    public function delete($id)
    {
        global $wpdb;

        $deleteStatus = false;

        // Check if no cookie is linked to this cookie group
        $checkCookies = $wpdb->get_results(
            '
            SELECT
                `cookie_id`
            FROM
                `' . $this->tableCookie . "`
            WHERE
                `cookie_group_id` = '" . esc_sql($id) . "'
            LIMIT
                0,1
        "
        );

        if (empty($checkCookies[0]->cookie_id)) {
            $wpdb->query(
                '
                DELETE FROM
                    `' . $this->tableCookieGroup . "`
                WHERE
                    `id` = '" . (int) $id . "'
                    AND
                    `undeletable` = 0
            "
            );

            $deleteStatus = true;
        }

        return $deleteStatus;
    }

    /**
     * display function.
     */
    public function display()
    {
        $id = null;

        if (!empty($_POST['id'])) {
            $id = $_POST['id'];
        } elseif (!empty($_GET['id'])) {
            $id = $_GET['id'];
        }

        $action = false;

        if (!empty($_POST['action'])) {
            $action = $_POST['action'];
        } elseif (!empty($_GET['action'])) {
            $action = $_GET['action'];
        }

        if ($action !== false) {
            // Validate and save Cookie Group
            if ($action === 'save' && !empty($id) && check_admin_referer('borlabs_cookie_cookie_groups_save')) {
                // Validate
                $errorStatus = $this->validate($_POST);

                // Save
                if ($errorStatus === false) {
                    $id = $this->save($_POST);

                    Messages::getInstance()->add(
                        _x('Saved successfully.', 'Backend / Global / Alert Message', 'borlabs-cookie'),
                        'success'
                    );
                }
            }

            // Switch status of Cookie Group
            if (
                $action === 'switchStatus' && !empty($id)
                && wp_verify_nonce(
                    $_GET['_wpnonce'],
                    'switchStatus_' . $id
                )
            ) {
                $this->switchStatus($id);

                Messages::getInstance()->add(
                    _x('Changed status successfully.', 'Backend / Global / Alert Message', 'borlabs-cookie'),
                    'success'
                );
            }

            // Delete Cookie Group
            if ($action === 'delete' && !empty($id) && wp_verify_nonce($_GET['_wpnonce'], 'delete_' . $id)) {
                if ($this->delete($id)) {
                    Messages::getInstance()->add(
                        _x('Deleted successfully.', 'Backend / Global / Alert Message', 'borlabs-cookie'),
                        'success'
                    );
                } else {
                    Messages::getInstance()->add(
                        _x(
                            'Could not delete <strong>Cookie Group</strong> because <strong>Cookie Group</strong> is linked with <strong>Cookies</strong>.',
                            'Backend / Cookie Groups / Alert Message',
                            'borlabs-cookie'
                        ),
                        'error'
                    );
                }
            }

            // Reset default Cookie Groups
            if ($action === 'resetDefault' && check_admin_referer('borlabs_cookie_cookie_groups_reset_default')) {
                $this->resetDefault();

                Messages::getInstance()->add(
                    _x(
                        'Default <strong>Cookie Groups</strong> successfully reset.',
                        'Backend / Cookie Groups / Alert Message',
                        'borlabs-cookie'
                    ),
                    'success'
                );
            }
        }

        // Check if overview or edit mask should be displayed
        if ($action === 'edit' || $action === 'save') {
            $this->displayEdit($id, $_POST);
        } else {
            $this->displayOverview();
        }
    }

    /**
     * displayEdit function.
     *
     * @param int   $id       (default: 0)
     * @param mixed $formData (default: [])
     */
    public function displayEdit($id = 0, $formData = [])
    {
        $cookieGroupData = new stdClass();

        // Load settings
        if (!empty($id) && $id !== 'new') {
            $cookieGroupData = $this->get($id);

            // Check if the language was switched during editing
            if ($cookieGroupData->language !== Multilanguage::getInstance()->getCurrentLanguageCode()) {
                // Try to get the id for the switched language
                $previousGroupId = $cookieGroupData->group_id;
                $cookieGroupData = $this->getByGroupId($cookieGroupData->group_id);

                // If not found
                if (empty($cookieGroupData->id)) {
                    Messages::getInstance()->add(
                        _x(
                            'The selected <strong>Cookie Group</strong> is not available in the current language.',
                            'Backend / Cookie Groups / Alert Message',
                            'borlabs-cookie'
                        ),
                        'error'
                    );

                    $cookieGroupData = new stdClass();
                    $cookieGroupData->group_id = $previousGroupId;
                }
            }
        }

        // Re-insert data
        if (isset($formData['groupId'])) {
            $cookieGroupData->group_id = stripslashes($formData['groupId']);
        }

        if (isset($formData['name'])) {
            $cookieGroupData->name = stripslashes($formData['name']);
        }

        if (isset($formData['description'])) {
            $cookieGroupData->description = stripslashes($formData['description']);
        }

        if (isset($formData['preSelected'])) {
            $cookieGroupData->pre_selected = (int) ($formData['preSelected']);
        }

        if (isset($formData['status'])) {
            $cookieGroupData->status = (int) ($formData['status']);
        }

        if (isset($formData['position'])) {
            $cookieGroupData->position = (int) ($formData['position']);
        }

        // Preparing data for form mask
        $inputId = !empty($cookieGroupData->id) ? (int) ($cookieGroupData->id) : 'new';
        $inputGroupId = esc_attr(!empty($cookieGroupData->group_id) ? $cookieGroupData->group_id : '');
        $inputStatus = !empty($cookieGroupData->status) ? 1 : 0;
        $switchStatus = $inputStatus ? ' active' : '';
        $inputName = esc_attr(!empty($cookieGroupData->name) ? $cookieGroupData->name : '');
        $textareaDescription = esc_textarea(
            !empty($cookieGroupData->description) ? $cookieGroupData->description : ''
        );
        $inputPreSelected = !empty($cookieGroupData->pre_selected) ? 1 : 0;
        $switchPreSelected = $inputPreSelected ? ' active' : '';
        $inputPosition = (int) (!empty($cookieGroupData->position) ? $cookieGroupData->position : '1');

        $languageFlag = !empty($cookieGroupData->language) ? Multilanguage::getInstance()->getLanguageFlag(
            $cookieGroupData->language
        ) : '';
        $languageName = !empty($cookieGroupData->language) ? Multilanguage::getInstance()->getLanguageName(
            $cookieGroupData->language
        ) : '';

        $ignorePreSelectStatusIsActive = false;

        if (Config::getInstance()->get('cookieBoxIgnorePreSelectStatus')) {
            $ignorePreSelectStatusIsActive = true;
        }

        include Backend::getInstance()->templatePath . '/cookie-groups-edit.html.php';
    }

    /**
     * displayOverview function.
     */
    public function displayOverview()
    {
        global $wpdb;

        $cookieGroups = $wpdb->get_results(
            '
            SELECT
                `id`,
                `group_id`,
                `name`,
                `position`,
                `status`,
                `undeletable`
            FROM
                `' . $this->tableCookieGroup . "`
            WHERE
                `language` = '" . esc_sql(Multilanguage::getInstance()->getCurrentLanguageCode()) . "'
            ORDER BY
                `name` ASC
        "
        );

        if (!empty($cookieGroups)) {
            foreach ($cookieGroups as $key => $data) {
                $cookieGroups[$key]->undeletable = (int) ($data->undeletable);
            }
        }

        include Backend::getInstance()->templatePath . '/cookie-groups-overview.html.php';
    }

    /**
     * get function.
     *
     * @param mixed $id
     */
    public function get($id)
    {
        global $wpdb;

        $data = false;

        $cookieGroupData = $wpdb->get_results(
            '
            SELECT
                `id`,
                `group_id`,
                `language`,
                `name`,
                `description`,
                `pre_selected`,
                `position`,
                `status`
            FROM
                `' . $this->tableCookieGroup . "`
            WHERE
                `id` = '" . esc_sql($id) . "'
        "
        );

        if (!empty($cookieGroupData[0]->id)) {
            $data = $cookieGroupData[0];
        }

        return $data;
    }

    /**
     * getByGroupId function.
     *
     * @param mixed $groupId
     */
    public function getByGroupId($groupId)
    {
        global $wpdb;

        $data = false;

        $language = Multilanguage::getInstance()->getCurrentLanguageCode();

        // Get cookie group id for the current language
        $cookieGroupId = $wpdb->get_results(
            '
            SELECT
                `id`
            FROM
                `' . $this->tableCookieGroup . "`
            WHERE
                `language` = '" . esc_sql($language) . "'
                AND
                `group_id` = '" . esc_sql($groupId) . "'
        "
        );

        if (!empty($cookieGroupId[0]->id)) {
            $data = $this->get($cookieGroupId[0]->id);
        }

        return $data;
    }

    /**
     * modify function.
     *
     * @param mixed $id
     * @param mixed $data
     */
    public function modify($id, $data)
    {
        global $wpdb;

        $default = [
            'name' => '',
            'description' => '',
            'preSelected' => false,
            'position' => 1,
            'status' => false,
        ];

        $data = array_merge($default, $data);

        $wpdb->query(
            '
            UPDATE
                `' . $this->tableCookieGroup . "`
            SET
                `name` = '" . esc_sql(stripslashes($data['name'])) . "',
                `description` = '" . esc_sql(stripslashes($data['description'])) . "',
                `pre_selected` = '" . ((int) ($data['preSelected']) ? 1 : 0) . "',
                `position` = '" . (int) ($data['position']) . "',
                `status` = '" . ((int) ($data['status']) ? 1 : 0) . "'
            WHERE
                `id` = '" . (int) $id . "'
        "
        );

        return $id;
    }

    /**
     * resetDefault function.
     */
    public function resetDefault()
    {
        global $wpdb;

        $language = Multilanguage::getInstance()->getCurrentLanguageCode();

        // Get old cookie groups and their ids
        $cookieGroups = $wpdb->get_results(
            '
            SELECT
                `id`,
                `group_id`
            FROM
                `' . $this->tableCookieGroup . "`
            WHERE
                `language` = '" . esc_sql($language) . "'
                AND
                `group_id` IN ('essential', 'statistics', 'marketing', 'external-media')
        "
        );

        // Delete default Cookie Groups
        $wpdb->query(
            '
            DELETE FROM
                `' . $this->tableCookieGroup . "`
            WHERE
                `language` = '" . esc_sql($language) . "'
                AND
                `group_id` IN ('essential', 'statistics', 'marketing', 'external-media')
        "
        );

        $sqlDefaultEntriesCookieGroups = Install::getInstance()->getDefaultEntriesCookieGroups(
            $this->tableCookieGroup,
            $language
        );

        $wpdb->query($sqlDefaultEntriesCookieGroups);

        // Get new cookie groups and their ids
        $cookieGroupsNew = $wpdb->get_results(
            '
            SELECT
                `id`,
                `group_id`
            FROM
                `' . $this->tableCookieGroup . "`
            WHERE
                `language` = '" . esc_sql($language) . "'
                AND
                `group_id` IN ('essential', 'statistics', 'marketing', 'external-media')
        "
        );

        // Match old / new
        $matchCookieGroups = [];

        if (!empty($cookieGroups)) {
            foreach ($cookieGroups as $oldGroupData) {
                $matchCookieGroups[$oldGroupData->group_id] = [
                    'old' => $oldGroupData->id,
                    'new' => 0,
                ];
            }

            if (!empty($cookieGroupsNew)) {
                foreach ($cookieGroupsNew as $newGroupData) {
                    if (isset($matchCookieGroups[$newGroupData->group_id])) {
                        $matchCookieGroups[$newGroupData->group_id]['new'] = $newGroupData->id;
                    }
                }
            }

            // Fix Cookie <-> Cookie Group connection
            if (!empty($matchCookieGroups)) {
                foreach ($matchCookieGroups as $matchData) {
                    if (!empty($matchData['new'])) {
                        $wpdb->query(
                            '
                            UPDATE
                                `' . $this->tableCookie . "`
                            SET
                                `cookie_group_id` = '" . esc_sql($matchData['new']) . "'
                            WHERE
                                `cookie_group_id` = '" . esc_sql($matchData['old']) . "'
                        "
                        );
                    }
                }
            }
        }
    }

    /**
     * save function.
     *
     * @param mixed $formData
     */
    public function save($formData)
    {
        $formData = apply_filters('borlabsCookie/cookieGroup/save', $formData);

        $id = 0;

        if (!empty($formData['id']) && $formData['id'] !== 'new') {
            // Edit
            $id = $this->modify($formData['id'], $formData);
        } else {
            // Add
            $id = $this->add($formData);
        }

        return $id;
    }

    /**
     * switchStatus function.
     *
     * @param mixed $id
     */
    public function switchStatus($id)
    {
        global $wpdb;

        $wpdb->query(
            '
            UPDATE
                `' . $this->tableCookieGroup . "`
            SET
                `status` = IF(`status` <> 0, 0, 1)
            WHERE
                `id` = '" . (int) $id . "'
                AND
                `group_id` != 'essential'
        "
        );

        return true;
    }

    /**
     * validate function.
     *
     * @param mixed $formData
     */
    public function validate($formData)
    {
        $errorStatus = false;

        // Check groupId if a new cookie group is about to be added
        if (empty($formData['id']) || $formData['id'] === 'new') {
            if (empty($formData['groupId']) || preg_match('/^[a-z\-\_]{3,}$/', $formData['groupId']) === 0) {
                $errorStatus = true;
                Messages::getInstance()->add(
                    _x(
                        'Please fill out the field <strong>ID</strong>. The ID must be at least 3 characters long and may only contain: <strong><em>a-z - _</em></strong>',
                        'Backend / Global / Alert Message',
                        'borlabs-cookie'
                    ),
                    'error'
                );
            } elseif ($this->checkIdExists($formData['groupId'])) {
                $errorStatus = true;
                Messages::getInstance()->add(
                    _x('The <strong>ID</strong> already exists.', 'Backend / Global / Alert Message', 'borlabs-cookie'),
                    'error'
                );
            }
        }

        if (empty($formData['name'])) {
            $errorStatus = true;
            Messages::getInstance()->add(
                _x(
                    'Please fill out the field <strong>Name</strong>.',
                    'Backend / Global / Alert Message',
                    'borlabs-cookie'
                ),
                'error'
            );
        }

        return apply_filters('borlabsCookie/cookieGroup/validate', $errorStatus, $formData);
    }
}
