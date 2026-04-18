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

class ScriptBlocker
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
     * imagePath.
     *
     * @var mixed
     */
    private $imagePath;

    /**
     * tableScriptBlocker.
     *
     * (default value: '')
     *
     * @var string
     */
    private $tableScriptBlocker = '';

    public function __construct()
    {
        global $wpdb;

        $this->tableScriptBlocker = $wpdb->prefix . 'borlabs_cookie_legacy_script_blocker';

        $this->imagePath = plugins_url('assets/images', realpath(__DIR__ . '/../../'));
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
            'scriptBlockerId' => '',
            'name' => '',
            'blockHandles' => [],
            'blockPhrases' => [],
            'status' => false,
            'undeletable' => false,
        ];

        $data = array_merge($default, $data);

        // Remove handles which should not be blocked
        if (!empty($data['blockHandles'])) {
            $blockHandleList = [];

            foreach ($data['blockHandles'] as $handle => $status) {
                if (!empty($status)) {
                    $blockHandleList[$handle] = $handle;
                }
            }

            $data['blockHandles'] = $blockHandleList;
        }

        // Remove block phrase duplicates
        if (!empty($data['blockPhrases'])) {
            $blockPhrases = [];

            foreach ($data['blockPhrases'] as $phrase) {
                $blockPhrases[$phrase] = stripslashes($phrase);
            }

            $data['blockPhrases'] = $blockPhrases;
        }

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
                    '" . esc_sql($data['scriptBlockerId']) . "',
                    '" . esc_sql(stripslashes($data['name'])) . "',
                    '" . esc_sql(serialize($data['blockHandles'])) . "',
                    '" . esc_sql(serialize($data['blockPhrases'])) . "',
                    '" . ((int) ($data['status']) ? 1 : 0) . "',
                    '" . ((int) ($data['undeletable']) ? 1 : 0) . "'
                )
        "
        );

        if (!empty($wpdb->insert_id)) {
            return $wpdb->insert_id;
        }

        return false;
    }

    /**
     * checkIdExists function.
     *
     * @param mixed $scriptBlockerId
     */
    public function checkIdExists($scriptBlockerId)
    {
        global $wpdb;

        $checkId = $wpdb->get_results(
            '
            SELECT
                `script_blocker_id`
            FROM
                `' . $this->tableScriptBlocker . "`
            WHERE
                `script_blocker_id` = '" . esc_sql($scriptBlockerId) . "'
        "
        );

        return (bool) (!empty($checkId[0]->script_blocker_id));
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
                `' . $this->tableScriptBlocker . "`
            WHERE
                `id` = '" . (int) $id . "'
                AND
                `undeletable` = 0
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
            // Validate and create Script Blocker
            if ($action === 'create' && !empty($id) && check_admin_referer('borlabs_cookie_script_blocker_create')) {
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

            // Validate and save Script Blocker
            if ($action === 'save' && !empty($id) && check_admin_referer('borlabs_cookie_script_blocker_save')) {
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

            // Switch status of Script Blocker
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

            // Delete Script Blocker
            if ($action === 'delete' && !empty($id) && wp_verify_nonce($_GET['_wpnonce'], 'delete_' . $id)) {
                $this->delete($id);

                Messages::getInstance()->add(
                    _x('Deleted successfully.', 'Backend / Global / Alert Message', 'borlabs-cookie'),
                    'success'
                );
            }
        }

        if ($action === 'edit' || $action === 'create' || $action === 'save') {
            // Script Blocker should be created but an error occurred
            if (empty($id) || $id === 'new') {
                $this->displayWizardStep_3($_POST);
            } else {
                $this->displayEdit($id, $_POST);
            }
        } elseif ($action === 'wizardStep-1') {
            // Enter URL
            $this->displayWizardStep_1($_POST);
        } elseif ($action === 'wizardStep-2') {
            // Scan website for JavaScripts
            $this->displayWizardStep_2($_POST);
        } elseif ($action === 'wizardStep-3') {
            // Display results
            $this->displayWizardStep_3($_POST);
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
    public function displayEdit($id, $formData = [])
    {
        $scriptBlockerData = $this->get($id);

        if (empty($scriptBlockerData)) {
            Messages::getInstance()->add(
                _x(
                    'The selected <strong>Script Blocker</strong> is not available.',
                    'Backend / Script Blocker / Alert Message',
                    'borlabs-cookie'
                ),
                'error'
            );

            $this->displayOverview();
        } else {
            // Re-insert data
            if (isset($formData['name'])) {
                $scriptBlockerData->name = stripslashes($formData['name']);
            }

            if (isset($formData['status'])) {
                $scriptBlockerData->status = (int) ($formData['status']);
            }

            $inputId = (int) ($scriptBlockerData->id);
            $inputScriptBlockerId = esc_attr(
                !empty($scriptBlockerData->script_blocker_id) ? $scriptBlockerData->script_blocker_id : ''
            );
            $inputName = esc_attr(!empty($scriptBlockerData->name) ? $scriptBlockerData->name : '');
            $inputStatus = !empty($scriptBlockerData->status) ? 1 : 0;
            $switchStatus = $inputStatus ? ' active' : '';

            $blockedHandles = $scriptBlockerData->handles;
            $blockedPhrases = $scriptBlockerData->js_block_phrases;

            sort($blockedHandles, SORT_NATURAL | SORT_FLAG_CASE);
            sort($blockedPhrases, SORT_NATURAL | SORT_FLAG_CASE);

            $textareaUnblockScriptCookieCode = esc_textarea(
                '<script>window.BorlabsCookie.unblockScriptBlockerId("' . $inputScriptBlockerId . '");</script>'
            );
            $textareaUnblockScriptContentBlockerCode = esc_textarea(
                'window.BorlabsCookie.allocateScriptBlockerToContentBlocker(contentBlockerData.id, "'
                . $inputScriptBlockerId . '", "scriptBlockerId");'
            );
            $textareaUnblockScriptContentBlockerCode .= "\n" . esc_textarea(
                'window.BorlabsCookie.unblockScriptBlockerId("' . $inputScriptBlockerId . '");'
            );

            include Backend::getInstance()->templatePath . '/script-blocker-edit.html.php';
        }
    }

    /**
     * displayOverview function.
     */
    public function displayOverview()
    {
        global $wpdb;

        $scriptBlocker = $wpdb->get_results(
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
            ORDER BY
                `name` ASC
        '
        );

        if (!empty($scriptBlocker)) {
            foreach ($scriptBlocker as $key => $data) {
                $data->handles = unserialize($data->handles);
                $data->js_block_phrases = unserialize($data->js_block_phrases);

                sort($data->handles, SORT_NATURAL | SORT_FLAG_CASE);
                sort($data->js_block_phrases, SORT_NATURAL | SORT_FLAG_CASE);

                $scriptBlocker[$key]->handles = implode(', ', $data->handles);
                $scriptBlocker[$key]->js_block_phrases = implode(', ', $data->js_block_phrases);
                $scriptBlocker[$key]->undeletable = (int) ($data->undeletable);
            }
        }

        include Backend::getInstance()->templatePath . '/script-blocker-overview.html.php';
    }

    /**
     * displayWizardStep_1 function.
     *
     * @param mixed $id
     * @param mixed $formData
     */
    public function displayWizardStep_1($formData = [])
    {
        $borlabsCookieStatus = !empty(Config::getInstance()->get('cookieStatus'))
        || !empty(
        Config::getInstance()->get('setupMode')
        ) ? true : false;
        $inputScanPageId = esc_attr(!empty($formData['scanPageId']) ? (int) ($formData['scanPageId']) : 0);
        $inputScanCustomURL = esc_attr(
            !empty($formData['scanCustomURL']) ? stripslashes($formData['scanCustomURL']) : ''
        );
        $inputSearchPhrases = esc_attr(
            !empty($formData['searchPhrases']) ? stripslashes($formData['searchPhrases']) : ''
        );

        include Backend::getInstance()->templatePath . '/script-blocker-wizard-step-1.html.php';
    }

    /**
     * displayWizardStep_2 function.
     *
     * @param mixed $formData (default: [])
     */
    public function displayWizardStep_2($formData = [])
    {
        $errorStatus = false;

        if (empty($formData['scanPageId']) && empty($formData['enableScanCustomURL'])) {
            Messages::getInstance()->add(
                _x('Please select a page.', 'Backend / Script Blocker / Alert Message', 'borlabs-cookie'),
                'error'
            );

            $errorStatus = true;
        }

        if (!empty($formData['enableScanCustomURL'])) {
            if (empty($formData['scanCustomURL'])) {
                Messages::getInstance()->add(
                    _x('Please enter a URL.', 'Backend / Script Blocker / Alert Message', 'borlabs-cookie'),
                    'error'
                );

                $errorStatus = true;
            } else {
                if (filter_var($formData['scanCustomURL'], FILTER_VALIDATE_URL) === false) {
                    Messages::getInstance()->add(
                        _x('URL is not valid.', 'Backend / Script Blocker / Alert Message', 'borlabs-cookie'),
                        'error'
                    );

                    $errorStatus = true;
                }
            }
        }

        if ($errorStatus !== false) {
            $this->displayWizardStep_1($formData);
        } else {
            $scanURL = '';

            if (!empty($formData['scanPageId'])) {
                $postData = get_post($formData['scanPageId']);

                if (!empty($postData->ID)) {
                    $scanURL = get_permalink($postData->ID);
                }
            } else {
                $scanURL = stripslashes($formData['scanCustomURL']);
            }

            // Fallback - Should never happen
            if (empty($scanURL)) {
                $scanURL = get_home_url();
            }

            $inputScanURL = esc_attr($scanURL);
            $inputSearchPhrases = esc_attr(
                !empty($formData['searchPhrases']) ? stripslashes($formData['searchPhrases']) : ''
            );

            $loadingIcon = $this->imagePath . '/borlabs-cookie-icon-black.svg';

            include Backend::getInstance()->templatePath . '/script-blocker-wizard-step-2.html.php';
        }
    }

    /**
     * displayWizardStep_3 function.
     *
     * @param mixed $formData
     */
    public function displayWizardStep_3($formData)
    {
        $errorStatus = false;

        $detectedJavaScripts = $this->getDetectedJavaScripts();

        if (empty(count($detectedJavaScripts, COUNT_RECURSIVE))) {
            Messages::getInstance()->add(
                _x('No JavaScripts could be found.', 'Backend / Script Blocker / Alert Message', 'borlabs-cookie'),
                'error'
            );

            $errorStatus = true;
        }

        if ($errorStatus !== false) {
            $this->displayWizardStep_1($formData);
        } else {
            $inputScriptBlockerId = esc_attr(
                !empty($formData['scriptBlockerId']) ? stripslashes($formData['scriptBlockerId']) : ''
            );
            $inputName = esc_attr(!empty($formData['name']) ? stripslashes($formData['name']) : '');
            $inputStatus = esc_attr(!empty($formData['status']) ? 1 : 0);
            $switchStatus = $inputStatus ? ' active' : '';

            // If an error occurred during saving, these variables are filled with the information,
            // whether a handle or script has been selected to be blocked
            $blockedHandles = [];
            $blockedScriptTags = [];
            $blockedPhrases = [];

            if (!empty($formData['blockHandles'])) {
                $blockHandles = $formData['blockHandles'];
            }

            if (!empty($formData['blockScriptTags'])) {
                $blockedScriptTags = $formData['blockScriptTags'];
            }

            if (!empty($formData['blockPhrases'])) {
                $blockedPhrases = $formData['blockPhrases'];
            }

            include Backend::getInstance()->templatePath . '/script-blocker-wizard-step-3.html.php';
        }
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

        $scriptBlockerData = $wpdb->get_results(
            '
            SELECT
                `id`,
                `script_blocker_id`,
                `name`,
                `handles`,
                `js_block_phrases`,
                `status`
            FROM
                `' . $this->tableScriptBlocker . "`
            WHERE
                `id` = '" . esc_sql($id) . "'
        "
        );

        if (!empty($scriptBlockerData[0]->id)) {
            $data = $scriptBlockerData[0];

            $data->handles = unserialize($data->handles);
            $data->js_block_phrases = unserialize($data->js_block_phrases);
        }

        return $data;
    }

    /**
     * getDetectedJavaScripts function.
     */
    public function getDetectedJavaScripts()
    {
        return get_option('BorlabsCookieLegacyDetectedJavaScripts', []);
    }

    /**
     * handleScanRequest function.
     *
     * @param mixed  $scanURL
     * @param string $searchPhrases (default: '')
     */
    public function handleScanRequest($scanURL, $searchPhrases = '')
    {
        // Prepare search phrase
        if (!empty($searchPhrases)) {
            $searchPhrases = explode(',', $searchPhrases);

            foreach ($searchPhrases as $index => $phrase) {
                $phrase = trim($phrase);

                if (!empty($phrase)) {
                    $searchPhrases[$index] = $phrase;
                }
            }
        }

        update_option('BorlabsCookieLegacyJavaScriptSearchPhrases', $searchPhrases, 'no');

        // Enable JavaScript Handle Scan - will be disabled in JavaScript->saveDetectedJavaScripts()
        update_option('BorlabsCookieLegacyScanJavaScripts', true, 'no');

        // Request website and scan for JavaScript Handles
        $args = [
            'timeout' => 45,
            'body' => ['borlabsCookie' => ['scanJavaScripts' => true]],
        ];

        $response = wp_remote_post(
            $scanURL,
            $args
        );

        $status = false;

        if (
            !empty($response) && is_array($response) && $response['response']['code'] == 200
            && !empty($response['body'])
        ) {
            $status = true;
        }

        return $status;
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
            'status' => false,
        ];

        $data = array_merge($default, $data);

        $wpdb->query(
            '
            UPDATE
                `' . $this->tableScriptBlocker . "`
            SET
                `name` = '" . esc_sql(stripslashes($data['name'])) . "',
                `status` = '" . ((int) ($data['status']) ? 1 : 0) . "'
            WHERE
                `id` = '" . (int) $id . "'
        "
        );

        return $id;
    }

    /**
     * save function.
     *
     * @param mixed $formData
     */
    public function save($formData)
    {
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
                `' . $this->tableScriptBlocker . "`
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

        // Check id if a new script blocker is about to be added
        if (empty($formData['id']) || $formData['id'] === 'new') {
            if (
                empty($formData['scriptBlockerId'])
                || preg_match('/^[a-z\-\_]{3,}$/', $formData['scriptBlockerId']) === 0
            ) {
                $errorStatus = true;
                Messages::getInstance()->add(
                    _x(
                        'Please fill out the field <strong>ID</strong>. The ID must be at least 3 characters long and may only contain: <strong><em>a-z - _</em></strong>',
                        'Backend / Global / Alert Message',
                        'borlabs-cookie'
                    ),
                    'error'
                );
            } elseif ($this->checkIdExists($formData['scriptBlockerId'])) {
                $errorStatus = true;
                Messages::getInstance()->add(
                    _x('The <strong>ID</strong> already exists.', 'Backend / Global / Alert Message', 'borlabs-cookie'),
                    'error'
                );
            }

            $isBlockHandlesEmpty = true;
            $isBlockPhrasesEmpty = true;

            if (!empty($formData['blockHandles'])) {
                foreach ($formData['blockHandles'] as $status) {
                    if ($status === '1') {
                        $isBlockHandlesEmpty = false;

                        break;
                    }
                }
            }

            if (!empty($formData['blockPhrases'])) {
                foreach ($formData['blockPhrases'] as $phrase) {
                    if (strlen($phrase) >= 5) {
                        $isBlockPhrasesEmpty = false;

                        break;
                    }
                }
            }

            if ($isBlockHandlesEmpty === true && $isBlockPhrasesEmpty === true) {
                $errorStatus = true;
                Messages::getInstance()->add(
                    _x(
                        'No JavaScript has been selected for blocking.',
                        'Backend / Script Blocker / Alert Message',
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

        return $errorStatus;
    }
}
