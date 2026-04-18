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

use BorlabsCookie\Cookie\Backend\License;
use BorlabsCookie\Cookie\Backend\Telemetry;
use Exception;

class API
{
    private static $instance;

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private $apiURL = 'https://api.cookie.borlabs.io/v3';

    private $response = [];

    private $updateURL = 'https://update.borlabs.io/v2';

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
     * addVars function.
     *
     * @param mixed $vars
     */
    public function addVars($vars)
    {
        $vars[] = '__borlabsCookieCall';

        return $vars;
    }

    /**
     * detectRequests function.
     */
    public function detectRequests()
    {
        global $wp;

        if (!empty($wp->query_vars['__borlabsCookieCall'])) {
            $data = json_decode(file_get_contents('php://input'));
            $this->handleRequest($wp->query_vars['__borlabsCookieCall'], $data);

            exit;
        }
    }

    /**
     * getLatestVersion function.
     *
     * @param null|mixed $borlabsCookieVersion
     */
    public function getLatestVersion($borlabsCookieVersion = null)
    {
        if ($borlabsCookieVersion === null) {
            $borlabsCookieVersion = BORLABS_COOKIE_VERSION;
        }

        $licenseData = License::getInstance()->getLicenseData();
        $response = wp_remote_post(
            $this->updateURL . '/latest-version/' . (defined('BORLABS_COOKIE_DEV_BUILD')
            && BORLABS_COOKIE_DEV_BUILD == true ? 'dev-' : '') . BORLABS_COOKIE_SLUG,
            [
                'timeout' => 45,
                'body' => [
                    'backend_url' => get_site_url(),
                    'dbVersion' => $this->getDbVersion(),
                    'debug_php_time' => date('Y-m-d H:i:s'),
                    'debug_php_timestamp' => time(),
                    'debug_timezone' => date_default_timezone_get(),
                    'frontend_url' => get_home_url(),
                    'licenseKey' => !empty($licenseData->licenseKey) ? $licenseData->licenseKey : '',
                    'network_url' => network_site_url(),
                    'php_version' => phpversion(), // Used to distinguish between >=7.4 and <7.4 builds
                    'product' => BORLABS_COOKIE_SLUG,
                    'securityPatchesForExpiredLicenses' => !License::getInstance()->isLicenseValid(),
                    'securityPatchesForTestEnvironmentLicenses' => !empty(Config::getInstance()->get('testEnvironment')) ? '1' : '0',
                    'twigConflict' => class_exists('Twig\Environment', false) ? '1' : '0', // Detects if the Twig class is present and can cause a future conflict
                    'version' => $borlabsCookieVersion,
                ],
            ]
        );

        if (!empty($response) && is_array($response) && !empty($response['body'])) {
            $body = json_decode($response['body']);

            if (!empty($body->success) && !empty($body->updateInformation)) {
                return unserialize($body->updateInformation);
            }
        }
    }

    /**
     * getNews function.
     */
    public function getNews()
    {
        $licenseData = License::getInstance()->getLicenseData();
        // Get latest news
        $response = $this->restPostRequest('/news', [
            'licenseKey' => !empty($licenseData->licenseKey) ? $licenseData->licenseKey : '',
            'product' => BORLABS_COOKIE_SLUG,
            'version' => BORLABS_COOKIE_VERSION,
        ]);

        if (!empty($response->success)) {
            update_site_option('BorlabsCookieLegacyNews', $response->news);
            // Update last check
            update_site_option('BorlabsCookieLegacyNewsLastCheck', date('Ymd'), 'no');

            return (object) [
                'success' => true,
            ];
        }

        return $response;
    }

    /**
     * getPluginInformation function.
     *
     * @param null|mixed $borlabsCookieVersion
     */
    public function getPluginInformation($borlabsCookieVersion = null)
    {
        if ($borlabsCookieVersion === null) {
            $borlabsCookieVersion = BORLABS_COOKIE_VERSION;
        }

        $licenseData = License::getInstance()->getLicenseData();
        $response = wp_remote_post(
            $this->updateURL . '/plugin-information/' . (defined('BORLABS_COOKIE_DEV_BUILD')
            && BORLABS_COOKIE_DEV_BUILD == true ? 'dev-' : '') . BORLABS_COOKIE_SLUG,
            [
                'timeout' => 45,
                'body' => [
                    'backend_url' => get_site_url(),
                    'dbVersion' => $this->getDbVersion(),
                    'frontend_url' => get_home_url(),
                    'language' => get_locale(),
                    'licenseKey' => !empty($licenseData->licenseKey) ? $licenseData->licenseKey : '',
                    'network_url' => network_site_url(),
                    'php_version' => phpversion(), // Used to distinguish between >=7.4 and <7.4 builds
                    'product' => BORLABS_COOKIE_SLUG,
                    'twigConflict' => class_exists('Twig\Environment', false) ? '1' : '0', // Detects if the Twig class is present and can cause a future conflict
                    'version' => $borlabsCookieVersion,
                ],
            ]
        );

        if (!empty($response) && is_array($response) && !empty($response['body'])) {
            $body = json_decode($response['body']);

            if (!empty($body->success) && !empty($body->pluginInformation)) {
                return unserialize($body->pluginInformation);
            }
        }
    }

    /**
     * handleRequest function.
     *
     * @param mixed $call
     * @param mixed $token
     * @param mixed $data
     */
    public function handleRequest($call, $data)
    {
        // Check if request is authorized
        if ($this->isAuthorized($data)) {
            if ($call === 'updateLicense') {
                $this->updateLicense($data);
            }
        } else {
            // cDC = crossDomainCookie
            if ($call === 'cDC') {
                Frontend\CrossDomainCookie::getInstance()->handleRequest($_GET);
            }
        }
    }

    /**
     * isAuthorized function.
     *
     * @param mixed $data
     */
    public function isAuthorized($data)
    {
        $isAuthorized = false;

        // Function getallheaders doesn't exist on FPM...
        $allHeaders = [];

        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $allHeaders[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))]
                    = $value;
            }
        }

        $hash = '';

        if (!empty($allHeaders['X-Borlabs-Cookie-Auth'])) {
            $hash = $allHeaders['X-Borlabs-Cookie-Auth'];
        }

        if (
            !empty(License::getInstance()->getLicenseData()->salt)
            && HMAC::getInstance()->isValid(
                $data,
                License::getInstance()->getLicenseData()->salt,
                $hash
            )
        ) {
            $isAuthorized = true;
        }

        return $isAuthorized;
    }

    /**
     * registerLicense function.
     *
     * @param mixed $licenseKey
     */
    public function registerLicense($licenseKey)
    {
        $licenseKey = trim($licenseKey);
        $data = [
            'backend_url' => get_site_url(),
            'email' => '',
            'frontend_url' => get_home_url(),
            'licenseKey' => $licenseKey,
            'network_url' => network_site_url(),
            'version' => BORLABS_COOKIE_VERSION,
        ];

        // Register site
        $response = $this->restPostRequest('/register', $data);

        if (!empty($response->licenseKey)) {
            // Save license data
            License::getInstance()->saveLicenseData($response);

            return (object) [
                'success' => true,
                'successMessage' => _x(
                    'License registered successfully.',
                    'Backend / API / Alert Message',
                    'borlabs-cookie'
                ),
            ];
        }

        if (!empty($response->unlink)) {
            return $response;
        }

        return $response;
    }

    public function sendTelemetry()
    {
        $telemetryData = [];

        try {
            $telemetryData = Telemetry::getInstance()->getTelemetry();
        } catch (Exception $e) {
        }

        $licenseData = License::getInstance()->getLicenseData();

        if (!isset($licenseData->licenseKey) || $telemetryData === null) {
            return;
        }

        wp_remote_post(
            'https://service.borlabs.io/api/telemetry',
            [
                'timeout' => 45,
                'body' => [
                    'backend_url' => get_site_url(),
                    'frontend_url' => get_home_url(),
                    'licenseKey' => $licenseData->licenseKey,
                    'telemetry' => $telemetryData,
                ],
            ]
        );
    }

    /**
     * updateLicense function.
     *
     * @param mixed $data
     */
    public function updateLicense($data)
    {
        if (!empty($data->licenseKey)) {
            License::getInstance()->saveLicenseData($data);
        } elseif (!empty($data->removeLicense)) {
            License::getInstance()->removeLicense();
        }

        echo json_encode([
            'success' => true,
        ]);
    }

    private function getDbVersion()
    {
        global $wpdb;

        $dbServerInfo = $wpdb->get_var('SELECT VERSION()');

        return $dbServerInfo;
    }

    /**
     * restPostRequest function.
     *
     * @param mixed $route
     * @param mixed $data
     * @param mixed $salt  (default: null)
     */
    private function restPostRequest($route, $data, $salt = null)
    {
        $args = [
            'timeout' => 45,
            'body' => $data,
        ];

        // Add authentification header
        if (!empty($salt)) {
            $args['headers'] = [
                'X-Borlabs-Cookie-Auth' => HMAC::getInstance()->hash($data, $salt),
            ];
        }

        // Make post request
        $response = wp_remote_post(
            $this->apiURL . $route,
            $args
        );

        if (
            !empty($response) && is_array($response) && $response['response']['code'] == 200
            && !empty($response['body'])
        ) {
            $responseBody = json_decode($response['body']);

            if (empty($responseBody->error)) {
                return $responseBody;
            }
            // Borlabs Cookie API messages
            $responseBody->errorMessage = $this->translateErrorCode(
                $responseBody->errorCode,
                $responseBody->message
            );

            return $responseBody;
        }

        if (empty($response->errors) && !empty($response['response']['message'])) {
            // Server message
            return (object) [
                'serverError' => true,
                'errorMessage' => $response['response']['code'] . ' ' . $response['response']['message'],
            ];
        }
        // WP_Error messages
        return (object) [
            'serverError' => true,
            'errorMessage' => implode('<br>', $response->get_error_messages()),
        ];
    }

    /**
     * translateErrorCode function.
     *
     * @param mixed $errorCode
     * @param mixed $message
     */
    private function translateErrorCode($errorCode, $message = '')
    {
        $errorMessage = '';

        if ($errorCode == 'accessError') {
            $errorMessage = _x(
                'The request was blocked. Please try again later.',
                'Backend / API / Alert Message',
                'borlabs-cookie'
            );
        } elseif ($errorCode == 'unlinkRoutine') {
            $errorMessage = _x(
                'Your license key is already being used by another website. Please visit <a href="https://borlabs.io/account/" rel="nofollow noopener noreferrer" target="_blank">https://borlabs.io/account/</a> to remove the website from your license.',
                'Backend / API / Alert Message',
                'borlabs-cookie'
            );
        } elseif ($errorCode == 'validateHash') {
            $errorMessage = sprintf(
                _x(
                    'The request to the API could not be validated. %s',
                    'Backend / API / Alert Message',
                    'borlabs-cookie'
                ),
                $message
            );
        } elseif ($errorCode == 'invalidLicenseKey') {
            $errorMessage = _x('Your license key is not valid.', 'Backend / API / Alert Message', 'borlabs-cookie');
        } elseif ($errorCode == 'invalidMajorVersionLicenseKey') {
            $errorMessage = _x(
                'Your license key is not valid for this major version. Please upgrade your license key.',
                'Backend / API / Alert Message',
                'borlabs-cookie'
            );
        } else {
            // errorCode == error
            $errorMessage = sprintf(
                _x(
                    'An error occurred. Please contact the support. %s',
                    'Backend / API / Alert Message',
                    'borlabs-cookie'
                ),
                $message
            );
        }

        return $errorMessage;
    }
}
