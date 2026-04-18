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

use BorlabsCookie\Cookie\Config;
use BorlabsCookie\Cookie\Tools;

class CookieBox
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

    public function insertCookieBox()
    {
        $testEnvironment = false;

        if (Config::getInstance()->get('testEnvironment') === true) {
            $testEnvironment = true;
        }

        // Integration
        $integration = 'script';

        if (Config::getInstance()->get('cookieBoxIntegration') === 'html') {
            $integration = 'html';
        }

        // Refuse option type - Cookie Box
        $cookieBoxManageOptionType = Config::getInstance()->get('cookieBoxManageOptionType');

        // Refuse option type - Cookie Box
        $cookieBoxRefuseOptionType = Config::getInstance()->get('cookieBoxRefuseOptionType');

        // Refuse option type - Cookie Preferences
        $cookieBoxPreferenceRefuseOptionType = Config::getInstance()->get('cookieBoxPreferenceRefuseOptionType');

        // Hide Refuse option
        $cookieBoxHideRefuseOption = Config::getInstance()->get('cookieBoxHideRefuseOption');

        // Privacy Policy Link
        $cookieBoxPrivacyLink = '';

        if (!empty(Config::getInstance()->get('privacyPageURL'))) {
            $cookieBoxPrivacyLink = Config::getInstance()->get('privacyPageURL');
        }

        if (!empty(Config::getInstance()->get('privacyPageCustomURL'))) {
            $cookieBoxPrivacyLink = Config::getInstance()->get('privacyPageCustomURL');
        }

        // Imprint Link
        $cookieBoxImprintLink = '';

        if (!empty(Config::getInstance()->get('imprintPageURL'))) {
            $cookieBoxImprintLink = Config::getInstance()->get('imprintPageURL');
        }

        if (!empty(Config::getInstance()->get('imprintPageCustomURL'))) {
            $cookieBoxImprintLink = Config::getInstance()->get('imprintPageCustomURL');
        }

        $brightBackground = false;
        $bgColorHSL = Tools::getInstance()->hexToHsl(Config::getInstance()->get('cookieBoxBgColor'));

        if (isset($bgColorHSL[2]) && $bgColorHSL[2] <= 50) {
            $brightBackground = true;
        }

        // Support Borlabs Cookie
        $supportBorlabsCookie = Config::getInstance()->get('supportBorlabsCookie');
        $supportBorlabsCookieLogo = '';

        if ($supportBorlabsCookie) {
            $bgColorHSL = Tools::getInstance()->hexToHsl(Config::getInstance()->get('cookieBoxBgColor'));

            if ($brightBackground) {
                $supportBorlabsCookieLogo = BORLABS_COOKIE_PLUGIN_URL . 'assets/images/borlabs-cookie-icon-white.svg';
            } else {
                $supportBorlabsCookieLogo = BORLABS_COOKIE_PLUGIN_URL . 'assets/images/borlabs-cookie-icon-black.svg';
            }
        }

        // Cookie Settings
        $cookieBoxShowAcceptAllButton = Config::getInstance()->get('cookieBoxShowAcceptAllButton');

        // Position
        $cookieBoxPosition = esc_attr(Config::getInstance()->get('cookieBoxPosition'));

        // Logo
        $cookieBoxShowLogo = Config::getInstance()->get('cookieBoxShowLogo');
        $cookieBoxLogo = Config::getInstance()->get('cookieBoxLogo');
        $cookieBoxLogoHD = Config::getInstance()->get('cookieBoxLogoHD');
        $cookieBoxLogoSrcSet = [];
        $cookieBoxLogoSrcSet[] = $cookieBoxLogo;

        if (!empty($cookieBoxLogoHD)) {
            $cookieBoxLogoSrcSet[] = $cookieBoxLogoHD . ' 2x';
        }

        // Texts
        $cookieBoxTextHeadline = Config::getInstance()->get('cookieBoxTextHeadline');

        $cookieBoxDescriptionParts = [];
        $cookieBoxPreferencesDescriptionParts = [];
        $cookieBoxDescriptionParts[] = '<span class="_brlbs-paragraph _brlbs-text-description">' . nl2br(
            Config::getInstance()->get('cookieBoxTextDescription')
        ) . '</span>';

        if (Config::getInstance()->get('cookieBoxShowTextDescriptionConfirmAge')) {
            $cookieBoxDescriptionParts[] = '<span class="_brlbs-paragraph _brlbs-text-confirm-age">' . nl2br(
                Config::getInstance()->get('cookieBoxTextDescriptionConfirmAge')
            ) . '</span>';
            $cookieBoxPreferencesDescriptionParts[] = end($cookieBoxDescriptionParts);
        }

        if (Config::getInstance()->get('cookieBoxShowTextDescriptionTechnology')) {
            $cookieBoxDescriptionParts[] = '<span class="_brlbs-paragraph _brlbs-text-technology">' . nl2br(
                Config::getInstance()->get('cookieBoxTextDescriptionTechnology')
            ) . '</span>';
            $cookieBoxPreferencesDescriptionParts[] = end($cookieBoxDescriptionParts);
        }

        if (Config::getInstance()->get('cookieBoxShowTextDescriptionPersonalData')) {
            $cookieBoxDescriptionParts[] = '<span class="_brlbs-paragraph _brlbs-text-personal-data">' . nl2br(
                Config::getInstance()->get('cookieBoxTextDescriptionPersonalData')
            ) . '</span>';
            $cookieBoxPreferencesDescriptionParts[] = end($cookieBoxDescriptionParts);
        }

        if (Config::getInstance()->get('cookieBoxShowDescriptionMoreInformation')) {
            $cookieBoxDescriptionParts[] = '<span class="_brlbs-paragraph _brlbs-text-more-information">' . nl2br(
                Config::getInstance()->get('cookieBoxTextDescriptionMoreInformation')
            ) . '</span>';
            $cookieBoxPreferencesDescriptionParts[] = end($cookieBoxDescriptionParts);
        }

        if (Config::getInstance()->get('cookieBoxShowTextDescriptionNoObligation')) {
            $cookieBoxDescriptionParts[] = '<span class="_brlbs-paragraph _brlbs-text-no-commitment">' . nl2br(
                Config::getInstance()->get('cookieBoxTextDescriptionNoObligation')
            ) . '</span>';
            $cookieBoxPreferencesDescriptionParts[] = end($cookieBoxDescriptionParts);
        }

        if (Config::getInstance()->get('cookieBoxShowTextDescriptionRevoke')) {
            $cookieBoxDescriptionParts[] = '<span class="_brlbs-paragraph _brlbs-text-revoke">' . nl2br(
                Config::getInstance()->get('cookieBoxTextDescriptionRevoke')
            ) . '</span>';
        }

        if (Config::getInstance()->get('cookieBoxShowTextDescriptionIndividualSettings')) {
            $cookieBoxDescriptionParts[] = '<span class="_brlbs-paragraph _brlbs-text-individual-settings">' . nl2br(
                Config::getInstance()->get('cookieBoxTextDescriptionIndividualSettings')
            ) . '</span>';
            $cookieBoxPreferencesDescriptionParts[] = end($cookieBoxDescriptionParts);
        }

        if (Config::getInstance()->get('cookieBoxShowTextDescriptionNonEUDataTransfer')) {
            $cookieBoxDescriptionParts[] = '<span class="_brlbs-paragraph _brlbs-text-non-eu-data-transfer">' . nl2br(
                Config::getInstance()->get('cookieBoxTextDescriptionNonEUDataTransfer')
            ) . '</span>';
            $cookieBoxPreferencesDescriptionParts[] = end($cookieBoxDescriptionParts);
        }

        $cookieBoxTextDescription = implode(' ', $cookieBoxDescriptionParts);
        $cookieBoxTextDescription = str_replace(
            '{privacyPageURL}',
            $cookieBoxPrivacyLink,
            $cookieBoxTextDescription
        );

        $cookieBoxTextAcceptButton = Config::getInstance()->get('cookieBoxTextAcceptButton');
        $cookieBoxTextManageLink = Config::getInstance()->get('cookieBoxTextManageLink');
        $cookieBoxTextRefuseLink = Config::getInstance()->get('cookieBoxTextRefuseLink');
        $cookieBoxTextCookieDetailsLink = Config::getInstance()->get('cookieBoxTextCookieDetailsLink');
        $cookieBoxTextPrivacyLink = Config::getInstance()->get('cookieBoxTextPrivacyLink');
        $cookieBoxTextImprintLink = Config::getInstance()->get('cookieBoxTextImprintLink');

        $cookieBoxPreferenceTextHeadline = Config::getInstance()->get('cookieBoxPreferenceTextHeadline');
        $cookieBoxPreferencesDescriptionParts[] = '<span class="_brlbs-paragraph _brlbs-text-description">' . nl2br(
            Config::getInstance()->get('cookieBoxPreferenceTextDescription')
        ) . '</span>';
        $cookieBoxPreferenceTextDescription = implode(' ', $cookieBoxPreferencesDescriptionParts);
        $cookieBoxPreferenceTextDescription = str_replace(
            '{privacyPageURL}',
            $cookieBoxPrivacyLink,
            $cookieBoxPreferenceTextDescription
        );

        $cookieBoxPreferenceTextSaveButton = Config::getInstance()->get('cookieBoxPreferenceTextSaveButton');
        $cookieBoxPreferenceTextAcceptAllButton = Config::getInstance()->get('cookieBoxPreferenceTextAcceptAllButton');
        $cookieBoxPreferenceTextRefuseLink = Config::getInstance()->get('cookieBoxPreferenceTextRefuseLink');
        $cookieBoxPreferenceTextBackLink = Config::getInstance()->get('cookieBoxPreferenceTextBackLink');
        $cookieBoxPreferenceTextSwitchStatusActive = Config::getInstance()->get(
            'cookieBoxPreferenceTextSwitchStatusActive'
        );
        $cookieBoxPreferenceTextSwitchStatusInactive = Config::getInstance()->get(
            'cookieBoxPreferenceTextSwitchStatusInactive'
        );
        $cookieBoxPreferenceTextShowCookieLink = Config::getInstance()->get('cookieBoxPreferenceTextShowCookieLink');
        $cookieBoxPreferenceTextHideCookieLink = Config::getInstance()->get('cookieBoxPreferenceTextHideCookieLink');

        $cookieBoxCookieDetailsTableAccept = Config::getInstance()->get('cookieBoxCookieDetailsTableAccept');
        $cookieBoxCookieDetailsTableName = Config::getInstance()->get('cookieBoxCookieDetailsTableName');
        $cookieBoxCookieDetailsTableProvider = Config::getInstance()->get('cookieBoxCookieDetailsTableProvider');
        $cookieBoxCookieDetailsTablePurpose = Config::getInstance()->get('cookieBoxCookieDetailsTablePurpose');
        $cookieBoxCookieDetailsTablePrivacyPolicy = Config::getInstance()->get(
            'cookieBoxCookieDetailsTablePrivacyPolicy'
        );
        $cookieBoxCookieDetailsTableHosts = Config::getInstance()->get('cookieBoxCookieDetailsTableHosts');
        $cookieBoxCookieDetailsTableCookieName = Config::getInstance()->get('cookieBoxCookieDetailsTableCookieName');
        $cookieBoxCookieDetailsTableCookieExpiry = Config::getInstance()->get(
            'cookieBoxCookieDetailsTableCookieExpiry'
        );

        // Cookie Groups
        $cookieGroups = Cookies::getInstance()->getAllCookieGroups();

        if (!empty($cookieGroups)) {
            foreach ($cookieGroups as $key => $groupData) {
                $cookieGroups[$key]->hasCookies = !empty($groupData->cookies) ? true : false;
                $cookieGroups[$key]->displayCookieGroup = !empty($groupData->pre_selected) ? true : false;
                $cookieGroups[$key]->description = nl2br($groupData->description);
            }
        }

        if (Config::getInstance()->get('testEnvironment') === true) {
            $cookieBoxTextDescription .= '<span class="text-center" style="display: block !important;background: #fff;color: #f00;">'
                . _x(
                    'Borlabs Cookie - Test Environment active!',
                    'Frontend / Global / Alert Message',
                    'borlabs-cookie'
                ) . '</span>';
        }

        // Widget
        $cookieBoxShowWidget = Config::getInstance()->get('cookieBoxShowWidget');
        $cookieBoxWidgetLogo = BORLABS_COOKIE_PLUGIN_URL . 'assets/images/borlabs-cookie-icon-dynamic.svg';
        $cookieBoxWidgetPosition = esc_attr(Config::getInstance()->get('cookieBoxWidgetPosition'));

        // Cookie Box Layout
        $cookieBoxLayout = Config::getInstance()->get('cookieBoxLayout');
        $cookieBoxTemplate = 'cookie-box-layout-' . $cookieBoxLayout . '.html.php';
        $cookiePreferenceTemplate = 'cookie-box-preferences.html.php';

        $themePath = get_stylesheet_directory();
        $pluginTemplatePath = BORLABS_COOKIE_PLUGIN_PATH . 'templates';
        $cookieBoxTemplateFile = $pluginTemplatePath . '/' . $cookieBoxTemplate;
        $cookiePreferenceTemplateFile = $pluginTemplatePath . '/' . $cookiePreferenceTemplate;

        // Check if custom template file exists
        if (file_exists($themePath . '/plugins/' . dirname(BORLABS_COOKIE_BASENAME) . '/' . $cookieBoxTemplate)) {
            $cookieBoxTemplateFile = $themePath . '/plugins/' . dirname(BORLABS_COOKIE_BASENAME) . '/'
                . $cookieBoxTemplate;
        }

        // Check if custom preference template file exists
        if (
            file_exists(
                $themePath . '/plugins/' . dirname(BORLABS_COOKIE_BASENAME) . '/' . $cookiePreferenceTemplate
            )
        ) {
            $cookiePreferenceTemplateFile = $themePath . '/plugins/' . dirname(BORLABS_COOKIE_BASENAME) . '/'
                . $cookiePreferenceTemplate;
        }

        // Disable indexing of Borlabs Cookie data
        echo '<!--googleoff: all-->';
        echo '<div data-nosnippet>';

        if ($integration === 'script') {
            echo '<script id="BorlabsCookieBoxWrap" type="text/template">';
        } else {
            echo '<div id="BorlabsCookieBoxWrap">';
        }

        if (file_exists($cookieBoxTemplateFile)) {
            include $cookieBoxTemplateFile;
        }

        if ($integration === 'script') {
            echo '</script>';
        } else {
            echo '</div>';
        }

        if ($cookieBoxShowWidget) {
            include $pluginTemplatePath . '/cookie-box-widget.html.php';
        }

        // Re-enable indexing
        echo '</div>';
        echo '<!--googleon: all-->';
    }
}
