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

use BorlabsCookie\Cookie\Backend\CSS;
use BorlabsCookie\Cookie\Config;
use BorlabsCookie\Cookie\Multilanguage;

class Style
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

    public function register()
    {
        if (defined('REST_REQUEST') && apply_filters('borlabsCookie/style/disabledOnRestRequest', true)) {
            return;
        }

        $language = Multilanguage::getInstance()->getCurrentLanguageCode();

        // Avoid cached styles
        $styleVersion = get_option('BorlabsCookieLegacyStyleVersion_' . $language, 1);

        $contentURL = content_url();

        // If CSS does not exist, try to create it on the fly
        if (
            file_exists(
                WP_CONTENT_DIR . '/cache/borlabs-cookie/borlabs-cookie_' . get_current_blog_id() . '_' . $language
                . '.css'
            ) === false
        ) {
            CSS::getInstance()->save();
        }

        // If CSS does not exist, try fallback
        if (
            file_exists(
                WP_CONTENT_DIR . '/cache/borlabs-cookie/borlabs-cookie_' . get_current_blog_id() . '_' . $language
                . '.css'
            )
        ) {
            if (defined('BORLABS_COOKIE_DEV_MODE') && BORLABS_COOKIE_DEV_MODE === true) {
                wp_enqueue_style(
                    'borlabs-cookie-origin',
                    BORLABS_COOKIE_PLUGIN_URL . 'assets/css/borlabs-cookie.css',
                    [],
                    BORLABS_COOKIE_VERSION . '-' . $styleVersion
                );
            }

            wp_enqueue_style(
                'borlabs-cookie',
                $contentURL . '/cache/borlabs-cookie/borlabs-cookie_' . get_current_blog_id() . '_' . $language
                . '.css',
                [],
                BORLABS_COOKIE_VERSION . '-' . $styleVersion
            );
        } else {
            // Fallback
            $inlineCSS = CSS::getInstance()->getCookieBoxCSS();
            $inlineCSS .= Config::getInstance()->get('cookieBoxCustomCSS');
            $inlineCSS .= CSS::getInstance()->getContentBlockerCSS($language);

            wp_enqueue_style(
                'borlabs-cookie',
                BORLABS_COOKIE_PLUGIN_URL . 'assets/css/borlabs-cookie.css',
                [],
                BORLABS_COOKIE_VERSION . '-' . $styleVersion
            );
            wp_add_inline_style('borlabs-cookie', $inlineCSS);
        }
    }
}
