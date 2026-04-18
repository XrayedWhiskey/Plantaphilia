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

class CookieBlocker
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
     * deleteImpreciseCookie function.
     *
     * @param mixed $cookieName
     * @param mixed $impreciseCookieName
     */
    public function deleteImpreciseCookie($impreciseCookieName)
    {
        if (!empty($_COOKIE)) {
            $impreciseCookieName = str_replace('*', '', $impreciseCookieName);

            foreach ($_COOKIE as $cookieName => $cookieData) {
                if (strpos($cookieName, $impreciseCookieName) !== false) {
                    unset($_COOKIE[$cookieName]);

                    setcookie($cookieName, '', -1, '/');
                }
            }
        }
    }

    /**
     * deletePreciseCookie function.
     *
     * @param mixed $cookieName
     */
    public function deletePreciseCookie($cookieName)
    {
        if (!empty($_COOKIE[$cookieName])) {
            unset($_COOKIE[$cookieName]);

            setcookie($cookieName, '', -1, '/');
        }
    }

    /**
     * handleBlocking function.
     */
    public function handleBlocking()
    {
        if (!empty($_COOKIE)) {
            // Get all Cookies were blocking is active
            $cookieGroups = Cookies::getInstance()->getAllCookieGroups();

            if (!empty($cookieGroups)) {
                foreach ($cookieGroups as $groupData) {
                    if (!empty($groupData->cookies)) {
                        foreach ($groupData->cookies as $cookieData) {
                            if (!empty($cookieData->settings['blockCookiesBeforeConsent'])) {
                                // Check if consent was given
                                if (Cookies::getInstance()->checkConsent($cookieData->cookie_id) === false) {
                                    // Find and block/delete cookies
                                    $cookieNameList = $this->prepareCookieNamesList($cookieData->cookie_name);

                                    if (!empty($cookieNameList)) {
                                        foreach ($cookieNameList as $cookieName) {
                                            if (strpos($cookieName, '*') !== false) {
                                                $this->deleteImpreciseCookie($cookieName);
                                            } else {
                                                $this->deletePreciseCookie($cookieName);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * prepareCookieNamesList function.
     *
     * @param mixed $cookieNames
     */
    public function prepareCookieNamesList($cookieNames)
    {
        $cookieNameList = [];

        if (!empty($cookieNames) && is_string($cookieNames)) {
            $cookieNames = explode(',', $cookieNames);

            if (!empty($cookieNames)) {
                foreach ($cookieNames as $cookieName) {
                    $cookieName = trim($cookieName);

                    if (!empty($cookieName)) {
                        $cookieNameList[$cookieName] = $cookieName;
                    }
                }
            }
        }

        return $cookieNameList;
    }
}
