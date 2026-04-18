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

class Help
{
    private static $instance;

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

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
        $inputTelemetryStatus = get_option('BorlabsCookieLegacyTelemetryStatus', false) ? 1 : 0;
        $switchTelemetryStatus = $inputTelemetryStatus ? ' active' : '';

        $borlabsCookieStatus = Config::getInstance()->get('cookieStatus');
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

        include Backend::getInstance()->templatePath . '/help.html.php';
    }
}
