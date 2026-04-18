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
use BorlabsCookie\Cookie\Tools;

class Settings
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
            // Save Cookie Settings
            if ($action === 'save' && check_admin_referer('borlabs_cookie_settings_save')) {
                $this->save($_POST);

                Messages::getInstance()->add(
                    _x('Saved successfully.', 'Backend / Global / Alert Message', 'borlabs-cookie'),
                    'success'
                );
            }
        }

        $this->displayOverview();
    }

    /**
     * displayOverview function.
     */
    public function displayOverview()
    {
        $siteURLInfo = parse_url(home_url());
        $networkDomain = $siteURLInfo['host'];
        $networkPath = !empty($siteURLInfo['path']) ? $siteURLInfo['path'] : '/';
        $postTypes = $this->getPostTypes();

        $inputCookieStatus = !empty(Config::getInstance()->get('cookieStatus')) ? 1 : 0;
        $switchCookieStatus = $inputCookieStatus ? ' active' : '';
        $inputSetupModeStatus = !empty(Config::getInstance()->get('setupMode')) ? 1 : 0;
        $switchSetupModeStatus = $inputSetupModeStatus ? ' active' : '';
        $cookieVersion = esc_html(get_site_option('BorlabsCookieCookieVersion', 1));
        $inputCookieBeforeConsent = !empty(Config::getInstance()->get('cookieBeforeConsent')) ? 1 : 0;
        $switchCookieBeforeConsent = $inputCookieBeforeConsent ? ' active' : '';
        $inputAggregateCookieConsent = !empty(Config::getInstance()->get('aggregateCookieConsent')) ? 1 : 0;
        $switchAggregateCookieConsent = $inputAggregateCookieConsent ? ' active' : '';
        $inputCookiesForBots = !empty(Config::getInstance()->get('cookiesForBots')) ? 1 : 0;
        $switchCookiesForBots = $inputCookiesForBots ? ' active' : '';
        $inputRespectDoNotTrack = !empty(Config::getInstance()->get('respectDoNotTrack')) ? 1 : 0;
        $switchRespectDoNotTrack = $inputRespectDoNotTrack ? ' active' : '';
        $inputReloadAfterConsent = !empty(Config::getInstance()->get('reloadAfterConsent')) ? 1 : 0;
        $switchReloadAfterConsent = $inputReloadAfterConsent ? ' active' : '';
        $inputReloadAfterOptOut = !empty(Config::getInstance()->get('reloadAfterOptOut')) ? 1 : 0;
        $switchReloadAfterOptOut = $inputReloadAfterOptOut ? ' active' : '';

        $inputJqueryHandle = esc_attr(
            !empty(Config::getInstance()->get('jQueryHandle')) ? Config::getInstance()->get('jQueryHandle')
                : 'jquery-core'
        );
        $enabledPostTypes = !empty(Config::getInstance()->get('metaBox')) ? Config::getInstance()->get('metaBox') : [];

        $inputAutomaticCookieDomainAndPath = !empty(Config::getInstance()->get('automaticCookieDomainAndPath')) ? 1
            : 0;
        $switchAutomaticCookieDomainAndPath = $inputAutomaticCookieDomainAndPath ? ' active' : '';
        $inputCookieDomain = esc_attr(
            !empty(Config::getInstance()->get('cookieDomain')) ? Config::getInstance()->get('cookieDomain')
                : $networkDomain
        );
        $inputCookiePath = esc_attr(
            !empty(Config::getInstance()->get('cookiePath')) ? Config::getInstance()->get('cookiePath') : ''
        );
        $inputCookieSecure = !empty(Config::getInstance()->get('cookieSecure')) ? 1
            : 0;
        $switchCookieSecure = $inputCookieSecure ? ' active' : '';
        $inputCookieLifetime = esc_attr(
            !empty(Config::getInstance()->get('cookieLifetime')) ? Config::getInstance()->get('cookieLifetime') : 365
        );
        $inputCookieLifetimeEssentialOnly = esc_attr(
            !empty(Config::getInstance()->get('cookieLifetimeEssentialOnly')) ? Config::getInstance()->get(
                'cookieLifetimeEssentialOnly'
            ) : 365
        );
        $textareaCrossDomainCookie = esc_textarea(
            !empty(Config::getInstance()->get('crossDomainCookie')) ? implode(
                "\n",
                Config::getInstance()->get('crossDomainCookie')
            ) : ''
        );

        // Check if Do Not Track is enabled
        $doNotTrackIsActive = false;

        if (!empty($_SERVER['HTTP_DNT'])) {
            $doNotTrackIsActive = true;
        }

        // Check if host is different
        $cookieDomainIsDifferent = false;

        if (
            !empty(Config::getInstance()->get('cookieDomain'))
            && $networkDomain !== Config::getInstance()->get(
                'cookieDomain'
            )
        ) {
            if (
                strpos(Config::getInstance()->get('cookieDomain'), '.') !== 0
                || strpos(
                    $networkDomain,
                    ltrim(Config::getInstance()->get('cookieDomain'), '.')
                ) === false
            ) {
                $cookieDomainIsDifferent = true;
            }
        }

        $optionCookieSameSiteLax = Config::getInstance()->get('cookieSameSite') === 'Lax' ? ' selected'
            : '';
        $optionCookieSameSiteNone = Config::getInstance()->get('cookieSameSite') === 'None'
            ? ' selected' : '';

        $sameSiteError = false;

        if (empty(Config::getInstance()->get('cookieSecure')) && Config::getInstance()->get('cookieSameSite') === 'None') {
            $sameSiteError = true;
        }

        // No SSL but secure attribut is active
        $secureAttributError = false;

        if (!empty(Config::getInstance()->get('cookieSecure')) && SystemCheck::getInstance()->checkSSLSettings()['success'] === false) {
            $secureAttributError = true;
        }

        include Backend::getInstance()->templatePath . '/settings.html.php';
    }

    /**
     * getPostTypes function.
     */
    public function getPostTypes()
    {
        $postTypes = get_post_types(['public' => true], 'objects');

        $orderedPostTypes = [];

        // Build list
        foreach ($postTypes as $postType) {
            $orderedPostTypes[$postType->name] = $postType->label;
        }

        // Order list
        asort($orderedPostTypes, SORT_NATURAL | SORT_FLAG_CASE);

        $newOrderedPostTypes = [];

        foreach ($orderedPostTypes as $postType => $postTypeData) {
            // Exclude attachments from list
            if (!in_array($postType, ['attachment'], true)) {
                $newOrderedPostTypes[$postType] = $postTypes[$postType];
            }
        }

        unset($postTypes);
        unset($orderedPostTypes);

        return $newOrderedPostTypes;
    }

    /**
     * save function.
     *
     * @param mixed $formData
     */
    public function save($formData)
    {
        $updatedConfig = Config::getInstance()->get();

        $updatedConfig['cookieStatus'] = !empty($formData['cookieStatus']) ? true : false;
        $updatedConfig['setupMode'] = !empty($formData['setupMode']) ? true : false;

        if (!empty($formData['updateCookieVersion'])) {
            $currentVersion = get_site_option('BorlabsCookieCookieVersion', 1);

            update_site_option('BorlabsCookieCookieVersion', $currentVersion + 1);
        }

        $updatedConfig['cookieBeforeConsent'] = !empty($formData['cookieBeforeConsent']) ? true : false;
        $updatedConfig['aggregateCookieConsent'] = !empty($formData['aggregateCookieConsent']) ? true : false;
        $updatedConfig['cookiesForBots'] = !empty($formData['cookiesForBots']) ? true : false;
        $updatedConfig['respectDoNotTrack'] = !empty($formData['respectDoNotTrack']) ? true : false;
        $updatedConfig['reloadAfterConsent'] = !empty($formData['reloadAfterConsent']) ? true : false;
        $updatedConfig['reloadAfterOptOut'] = !empty($formData['reloadAfterOptOut']) ? true : false;
        $updatedConfig['jQueryHandle'] = !empty($formData['jQueryHandle']) ? preg_replace(
            '/[^a-zA-z0-9\-_\.]+/',
            '',
            stripslashes($formData['jQueryHandle'])
        ) : 'jquery-core';
        $updatedConfig['metaBox'] = !empty($formData['metaBox']) ? $formData['metaBox'] : [];

        $siteURLInfo = parse_url(home_url());
        $networkDomain = $siteURLInfo['host'];

        if (!empty($formData['cookieDomain'])) {
            $formData['cookieDomain'] = str_replace(
                ['https://', 'http://'],
                '',
                stripslashes($formData['cookieDomain'])
            );
        }

        $updatedConfig['automaticCookieDomainAndPath'] = !empty($formData['automaticCookieDomainAndPath']) ? true : false;
        $updatedConfig['cookieDomain'] = !empty($formData['cookieDomain']) ? stripslashes($formData['cookieDomain']) : $networkDomain;
        $updatedConfig['cookiePath'] = !empty($formData['cookiePath']) ? stripslashes($formData['cookiePath']) : '/';
        $updatedConfig['cookieSameSite'] = 'Lax';

        if (!empty($formData['cookieSameSite'])) {
            if ($formData['cookieSameSite'] === 'None') {
                $updatedConfig['cookieSameSite'] = 'None';
            }
        }

        $updatedConfig['cookieSecure'] = !empty($formData['cookieSecure']) ? true : false;
        $updatedConfig['cookieLifetime'] = !empty($formData['cookieLifetime']) ? (int) ($formData['cookieLifetime']) : 365;
        $updatedConfig['cookieLifetimeEssentialOnly'] = !empty($formData['cookieLifetimeEssentialOnly']) ? (int) ($formData['cookieLifetimeEssentialOnly']) : 365;

        // Clean hosts
        $updatedConfig['crossDomainCookie'] = Tools::getInstance()->cleanHostList($formData['crossDomainCookie'], true);

        // Save config
        Config::getInstance()->saveConfig($updatedConfig);

        // Update CSS File
        CSS::getInstance()->save();
    }
}
