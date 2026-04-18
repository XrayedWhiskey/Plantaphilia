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

namespace BorlabsCookie\Cookie\Backend;

use BorlabsCookie\Cookie\Config;

class MetaBox
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
     * add function.
     */
    public function add()
    {
        $currentScreenData = get_current_screen();

        if (
            !empty($currentScreenData->post_type)
            && !empty(
            Config::getInstance()->get(
                'metaBox'
            )[$currentScreenData->post_type]
            )
        ) {
            add_meta_box(
                'borlabs-cookie-meta-box',
                _x('Borlabs Cookie', 'Backend / Meta Box / Headline', 'borlabs-cookie'),
                [$this, 'display'],
                null,
                'normal',
                'default',
                null
            );
        }
    }

    /**
     * display function.
     *
     * @param mixed $post
     */
    public function display($post)
    {
        $textareaBorlabsCookieCustomCode = esc_textarea(get_post_meta($post->ID, '_borlabs-cookie-custom-code', true));

        include Backend::getInstance()->templatePath . '/meta-box.html.php';
    }

    /**
     * register function.
     */
    public function register()
    {
        add_action('add_meta_boxes', [MetaBox::getInstance(), 'add']);
        add_action('save_post', [MetaBox::getInstance(), 'save'], 10, 3);
    }

    /**
     * save function.
     *
     * @param mixed $postId
     * @param mixed $post   (default: null)
     * @param mixed $update (default: null)
     */
    public function save($postId, $post = null, $update = null)
    {
        if (isset($_POST['borlabs-cookie']['custom-code'])) {
            update_post_meta(
                $postId,
                '_borlabs-cookie-custom-code',
                stripslashes($_POST['borlabs-cookie']['custom-code'])
            );
        }
    }
}
