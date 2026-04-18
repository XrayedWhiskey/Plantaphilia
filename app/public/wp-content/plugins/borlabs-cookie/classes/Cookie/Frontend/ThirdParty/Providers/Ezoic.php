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

namespace BorlabsCookie\Cookie\Frontend\ThirdParty\Providers;

use BorlabsCookie\Cookie\Frontend\Cookies;

class Ezoic
{
    private static $instance;

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private $isEzoicActive = false;

    /**
     * __construct function.
     */
    public function __construct()
    {
        $allCookiesGroups = Cookies::getInstance()->getAllCookieGroups();

        if (!empty($allCookiesGroups)) {
            foreach ($allCookiesGroups as $cookieGroupData) {
                if ($cookieGroupData->group_id === 'essential') {
                    if (!empty($cookieGroupData->cookies['ezoic'])) {
                        $this->isEzoicActive = true;
                    }

                    break;
                }

                continue;
            }
        }
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
     * addDataAttribute function.
     *
     * @param mixed $tag
     * @param mixed $handle
     * @param mixed $src
     */
    public function addDataAttribute($tag, $handle, $src)
    {
        if ($this->isEzoicActive) {
            if ($handle === 'borlabs-cookie' || $handle === 'borlabs-cookie-prioritize') {
                $tag = preg_replace('/\<script/', '<script data-pagespeed-no-defer', $tag, 1);
            }
        }

        return $tag;
    }
}
