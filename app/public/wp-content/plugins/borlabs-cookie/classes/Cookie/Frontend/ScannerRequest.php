<?php

namespace BorlabsCookie\Cookie\Frontend;

use BorlabsCookie\Cookie\Backend\License;
use BorlabsCookie\Cookie\HMAC;

class ScannerRequest
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

    public function isAuthorized()
    {
        if (!isset($_GET['__borlabsCookieScannerRequest']) || !isset($_GET['__borlabsCookieSignature'])) {
            return false;
        }

        if (!isset(License::getInstance()->getLicenseData()->siteSalt)) {
            return false;
        }

        $isValid = HMAC::getInstance()->isValid(
            $_GET['__borlabsCookieScannerRequest'],
            License::getInstance()->getLicenseData()->siteSalt,
            $_GET['__borlabsCookieSignature']
        );

        if ($isValid && !defined('DONOTCACHEPAGE')) {
            // Disables caching of WordPress plugins.
            define('DONOTCACHEPAGE', true);

            // Prevent a crawler from indexing a page requested by the scanner.
            if (!headers_sent()) {
                header('X-Robots-Tag: noindex');
            }
        }

        if ((int) $_GET['__borlabsCookieScannerRequest']['expires'] < time()) {
            return false;
        }

        return $isValid;
    }

    public function noBorlabsCookie()
    {
        return isset($_GET['__borlabsCookieScannerRequest']['noBorlabsCookie']) && $_GET['__borlabsCookieScannerRequest']['noBorlabsCookie'] === '1';
    }

    public function noConsentDialog()
    {
        return isset($_GET['__borlabsCookieScannerRequest']['noConsentDialog']) && $_GET['__borlabsCookieScannerRequest']['noConsentDialog'] === '1';
    }

    public function noContentBlocker()
    {
        return isset($_GET['__borlabsCookieScannerRequest']['noContentBlocker']) && $_GET['__borlabsCookieScannerRequest']['noContentBlocker'] === '1';
    }

    public function noScriptBlocker()
    {
        return isset($_GET['__borlabsCookieScannerRequest']['noScriptBlocker']) && $_GET['__borlabsCookieScannerRequest']['noScriptBlocker'] === '1';
    }
}
