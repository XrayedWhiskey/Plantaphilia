<?php
/*
 *  Copyright (c) 2021 Borlabs GmbH. All rights reserved.
 *  This file may not be redistributed in whole or significant part.
 *  Content of this file is protected by international copyright laws.
 *
 *  ----------------- Borlabs Cookie IS NOT FREE SOFTWARE -----------------
 *
 *  @copyright Borlabs GmbH, https://borlabs.io
 *  @author Benjamin A. Bornschein
 */

namespace BorlabsCookie\Cookie\Frontend\ThirdParty\Themes;

use BorlabsCookie\Cookie\Frontend\ContentBlocker;
use BorlabsCookie\Cookie\Frontend\CookieBox;
use BorlabsCookie\Cookie\Frontend\JavaScript;

class Bricks
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

    public function detectIframes($output, $postId = 0)
    {
        return ContentBlocker::getInstance()->detectIframes($output);
    }

    public function register()
    {
        add_action(
            'bricks/frontend/render_data',
            [$this, 'detectIframes'],
            100
        );

        if (isset($_GET['bricks']) && $_GET['bricks'] === 'run') {
            // Disable buffer while Bricks Builder is active. Is required due a bug in Bricks.
            add_filter('borlabsCookie/buffer/active', function ($status) {
                return false;
            });

            remove_action('wp_footer', [JavaScript::getInstance(), 'registerFooter']);
            remove_action('wp_footer', [CookieBox::getInstance(), 'insertCookieBox']);
        }
    }
}
