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

class Post
{
    private static $instance;

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public $customCode = '';

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
     * embedCustomCode function.
     */
    public function embedCustomCode()
    {
        if (!empty($this->customCode)) {
            echo $this->customCode;
        }
    }

    /**
     * getCustomCode function.
     *
     * @param mixed $query
     */
    public function getCustomCode($query)
    {
        global $post;

        $postId = null;

        if (!empty($post->ID)) {
            if (is_front_page()) {
                $postId = $post->ID;
            } elseif (is_single()) {
                $postId = $post->ID;
            } elseif (is_page()) {
                $postId = $post->ID;
            }

            if (!empty($postId)) {
                $customCode = get_post_meta($postId, '_borlabs-cookie-custom-code', true);

                if (!empty($customCode)) {
                    $this->customCode = do_shortcode($customCode);
                }
            }
        }
    }
}
