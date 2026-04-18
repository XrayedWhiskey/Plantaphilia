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

use BorlabsCookie\Cookie\API;
use BorlabsCookie\Cookie\Config;
use BorlabsCookie\Cookie\Multilanguage;
use BorlabsCookie\Cookie\Tools;
use DateInterval;
use DateTime;

class Dashboard
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

    public function __construct()
    {
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
     * display function.
     */
    public function display()
    {
        $news = $this->getNews();

        $inputBorlabsCookieUpgradeStatus = (is_multisite() ? get_site_option('BorlabsCookieLegacyAutomaticImport', false) : get_option('BorlabsCookieLegacyAutomaticImport', false)) ? 1 : 0;
        $switchBorlabsCookieUpgradeStatus = $inputBorlabsCookieUpgradeStatus ? ' active' : '';

        $inputTelemetryStatus = get_option('BorlabsCookieLegacyTelemetryStatus', false) ? 1 : 0;
        $switchTelemetryStatus = $inputTelemetryStatus ? ' active' : '';
        $showTelemetryModal = false;

        if ($inputTelemetryStatus === 0 && License::getInstance()->isLicenseValid()) {
            $lastTimeDisplayTelemetryModal = (int) get_option('BorlabsCookieLegacyShowTelemetryModal', 0);
            $lastTimeDisplayTelemetryModal7d = (int) get_option('BorlabsCookieLegacyShowTelemetryModal7d', 0);
            $lastTimeDisplayTelemetryModal14d = (int) get_option('BorlabsCookieLegacyShowTelemetryModal14d', 0);

            if (
                $lastTimeDisplayTelemetryModal === 0
                || $lastTimeDisplayTelemetryModal < date('Ymd', mktime(date('H'), date('i'), date('s'), date('m') - 3, date('d')))
            ) {
                $showTelemetryModal = true;
                update_option('BorlabsCookieLegacyShowTelemetryModal', date('Ymd'), 'no');
            } elseif (
                $lastTimeDisplayTelemetryModal7d === 0
                && $lastTimeDisplayTelemetryModal < date('Ymd', strtotime('-7 days'))
            ) {
                $showTelemetryModal = true;
                update_option('BorlabsCookieLegacyShowTelemetryModal7d', date('Ymd'), 'no');
            } elseif (
                $lastTimeDisplayTelemetryModal14d === 0
                && $lastTimeDisplayTelemetryModal < date('Ymd', strtotime('-14 days'))
            ) {
                $showTelemetryModal = true;
                update_option('BorlabsCookieLegacyShowTelemetryModal14d', date('Ymd'), 'no');
            }
        }

        $borlabsCookieStatus = Config::getInstance()->get('cookieStatus');
        $cookieVersion = esc_html(get_site_option('BorlabsCookieCookieVersion', 1));
        $statusPHPVersion = SystemCheck::getInstance()->checkPHPVersion();
        $statusDBVersion = SystemCheck::getInstance()->checkDBVersion();
        $statusCacheFolder = SystemCheck::getInstance()->checkCacheFolders();
        $statusSSLSettings = SystemCheck::getInstance()->checkSSLSettings();

        $statusTableContentBlocker = SystemCheck::getInstance()->checkTableContentBlocker();
        $statusTableCookieConsentLog = SystemCheck::getInstance()->checkTableCookieConsentLog();
        $statusTableCookieGroups = SystemCheck::getInstance()->checkTableCookieGroups();
        $statusTableCookies = SystemCheck::getInstance()->checkTableCookies();
        $statusTableScriptBlocker = SystemCheck::getInstance()->checkTableScriptBlocker();
        $statusTableStatistics = SystemCheck::getInstance()->checkTableStatistics();

        $statusDefaultContentBlocker = SystemCheck::getInstance()->checkDefaultContentBlocker();
        $statusDefaultCookieGroups = SystemCheck::getInstance()->checkDefaultCookieGroups();
        $statusDefaultCookies = SystemCheck::getInstance()->checkDefaultCookies();

        // Fix Script Blocker table
        SystemCheck::getInstance()->checkAndFixScriptBlockerTable();

        // Fix Statistics table
        SystemCheck::getInstance()->checkAndFixStatisticsTable();

        // Check and change index of log table
        SystemCheck::getInstance()->checkAndChangeCookieConsentLogIndex();

        // Check and change columns of cookie table
        SystemCheck::getInstance()->checkAndChangeCookiesTable();

        // Check and change index of statistic table
        SystemCheck::getInstance()->checkAndChangeStatisticIndex();

        $totalConsentLogs = number_format_i18n(SystemCheck::getInstance()->getTotalConsentLogs());
        $consentLogTableSize = number_format_i18n(SystemCheck::getInstance()->getConsentLogTableSize(), 2);

        $language = Multilanguage::getInstance()->getCurrentLanguageCode();

        $loadingIcon = $this->imagePath . '/borlabs-cookie-icon-black.svg';

        $chartData = json_encode($this->getChartData());

        $latestUIDData = $this->getLatestUID();

        $statsActive6h = false;
        $statsActive7d = false;
        $statsActive30d = false;

        if (!empty($_GET['borlabsCookieStats'])) {
            if ($_GET['borlabsCookieStats'] === '6h') {
                $statsActive6h = true;
            } elseif ($_GET['borlabsCookieStats'] === '7d') {
                $statsActive7d = true;
            }
        } else {
            $statsActive30d = true;
        }

        include Backend::getInstance()->templatePath . '/dashboard.html.php';
    }

    /**
     * getChartData function.
     */
    public function getChartData()
    {
        global $wpdb;

        $chartData = [
            'labels' => [],
            'datasets' => [
                [
                    'labels' => '',
                    'backgroundColor' => [],
                    'borderColor' => [],
                    'borderWidth' => 1,
                    'data' => [],
                ],
            ],
        ];

        $type = 'standard';

        if (
            !empty($_GET['borlabsCookieStats'])
            && ($_GET['borlabsCookieStats'] === '6h'
                || $_GET['borlabsCookieStats'] === '7d')
        ) {
            if ($_GET['borlabsCookieStats'] === '6h') {
                $cachedChartData = get_transient('borlabs.cookie.chart_data.6h');

                if ($cachedChartData !== false) {
                    return unserialize($cachedChartData);
                }
                $type = '6h';
            } elseif ($_GET['borlabsCookieStats'] === '7d') {
                $cachedChartData = get_transient('borlabs.cookie.chart_data.7d');

                if ($cachedChartData !== false) {
                    return unserialize($cachedChartData);
                }
                $type = '7d';
            }
        } else {
            $cachedChartData = get_transient('borlabs.cookie.chart_data.30d');

            if ($cachedChartData !== false) {
                return unserialize($cachedChartData);
            }
        }

        // Get all Cookie Groups
        $tableCookieGroup = $wpdb->prefix . 'borlabs_cookie_legacy_groups';
        $cookieGroups = $wpdb->get_results(
            '
            SELECT
                `group_id`,
                `name`
            FROM
                `' . $tableCookieGroup . "`
            WHERE
                `language` = '" . esc_sql(Multilanguage::getInstance()->getCurrentLanguageCode()) . "'
                AND
                `status` = 1
            ORDER BY
                `position` ASC
        "
        );

        // Get Chart data
        $tableStatistics = (Config::getInstance()->get('aggregateCookieConsent') ? $wpdb->base_prefix
                : $wpdb->prefix) . 'borlabs_cookie_legacy_statistics';

        // Get last 10000 entries
        $stack = false;
        $chartDataValues = [];

        if ($type === '6h') {
            $consentLogsStatistics = $wpdb->get_results(
                '
                    SELECT
                        count(*) as `count`,
                        date(`stamp`) as `date`,
                        HOUR(`stamp`) as `hour`,
                        `service_group`
                    FROM
                        `' . $tableStatistics . "`
                    WHERE `stamp` >= DATE_FORMAT(NOW() - INTERVAL 5 HOUR, '%Y-%m-%d %H:00:00.000')
                    GROUP BY `service_group`, date(`stamp`), HOUR(`stamp`)
                "
            );

            foreach ($cookieGroups as $cookieGroup) {
                $chartDataValues[$cookieGroup->group_id] = [];

                for ($i = 5; $i >= 0; --$i) {
                    $chartDataValues[$cookieGroup->group_id][Tools::getInstance()->formatTimestamp(
                        (new DateTime())->sub(new DateInterval('PT' . $i . 'H'))->format('Y-m-d H:i:s'),
                        'Y-m-d',
                        'H:00'
                    )]
                        = 0;
                }
            }

            foreach ($consentLogsStatistics as $consentLogsStatistic) {
                if (!isset($chartDataValues[$consentLogsStatistic->service_group])) {
                    $chartDataValues[$consentLogsStatistic->service_group] = [];
                }
                $chartDataValues[$consentLogsStatistic->service_group][Tools::getInstance()->formatTimestamp(
                    $consentLogsStatistic->date . ' ' . $consentLogsStatistic->hour . ':00',
                    'Y-m-d',
                    'H:00'
                )]
                    = (int) ($consentLogsStatistic->count);
            }
            $stack = true;
        } elseif ($type === '7d') {
            $consentLogsStatistics = $wpdb->get_results(
                '
                SELECT
                    count(*) as `count`,
                    date(`stamp`) as `date`,
                    `service_group`
                FROM
                    `' . $tableStatistics . "`
                WHERE `stamp` >= DATE_FORMAT(NOW() - INTERVAL 6 DAY, '%Y-%m-%d 00:00:00.000')
                GROUP BY `service_group`, date(`stamp`)
            "
            );

            foreach ($cookieGroups as $cookieGroup) {
                $chartDataValues[$cookieGroup->group_id] = [];

                for ($i = 6; $i >= 0; --$i) {
                    $chartDataValues[$cookieGroup->group_id][Tools::getInstance()->formatTimestamp((new DateTime())->sub(new DateInterval('P' . $i . 'D'))->format('Y-m-d H:i:s'), 'Y-m-d', '00:00')] = 0;
                }
            }

            foreach ($consentLogsStatistics as $consentLogsStatistic) {
                if (!isset($chartDataValues[$consentLogsStatistic->service_group])) {
                    $chartDataValues[$consentLogsStatistic->service_group] = [];
                }
                $chartDataValues[$consentLogsStatistic->service_group][Tools::getInstance()->formatTimestamp($consentLogsStatistic->date, 'Y-m-d', '00:00')] = (int) ($consentLogsStatistic->count);
            }
            $stack = true;
        } else {
            $stack = false;
            $consentLogsStatistics = $wpdb->get_results(
                '
                SELECT
                    count(*) as `count`,
                    `service_group`
                FROM
                    `' . $tableStatistics . '`
                WHERE `stamp` >= NOW() - INTERVAL 30 DAY
                GROUP BY `service_group`
            '
            );

            foreach ($consentLogsStatistics as $consentLogsStatistic) {
                $chartDataValues[$consentLogsStatistic->service_group] = (int) ($consentLogsStatistic->count);
            }
        }

        if ($stack === true) {
            if (!empty($chartDataValues)) {
                $index = 0;
                $chartData['labels'] = array_keys($chartDataValues['essential']);

                foreach ($chartData['labels'] as $key => $stamp) {
                    if ($_GET['borlabsCookieStats'] === '6h') {
                        $chartData['labels'][$key] = Tools::getInstance()->formatTimestamp($stamp, '', null);
                    } else {
                        $chartData['labels'][$key] = Tools::getInstance()->formatTimestamp($stamp, null, '');
                    }
                }

                $cookieGroupMap = array_column($cookieGroups, 'name', 'group_id');

                foreach ($chartDataValues as $cookieGroup => $data) {
                    $chartData['datasets'][$index] = [
                        'borderColor' => $this->getColor($index, 1),
                        'data' => array_values($data),
                        'label' => $cookieGroupMap[$cookieGroup] ?? $cookieGroup,
                    ];

                    ++$index;
                }
            }
        } else {
            $index = 0;

            foreach ($cookieGroups as $data) {
                $chartData['labels'][] = $data->name;
                $chartData['datasets'][0]['backgroundColor'][$index] = $this->getColor($index, 0.8);
                $chartData['datasets'][0]['borderColor'][$index] = $this->getColor($index, 1);
                $chartData['datasets'][0]['data'][$index] = $chartDataValues[$data->group_id] ?? 0;

                ++$index;
            }
        }

        if ($type === '7d') {
            $transientKey = 'borlabs.cookie.chart_data.7d';
        } elseif ($type === '6h') {
            $transientKey = 'borlabs.cookie.chart_data.6h';
        } else {
            $transientKey = 'borlabs.cookie.chart_data.30d';
        }
        set_transient($transientKey, serialize($chartData), 10 * MINUTE_IN_SECONDS);

        return $chartData;
    }

    /**
     * getColor function.
     *
     * @param mixed $index
     * @param int   $opacity (default: 1)
     */
    public function getColor($index, $opacity = 1)
    {
        $colors = [
            'rgba(255, 99, 132, %opacity%)',
            'rgba(255, 159, 64, %opacity%)',
            'rgba(255, 205, 86, %opacity%)',
            'rgba(75, 192, 192, %opacity%)',
            'rgba(54, 162, 235, %opacity%)',
            'rgba(153, 102, 255, %opacity%)',
            'rgba(201, 203, 207, %opacity%)',
        ];

        return str_replace(
            '%opacity%',
            round($opacity, 2),
            $colors[$index] ?? $colors[0]
        );
    }

    public function getLatestUID()
    {
        global $wpdb;

        $tableCookieConsentLog = (Config::getInstance()->get('aggregateCookieConsent') ? $wpdb->base_prefix
                : $wpdb->prefix) . 'borlabs_cookie_legacy_consent_log';

        $consentLogs = $wpdb->get_results(
            '
            SELECT
                `uid`,
                `cookie_version`,
                `stamp`
            FROM
                `' . $tableCookieConsentLog . '`
            WHERE
                `is_latest` = 1
            ORDER BY
                `stamp` DESC
            LIMIT
                0, 5
        '
        );

        return $consentLogs;
    }

    /**
     * getNews function.
     */
    public function getNews()
    {
        $newsData = [];

        $lastCheck = (int) (get_site_option('BorlabsCookieLegacyNewsLastCheck', 0));

        if (
            empty($lastCheck)
            || $lastCheck < (int) (
                date('Ymd', mktime(date('H'), date('i'), date('s'), date('m'), date('d') - 3))
            )
        ) {
            $responseNews = API::getInstance()->getNews();
        }

        $borlabsCookieNews = get_site_option('BorlabsCookieLegacyNews');

        if (!empty($borlabsCookieNews)) {
            $currentLanguageCode = Multilanguage::getInstance()->getCurrentLanguageCode();

            if (!empty($borlabsCookieNews->{$currentLanguageCode})) {
                $newsData = $borlabsCookieNews->{$currentLanguageCode};
            } else {
                if (!empty($borlabsCookieNews->en)) {
                    $newsData = $borlabsCookieNews->en;
                }
            }
        }

        return $newsData;
    }
}
