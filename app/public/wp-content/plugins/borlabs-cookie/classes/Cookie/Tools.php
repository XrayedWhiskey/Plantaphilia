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

class Tools
{
    private static $instance;

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private $generatedStrings = [];

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
     * arrayFlat function.
     *
     * By https://stackoverflow.com/users/370290/j-bruni
     * Found at: https://stackoverflow.com/questions/9546181/flatten-multidimensional-array-concatenating-keys
     *
     * @param mixed $array
     * @param mixed $prefix (default: '')
     */
    public function arrayFlat($array, $prefix = '')
    {
        $result = [];

        foreach ($array as $key => $value) {
            $newKey = $prefix . (!empty($prefix) ? '.' : '') . $key;

            if (is_array($value)) {
                $result = array_merge($result, $this->arrayFlat($value, $newKey));
            } else {
                $result[$newKey] = $value;
            }
        }

        return $result;
    }

    /**
     * cleanHostList function.
     *
     * @param mixed $hosts
     * @param bool  $allowURL (default: false)
     */
    public function cleanHostList($hosts, $allowURL = false)
    {
        // Clean hosts
        $cleanedHosts = [];

        if (is_array($hosts)) {
            $uncleanedHosts = $hosts;
        } else {
            $uncleanedHosts = explode("\n", $hosts);
        }

        foreach ($uncleanedHosts as $hostLine) {
            // Clean hosts by ,
            $hosts = explode(',', $hostLine);

            foreach ($hosts as $host) {
                $host = trim($host);

                if (!empty($host)) {
                    if (filter_var($host, FILTER_VALIDATE_URL)) {
                        if ($allowURL == false) {
                            $urlInfo = parse_url($host);
                            $host = $urlInfo['host'];
                        }
                    }

                    $cleanedHosts[$host] = strtolower(stripslashes($host));
                }
            }
        }

        sort($cleanedHosts, SORT_NATURAL);

        return $cleanedHosts;
    }

    /**
     * formatTimestamp function.
     *
     * @param mixed      $timestamp
     * @param mixed      $dateFormat (default: null)
     * @param null|mixed $timeFormat
     */
    public function formatTimestamp($timestamp, $dateFormat = null, $timeFormat = null)
    {
        if (is_string($timestamp)) {
            $timestamp = strtotime($timestamp);
        }

        if (!isset($dateFormat)) {
            $dateFormat = get_option('date_format');
        }

        if (!isset($timeFormat)) {
            $timeFormat = get_option('time_format');
        }

        $dateFormat = $dateFormat . (isset($timeFormat) ? ' ' : '') . $timeFormat;

        return date_i18n($dateFormat, $timestamp);
    }

    /**
     * generateRandomString function.
     *
     * @param int $stringLength (default: 32)
     */
    public function generateRandomString($stringLength = 32)
    {
        $charPool = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

        $generatedString = '';

        if ($stringLength <= 0) {
            $stringLength = 32;
        }

        for ($i = 0; $i < $stringLength; ++$i) {
            $index = 0;

            // PHP 7
            if (function_exists('random_int')) {
                $index = random_int(0, 61);
            } elseif (function_exists('mt_rand')) {
                $index = mt_rand(0, 61);
            } else {
                $index = rand(0, 61);
            }

            $generatedString .= $charPool[$index];
        }

        // Make sure, the generated string is unique
        if (isset($this->generatedStrings[$generatedString])) {
            $generatedString = $this->generateRandomString($stringLength);
        } else {
            $this->generateRandomString[$generatedString] = $generatedString;
        }

        return $generatedString;
    }

    /**
     * hexToHsl function.
     *
     * @param mixed $hex
     */
    public function hexToHsl($hex)
    {
        $rgb = $this->hexToRgb($hex);

        $max = max($rgb);
        $min = min($rgb);

        $l = ($max + $min) / 2;

        if ($max == $min) {
            $h = $s = 0;
        } else {
            $diff = $max - $min;
            $s = $l > 0.5 ? $diff / (2 - $max - $min) : $diff / ($max + $min);

            switch ($max) {
                case $rgb['r']:
                    $h = ($rgb['g'] - $rgb['b']) / $diff + ($rgb['g'] < $rgb['b'] ? 6 : 0);

                    break;

                case $rgb['g']:
                    $h = ($rgb['b'] - $rgb['r']) / $diff + 2;

                    break;

                case $rgb['b']:
                    $h = ($rgb['r'] - $rgb['g']) / $diff + 4;

                    break;
            }

            $h = round($h * 60);
        }

        return [$h, $s * 100, $l * 100];
    }

    /**
     * hexToRgb function.
     *
     * @param mixed $hex
     */
    public function hexToRgb($hex)
    {
        $hex = str_replace('#', '', $hex);

        if (strlen($hex) == 3) {
            $hex .= $hex;
        }

        $hex = [
            $hex[0] . $hex[1],
            $hex[2] . $hex[3],
            $hex[4] . $hex[5],
        ];

        $rgb = array_map(
            function ($part) {
                return hexdec($part) / 255;
            },
            $hex
        );

        return [
            'r' => $rgb[0],
            'g' => $rgb[1],
            'b' => $rgb[2],
        ];
    }

    public function isObjectEmpty($obj)
    {
        foreach ($obj as $property) {
            return false;
        }

        return true;
    }

    /**
     * isStringJSON function.
     *
     * @param mixed $string
     */
    public function isStringJSON($string)
    {
        json_decode($string);

        return json_last_error() == JSON_ERROR_NONE ? true : false;
    }

    /**
     * validateHexColor function.
     *
     * @param mixed $color
     */
    public function validateHexColor($color)
    {
        return preg_match('/#([a-f0-9]{3}){1,2}\b/i', $color);
    }
}
