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

class CrossDomainCookie
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
     * handleRequest function.
     *
     * @param mixed $data
     */
    public function handleRequest($data)
    {
        if (!empty($data['cookieData']) && !empty($_SERVER['HTTP_REFERER'])) {
            // Validate referer
            $refererURLInfo = parse_url($_SERVER['HTTP_REFERER']);

            $isValidRequest = false;

            foreach (Config::getInstance()->get('crossDomainCookie') as $url) {
                if (
                    strpos(
                        $refererURLInfo['scheme'] . '://' . $refererURLInfo['host'] . ($refererURLInfo['path'] ?? '/'),
                        $url
                    ) !== false
                ) {
                    $isValidRequest = true;
                }
            }

            if ($isValidRequest === false) {
                return;
            }

            $cookieData = stripslashes($data['cookieData']);

            if (Tools::getInstance()->isStringJSON($cookieData)) {
                $cookieData = json_decode($cookieData, true);
                $language = !empty($_GET['cookieLang']) ? strtolower(
                    preg_replace('/[^a-z\-_]+/', '', $_GET['cookieLang'])
                ) : 'en';

                $language = apply_filters('borlabsCookie/crossDomainCookie/language', $language);

                $consents = [];

                $allowedCookieGroups = Cookies::getInstance()->getAllCookieGroupsOfLanguage($language);
                $allowedCookies = Cookies::getInstance()->getAllCookiesOfLanguage($language);

                if (empty($allowedCookieGroups) && empty($allowedCookies)) {
                    $language = Multilanguage::getInstance()->getCurrentLanguageCode();

                    $allowedCookieGroups = Cookies::getInstance()->getAllCookieGroupsOfLanguage($language);
                    $allowedCookies = Cookies::getInstance()->getAllCookiesOfLanguage($language);
                }

                // Validate consents
                if (!empty($cookieData['consents'])) {
                    foreach ($cookieData['consents'] as $cookieGroup => $cookies) {
                        if (!empty($allowedCookieGroups[$cookieGroup])) {
                            $consents[$cookieGroup] = [];

                            if (!empty($cookies)) {
                                foreach ($cookies as $cookie) {
                                    if (!empty($allowedCookies[$cookie])) {
                                        $consents[$cookieGroup][] = $cookie;
                                    }
                                }
                            }
                        }
                    }
                }

                $consents = apply_filters('borlabsCookie/crossDomainCookie/consents', $consents, $cookieData);
                $cookieData['consents'] = $consents;
                $siteURL = get_home_url();
                $siteURLInfo = parse_url($siteURL);
                $cookiePath = !empty($siteURLInfo['path']) ? $siteURLInfo['path'] : '/';

                if (Config::getInstance()->get('automaticCookieDomainAndPath') === false) {
                    $cookiePath = Config::getInstance()->get('cookiePath');
                }

                $cookieData['domainPath'] = Config::getInstance()->get('cookieDomain') . $cookiePath;
                ConsentLog::getInstance()->add($cookieData, $language, isset($data['essentialStatistic']) && $data['essentialStatistic'] === '1');
                $cookieInformation = [];
                $cookieInformation[] = 'borlabs-cookie=' . rawurlencode(json_encode($cookieData));

                // Cookie Domain
                if (
                    !empty(Config::getInstance()->get('cookieDomain'))
                    && empty(
                    Config::getInstance()->get(
                        'automaticCookieDomainAndPath'
                    )
                    )
                ) {
                    $cookieInformation[] = 'domain=' . Config::getInstance()->get('cookieDomain');
                }

                // Cookie Path
                $cookieInformation[] = 'path=' . Config::getInstance()->get('cookiePath');

                // Expiration Date
                $cookieInformation[] = 'expires=' . $cookieData['expires'];

                // Set cookie
                $javascript = '<script>document.cookie = "' . implode(';', $cookieInformation) . '";</script>';

                // Cross-Cookie workaround due SameSite Policy - Does not work in incognito mode because browsers block third party cookies in that mode by default
                header(
                    'Set-Cookie: borlabs-cookie=' . rawurlencode(json_encode($cookieData)) . '; SameSite=None; Secure'
                );

                echo '<html><head><meta name="robots" content="noindex,nofollow,norarchive"></head><body>'
                    . $javascript . '</body></html>';
            }
        }
    }
}
