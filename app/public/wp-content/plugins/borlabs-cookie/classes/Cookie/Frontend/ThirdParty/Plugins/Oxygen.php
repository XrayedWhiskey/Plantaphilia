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

namespace BorlabsCookie\Cookie\Frontend\ThirdParty\Plugins;

use BorlabsCookie\Cookie\Frontend\ContentBlocker;
use BorlabsCookie\Cookie\Frontend\CookieBox;
use BorlabsCookie\Cookie\Frontend\JavaScript;

class Oxygen
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
     * register function.
     */
    public function register()
    {
        add_action('ct_builder_end', function () {
            global $template_content;

            $template_content = ContentBlocker::getInstance()->detectIframes($template_content);
        });

        if (isset($_GET['ct_builder'], $_GET['ct_inner']) && $_GET['ct_builder'] === 'true' && $_GET['ct_inner'] === 'true') {
            remove_action('wp_footer', [JavaScript::getInstance(), 'registerFooter']);
            remove_action('wp_footer', [CookieBox::getInstance(), 'insertCookieBox']);
        }
    }
}
