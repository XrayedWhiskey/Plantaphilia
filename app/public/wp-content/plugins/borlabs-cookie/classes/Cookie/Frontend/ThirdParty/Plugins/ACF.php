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

class ACF
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
     * handleOembed function.
     *
     * @param mixed $html
     * @param mixed $id
     * @param mixed $atts
     */
    public function handleOembed($html = '', $id = null, $atts = [])
    {
        // Detect URL
        $url = '';
        $match = [];

        if (!empty($html)) {
            preg_match_all('#\bhttps?://[^,\s()<>]+(?:\([\w\d]+\)|([^,[:punct:]\s]|/))#', $html, $match);

            // Let's just hope the first URL is the right one...
            if (!empty($match[0][0])) {
                $url = $match[0][0];
            }

            $html = ContentBlocker::getInstance()->handleContentBlocking($html, $url);
        }

        return $html;
    }

    /**
     * register function.
     */
    public function register()
    {
        add_filter('acf/format_value/type=oembed', [$this, 'handleOembed'], 100, 3);
        add_filter('acf/format_value/type=textarea', [ContentBlocker::getInstance(), 'detectIframes'], 100, 3);
        add_filter('acf_the_content', [ContentBlocker::getInstance(), 'detectIframes'], 100, 3);
    }
}
