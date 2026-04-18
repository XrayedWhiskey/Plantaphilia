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

class Custom
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

    public function getDefault()
    {
        return [
            'cookieId' => '',
            'service' => 'Custom',
            'name' => _x('Custom', 'Frontend / Cookie / Custom / Text', 'borlabs-cookie'),
            'provider' => '',
            'purpose' => '',
            'privacyPolicyURL' => '',
            'hosts' => [],
            'cookieName' => '',
            'cookieExpiry' => '',
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
