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
use BorlabsCookie\Cookie\Frontend\ContentBlocker\Fallback;
use BorlabsCookie\Cookie\Multilanguage;
use BorlabsCookie\Cookie\Tools;
use stdClass;

class ContentBlocker
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
     * defaultContentBlocker.
     *
     * @var mixed
     */
    private $defaultContentBlocker
        = [
            'facebook' => 'Facebook',
            'default' => 'Fallback', // Default
            'googlemaps' => 'GoogleMaps',
            'instagram' => 'Instagram',
            'openstreetmap' => 'OpenStreetMap',
            'twitter' => 'Twitter',
            'vimeo' => 'Vimeo',
            'youtube' => 'YouTube',
        ];

    /**
     * table.
     *
     * (default value: '')
     *
     * @var string
     */
    private $table = '';

    public function __construct()
    {
        global $wpdb;

        $this->table = $wpdb->prefix . 'borlabs_cookie_legacy_content_blocker';
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
            'contentBlockerId' => '',
            'language' => '',
            'name' => '',
            'description' => '',
            'privacyPolicyURL' => '',
            'hosts' => [],
            'previewHTML' => '',
            'previewCSS' => '',
            'globalJS' => '',
            'initJS' => '',
            'settings' => [],
            'status' => false,
            'undeletable' => false,
        ];

        $data = array_merge($default, $data);

        if (empty($data['language'])) {
            $data['language'] = Multilanguage::getInstance()->getCurrentLanguageCode();
        }

        if ($this->checkIdExists($data['contentBlockerId'], $data['language']) === false) {
            $wpdb->query(
                '
                INSERT INTO
                    `' . $this->table . "`
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
                        '" . esc_sql($data['contentBlockerId']) . "',
                        '" . esc_sql($data['language']) . "',
                        '" . esc_sql(stripslashes($data['name'])) . "',
                        '" . esc_sql(stripslashes($data['description'])) . "',
                        '" . esc_sql(stripslashes($data['privacyPolicyURL'])) . "',
                        '" . esc_sql(serialize($data['hosts'])) . "',
                        '" . esc_sql(stripslashes($data['previewHTML'])) . "',
                        '" . esc_sql(stripslashes($data['previewCSS'])) . "',
                        '" . esc_sql(stripslashes($data['globalJS'])) . "',
                        '" . esc_sql(stripslashes($data['initJS'])) . "',
                        '" . esc_sql(serialize($data['settings'])) . "',
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
     * Checks if the contentBlockerId for the current language exists.
     *
     * @param mixed $contentBlockerId
     * @param mixed $language
     */
    public function checkIdExists($contentBlockerId, $language = null)
    {
        global $wpdb;

        if (empty($language)) {
            $language = Multilanguage::getInstance()->getCurrentLanguageCode();
        }

        $checkId = $wpdb->get_results(
            '
            SELECT
                `content_blocker_id`
            FROM
                `' . $this->table . "`
            WHERE
                `content_blocker_id` = '" . esc_sql($contentBlockerId) . "'
                AND
                `language` = '" . esc_sql($language) . "'
        "
        );

        return (bool) (!empty($checkId[0]->content_blocker_id));
    }

    /**
     * delete function.
     *
     * @param mixed $id
     */
    public function delete($id)
    {
        global $wpdb;

        $wpdb->query(
            '
            DELETE FROM
                `' . $this->table . "`
            WHERE
                `id` = '" . (int) $id . "'
        "
        );

        return true;
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
            // Validate and save Content Blocker
            if ($action === 'save' && !empty($id) && check_admin_referer('borlabs_cookie_content_blocker_save')) {
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

            // Switch status of Content Blocker
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

            // Delete Content Blocker
            if ($action === 'delete' && !empty($id) && wp_verify_nonce($_GET['_wpnonce'], 'delete_' . $id)) {
                $this->delete($id);

                Messages::getInstance()->add(
                    _x('Deleted successfully.', 'Backend / Global / Alert Message', 'borlabs-cookie'),
                    'success'
                );
            }

            // Save settings
            if ($action === 'saveSettings' && check_admin_referer('borlabs_cookie_content_blocker_save_settings')) {
                $this->saveSettings($_POST);

                Messages::getInstance()->add(
                    _x('Saved successfully.', 'Backend / Global / Alert Message', 'borlabs-cookie'),
                    'success'
                );
            }

            // Reset default Content Blocker
            if ($action === 'resetDefault' && check_admin_referer('borlabs_cookie_content_blocker_reset_default')) {
                $this->resetDefault();

                Messages::getInstance()->add(
                    _x(
                        'Default <strong>Content Blocker</strong> successfully reset.',
                        'Backend / Content Blocker / Alert Message',
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
        $contentBlockerData = new stdClass();

        // Default data
        $contentBlockerData->preview_html = Fallback::getInstance()->getDefault()['previewHTML'];
        $contentBlockerData->preview_css = Fallback::getInstance()->getDefault()['previewCSS'];

        // Load settings
        if (!empty($id) && $id !== 'new') {
            $contentBlockerData = $this->get($id);

            // Load defaults
            if (!empty($this->defaultContentBlocker[$contentBlockerData->content_blocker_id])) {
                $contentBlockerClass = '\BorlabsCookie\Cookie\Frontend\ContentBlocker\\'
                    . $this->defaultContentBlocker[$contentBlockerData->content_blocker_id];

                if (class_exists($contentBlockerClass)) {
                    // Init and register action hooks
                    $contentBlockerClass::getInstance();
                }
            }

            // Check if the language was switched during editing
            if ($contentBlockerData->language !== Multilanguage::getInstance()->getCurrentLanguageCode()) {
                // Try to get the id for the switched language
                $previousContentBlockerId = $contentBlockerData->content_blocker_id;
                $contentBlockerData = $this->getByContentBlockerId($contentBlockerData->content_blocker_id);

                // If not found
                if (empty($contentBlockerData->id)) {
                    Messages::getInstance()->add(
                        _x(
                            'The selected <strong>Content Blocker</strong> is not available in the current language.',
                            'Backend / Content Blocker / Alert Message',
                            'borlabs-cookie'
                        ),
                        'error'
                    );

                    $contentBlockerData = new stdClass();
                    $contentBlockerData->content_blocker_id = $previousContentBlockerId;
                }
            }
        }

        // Re-insert data
        if (isset($formData['contentBlockerId'])) {
            $contentBlockerData->content_blocker_id = stripslashes($formData['contentBlockerId']);
        }

        if (isset($formData['status'])) {
            $contentBlockerData->status = (int) ($formData['status']);
        }

        if (isset($formData['name'])) {
            $contentBlockerData->name = stripslashes($formData['name']);
        }

        if (isset($formData['privacyPolicyURL'])) {
            $contentBlockerData->privacy_policy_url = stripslashes($formData['privacyPolicyURL']);
        }

        if (isset($formData['hosts'])) {
            $contentBlockerData->hosts = implode(
                "\n",
                Tools::getInstance()->cleanHostList(stripslashes($formData['hosts']))
            );
        } elseif (!empty($contentBlockerData->hosts)) {
            $contentBlockerData->hosts = implode("\n", $contentBlockerData->hosts);
        }

        if (isset($formData['settings']['unblockAll'])) {
            $contentBlockerData->settings['unblockAll'] = (int) ($formData['settings']['unblockAll']);
        }

        // previewHTML is required and should never by empty
        if (!empty($formData['previewHTML'])) {
            $contentBlockerData->preview_html = stripslashes($formData['previewHTML']);
        }

        if (isset($formData['previewCSS'])) {
            $contentBlockerData->preview_css = stripslashes($formData['previewCSS']);
        }

        if (isset($formData['globalJS'])) {
            $contentBlockerData->global_js = stripslashes($formData['globalJS']);
        }

        if (isset($formData['initJS'])) {
            $contentBlockerData->init_js = stripslashes($formData['initJS']);
        }

        // Preparing data for form mask
        $inputId = !empty($contentBlockerData->id) ? (int) ($contentBlockerData->id) : 'new';
        $inputContentBlockerId = esc_attr(
            !empty($contentBlockerData->content_blocker_id) ? $contentBlockerData->content_blocker_id : ''
        );
        $inputStatus = !empty($contentBlockerData->status) ? 1 : 0;
        $switchStatus = $inputStatus ? ' active' : '';
        $inputName = esc_attr(!empty($contentBlockerData->name) ? $contentBlockerData->name : '');
        $inputPrivacyPolicyURL = esc_url(
            !empty($contentBlockerData->privacy_policy_url) ? $contentBlockerData->privacy_policy_url : ''
        );
        $textareaHosts = esc_textarea(!empty($contentBlockerData->hosts) ? $contentBlockerData->hosts : '');

        $inputSettingsUnblockAll = !empty($contentBlockerData->settings['unblockAll']) ? 1 : 0;
        $switchSettingsUnblockAll = $inputSettingsUnblockAll ? ' active' : '';

        $inputSettingsExecuteGlobalCodeBeforeUnblocking
            = !empty($contentBlockerData->settings['executeGlobalCodeBeforeUnblocking']) ? 1 : 0;
        $switchSettingsExecuteGlobalCodeBeforeUnblocking = $inputSettingsExecuteGlobalCodeBeforeUnblocking ? ' active'
            : '';

        $textareaPreviewHTML = esc_textarea(
            !empty($contentBlockerData->preview_html) ? $contentBlockerData->preview_html : ''
        );
        $textareaPreviewCSS = esc_textarea(
            !empty($contentBlockerData->preview_css) ? $contentBlockerData->preview_css : ''
        );

        $textareaGlobalJS = esc_textarea(!empty($contentBlockerData->global_js) ? $contentBlockerData->global_js : '');
        $textareaInitJS = esc_textarea(!empty($contentBlockerData->init_js) ? $contentBlockerData->init_js : '');

        $languageFlag = !empty($contentBlockerData->language) ? Multilanguage::getInstance()->getLanguageFlag(
            $contentBlockerData->language
        ) : '';
        $languageName = !empty($contentBlockerData->language) ? Multilanguage::getInstance()->getLanguageName(
            $contentBlockerData->language
        ) : '';

        include Backend::getInstance()->templatePath . '/content-blocker-edit.html.php';
    }

    /**
     * displayOverview function.
     */
    public function displayOverview()
    {
        global $wpdb;

        // Get all blocked content types for the current language
        $contentBlocker = $wpdb->get_results(
            '
            SELECT
                `id`,
                `content_blocker_id`,
                `name`,
                `hosts`,
                `status`,
                `undeletable`
            FROM
                `' . $this->table . "`
            WHERE
                `language` = '" . esc_sql(Multilanguage::getInstance()->getCurrentLanguageCode()) . "'
            ORDER BY
                `name` ASC
        "
        );

        if (!empty($contentBlocker)) {
            foreach ($contentBlocker as $key => $data) {
                $hosts = unserialize($data->hosts);

                if (!empty($hosts)) {
                    $contentBlocker[$key]->hosts = esc_html(implode(', ', $hosts));
                } else {
                    $contentBlocker[$key]->hosts = '';
                }

                $contentBlocker[$key]->undeletable = (int) ($data->undeletable);
            }
        }

        $textareaHostWhitelist = esc_textarea(
            !empty(Config::getInstance()->get('contentBlockerHostWhitelist')) ? implode(
                "\n",
                Config::getInstance()->get('contentBlockerHostWhitelist')
            ) : ''
        );
        $inputRemoveIframesInFeeds = !empty(Config::getInstance()->get('removeIframesInFeeds')) ? 1 : 0;
        $switchRemoveIframesInFeeds = $inputRemoveIframesInFeeds ? ' active' : '';

        $inputContentBlockerFontFamily = esc_attr(
            !empty(Config::getInstance()->get('contentBlockerFontFamily'))
            && Config::getInstance()->get('contentBlockerFontFamily') !== 'inherit' ? Config::getInstance()->get(
                'contentBlockerFontFamily'
            ) : ''
        );
        $inputContentBlockerFontSize = esc_attr(
            !empty(Config::getInstance()->get('contentBlockerFontSize'))
            && Config::getInstance()->get('contentBlockerFontSize') ? Config::getInstance()->get(
                'contentBlockerFontSize'
            ) : ''
        );
        $inputContentBlockerBgColor = esc_attr(
            !empty(Config::getInstance()->get('contentBlockerBgColor')) ? Config::getInstance()->get(
                'contentBlockerBgColor'
            ) : ''
        );
        $inputContentBlockerTxtColor = esc_attr(
            !empty(Config::getInstance()->get('contentBlockerTxtColor')) ? Config::getInstance()->get(
                'contentBlockerTxtColor'
            ) : ''
        );
        $inputContentBlockerBgOpacity = esc_attr(Config::getInstance()->get('contentBlockerBgOpacity'));
        $inputContentBlockerBtnBorderRadius = esc_attr(Config::getInstance()->get('contentBlockerBtnBorderRadius'));
        $inputContentBlockerBtnColor = esc_attr(
            !empty(Config::getInstance()->get('contentBlockerBtnColor')) ? Config::getInstance()->get(
                'contentBlockerBtnColor'
            ) : ''
        );
        $inputContentBlockerBtnHoverColor = esc_attr(
            !empty(Config::getInstance()->get('contentBlockerBtnHoverColor')) ? Config::getInstance()->get(
                'contentBlockerBtnHoverColor'
            ) : ''
        );
        $inputContentBlockerBtnTxtColor = esc_attr(
            !empty(Config::getInstance()->get('contentBlockerBtnTxtColor')) ? Config::getInstance()->get(
                'contentBlockerBtnTxtColor'
            ) : ''
        );
        $inputContentBlockerBtnTxtHoverColor = esc_attr(
            !empty(Config::getInstance()->get('contentBlockerBtnHoverTxtColor')) ? Config::getInstance()->get(
                'contentBlockerBtnHoverTxtColor'
            ) : ''
        );
        $inputContentBlockerLinkColor = esc_attr(
            !empty(Config::getInstance()->get('contentBlockerLinkColor')) ? Config::getInstance()->get(
                'contentBlockerLinkColor'
            ) : ''
        );
        $inputContentBlockerLinkHoverColor = esc_attr(
            !empty(Config::getInstance()->get('contentBlockerLinkHoverColor')) ? Config::getInstance()->get(
                'contentBlockerLinkHoverColor'
            ) : ''
        );

        include Backend::getInstance()->templatePath . '/content-blocker-overview.html.php';
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
                `status`
            FROM
                `' . $this->table . "`
            WHERE
                `id` = '" . esc_sql($id) . "'
        "
        );

        if (!empty($contentBlockerData[0]->id)) {
            $data = $contentBlockerData[0];

            $data->hosts = unserialize($data->hosts);
            $data->settings = unserialize($data->settings);

            $data->description = wp_kses($data->description, [
                'a' => [],
                'br' => [],
                'div' => [],
                'em' => [],
                'pre' => [],
                'span' => [],
                'strong' => [],
            ], [
                'https',
            ]);
        }

        return $data;
    }

    /**
     * getByContentBlockerId function.
     *
     * @param mixed $contentBlockerId
     */
    public function getByContentBlockerId($contentBlockerId)
    {
        global $wpdb;

        $data = false;

        $language = Multilanguage::getInstance()->getCurrentLanguageCode();

        // Get content blocker id for the current language
        $contentBlockerId = $wpdb->get_results(
            '
            SELECT
                `id`
            FROM
                `' . $this->table . "`
            WHERE
                `language` = '" . esc_sql($language) . "'
                AND
                `content_blocker_id` = '" . esc_sql($contentBlockerId) . "'
        "
        );

        if (!empty($contentBlockerId[0]->id)) {
            $data = $this->get($contentBlockerId[0]->id);
        }

        return $data;
    }

    /**
     * initDefault function.
     */
    public function initDefault()
    {
        global $wpdb;

        $language = Multilanguage::getInstance()->getCurrentLanguageCode();

        // Checks if default content blocker does not exist and add them with default settings
        foreach ($this->defaultContentBlocker as $contentBlockerId => $class) {
            $contentBlocker = $wpdb->get_row(
                '
                SELECT `content_blocker_id` FROM
                    `' . $this->table . "`
                WHERE
                    `language` = '" . esc_sql($language) . "'
                    AND
                    `content_blocker_id` = '" . esc_sql($contentBlockerId) . "'
            "
            );

            if (empty($contentBlocker->content_blocker_id)) {
                // Add
                $ContentBlocker = '\BorlabsCookie\Cookie\Frontend\ContentBlocker\\' . $class;
                $defaultData = $ContentBlocker::getInstance()->getDefault();

                $this->add($defaultData);
            }
        }

        // Update CSS File
        CSS::getInstance()->save($language);
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
            'privacyPolicyURL' => '',
            'hosts' => [],
            'previewHTML' => '',
            'previewCSS' => '',
            'globalJS' => '',
            'initJS' => '',
            'settings' => [],
            'status' => false,
        ];

        $data = array_merge($default, $data);

        $wpdb->query(
            '
            UPDATE
                `' . $this->table . "`
            SET
                `name` = '" . esc_sql(stripslashes($data['name'])) . "',
                `privacy_policy_url` = '" . esc_sql(stripslashes($data['privacyPolicyURL'])) . "',
                `hosts` = '" . esc_sql(serialize($data['hosts'])) . "',
                `preview_html` = '" . esc_sql(stripslashes($data['previewHTML'])) . "',
                `preview_css` = '" . esc_sql(stripslashes($data['previewCSS'])) . "',
                `global_js` = '" . esc_sql(stripslashes($data['globalJS'])) . "',
                `init_js` = '" . esc_sql(stripslashes($data['initJS'])) . "',
                `settings` = '" . esc_sql(serialize($data['settings'])) . "',
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

        // Delete default content blocker and restore them with default settings
        foreach ($this->defaultContentBlocker as $contentBlockerId => $class) {
            // Delete
            $contentBlocker = $wpdb->query(
                '
                DELETE FROM
                    `' . $this->table . "`
                WHERE
                    `language` = '" . esc_sql($language) . "'
                    AND
                    `content_blocker_id` = '" . esc_sql($contentBlockerId) . "'
            "
            );

            // Restore
            $ContentBlocker = '\BorlabsCookie\Cookie\Frontend\ContentBlocker\\' . $class;
            $defaultData = $ContentBlocker::getInstance()->getDefault();

            $this->add($defaultData);
        }

        // Update CSS File
        CSS::getInstance()->save($language);
    }

    /**
     * save function.
     *
     * @param mixed $formData
     */
    public function save($formData)
    {
        $formData = apply_filters_deprecated(
            'borlabsCookie/bct/save',
            [$formData],
            'Borlabs Cookie 2.0',
            'borlabsCookie/contentBlocker/save'
        );

        $formData = apply_filters('borlabsCookie/contentBlocker/save', $formData);

        // Clean hosts
        $formData['hosts'] = Tools::getInstance()->cleanHostList($formData['hosts']);

        // Check if previewHTML is empty
        if (empty($formData['previewHTML'])) {
            // If it is a default Content Blocker we load its default previewHTML
            if (!empty($formData['id'])) {
                $contentBlockerData = $this->get($formData['id']);

                if (
                    !empty($contentBlockerData->content_blocker_id)
                    && !empty($this->defaultContentBlocker[$contentBlockerData->content_blocker_id])
                ) {
                    $className = '\BorlabsCookie\Cookie\Frontend\ContentBlocker\\'
                        . $this->defaultContentBlocker[$contentBlockerData->content_blocker_id];
                    $defaultContentBlockerData = $className::getInstance()->getDefault();

                    $formData['previewHTML'] = $defaultContentBlockerData['previewHTML'];
                }
            }

            if (empty($formData['previewHTML'])) {
                $formData['previewHTML'] = Fallback::getInstance()->getDefault()['previewHTML'];
            }
        }

        $id = 0;

        if (!empty($formData['id']) && $formData['id'] !== 'new') {
            // Edit
            $id = $this->modify($formData['id'], $formData);
        } else {
            // Add
            $id = $this->add($formData);
        }

        // Update CSS File
        CSS::getInstance()->save();

        return $id;
    }

    /**
     * saveSettings function.
     *
     * @param mixed $formData
     */
    public function saveSettings($formData)
    {
        $defaultConfig = Config::getInstance()->defaultConfig();
        $updatedConfig = Config::getInstance()->get();

        // Clean hosts
        $updatedConfig['contentBlockerHostWhitelist'] = Tools::getInstance()->cleanHostList(
            $formData['contentBlockerHostWhitelist']
        );
        $updatedConfig['removeIframesInFeeds'] = !empty($formData['removeIframesInFeeds']) ? true : false;

        $updatedConfig['contentBlockerFontFamily'] = !empty($formData['contentBlockerFontFamily']) ? stripslashes(
            $formData['contentBlockerFontFamily']
        ) : $defaultConfig['contentBlockerFontFamily'];
        $updatedConfig['contentBlockerFontSize'] = !empty($formData['contentBlockerFontSize']) ? (int) (
            $formData['contentBlockerFontSize']
        ) : $defaultConfig['contentBlockerFontSize'];

        // Colors
        $updatedConfig['contentBlockerBgColor'] = !empty($formData['contentBlockerBgColor'])
        && Tools::getInstance()->validateHexColor($formData['contentBlockerBgColor'])
            ? $formData['contentBlockerBgColor'] : $defaultConfig['contentBlockerBgColor'];
        $updatedConfig['contentBlockerTxtColor'] = !empty($formData['contentBlockerTxtColor'])
        && Tools::getInstance()->validateHexColor($formData['contentBlockerTxtColor'])
            ? $formData['contentBlockerTxtColor'] : $defaultConfig['contentBlockerTxtColor'];
        $updatedConfig['contentBlockerBgOpacity'] = isset($formData['contentBlockerBgOpacity']) ? (int) (
            $formData['contentBlockerBgOpacity']
        ) : $defaultConfig['contentBlockerBgOpacity'];
        $updatedConfig['contentBlockerBtnBorderRadius'] = isset($formData['contentBlockerBtnBorderRadius']) ? (int) (
            $formData['contentBlockerBtnBorderRadius']
        ) : $defaultConfig['contentBlockerBtnBorderRadius'];
        $updatedConfig['contentBlockerBtnColor'] = !empty($formData['contentBlockerBtnColor'])
        && Tools::getInstance()->validateHexColor($formData['contentBlockerBtnColor'])
            ? $formData['contentBlockerBtnColor'] : $defaultConfig['contentBlockerBtnColor'];
        $updatedConfig['contentBlockerBtnHoverColor'] = !empty($formData['contentBlockerBtnHoverColor'])
        && Tools::getInstance()->validateHexColor($formData['contentBlockerBtnHoverColor'])
            ? $formData['contentBlockerBtnHoverColor'] : $defaultConfig['contentBlockerBtnHoverColor'];
        $updatedConfig['contentBlockerBtnTxtColor'] = !empty($formData['contentBlockerBtnTxtColor'])
        && Tools::getInstance()->validateHexColor($formData['contentBlockerBtnTxtColor'])
            ? $formData['contentBlockerBtnTxtColor'] : $defaultConfig['contentBlockerBtnTxtColor'];
        $updatedConfig['contentBlockerBtnHoverTxtColor'] = !empty($formData['contentBlockerBtnHoverTxtColor'])
        && Tools::getInstance()->validateHexColor($formData['contentBlockerBtnHoverTxtColor'])
            ? $formData['contentBlockerBtnHoverTxtColor'] : $defaultConfig['contentBlockerBtnHoverTxtColor'];
        $updatedConfig['contentBlockerLinkColor'] = !empty($formData['contentBlockerLinkColor'])
        && Tools::getInstance()->validateHexColor($formData['contentBlockerLinkColor'])
            ? $formData['contentBlockerLinkColor'] : $defaultConfig['contentBlockerLinkColor'];
        $updatedConfig['contentBlockerLinkHoverColor'] = !empty($formData['contentBlockerLinkHoverColor'])
        && Tools::getInstance()->validateHexColor($formData['contentBlockerLinkHoverColor'])
            ? $formData['contentBlockerLinkHoverColor'] : $defaultConfig['contentBlockerLinkHoverColor'];

        // Save config
        Config::getInstance()->saveConfig($updatedConfig);

        // Update CSS File
        CSS::getInstance()->save();
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
                `' . $this->table . "`
            SET
                `status` = IF(`status` <> 0, 0, 1)
            WHERE
                `id` = '" . (int) $id . "'
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

        // Check contentBlockerId if a new CB is about to be added
        if (empty($formData['id']) || $formData['id'] === 'new') {
            if (
                empty($formData['contentBlockerId'])
                || preg_match('/^[a-z\-\_]{3,}$/', $formData['contentBlockerId']) === 0
            ) {
                $errorStatus = true;
                Messages::getInstance()->add(
                    _x(
                        'Please fill out the field <strong>ID</strong>. The id has to be minimum 3 letters and only contains letters from <strong><em>a-z</em></strong>.',
                        'Backend / Global / Alert Message',
                        'borlabs-cookie'
                    ),
                    'error'
                );
            } elseif ($this->checkIdExists($formData['contentBlockerId'])) {
                $errorStatus = true;
                Messages::getInstance()->add(
                    _x(
                        'The <strong>ID</strong> already exists.',
                        'Backend / Content Blocker / Alert Message',
                        'borlabs-cookie'
                    ),
                    'error'
                );
            } elseif (in_array($formData['contentBlockerId'], ['all', 'cookie', 'thirdparty', 'firstparty'], true)) {
                $errorStatus = true;
                Messages::getInstance()->add(
                    _x(
                        'Please change the name of the <strong>ID</strong>. Your selected name for <strong>ID</strong> is reserved and can not be used.',
                        'Backend / Global / Alert Message',
                        'borlabs-cookie'
                    ),
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

        $errorStatus = apply_filters_deprecated(
            'borlabsCookie/bct/validate',
            [$errorStatus, $formData],
            'Borlabs Cookie 2.0',
            'borlabsCookie/contentBlocker/validate'
        );

        return apply_filters('borlabsCookie/contentBlocker/validate', $errorStatus, $formData);
    }
}
