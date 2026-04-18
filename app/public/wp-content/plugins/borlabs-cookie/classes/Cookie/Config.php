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

class Config
{
    private static $instance;

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private $config;

    public function __construct()
    {
        // Get all config values
        $this->loadConfig();
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
     * defaultConfig function.
     *
     * @param bool $installRoutine (default: false)
     */
    public function defaultConfig()
    {
        $imagePath = plugins_url('assets/images', realpath(__DIR__ . '/../'));

        return [
            'cookieStatus' => false,
            'setupMode' => false,
            'cookieBeforeConsent' => false, // Deprecated
            'aggregateCookieConsent' => false,
            'cookiesForBots' => true,
            'respectDoNotTrack' => false,
            'reloadAfterOptOut' => true,
            'reloadAfterConsent' => false,
            'jQueryHandle' => 'jquery-core',
            'metaBox' => [],

            'automaticCookieDomainAndPath' => false,
            'cookieDomain' => '',
            'cookiePath' => '/',
            'cookieSameSite' => 'Lax',
            'cookieSecure' => true,
            'cookieLifetime' => 182,
            'cookieLifetimeEssentialOnly' => 182,
            'crossDomainCookie' => [],

            'showCookieBox' => true,
            'showCookieBoxOnLoginPage' => false,
            'cookieBoxIntegration' => 'javascript',
            'cookieBoxBlocksContent' => true,
            'cookieBoxManageOptionType' => 'button',
            'cookieBoxRefuseOptionType' => 'button',
            'cookieBoxPreferenceRefuseOptionType' => 'button',
            'cookieBoxHideRefuseOption' => false,
            'privacyPageId' => 0,
            'privacyPageURL' => '',
            'privacyPageCustomURL' => '',
            'imprintPageId' => 0,
            'imprintPageURL' => '',
            'imprintPageCustomURL' => '',
            'hideCookieBoxOnPages' => [],
            'supportBorlabsCookie' => true,

            'cookieBoxShowAcceptAllButton' => true,
            'cookieBoxIgnorePreSelectStatus' => true,

            'cookieBoxLayout' => 'box-plus',
            'cookieBoxPosition' => 'top-center',
            'cookieBoxCookieGroupJustification' => 'space-between',
            'cookieBoxAnimation' => true,
            'cookieBoxAnimationDelay' => false,
            'cookieBoxAnimationIn' => 'fadeInDown',
            'cookieBoxAnimationOut' => 'flipOutX',

            'cookieBoxWidgetColor' => '#0063e3',
            'cookieBoxShowWidget' => true,
            'cookieBoxWidgetPosition' => 'bottom-left',
            'cookieBoxShowLogo' => true,
            'cookieBoxLogo' => $imagePath . '/borlabs-cookie-logo.svg',
            'cookieBoxLogoHD' => $imagePath . '/borlabs-cookie-logo.svg',
            'cookieBoxFontFamily' => 'inherit',
            'cookieBoxFontSize' => 14,
            'cookieBoxBgColor' => '#fff',
            'cookieBoxTxtColor' => '#555',
            'cookieBoxAccordionBgColor' => '#f7f7f7',
            'cookieBoxAccordionTxtColor' => '#555',
            'cookieBoxTableBgColor' => '#fff',
            'cookieBoxTableTxtColor' => '#555',
            'cookieBoxTableBorderColor' => '#eee',
            'cookieBoxBorderRadius' => 4,
            'cookieBoxBtnBorderRadius' => 4,
            'cookieBoxCheckboxBorderRadius' => 4,
            'cookieBoxAccordionBorderRadius' => 0,
            'cookieBoxTableBorderRadius' => 0,

            'cookieBoxBtnFullWidth' => true,
            'cookieBoxBtnColor' => '#000',
            'cookieBoxBtnHoverColor' => '#262626',
            'cookieBoxBtnTxtColor' => '#fff',
            'cookieBoxBtnHoverTxtColor' => '#fff',
            'cookieBoxRefuseBtnColor' => '#000',
            'cookieBoxRefuseBtnHoverColor' => '#262626',
            'cookieBoxRefuseBtnTxtColor' => '#fff',
            'cookieBoxRefuseBtnHoverTxtColor' => '#fff',
            'cookieBoxAcceptAllBtnColor' => '#000',
            'cookieBoxAcceptAllBtnHoverColor' => '#262626',
            'cookieBoxAcceptAllBtnTxtColor' => '#fff',
            'cookieBoxAcceptAllBtnHoverTxtColor' => '#fff',
            'cookieBoxIndividualSettingsBtnColor' => '#000',
            'cookieBoxIndividualSettingsBtnHoverColor' => '#262626',
            'cookieBoxIndividualSettingsBtnTxtColor' => '#fff',
            'cookieBoxIndividualSettingsBtnHoverTxtColor' => '#fff',
            'cookieBoxBtnSwitchActiveBgColor' => '#0063e3',
            'cookieBoxBtnSwitchInactiveBgColor' => '#bdc1c8',
            'cookieBoxBtnSwitchActiveColor' => '#fff',
            'cookieBoxBtnSwitchInactiveColor' => '#fff',
            'cookieBoxBtnSwitchRound' => true,

            'cookieBoxCheckboxInactiveBgColor' => '#fff',
            'cookieBoxCheckboxInactiveBorderColor' => '#a72828',
            'cookieBoxCheckboxActiveBgColor' => '#0063e3',
            'cookieBoxCheckboxActiveBorderColor' => '#0063e3',
            'cookieBoxCheckboxDisabledBgColor' => '#e6e6e6',
            'cookieBoxCheckboxDisabledBorderColor' => '#e6e6e6',
            'cookieBoxCheckboxCheckMarkActiveColor' => '#fff',
            'cookieBoxCheckboxCheckMarkDisabledColor' => '#999',

            'cookieBoxPrimaryLinkColor' => '#0063e3',
            'cookieBoxPrimaryLinkHoverColor' => '#1a66ff',
            'cookieBoxSecondaryLinkColor' => '#555',
            'cookieBoxSecondaryLinkHoverColor' => '#262626',
            'cookieBoxRejectionLinkColor' => '#555',
            'cookieBoxRejectionLinkHoverColor' => '#262626',
            'cookieBoxCustomCSS' => '',
            'cookieBoxTextHeadline' => _x('Privacy Preference', 'Frontend / Cookie Box / Headline', 'borlabs-cookie'),
            'cookieBoxTextDescription' => _x(
                'We need your consent before you can continue on our website.',
                'Frontend / Cookie Box / Text',
                'borlabs-cookie'
            ),
            'cookieBoxShowTextDescriptionConfirmAge' => true,
            'cookieBoxTextDescriptionConfirmAge' => _x(
                'If you are under 16 and wish to give consent to optional services, you must ask your legal guardians for permission.',
                'Frontend / Cookie Box / Text',
                'borlabs-cookie'
            ),
            'cookieBoxShowTextDescriptionTechnology' => true,
            'cookieBoxTextDescriptionTechnology' => _x(
                'We use cookies and other technologies on our website. Some of them are essential, while others help us to improve this website and your experience.',
                'Frontend / Cookie Box / Text',
                'borlabs-cookie'
            ),
            'cookieBoxShowTextDescriptionPersonalData' => true,
            'cookieBoxTextDescriptionPersonalData' => _x(
                'Personal data may be processed (e.g. IP addresses), for example for personalized ads and content or ad and content measurement.',
                'Frontend / Cookie Box / Text',
                'borlabs-cookie'
            ),
            'cookieBoxShowDescriptionMoreInformation' => true,
            'cookieBoxTextDescriptionMoreInformation' => _x(
                'You can find more information about the use of your data in our <a class="_brlbs-cursor" href="{privacyPageURL}">privacy policy</a>.',
                'Frontend / Cookie Box / Text',
                'borlabs-cookie'
            ),
            'cookieBoxShowTextDescriptionNoObligation' => false,
            'cookieBoxTextDescriptionNoObligation' => _x(
                'There is no obligation to consent to the processing of your data in order to use this offer.',
                'Frontend / Cookie Box / Text',
                'borlabs-cookie'
            ),
            'cookieBoxShowTextDescriptionRevoke' => true,
            'cookieBoxTextDescriptionRevoke' => _x(
                'You can revoke or adjust your selection at any time under <a class="_brlbs-cursor" href="#" data-cookie-individual>Settings</a>.',
                'Frontend / Cookie Box / Text',
                'borlabs-cookie'
            ),
            'cookieBoxShowTextDescriptionIndividualSettings' => false,
            'cookieBoxTextDescriptionIndividualSettings' => _x(
                'Please note that based on individual settings not all functions of the site may be available.',
                'Frontend / Cookie Box / Text',
                'borlabs-cookie'
            ),
            'cookieBoxShowTextDescriptionNonEUDataTransfer' => false,
            'cookieBoxTextDescriptionNonEUDataTransfer' => _x(
                'Some services process personal data in the USA. With your consent to use these services, you also consent to the processing of your data in the USA pursuant to Art. 49 (1) lit. a GDPR. The ECJ classifies the USA as a country with insufficient data protection according to EU standards. For example, there is a risk that U.S. authorities will process personal data in surveillance programs without any existing possibility of legal action for Europeans.',
                'Frontend / Cookie Box / Text',
                'borlabs-cookie'
            ),

            'cookieBoxTextAcceptButton' => _x('I accept', 'Frontend / Cookie Box / Button Title', 'borlabs-cookie'),
            'cookieBoxTextManageLink' => _x(
                'Individual Privacy Preferences',
                'Frontend / Cookie Box / Link Text',
                'borlabs-cookie'
            ),
            'cookieBoxTextRefuseLink' => _x(
                'Accept only essential cookies',
                'Frontend / Cookie Box / Link Text',
                'borlabs-cookie'
            ),
            'cookieBoxTextCookieDetailsLink' => _x(
                'Cookie Details',
                'Frontend / Cookie Box / Link Text',
                'borlabs-cookie'
            ),
            'cookieBoxTextPrivacyLink' => _x('Privacy Policy', 'Frontend / Cookie Box / Link Text', 'borlabs-cookie'),
            'cookieBoxTextImprintLink' => _x('Imprint', 'Frontend / Cookie Box / Link Text', 'borlabs-cookie'),
            'cookieBoxPreferenceTextHeadline' => _x(
                'Privacy Preference',
                'Frontend / Cookie Box / Headline',
                'borlabs-cookie'
            ),
            'cookieBoxPreferenceTextDescription' => _x(
                'Here you will find an overview of all cookies used. You can give your consent to whole categories or display further information and select certain cookies.',
                'Frontend / Cookie Box / Text',
                'borlabs-cookie'
            ),
            'cookieBoxPreferenceTextSaveButton' => _x('Save', 'Frontend / Cookie Box / Button Title', 'borlabs-cookie'),
            'cookieBoxPreferenceTextAcceptAllButton' => _x(
                'Accept all',
                'Frontend / Cookie Box / Button Title',
                'borlabs-cookie'
            ),
            'cookieBoxPreferenceTextRefuseLink' => _x(
                'Accept only essential cookies',
                'Frontend / Cookie Box / Link Text',
                'borlabs-cookie'
            ),
            'cookieBoxPreferenceTextBackLink' => _x('Back', 'Frontend / Cookie Box / Link Text', 'borlabs-cookie'),
            'cookieBoxPreferenceTextSwitchStatusActive' => _x(
                'On',
                'Frontend / Cookie Box / Switch Button Status',
                'borlabs-cookie'
            ),
            'cookieBoxPreferenceTextSwitchStatusInactive' => _x(
                'Off',
                'Frontend / Cookie Box / Switch Button Status',
                'borlabs-cookie'
            ),
            'cookieBoxPreferenceTextShowCookieLink' => _x(
                'Show Cookie Information',
                'Frontend / Cookie Box / Link Text',
                'borlabs-cookie'
            ),
            'cookieBoxPreferenceTextHideCookieLink' => _x(
                'Hide Cookie Information',
                'Frontend / Cookie Box / Link Text',
                'borlabs-cookie'
            ),

            'cookieBoxCookieDetailsTableAccept' => _x(
                'Accept',
                'Frontend / Cookie Box / Table Headline',
                'borlabs-cookie'
            ),
            'cookieBoxCookieDetailsTableName' => _x('Name', 'Frontend / Cookie Box / Table Headline', 'borlabs-cookie'),
            'cookieBoxCookieDetailsTableProvider' => _x(
                'Provider',
                'Frontend / Cookie Box / Table Headline',
                'borlabs-cookie'
            ),
            'cookieBoxCookieDetailsTablePurpose' => _x(
                'Purpose',
                'Frontend / Cookie Box / Table Headline',
                'borlabs-cookie'
            ),
            'cookieBoxCookieDetailsTablePrivacyPolicy' => _x(
                'Privacy Policy',
                'Frontend / Cookie Box / Table Headline',
                'borlabs-cookie'
            ),
            'cookieBoxCookieDetailsTableHosts' => _x(
                'Host(s)',
                'Frontend / Cookie Box / Table Headline',
                'borlabs-cookie'
            ),
            'cookieBoxCookieDetailsTableCookieName' => _x(
                'Cookie Name',
                'Frontend / Cookie Box / Table Headline',
                'borlabs-cookie'
            ),
            'cookieBoxCookieDetailsTableCookieExpiry' => _x(
                'Cookie Expiry',
                'Frontend / Cookie Box / Table Headline',
                'borlabs-cookie'
            ),

            'cookieBoxConsentHistoryTableDate' => _x(
                'Date',
                'Frontend / Consent History / Table Headline',
                'borlabs-cookie'
            ),
            'cookieBoxConsentHistoryTableVersion' => _x(
                'Version',
                'Frontend / Consent History / Table Headline',
                'borlabs-cookie'
            ),
            'cookieBoxConsentHistoryTableConsents' => _x(
                'Consents',
                'Frontend / Consent History / Table Headline',
                'borlabs-cookie'
            ),

            'contentBlockerHostWhitelist' => [],
            'removeIframesInFeeds' => true,

            'contentBlockerFontFamily' => 'inherit',
            'contentBlockerFontSize' => 14,
            'contentBlockerBgColor' => '#000',
            'contentBlockerTxtColor' => '#fff',
            'contentBlockerBgOpacity' => 80,
            'contentBlockerBtnBorderRadius' => 4,
            'contentBlockerBtnColor' => '#0063e3',
            'contentBlockerBtnHoverColor' => '#1a66ff',
            'contentBlockerBtnTxtColor' => '#fff',
            'contentBlockerBtnHoverTxtColor' => '#fff',
            'contentBlockerLinkColor' => '#2277ff',
            'contentBlockerLinkHoverColor' => '#1a66ff',

            'testEnvironment' => false,
        ];
    }

    /**
     * get function.
     *
     * @param mixed $configKey (default: null)
     */
    public function get($configKey = null)
    {
        // Get complete config
        if (empty($configKey)) {
            if (!empty($this->config)) {
                return $this->config;
            }

            return false;
        }

        if (isset($this->config[$configKey])) {
            return $this->config[$configKey];
        }
        // Fallback
        if (isset($this->defaultConfig()[$configKey])) {
            return $this->defaultConfig()[$configKey];
        }

        return false;
    }

    /**
     * getConfig function.
     *
     * @param mixed $language (default: null)
     */
    public function getConfig($language = null)
    {
        $config = [];

        if (empty($language)) {
            $configLanguage = Multilanguage::getInstance()->getCurrentLanguageCode();
        } else {
            $configLanguage = strtolower($language);
        }

        $config = get_option('BorlabsCookieLegacyConfig_' . $configLanguage, 'does not exist');

        if ($config === 'does not exist' || is_array($config) === false) {
            $config = $this->defaultConfig();
        }

        return $config;
    }

    /**
     * loadConfig function.
     *
     * @param mixed $language (default: null)
     */
    public function loadConfig($language = null)
    {
        $this->config = $this->getConfig($language);

        return $this->config;
    }

    /**
     * saveConfig function.
     *
     * @param mixed $configData
     */
    public function saveConfig($configData)
    {
        $configLanguage = Multilanguage::getInstance()->getCurrentLanguageCode();

        update_option('BorlabsCookieLegacyConfig_' . $configLanguage, $configData, 'no');

        $this->loadConfig();
    }
}
