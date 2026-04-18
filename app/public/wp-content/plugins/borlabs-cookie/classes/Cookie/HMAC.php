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

namespace BorlabsCookie\Cookie;

/**
 * Class HMAC.
 */
class HMAC
{
    private static $instance;

    /**
     * @return null|HMAC
     */
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
     * hash function.
     *
     * @param mixed $data
     * @param mixed $salt
     *
     * @return string
     */
    public function hash($data, $salt)
    {
        if (!is_string($data)) {
            $data = json_encode($data);
        }

        return hash_hmac('sha256', $data, $salt);
    }

    /**
     * isValid function.
     *
     * @param mixed $data
     * @param mixed $salt
     * @param mixed $hash
     *
     * @return bool
     */
    public function isValid($data, $salt, $hash)
    {
        $is_valid = false;

        if (!is_string($data)) {
            $data = json_encode($data);
        }

        $data_hash = hash_hmac('sha256', $data, $salt);

        if ($data_hash == $hash) {
            $is_valid = true;
        }

        return $is_valid;
    }
}
