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

class SiteOriginPageBuilder
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
        add_filter('siteorigin_panels_the_widget_html', function ($empty, $the_widget, $args, $instance) {
            if (!isset($the_widget->id_base) || $the_widget->id_base !== 'media_video') {
                return '';
            }

            if (!isset($instance['url'])) {
                return '';
            }
            $oEmbedHTML = wp_oembed_get($instance['url']);

            if (!isset($oEmbedHTML)) {
                return '';
            }

            $widgetHTML = $args['before_widget'];
            $widgetHTML .= $args['before_title'];
            $widgetHTML .= $instance['title'];
            $widgetHTML .= $args['after_title'];
            $widgetHTML .= ContentBlocker::getInstance()->detectIframes($oEmbedHTML);
            $widgetHTML .= $args['after_widget'];

            return $widgetHTML;
        }, 100, 4);
    }
}
