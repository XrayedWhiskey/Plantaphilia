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

use BorlabsCookie\Cookie\API;
use BorlabsCookie\Cookie\Config;
use BorlabsCookie\Cookie\Tools;
use DateTime;

class License
{
    private static $instance;

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private $licenseData;

    private $validLicenseTypes
        = [
            'borlabs-cookie-legacy',
            'borlabs-cookie',
            'borlabs-cookie-personal',
            'borlabs-cookie-business',
            'borlabs-cookie-professional',
            'borlabs-cookie-agency',
            'borlabs-cookie-business-small',
            'borlabs-cookie-business-medium',
            'borlabs-cookie-agency-small',
        ];

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
     * display function.
     */
    public function display()
    {
        $action = false;

        if (!empty($_POST['action'])) {
            $action = $_POST['action'];
        }

        if ($action !== false) {
            // Register
            if ($action === 'register' && check_admin_referer('borlabs_cookie_license_register')) {
                $responseRegisterLicense = API::getInstance()->registerLicense($_POST['licenseKey']);

                if (!empty($responseRegisterLicense->successMessage)) {
                    Messages::getInstance()->add($responseRegisterLicense->successMessage, 'success');
                } else {
                    Messages::getInstance()->add($responseRegisterLicense->errorMessage, 'error');
                }
            }

            // Refresh
            if ($action === 'refresh' && check_admin_referer('borlabs_cookie_license_refresh')) {
                $this->refreshLicense();

                Messages::getInstance()->add(
                    _x(
                        'License information refreshed successfully.',
                        'Backend / License / Alert Message',
                        'borlabs-cookie'
                    ),
                    'success'
                );
            }

            // Remove
            if ($action === 'remove' && check_admin_referer('borlabs_cookie_license_remove')) {
                $this->removeLicense();

                Messages::getInstance()->add(
                    _x('License removed successfully.', 'Backend / License / Alert Message', 'borlabs-cookie'),
                    'success'
                );
            }

            // Test environment
            if ($action === 'save' && check_admin_referer('borlabs_cookie_license_test_environment')) {
                $this->save($_POST);

                Messages::getInstance()->add(
                    _x('Saved successfully.', 'Backend / Global / Alert Message', 'borlabs-cookie'),
                    'success'
                );
            }
        }

        $this->displayOverview();
    }

    /**
     * displayOverview function.
     */
    public function displayOverview()
    {
        // Validate license
        $this->validateLicense();

        // License information
        $licenseData = $this->getLicenseData();
        $validUntil = !empty($licenseData->validUntil) ? new DateTime($licenseData->validUntil) : null;
        $supportUntil = !empty($licenseData->supportUntil) ? new DateTime($licenseData->supportUntil) : null;

        $licenseStatus = $this->isLicenseValid() ? 'valid' : 'expired';
        $licenseStatusMessage = $this->getLicenseMessageStatus($licenseStatus);
        $licenseTypeTitle = esc_html(
            !empty($licenseData->licenseType) ? $this->getLicenseTypeTitle($licenseData->licenseType) : ''
        );
        $licenseValidUntil = !empty($licenseData->validUntil) ? Tools::getInstance()->formatTimestamp(
            $validUntil->getTimestamp(),
            null,
            false
        ) : '';
        $licenseSupportUntil = !empty($licenseData->supportUntil) ? Tools::getInstance()->formatTimestamp(
            $supportUntil->getTimestamp(),
            null,
            false
        ) : '';
        $licenseMaxSites = !empty($licenseData->maxSites) ? (int) ($licenseData->maxSites) : '';
        $licenseHideLicenseInformation = !empty($licenseData->hideLicenseInformation) ? true : false;

        if (
            !empty($licenseData->licenseType)
            && in_array($licenseData->licenseType, [
                'borlabs-cookie-personal',
                'borlabs-cookie-business',
                'borlabs-cookie-professional',
                'borlabs-cookie-agency',
                'borlabs-cookie-business-small',
                'borlabs-cookie-business-medium',
                'borlabs-cookie-agency-small',
            ], true) === false
        ) {
            if (!empty($licenseMaxSites)) {
                $licenseMaxSites .= ' Multisite';
            }
        }

        // Mask license key
        $inputLicenseKey = preg_replace('/[^\-]/', '*', sanitize_text_field($this->getLicenseKey()));

        $inputTestEnvironment = !empty(Config::getInstance()->get('testEnvironment')) ? 1 : 0;
        $switchTestEnvironment = $inputTestEnvironment ? ' active' : '';

        // Show information how to get the license key
        if ($this->isPluginUnlocked() === false) {
            Messages::getInstance()->add(
                _x(
                    'You can not find your license key? No problem! <a href="https://borlabs.io/account/?utm_source=Borlabs+Cookie&amp;utm_medium=Account+Link&amp;utm_campaign=Analysis" rel="nofollow noopener noreferrer" target="_blank">Click here</a> and log in to your account to get your license key.',
                    'Backend / License / Alert Message',
                    'borlabs-cookie'
                ),
                'info'
            );

            $this->handleLicenseNotValidForCurrentBuildMessage();
        }

        include Backend::getInstance()->templatePath . '/license.html.php';
    }

    /**
     * getLicenseData function.
     */
    public function getLicenseData()
    {
        if (empty($this->licenseData)) {
            // Such license system, much secure, wow.
            // Just kidding, you want all the trouble with updates, just to save some bucks?
            // Please support an independent developer and buy a license, thank you :)
            $licenseDataNetwork = get_site_option('BorlabsCookieLegacyLicenseData');
            $licenseDataBlog = get_option('BorlabsCookieLegacyLicenseData');

            if (!empty($licenseDataBlog)) {
                $licenseData = $licenseDataBlog;
            } else {
                $licenseData = $licenseDataNetwork;
            }

            if (!empty($licenseData)) {
                $this->licenseData = unserialize(base64_decode($licenseData, true));
            } else {
                $this->licenseData = (object) ['noLicense' => true];
            }
        }

        return $this->licenseData;
    }

    /**
     * getLicenseKey function.
     */
    public function getLicenseKey()
    {
        $licenseKey = '';
        $licenseKeyNetwork = get_site_option('BorlabsCookieLegacyLicenseKey');
        $licenseKeyBlog = get_option('BorlabsCookieLegacyLicenseKey');

        if (!empty($licenseKeyNetwork)) {
            $licenseKey = $licenseKeyNetwork;
        }

        if (!empty($licenseKeyBlog)) {
            $licenseKey = $licenseKeyBlog;
        }

        return $licenseKey;
    }

    /**
     * getLicenseMessageActivateKey function.
     */
    public function getLicenseMessageActivateKey()
    {
        $html = '<div class="alert alert-info" role="alert">';
        $html .= _x(
            'Please activate your license key first. <a href="?page=borlabs-cookie-license">Click here</a> to enter your license key.',
            'Backend / License / Alert Message',
            'borlabs-cookie'
        );
        $html .= '</div>';

        return $html;
    }

    /**
     * getLicenseMessageEnterKey function.
     */
    public function getLicenseMessageEnterKey()
    {
        return _ex(
            'Please enter your license key to receive updates.',
            'Backend / License / Alert Message',
            'borlabs-cookie'
        );
    }

    /**
     * getLicenseMessageKeyExpired function.
     */
    public function getLicenseMessageKeyExpired()
    {
        return _x(
            'Please renew your license key to receive updates. <a href="https://borlabs.io/account/" target="_blank" rel="nofollow noopener noreferrer">Click here</a> to log into your account and purchase a license renewal.',
            'Backend / License / Alert Message',
            'borlabs-cookie'
        );
    }

    /**
     * getLicenseMessageNotValidForCurrentBuild function.
     */
    public function getLicenseMessageNotValidForCurrentBuild()
    {
        return _x(
            'Your license key is not valid for this version. This version of Borlabs Cookie was released after your license has expired, therefore you have to <a href="https://borlabs.io/account/" target="_blank" rel="nofollow noopener noreferrer">click here</a> to log into your account and purchase a license renewal.',
            'Backend / License / Alert Message',
            'borlabs-cookie'
        );
    }

    /**
     * getLicenseMessageStatus function.
     *
     * @param mixed $status
     */
    public function getLicenseMessageStatus($status)
    {
        $message = '';

        if ($status === 'valid') {
            $message = _x('Your license is valid.', 'Backend / License / Text', 'borlabs-cookie');
        } elseif ($status === 'expired') {
            $message = _x('Your license has expired.', 'Backend / License / Text', 'borlabs-cookie');
        }

        return $message;
    }

    /**
     * getLicenseTypeTitle function.
     *
     * @param mixed $licenseType
     */
    public function getLicenseTypeTitle($licenseType)
    {
        $licenseType = strtolower($licenseType);

        switch ($licenseType) {
            case 'borlabs-cookie-personal':
                $licenseType = _x('Personal', 'Backend / License / Text', 'borlabs-cookie');

                break;

            case 'borlabs-cookie-business':
                $licenseType = _x('Business', 'Backend / License / Text', 'borlabs-cookie');

                break;

            case 'borlabs-cookie-professional':
                $licenseType = _x('Professional', 'Backend / License / Text', 'borlabs-cookie');

                break;

            case 'borlabs-cookie-agency':
                $licenseType = _x('Agency', 'Backend / License / Text', 'borlabs-cookie');

                break;

            case 'borlabs-cookie-legacy':
                $licenseType = _x('Agency (Legacy)', 'Backend / License / Text', 'borlabs-cookie');

                break;

            case 'borlabs-cookie-business-small':
                $licenseType = _x('Business - Small', 'Backend / License / Text', 'borlabs-cookie');

                break;

            case 'borlabs-cookie-business-medium':
                $licenseType = _x('Business - Medium', 'Backend / License / Text', 'borlabs-cookie');

                break;

            case 'borlabs-cookie-agency-small':
                $licenseType = _x('Agency - Small', 'Backend / License / Text', 'borlabs-cookie');

                break;

            case 'borlabs-cookie':
            default:
                $licenseType = _x('Classic (Legacy)', 'Backend / License / Text', 'borlabs-cookie');
        }

        return $licenseType;
    }

    /**
     * handleLicenseExpiredMessage function.
     */
    public function handleLicenseExpiredMessage()
    {
        if (!empty($this->getLicenseData()->validUntil) && $this->isLicenseValid() === false) {
            // Try to re-validate
            $this->validateLicense();
            $this->getLicenseData();

            if ($this->isLicenseValid() === false) {
                Messages::getInstance()->add($this->getLicenseMessageKeyExpired(), 'error');
            }
        }
    }

    /**
     * handleLicenseNotValidForCurrentBuildMessage function.
     */
    public function handleLicenseNotValidForCurrentBuildMessage()
    {
        if (!empty($this->getLicenseData()->validUntil) && $this->isLicenseValidForCurrentBuild() === false) {
            Messages::getInstance()->add($this->getLicenseMessageNotValidForCurrentBuild(), 'error');
        }
    }

    /**
     * isLicenseValid function.
     */
    public function isLicenseValid()
    {
        // Such license system, much secure, wow.
        // Just kidding, you want all the trouble with updates, just to save some bucks?
        // Please support an independent developer and buy a license, thank you :)
        $isValid = false;

        if (!empty($this->getLicenseData()->validUntil)) {
            if ($this->getLicenseData()->validUntil >= date('Y-m-d')) {
                $isValid = true;
            }
        }

        return $isValid;
    }

    /**
     * The method checks if the current build was created before the license date expired.
     *
     * @return bool
     */
    public function isLicenseValidForCurrentBuild()
    {
        return (bool) (date('ymd', strtotime($this->getLicenseData()->validUntil)) >= BORLABS_COOKIE_BUILD);
    }

    /**
     * isPluginUnlocked function.
     */
    public function isPluginUnlocked()
    {
        // Such license system, much secure, wow.
        // Just kidding, you want all the trouble with updates, just to save some bucks?
        // Please support an independent developer and buy a license, thank you :)
        $unlocked = false;

        if (!empty($this->getLicenseData()->licenseType)) {
            if (in_array($this->getLicenseData()->licenseType, $this->validLicenseTypes, true)) {
                $unlocked = $this->isLicenseValidForCurrentBuild();
            }
        }

        // If installation is in a test environment
        if (Config::getInstance()->get('testEnvironment')) {
            $unlocked = true;
        }

        return $unlocked;
    }

    /**
     * refreshLicense function.
     */
    public function refreshLicense()
    {
        $licenseKey = $this->getLicenseKey();

        if (!empty($licenseKey)) {
            $responseRegisterLicense = API::getInstance()->registerLicense($licenseKey);
        }
    }

    /**
     * removeLicense function.
     */
    public function removeLicense()
    {
        // Check if blog or network key should be removed
        $licenseKeyNetwork = get_site_option('BorlabsCookieLegacyLicenseKey');
        $licenseKeyBlog = get_option('BorlabsCookieLegacyLicenseKey');

        if (!empty($licenseKeyBlog)) {
            delete_option('BorlabsCookieLegacyLicenseData');
            delete_option('BorlabsCookieLegacyLicenseKey');
            delete_option('BorlabsCookieUnlinkData');
        } elseif (!empty($licenseKeyNetwork)) {
            delete_site_option('BorlabsCookieLegacyLicenseData');
            delete_site_option('BorlabsCookieLegacyLicenseKey');
            delete_site_option('BorlabsCookieUnlinkData');
        }

        // Set property to null
        $this->licenseData = null;

        // getLicenseData is now able to set the correct information for licenseData
        $this->getLicenseData();
    }

    /**
     * save function.
     *
     * @param mixed $formData
     */
    public function save($formData)
    {
        $updatedConfig = Config::getInstance()->get();

        $updatedConfig['testEnvironment'] = !empty($formData['testEnvironment']) ? true : false;

        // Save config
        Config::getInstance()->saveConfig($updatedConfig);
    }

    /**
     * saveLicenseData function.
     *
     * @param mixed $licenseData
     */
    public function saveLicenseData($licenseData)
    {
        if (!empty($licenseData->licenseKey)) {
            if (
                in_array($licenseData->licenseType, $this->validLicenseTypes, true)
                && $licenseData->licenseType !== 'borlabs-cookie-legacy'
            ) {
                update_option('BorlabsCookieLegacyLicenseData', base64_encode(serialize($licenseData)), 'no');
                update_option('BorlabsCookieLegacyLicenseKey', $licenseData->licenseKey, 'no');
            } else {
                update_site_option('BorlabsCookieLegacyLicenseData', base64_encode(serialize($licenseData)), 'no');
                update_site_option('BorlabsCookieLegacyLicenseKey', $licenseData->licenseKey, 'no');
            }

            $this->licenseData = $licenseData;
        }
    }

    /**
     * validateLicense function.
     */
    public function validateLicense()
    {
        $lastCheck = (int) (get_option('BorlabsCookieLegacyLicenseLastCheck', 0));
        $licenseKey = $this->getLicenseKey();

        if (
            !empty($licenseKey)
            && (empty($lastCheck)
                || $lastCheck < (int) (
                    date('Ymd', mktime(date('H'), date('i'), date('s'), date('m'), date('d') - 3))
                ))
        ) {
            $responseRegisterLicense = API::getInstance()->registerLicense($licenseKey);

            if (empty($responseRegisterLicense->successMessage)) {
                if (empty($responseRegisterLicense->serverError)) {
                    $this->removeLicense();
                }
            } else {
                // Update last check
                update_option('BorlabsCookieLegacyLicenseLastCheck', date('Ymd'), 'no');
            }
        }
    }
}
