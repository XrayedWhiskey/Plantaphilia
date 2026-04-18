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

use function esc_attr;

class Messages
{
    private static $instance;

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private $messages = [];

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
     *
     * @param mixed $message
     * @param mixed $type
     */
    public function add($message, $type)
    {
        if ($type === 'error') {
            $type = 'alert-danger';
        } elseif ($type === 'success') {
            $type = 'alert-success';
        } elseif ($type === 'info') {
            $type = 'alert-info';
        } elseif ($type === 'warning') {
            $type = 'alert-warning';
        } elseif ($type === 'offer') {
            $type = 'alert-offer';
        } elseif ($type === 'critical') {
            $type = 'alert-critical';
        }

        $this->messages[] = '<div class="alert ' . esc_attr($type) . '" role="alert">' . $message . '</div>';
    }

    /**
     * getAll function.
     */
    public function getAll()
    {
        return implode("\n", $this->messages);
    }
}
