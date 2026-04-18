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

class SystemCheck
{
    private static $instance;

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public $templatePath;

    private $messages = [];

    public function __construct()
    {
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
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
     * checkAndChangeCookieConsentLogIndex function.
     */
    public function checkAndChangeCookieConsentLogIndex()
    {
        global $wpdb;

        $tableName = $wpdb->prefix . 'borlabs_cookie_legacy_consent_log';

        if (Install::getInstance()->checkIfIndexExists($tableName, 'is_latest')) {
            // Remove key
            $wpdb->query(
                '
                ALTER TABLE
                    `' . $tableName . '`
                DROP INDEX
                    `is_latest`
            '
            );
        }

        // Add new key
        if (Install::getInstance()->checkIfIndexExists($tableName, 'uid') === false) {
            // Add key
            $wpdb->query(
                '
                ALTER TABLE
                    `' . $tableName . '`
                ADD KEY
                    `uid` (`uid`, `is_latest`)
            '
            );
        }
    }

    /**
     * checkAndChangeCookiesTable function.
     */
    public function checkAndChangeCookiesTable()
    {
        global $wpdb;

        $tableNameCookies = $wpdb->prefix . 'borlabs_cookie_legacy_cookies';

        $cookieNameColumnType = Install::getInstance()->checkTypeOfColumn($tableNameCookies, 'cookie_name', 'text');

        if ($cookieNameColumnType === false) {
            $wpdb->query(
                '
                ALTER TABLE
                    `' . $tableNameCookies . '`
                MODIFY
                    `cookie_name` TEXT NOT NULL
            '
            );
        }

        $cookieExpiryColumnType = Install::getInstance()->checkTypeOfColumn($tableNameCookies, 'cookie_expiry', 'text');

        if ($cookieExpiryColumnType === false) {
            $wpdb->query(
                '
                ALTER TABLE
                    `' . $tableNameCookies . '`
                MODIFY
                    `cookie_expiry` TEXT NOT NULL
            '
            );
        }

        $cookieProviderColumnType = Install::getInstance()->checkFullTypeOfColumn(
            $tableNameCookies,
            'provider',
            'varchar(255)'
        );

        if ($cookieProviderColumnType === false) {
            $wpdb->query(
                '
                ALTER TABLE
                    `' . $tableNameCookies . "`
                MODIFY
                    `provider` varchar(255) NOT NULL DEFAULT ''
            "
            );
        }
    }

    /**
     * checkAndChangeStatisticIndex function.
     */
    public function checkAndChangeStatisticIndex()
    {
        global $wpdb;

        $tableName = $wpdb->prefix . 'borlabs_cookie_legacy_statistics';

        if (Install::getInstance()->checkIfTableExists($tableName) === true) {
            if (Install::getInstance()->checkIfIndexExists($tableName, 'service_group_stamp') === false) {
                // Add key
                $wpdb->query('
                    ALTER TABLE
                        `' . $tableName . '`
                    ADD KEY
                        `service_group_stamp` (`stamp`, `service_group`)
                ');
            }
        }
    }

    /**
     * checkAndFixScriptBlockerTable function.
     */
    public function checkAndFixScriptBlockerTable()
    {
        global $wpdb;

        $charsetCollate = $wpdb->get_charset_collate();
        $tableNameScriptBlocker = $wpdb->prefix
            . 'borlabs_cookie_legacy_script_blocker'; // ->prefix contains base_prefix + blog id

        // Check if Script Blocker table is wrong schema
        $columnStatus = Install::getInstance()->checkIfColumnExists($tableNameScriptBlocker, 'content_blocker_id');

        if ($columnStatus === true) {
            // Fix Script Blocker Table
            $wpdb->query('DROP TABLE IF EXISTS `' . $tableNameScriptBlocker . '`');

            $sqlCreateTableScriptBlocker = Install::getInstance()->getCreateTableStatementScriptBlocker(
                $tableNameScriptBlocker,
                $charsetCollate
            );

            $wpdb->query($sqlCreateTableScriptBlocker);
        }
    }

    public function checkAndFixStatisticsTable()
    {
        global $wpdb;

        $charsetCollate = $wpdb->get_charset_collate();
        $tableName = $wpdb->prefix
            . 'borlabs_cookie_legacy_statistics'; // ->prefix contains base_prefix + blog id

        // Check if Statistic table is wrong schema
        $columnStatus = Install::getInstance()->checkIfColumnExists($tableName, 'script_blocker_id');

        if ($columnStatus === true) {
            // Fix Statistics table
            $wpdb->query('DROP TABLE IF EXISTS `' . $tableName . '`');

            $sqlCreateTableStatistics = Install::getInstance()->getCreateTableStatementStatistics(
                $tableName,
                $charsetCollate
            );

            $wpdb->query($sqlCreateTableStatistics);
        }
    }

    /**
     * checkCacheFolders function.
     */
    public function checkCacheFolders()
    {
        $data = [
            'success' => true,
            'message' => '',
        ];

        // Check if cache folder exists
        if (!file_exists(WP_CONTENT_DIR . '/cache')) {
            if (!is_writable(WP_CONTENT_DIR)) {
                $data['success'] = false;
                $data['message'] = sprintf(
                    _x(
                        'The folder <strong>/%s</strong> is not writable. Please set the right permissions. See <a href="https://borlabs.io/folder-permissions/" rel="nofollow noopener noreferrer" target="_blank">FAQ</a>.',
                        'Backend / System Check / Alert Message',
                        'borlabs-cookie'
                    ),
                    basename(WP_CONTENT_DIR)
                );
            } else {
                mkdir(WP_CONTENT_DIR . '/cache');
                mkdir(WP_CONTENT_DIR . '/cache/borlabs-cookie');
            }
        }

        if (file_exists(WP_CONTENT_DIR . '/cache') && !is_writable(WP_CONTENT_DIR . '/cache')) {
            $data['success'] = false;
            $data['message'] = sprintf(
                _x(
                    'The folder <strong>/%s/cache</strong> is not writable. Please set the right permissions. See <a href="https://borlabs.io/folder-permissions/" rel="nofollow noopener noreferrer" target="_blank">FAQ</a>.',
                    'Backend / System Check / Alert Message',
                    'borlabs-cookie'
                ),
                basename(WP_CONTENT_DIR)
            );
        }

        if (
            file_exists(WP_CONTENT_DIR . '/cache') && is_writable(WP_CONTENT_DIR . '/cache')
            && !file_exists(
                WP_CONTENT_DIR . '/cache/borlabs-cookie'
            )
        ) {
            mkdir(WP_CONTENT_DIR . '/cache/borlabs-cookie');
        }

        if (
            file_exists(WP_CONTENT_DIR . '/cache/borlabs-cookie')
            && !is_writable(
                WP_CONTENT_DIR . '/cache/borlabs-cookie'
            )
        ) {
            $data['success'] = false;
            $data['message'] = sprintf(
                _x(
                    'The folder <strong>/%s/cache/borlabs-cookie</strong> is not writable. Please set the right permissions. See <a href="https://borlabs.io/folder-permissions/" rel="nofollow noopener noreferrer" target="_blank">FAQ</a>.',
                    'Backend / System Check / Alert Message',
                    'borlabs-cookie'
                ),
                basename(WP_CONTENT_DIR)
            );
        }

        if (!file_exists(WP_CONTENT_DIR . '/cache/borlabs-cookie')) {
            $data['success'] = false;
            $data['message'] = sprintf(
                _x(
                    'The folder <strong>/%s/cache/borlabs-cookie</strong> does not exist. Please set the right permissions. See <a href="https://borlabs.io/folder-permissions/" rel="nofollow noopener noreferrer" target="_blank">FAQ</a>.',
                    'Backend / System Check / Alert Message',
                    'borlabs-cookie'
                ),
                basename(WP_CONTENT_DIR)
            );
        }

        return $data;
    }

    /**
     * checkDBVersion function.
     */
    public function checkDBVersion()
    {
        global $wpdb;

        if (method_exists($wpdb, 'db_server_info')) {
            $dbServerInfo = $wpdb->db_server_info() ? $wpdb->db_server_info() : '';
        } else {
            if ($wpdb->use_mysqli) {
                $dbServerInfo = mysqli_get_server_info($wpdb->dbh);
            } elseif (function_exists('mysql_get_server_info')) {
                $dbServerInfo = mysql_get_server_info($wpdb->dbh);
            }
        }

        $dbVersion = $wpdb->db_version();
        $data = [
            'success' => true,
            'message' => $dbServerInfo,
        ];

        if ($dbVersion === null) {
            return [
                'success' => false,
                'message' => $wpdb->db_server_info(),
            ];
        }

        if (version_compare($dbVersion, '5.6', '<') && strpos(strtolower($dbServerInfo), 'mariadb') === false) {
            $data['success'] = false;
            $data['message'] = sprintf(_x(
                'Your database version %s is outdated.',
                'Backend / Global / Alert Message',
                'borlabs-cookie'
            ), $dbVersion);
        } elseif (strpos(strtolower($dbServerInfo), 'mariadb') !== false) {
            $data['message'] = $wpdb->get_var('SELECT VERSION()');
        }

        return $data;
    }

    /**
     * checkDefaultContentBlocker function.
     */
    public function checkDefaultContentBlocker()
    {
        global $wpdb;

        $data = [
            'success' => true,
            'message' => '',
        ];

        $tableName = $wpdb->prefix . 'borlabs_cookie_legacy_content_blocker';
        $sql = '
            SELECT
                `content_blocker_id`
            FROM
                `' . $tableName . "`
            WHERE
                `content_blocker_id` IN ('default', 'facebook', 'googlemaps', 'instagram', 'openstreetmap', 'twitter', 'vimeo', 'youtube')
                AND
                `language` = '" . esc_sql(Multilanguage::getInstance()->getCurrentLanguageCode()) . "'
        ";

        $defaultContentBlocker = $wpdb->get_results($sql);

        if (empty($defaultContentBlocker) || count($defaultContentBlocker) !== 8) {
            // Try to insert default entries
            ContentBlocker::getInstance()->resetDefault();

            // Check again
            $defaultContentBlocker = $wpdb->get_results($sql);

            if (empty($defaultContentBlocker) || count($defaultContentBlocker) !== 8) {
                $data = [
                    'success' => false,
                    'message' => sprintf(
                        _x(
                            'Could not insert default <strong>Content Blocker</strong>.',
                            'Backend / System Check / Alert Message',
                            'borlabs-cookie'
                        ),
                        $tableName
                    ),
                ];
            }
        }

        return $data;
    }

    /**
     * checkDefaultCookieGroups function.
     */
    public function checkDefaultCookieGroups()
    {
        global $wpdb;

        $data = [
            'success' => true,
            'message' => '',
        ];

        $tableName = $wpdb->prefix . 'borlabs_cookie_legacy_groups';
        $sql = '
            SELECT
                `group_id`
            FROM
                `' . $tableName . "`
            WHERE
                `group_id` IN ('essential', 'statistics', 'marketing', 'external-media')
                AND
                `language` = '" . esc_sql(Multilanguage::getInstance()->getCurrentLanguageCode()) . "'
        ";

        $defaultCookieGroups = $wpdb->get_results($sql);

        if (empty($defaultCookieGroups) || count($defaultCookieGroups) !== 4) {
            // Try to insert default entries
            $wpdb->query(
                Install::getInstance()->getDefaultEntriesCookieGroups(
                    $tableName,
                    Multilanguage::getInstance()->getCurrentLanguageCode()
                )
            );

            // Check again
            $defaultCookieGroups = $wpdb->get_results($sql);

            if (empty($defaultCookieGroups) || count($defaultCookieGroups) !== 4) {
                $data = [
                    'success' => false,
                    'message' => sprintf(
                        _x(
                            'Could not insert default <strong>Cookie Groups</strong>.',
                            'Backend / System Check / Alert Message',
                            'borlabs-cookie'
                        ),
                        $tableName
                    ),
                ];
            }
        }

        // Change status of essential cookie group "essential"
        $wpdb->query(
            '
            UPDATE
                `' . $tableName . "`
            SET
                `status` = 1
            WHERE
                `group_id` = 'essential'
                AND
                `language` = '" . esc_sql(Multilanguage::getInstance()->getCurrentLanguageCode()) . "'
        "
        );

        return $data;
    }

    /**
     * checkDefaultCookies function.
     */
    public function checkDefaultCookies()
    {
        global $wpdb;

        $data = [
            'success' => true,
            'message' => '',
        ];

        $tableName = $wpdb->prefix . 'borlabs_cookie_legacy_cookies';
        $sql = '
            SELECT
                `cookie_id`
            FROM
                `' . $tableName . "`
            WHERE
                `cookie_id` IN ('borlabs-cookie')
                AND
                `language` = '" . esc_sql(Multilanguage::getInstance()->getCurrentLanguageCode()) . "'
        ";

        $defaultCookies = $wpdb->get_results($sql);

        if (empty($defaultCookies)) {
            // Try to insert default entries (if the result was empty, it was due a change of the language and in this case, we will add all default cookies)
            $wpdb->query(
                Install::getInstance()->getDefaultEntriesCookies(
                    $tableName,
                    Multilanguage::getInstance()->getCurrentLanguageCode(),
                    $wpdb->prefix . 'borlabs_cookie_legacy_groups'
                )
            );

            // Check again
            $defaultCookies = $wpdb->get_results($sql);

            if (empty($defaultCookies)) {
                $data = [
                    'success' => false,
                    'message' => sprintf(
                        _x(
                            'Could not insert default <strong>Cookies</strong>.',
                            'Backend / System Check / Alert Message',
                            'borlabs-cookie'
                        ),
                        $tableName
                    ),
                ];
            }
        }

        // Change status of essential cookie "borlabs-cookie"
        $wpdb->query(
            '
            UPDATE
                `' . $tableName . "`
            SET
                `status` = 1
            WHERE
                `cookie_id` = 'borlabs-cookie'
                AND
                `language` = '" . esc_sql(Multilanguage::getInstance()->getCurrentLanguageCode()) . "'
        "
        );

        return $data;
    }

    /**
     * checkLanguageSettings function.
     */
    public function checkLanguageSettings()
    {
        $data = [
            'success' => true,
            'message' => '',
        ];

        $language = Multilanguage::getInstance()->getCurrentLanguageCode();

        if (empty($language)) {
            $data['success'] = false;
            $data['message'] = _x(
                'Your language configuration is broken. Disable all plugins except <strong>Borlabs Cookie</strong> until this message disappears. When you have found the plugin that is causing this error, check if an update is available and install it.',
                'Backend / System Check / Alert Message',
                'borlabs-cookie'
            );
        }

        return $data;
    }

    /**
     * checkPHPVersion function.
     */
    public function checkPHPVersion()
    {
        $data = [
            'success' => true,
            'message' => phpversion(),
        ];

        if (version_compare(phpversion(), '7.4', '<')) {
            $data['success'] = false;
            $data['message'] = sprintf(_x(
                'Your PHP version %s is <a href="http://php.net/supported-versions.php" rel="nofollow noopener noreferrer" target="_blank">outdated</a>.',
                'Backend / Global / Alert Message',
                'borlabs-cookie'
            ), phpversion());
        }

        return $data;
    }

    /**
     * checkSettings function.
     */
    public function checkSSLSettings()
    {
        $data = [
            'success' => true,
            'message' => '',
        ];

        // Check if HTTPS settings are correct
        $contentURL = parse_url(WP_CONTENT_URL);

        if ($contentURL['scheme'] === 'https') {
            return $data;
        }

        if (
            empty($_SERVER['SERVER_PORT']) || empty($_SERVER['HTTPS'])
            || ($_SERVER['SERVER_PORT'] !== '443'
                && !isset($_SERVER['HTTP_X_FORWARDED_PORT']))
        ) {
            $data['success'] = false;
            $data['message'] = _x(
                'Your website is not using a SSL certification.',
                'Backend / System Check / Alert Message',
                'borlabs-cookie'
            );

            return $data;
        }

        $data['success'] = false;
        $data['message'] = _x(
            'Your SSL configuration is not correct. Please go to <strong>Settings &gt; General</strong> and replace <strong><em>http://</em></strong> with <strong><em>https://</em></strong> in the settings <strong>WordPress Address (URL)</strong> and <strong>Site Address (URL)</strong>.',
            'Backend / System Check / Alert Message',
            'borlabs-cookie'
        );
        $data['message'] .= '<br>WP_CONTENT_URL: ' . WP_CONTENT_URL;
        $data['message'] .= "<br>\$_SERVER['HTTPS']: " . $_SERVER['HTTPS'];
        $data['message'] .= "<br>\$_SERVER['SERVER_PORT']: " . $_SERVER['SERVER_PORT'];

        return $data;
    }

    /**
     * checkTable function.
     *
     * @param mixed $tableName
     * @param mixed $sqlCreateStatement
     */
    public function checkTable($tableName, $sqlCreateStatement)
    {
        global $wpdb;

        $data = [
            'success' => true,
            'message' => '',
        ];

        if (!Install::getInstance()->checkIfTableExists($tableName)) {
            // Try to install the table
            dbDelta($sqlCreateStatement);

            // Check again
            if (!Install::getInstance()->checkIfTableExists($tableName)) {
                $data = [
                    'success' => false,
                    'message' => sprintf(
                        _x(
                            'The table <strong>%s</strong> could not be created, please check your server error logs for more details.',
                            'Backend / System Check / Alert Message',
                            'borlabs-cookie'
                        ),
                        $tableName
                    ),
                ];
            }
        }

        return $data;
    }

    /**
     * checkTableContentBlocker function.
     */
    public function checkTableContentBlocker()
    {
        global $wpdb;

        $charsetCollate = $wpdb->get_charset_collate();
        $tableName = $wpdb->prefix . 'borlabs_cookie_legacy_content_blocker';

        $sqlCreateTable = Install::getInstance()->getCreateTableStatementContentBlocker($tableName, $charsetCollate);

        $data = $this->checkTable($tableName, $sqlCreateTable);

        return $data;
    }

    /**
     * checkTableCookieConsentLog function.
     */
    public function checkTableCookieConsentLog()
    {
        global $wpdb;

        $charsetCollate = $wpdb->get_charset_collate();
        $tableName = $wpdb->prefix . 'borlabs_cookie_legacy_consent_log';

        $sqlCreateTable = Install::getInstance()->getCreateTableStatementCookieConsentLog($tableName, $charsetCollate);

        $data = $this->checkTable($tableName, $sqlCreateTable);

        return $data;
    }

    /**
     * checkTableCookieGroups function.
     */
    public function checkTableCookieGroups()
    {
        global $wpdb;

        $charsetCollate = $wpdb->get_charset_collate();
        $tableName = $wpdb->prefix . 'borlabs_cookie_legacy_groups';

        $sqlCreateTable = Install::getInstance()->getCreateTableStatementCookieGroups($tableName, $charsetCollate);

        $data = $this->checkTable($tableName, $sqlCreateTable);

        return $data;
    }

    /**
     * checkTables function.
     */
    public function checkTableCookies()
    {
        global $wpdb;

        $charsetCollate = $wpdb->get_charset_collate();
        $tableName = $wpdb->prefix . 'borlabs_cookie_legacy_cookies';

        $sqlCreateTable = Install::getInstance()->getCreateTableStatementCookies($tableName, $charsetCollate);

        $data = $this->checkTable($tableName, $sqlCreateTable);

        return $data;
    }

    /**
     * checkTableScriptBlocker function.
     */
    public function checkTableScriptBlocker()
    {
        global $wpdb;

        $charsetCollate = $wpdb->get_charset_collate();
        $tableName = $wpdb->prefix . 'borlabs_cookie_legacy_script_blocker';

        $sqlCreateTable = Install::getInstance()->getCreateTableStatementScriptBlocker($tableName, $charsetCollate);

        $data = $this->checkTable($tableName, $sqlCreateTable);

        return $data;
    }

    /**
     * checkTableStatistics function.
     */
    public function checkTableStatistics()
    {
        global $wpdb;

        $charsetCollate = $wpdb->get_charset_collate();
        $tableName = $wpdb->prefix . 'borlabs_cookie_legacy_statistics';

        $sqlCreateTable = Install::getInstance()->getCreateTableStatementStatistics($tableName, $charsetCollate);

        $data = $this->checkTable($tableName, $sqlCreateTable);

        return $data;
    }

    /**
     * getConsentLogTableSize function.
     */
    public function getConsentLogTableSize()
    {
        global $wpdb;

        $table = (Config::getInstance()->get('aggregateCookieConsent') ? $wpdb->base_prefix : $wpdb->prefix)
            . 'borlabs_cookie_legacy_consent_log';

        $dbName = $wpdb->dbname;

        // HyperDB workaround
        if (empty($dbName) && defined('DB_NAME')) {
            $dbName = DB_NAME;
        }

        $consentLogTableSize = $wpdb->get_results(
            "
            SELECT
                round(((`data_length` + `index_length`) / 1024 / 1024), 2) `size_in_mb`
            FROM
                `information_schema`.`TABLES`
            WHERE
                `TABLE_SCHEMA` = '" . esc_sql($dbName) . "'
                AND
                `TABLE_NAME` = '" . $table . "'
        "
        );

        return !empty($consentLogTableSize[0]->size_in_mb) ? $consentLogTableSize[0]->size_in_mb : 0;
    }

    /**
     * getTotalConsentLogs function.
     */
    public function getTotalConsentLogs()
    {
        global $wpdb;

        $table = (Config::getInstance()->get('aggregateCookieConsent') ? $wpdb->base_prefix : $wpdb->prefix)
            . 'borlabs_cookie_legacy_consent_log';

        $totalConsentLogs = $wpdb->get_results(
            '
            SELECT
                COUNT(*) as `total`
            FROM
                `' . $table . '`
        '
        );

        return !empty($totalConsentLogs[0]->total) ? $totalConsentLogs[0]->total : 0;
    }
}
