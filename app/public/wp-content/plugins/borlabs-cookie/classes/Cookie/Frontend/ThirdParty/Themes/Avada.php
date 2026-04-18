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

namespace BorlabsCookie\Cookie\Frontend\ThirdParty\Themes;

class Avada
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
     * adminHeadCSS function.
     */
    public function adminHeadCSS()
    {
        echo '<style>#BorlabsCookieBox { display: none !important; }</style>';
    }

    /**
     * Disable buffer while Avada Live Builder is active. Is required due a bug in Avada.
     */
    public function disableBuffer()
    {
        if (isset($_GET['builder']) && $_GET['builder'] === 'true' || isset($_GET['fb-edit'])) {
            add_filter('borlabsCookie/buffer/active', function ($status) {
                return false;
            });
        }
    }
}
