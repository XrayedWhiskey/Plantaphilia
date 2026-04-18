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

class Log
{
    private static $instance;

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private $token;

    public function __construct()
    {
        $this->token = uniqid();
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
     * Action must be taken immediately.
     *
     * Example: Database down
     *
     * @param mixed $process
     * @param mixed $message
     * @param array $context (default: [])
     * @param array $data    (default: [])
     */
    public function alert($process, $message, array $context = [], array $data = [])
    {
        return $this->log('alert', $process, $message, $context, $data);
    }

    /**
     * Critical conditions.
     *
     * Example: Unexpected condition
     *
     * @param mixed $process
     * @param mixed $message
     * @param array $context (default: [])
     * @param array $data    (default: [])
     */
    public function critical($process, $message, array $context = [], array $data = [])
    {
        return $this->log('critical', $process, $message, $context, $data);
    }

    /**
     * Detailed debug information.
     *
     * @param mixed $process
     * @param mixed $message
     * @param array $context (default: [])
     * @param array $data    (default: [])
     */
    public function debug($process, $message, array $context = [], array $data = [])
    {
        return $this->log('debug', $process, $message, $context, $data);
    }

    /**
     * System is unusable.
     *
     * @param mixed $process
     * @param mixed $message
     * @param array $context (default: [])
     * @param array $data    (default: [])
     */
    public function emergency($process, $message, array $context = [], array $data = [])
    {
        return $this->log('emergency', $process, $message, $context, $data);
    }

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param mixed $process
     * @param mixed $message
     * @param array $context (default: [])
     * @param array $data    (default: [])
     */
    public function error($process, $message, array $context = [], array $data = [])
    {
        return $this->log('error', $process, $message, $context, $data);
    }

    public function getLogToken()
    {
        return $this->token;
    }

    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs
     *
     * @param mixed $process
     * @param mixed $message
     * @param array $context (default: [])
     * @param array $data    (default: [])
     */
    public function info($process, $message, array $context = [], array $data = [])
    {
        return $this->log('info', $process, $message, $context, $data);
    }

    /**
     * interpolate function.
     *
     * @param mixed $message
     * @param array $context (default: [])
     */
    public function interpolate($message, array $context = [])
    {
        $replace = [];

        foreach ($context as $key => $val) {
            if (!is_array($val) && (!is_object($val) || method_exists($val, '__toString'))) {
                $replace['{' . $key . '}'] = $val;
            }
        }

        return strtr($message, $replace);
    }

    /**
     * Normal but significant events.
     *
     * @param mixed $process
     * @param mixed $message
     * @param array $context (default: [])
     * @param array $data    (default: [])
     */
    public function notice($process, $message, array $context = [], array $data = [])
    {
        return $this->log('notice', $process, $message, $context, $data);
    }

    /**
     * Exceptional occurrences that are not errors.
     *
     * @param mixed $process
     * @param mixed $message
     * @param array $context (default: [])
     * @param array $data    (default: [])
     */
    public function warning($process, $message, array $context = [], array $data = [])
    {
        return $this->log('warning', $process, $message, $context, $data);
    }

    /**
     * log function.
     *
     * @param mixed $level
     * @param mixed $process
     * @param mixed $message
     * @param array $context (default: [])
     * @param array $data    (default: [])
     */
    private function log($level, $process, $message, array $context = [], array $data = [])
    {
        if (defined('BORLABS_COOKIE_DEBUG') && constant('BORLABS_COOKIE_DEBUG') === true) {
            if (!is_array($data) && !is_object($data)) {
                $data = [$data];
            }

            $message = $this->interpolate($message, $context);

            error_log('[' . $this->getLogToken() . '][' . $level . '] ' . $message);
        }

        return true;
    }
}
