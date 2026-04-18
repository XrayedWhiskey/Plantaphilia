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

namespace BorlabsCookie\Cookie\Frontend\Services;

class WPML
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
     * __construct function.
     */
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
     * getDefault function.
     */
    public function getDefault()
    {
        return [
            'cookieId' => 'wpml',
            'service' => 'WPML',
            'name' => 'WPML',
            'provider' => _x('Owner of this website', 'Frontend / Cookie / WPML / Name', 'borlabs-cookie'),
            'purpose' => _x('Stores the current language.', 'Frontend / Cookie / WPML / Text', 'borlabs-cookie'),
            'privacyPolicyURL' => '',
            'hosts' => [],
            'cookieName' => '_icl_*, wpml_*, wp-wpml_*',
            'cookieExpiry' => _x('1 Day', 'Frontend / Cookie / WPML / Text', 'borlabs-cookie'),
            'optInJS' => '',
            'optOutJS' => '',
            'fallbackJS' => '',
            'settings' => [
                'blockCookiesBeforeConsent' => false,
                'prioritize' => false,
            ],
            'status' => true,
            'undeletetable' => false,
        ];
    }
}
