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

    private $imagePath;

    public function __construct()
    {
        $this->imagePath = plugins_url('assets/images', realpath(__DIR__ . '/../../'));
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
            // Save
            if ($action === 'save' && check_admin_referer('borlabs_cookie_cookie_box_save')) {
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
        $inputShowCookieBox = !empty(Config::getInstance()->get('showCookieBox')) ? 1 : 0;
        $switchShowCookieBox = $inputShowCookieBox ? ' active' : '';
        $inputShowCookieBoxOnLoginPage = !empty(Config::getInstance()->get('showCookieBoxOnLoginPage')) ? 1 : 0;
        $switchShowCookieBoxOnLoginPage = $inputShowCookieBoxOnLoginPage ? ' active' : '';
        $optionCookieBoxIntegrationHTML = Config::getInstance()->get('cookieBoxIntegration') === 'html' ? ' selected'
            : '';
        $optionCookieBoxIntegrationJavaScript = Config::getInstance()->get('cookieBoxIntegration') === 'javascript'
            ? ' selected' : '';
        $inputCookieBoxBlocksContent = !empty(Config::getInstance()->get('cookieBoxBlocksContent')) ? 1 : 0;
        $switchCookieBoxBlocksContent = $inputCookieBoxBlocksContent ? ' active' : '';
        $optionCookieBoxManageOptionTypeButton = Config::getInstance()->get('cookieBoxManageOptionType') === 'button'
            ? ' selected' : '';
        $optionCookieBoxManageOptionTypeLink = Config::getInstance()->get('cookieBoxManageOptionType') === 'link'
            ? ' selected' : '';
        $optionCookieBoxRefuseOptionTypeButton = Config::getInstance()->get('cookieBoxRefuseOptionType') === 'button'
            ? ' selected' : '';
        $optionCookieBoxRefuseOptionTypeLink = Config::getInstance()->get('cookieBoxRefuseOptionType') === 'link'
            ? ' selected' : '';
        $optionCookieBoxPreferenceRefuseOptionTypeButton = Config::getInstance()->get(
            'cookieBoxPreferenceRefuseOptionType'
        ) === 'button' ? ' selected' : '';
        $optionCookieBoxPreferenceRefuseOptionTypeLink = Config::getInstance()->get(
            'cookieBoxPreferenceRefuseOptionType'
        ) === 'link' ? ' selected' : '';
        $inputCookieBoxHideRefuseOption = !empty(Config::getInstance()->get('cookieBoxHideRefuseOption')) ? 1 : 0;
        $switchCookieBoxHideRefuseOption = $inputCookieBoxHideRefuseOption ? ' active' : '';
        $privacyPageId = !empty(Config::getInstance()->get('privacyPageId'))
        || Config::getInstance()->get(
            'privacyPageId'
        ) === -1 ? (int) (Config::getInstance()->get('privacyPageId')) : get_option('wp_page_for_privacy_policy', 0);
        $inputPrivacyPageCustomURL = esc_url(
            !empty(Config::getInstance()->get('privacyPageCustomURL')) ? Config::getInstance()->get(
                'privacyPageCustomURL'
            ) : ''
        );
        $imprintPageId = !empty(Config::getInstance()->get('imprintPageId'))
        || Config::getInstance()->get(
            'imprintPageId'
        ) === -1 ? (int) (Config::getInstance()->get('imprintPageId')) : 0;
        $inputImprintPageCustomURL = esc_url(
            !empty(Config::getInstance()->get('imprintPageCustomURL')) ? Config::getInstance()->get(
                'imprintPageCustomURL'
            ) : ''
        );
        $textareaHideCookieBoxOnPages = esc_textarea(
            !empty(Config::getInstance()->get('hideCookieBoxOnPages')) ? implode(
                "\n",
                Config::getInstance()->get('hideCookieBoxOnPages')
            ) : ''
        );
        $inputSupportBorlabsCookie = !empty(Config::getInstance()->get('supportBorlabsCookie')) ? 1 : 0;
        $switchSupportBorlabsCookie = $inputSupportBorlabsCookie ? ' active' : '';

        $inputCookieBoxShowAcceptAllButton = !empty(Config::getInstance()->get('cookieBoxShowAcceptAllButton')) ? 1
            : 0;
        $switchCookieBoxShowAcceptAllButton = $inputCookieBoxShowAcceptAllButton ? ' active' : '';
        $inputCookieBoxIgnorePreSelectStatus = !empty(Config::getInstance()->get('cookieBoxIgnorePreSelectStatus')) ? 1
            : 0;
        $switchCookieBoxIgnorePreSelectStatus = $inputCookieBoxIgnorePreSelectStatus ? ' active' : '';

        $optionCookieBoxLayoutBar = Config::getInstance()->get('cookieBoxLayout') === 'bar' ? ' selected' : '';
        $optionCookieBoxLayoutBarAdvanced = Config::getInstance()->get('cookieBoxLayout') === 'bar-advanced'
            ? ' selected' : '';
        $optionCookieBoxLayoutBarSlim = Config::getInstance()->get('cookieBoxLayout') === 'bar-slim' ? ' selected' : '';
        $optionCookieBoxLayoutBox = Config::getInstance()->get('cookieBoxLayout') === 'box' ? ' selected' : '';
        $optionCookieBoxLayoutBoxAdvanced = Config::getInstance()->get('cookieBoxLayout') === 'box-advanced'
            ? ' selected' : '';
        $optionCookieBoxLayoutBoxPlus = Config::getInstance()->get('cookieBoxLayout') === 'box-plus'
            ? ' selected' : '';
        $optionCookieBoxLayoutBoxSlim = Config::getInstance()->get('cookieBoxLayout') === 'box-slim' ? ' selected' : '';
        $optionCookieBoxPositionTL = Config::getInstance()->get('cookieBoxPosition') === 'top-left' ? ' selected' : '';
        $optionCookieBoxPositionTC = Config::getInstance()->get('cookieBoxPosition') === 'top-center' ? ' selected'
            : '';
        $optionCookieBoxPositionTR = Config::getInstance()->get('cookieBoxPosition') === 'top-right' ? ' selected' : '';
        $optionCookieBoxPositionML = Config::getInstance()->get('cookieBoxPosition') === 'middle-left' ? ' selected'
            : '';
        $optionCookieBoxPositionMC = Config::getInstance()->get('cookieBoxPosition') === 'middle-center' ? ' selected'
            : '';
        $optionCookieBoxPositionMR = Config::getInstance()->get('cookieBoxPosition') === 'middle-right' ? ' selected'
            : '';
        $optionCookieBoxPositionBL = Config::getInstance()->get('cookieBoxPosition') === 'bottom-left' ? ' selected'
            : '';
        $optionCookieBoxPositionBC = Config::getInstance()->get('cookieBoxPosition') === 'bottom-center' ? ' selected'
            : '';
        $optionCookieBoxPositionBR = Config::getInstance()->get('cookieBoxPosition') === 'bottom-right' ? ' selected'
            : '';

        $optionCookieBoxCookieGroupJustificationSA = Config::getInstance()->get('cookieBoxCookieGroupJustification')
        === 'space-around' ? ' selected' : '';
        $optionCookieBoxCookieGroupJustificationSB = Config::getInstance()->get('cookieBoxCookieGroupJustification')
        === 'space-between' ? ' selected' : '';

        $inputCookieBoxAnimation = !empty(Config::getInstance()->get('cookieBoxAnimation')) ? 1 : 0;
        $switchCookieBoxAnimation = $inputCookieBoxAnimation ? ' active' : '';
        $inputCookieBoxAnimationDelay = !empty(Config::getInstance()->get('cookieBoxAnimationDelay')) ? 1 : 0;
        $switchCookieBoxAnimationDelay = $inputCookieBoxAnimationDelay ? ' active' : '';

        $optionCookieBoxAnimationInBounce = Config::getInstance()->get('cookieBoxAnimationIn') === 'bounce'
            ? ' selected' : '';
        $optionCookieBoxAnimationInFlash = Config::getInstance()->get('cookieBoxAnimationIn') === 'flash' ? ' selected'
            : '';
        $optionCookieBoxAnimationInPulse = Config::getInstance()->get('cookieBoxAnimationIn') === 'pulse' ? ' selected'
            : '';
        $optionCookieBoxAnimationInRubberBand = Config::getInstance()->get('cookieBoxAnimationIn') === 'rubberBand'
            ? ' selected' : '';
        $optionCookieBoxAnimationInShake = Config::getInstance()->get('cookieBoxAnimationIn') === 'shake' ? ' selected'
            : '';
        $optionCookieBoxAnimationInSwing = Config::getInstance()->get('cookieBoxAnimationIn') === 'swing' ? ' selected'
            : '';
        $optionCookieBoxAnimationInTada = Config::getInstance()->get('cookieBoxAnimationIn') === 'tada' ? ' selected'
            : '';
        $optionCookieBoxAnimationInWobble = Config::getInstance()->get('cookieBoxAnimationIn') === 'wobble'
            ? ' selected' : '';
        $optionCookieBoxAnimationInJello = Config::getInstance()->get('cookieBoxAnimationIn') === 'jello' ? ' selected'
            : '';
        $optionCookieBoxAnimationInHeartBeat = Config::getInstance()->get('cookieBoxAnimationIn') === 'heartBeat'
            ? ' selected' : '';
        $optionCookieBoxAnimationInBounceIn = Config::getInstance()->get('cookieBoxAnimationIn') === 'bounceIn'
            ? ' selected' : '';
        $optionCookieBoxAnimationInBounceInDown = Config::getInstance()->get('cookieBoxAnimationIn') === 'bounceInDown'
            ? ' selected' : '';
        $optionCookieBoxAnimationInBounceInLeft = Config::getInstance()->get('cookieBoxAnimationIn') === 'bounceInLeft'
            ? ' selected' : '';
        $optionCookieBoxAnimationInBounceInRight = Config::getInstance()->get('cookieBoxAnimationIn')
        === 'bounceInRight' ? ' selected' : '';
        $optionCookieBoxAnimationInBounceInUp = Config::getInstance()->get('cookieBoxAnimationIn') === 'bounceInUp'
            ? ' selected' : '';
        $optionCookieBoxAnimationInFadeIn = Config::getInstance()->get('cookieBoxAnimationIn') === 'fadeIn'
            ? ' selected' : '';
        $optionCookieBoxAnimationInFadeInDown = Config::getInstance()->get('cookieBoxAnimationIn') === 'fadeInDown'
            ? ' selected' : '';
        $optionCookieBoxAnimationInFadeInDownBig = Config::getInstance()->get('cookieBoxAnimationIn')
        === 'fadeInDownBig' ? ' selected' : '';
        $optionCookieBoxAnimationInFadeInLeft = Config::getInstance()->get('cookieBoxAnimationIn') === 'fadeInLeft'
            ? ' selected' : '';
        $optionCookieBoxAnimationInFadeInLeftBig = Config::getInstance()->get('cookieBoxAnimationIn')
        === 'fadeInLeftBig' ? ' selected' : '';
        $optionCookieBoxAnimationInFadeInRight = Config::getInstance()->get('cookieBoxAnimationIn') === 'fadeInRight'
            ? ' selected' : '';
        $optionCookieBoxAnimationInFadeInRightBig = Config::getInstance()->get('cookieBoxAnimationIn')
        === 'fadeInRightBig' ? ' selected' : '';
        $optionCookieBoxAnimationInFadeInUp = Config::getInstance()->get('cookieBoxAnimationIn') === 'fadeInUp'
            ? ' selected' : '';
        $optionCookieBoxAnimationInFadeInUpBig = Config::getInstance()->get('cookieBoxAnimationIn') === 'fadeInUpBig'
            ? ' selected' : '';
        $optionCookieBoxAnimationInFlip = Config::getInstance()->get('cookieBoxAnimationIn') === 'flip' ? ' selected'
            : '';
        $optionCookieBoxAnimationInFlipInX = Config::getInstance()->get('cookieBoxAnimationIn') === 'flipInX'
            ? ' selected' : '';
        $optionCookieBoxAnimationInFlipInY = Config::getInstance()->get('cookieBoxAnimationIn') === 'flipInY'
            ? ' selected' : '';
        $optionCookieBoxAnimationInLightSpeedIn = Config::getInstance()->get('cookieBoxAnimationIn') === 'lightSpeedIn'
            ? ' selected' : '';
        $optionCookieBoxAnimationInRotateIn = Config::getInstance()->get('cookieBoxAnimationIn') === 'rotateIn'
            ? ' selected' : '';
        $optionCookieBoxAnimationInRotateInDownLeft = Config::getInstance()->get('cookieBoxAnimationIn')
        === 'rotateInDownLeft' ? ' selected' : '';
        $optionCookieBoxAnimationInRotateInDownRight = Config::getInstance()->get('cookieBoxAnimationIn')
        === 'rotateInDownRight' ? ' selected' : '';
        $optionCookieBoxAnimationInRotateInUpLeft = Config::getInstance()->get('cookieBoxAnimationIn')
        === 'rotateInUpLeft' ? ' selected' : '';
        $optionCookieBoxAnimationInRotateInUpRight = Config::getInstance()->get('cookieBoxAnimationIn')
        === 'rotateInUpRight' ? ' selected' : '';
        $optionCookieBoxAnimationInSlideInUp = Config::getInstance()->get('cookieBoxAnimationIn') === 'slideInUp'
            ? ' selected' : '';
        $optionCookieBoxAnimationInSlideInDown = Config::getInstance()->get('cookieBoxAnimationIn') === 'slideInDown'
            ? ' selected' : '';
        $optionCookieBoxAnimationInSlideInLeft = Config::getInstance()->get('cookieBoxAnimationIn') === 'slideInLeft'
            ? ' selected' : '';
        $optionCookieBoxAnimationInSlideInRight = Config::getInstance()->get('cookieBoxAnimationIn') === 'slideInRight'
            ? ' selected' : '';
        $optionCookieBoxAnimationInZoomIn = Config::getInstance()->get('cookieBoxAnimationIn') === 'zoomIn'
            ? ' selected' : '';
        $optionCookieBoxAnimationInZoomInDown = Config::getInstance()->get('cookieBoxAnimationIn') === 'zoomInDown'
            ? ' selected' : '';
        $optionCookieBoxAnimationInZoomInLeft = Config::getInstance()->get('cookieBoxAnimationIn') === 'zoomInLeft'
            ? ' selected' : '';
        $optionCookieBoxAnimationInZoomInRight = Config::getInstance()->get('cookieBoxAnimationIn') === 'zoomInRight'
            ? ' selected' : '';
        $optionCookieBoxAnimationInZoomInUp = Config::getInstance()->get('cookieBoxAnimationIn') === 'zoomInUp'
            ? ' selected' : '';
        $optionCookieBoxAnimationInJackInTheBox = Config::getInstance()->get('cookieBoxAnimationIn') === 'jackInTheBox'
            ? ' selected' : '';
        $optionCookieBoxAnimationInRollIn = Config::getInstance()->get('cookieBoxAnimationIn') === 'rollIn'
            ? ' selected' : '';

        $optionCookieBoxAnimationOnBounceOut = Config::getInstance()->get('cookieBoxAnimationOut') === 'bounceOut'
            ? ' selected' : '';
        $optionCookieBoxAnimationOnBounceOutDown = Config::getInstance()->get('cookieBoxAnimationOut')
        === 'bounceOutDown' ? ' selected' : '';
        $optionCookieBoxAnimationOnBounceOutLeft = Config::getInstance()->get('cookieBoxAnimationOut')
        === 'bounceOutLeft' ? ' selected' : '';
        $optionCookieBoxAnimationOnBounceOutRight = Config::getInstance()->get('cookieBoxAnimationOut')
        === 'bounceOutRight' ? ' selected' : '';
        $optionCookieBoxAnimationOnBounceOutUp = Config::getInstance()->get('cookieBoxAnimationOut') === 'bounceOutUp'
            ? ' selected' : '';
        $optionCookieBoxAnimationOnFadeOut = Config::getInstance()->get('cookieBoxAnimationOut') === 'fadeOut'
            ? ' selected' : '';
        $optionCookieBoxAnimationOnFadeOutDown = Config::getInstance()->get('cookieBoxAnimationOut') === 'fadeOutDown'
            ? ' selected' : '';
        $optionCookieBoxAnimationOnFadeOutDownBig = Config::getInstance()->get('cookieBoxAnimationOut')
        === 'fadeOutDownBig' ? ' selected' : '';
        $optionCookieBoxAnimationOnFadeOutLeft = Config::getInstance()->get('cookieBoxAnimationOut') === 'fadeOutLeft'
            ? ' selected' : '';
        $optionCookieBoxAnimationOnFadeOutLeftBig = Config::getInstance()->get('cookieBoxAnimationOut')
        === 'fadeOutLeftBig' ? ' selected' : '';
        $optionCookieBoxAnimationOnFadeOutRight = Config::getInstance()->get('cookieBoxAnimationOut') === 'fadeOutRight'
            ? ' selected' : '';
        $optionCookieBoxAnimationOnFadeOutRightBig = Config::getInstance()->get('cookieBoxAnimationOut')
        === 'fadeOutRightBig' ? ' selected' : '';
        $optionCookieBoxAnimationOnFadeOutUp = Config::getInstance()->get('cookieBoxAnimationOut') === 'fadeOutUp'
            ? ' selected' : '';
        $optionCookieBoxAnimationOnFadeOutUpBig = Config::getInstance()->get('cookieBoxAnimationOut') === 'fadeOutUpBig'
            ? ' selected' : '';
        $optionCookieBoxAnimationOnFlipOutX = Config::getInstance()->get('cookieBoxAnimationOut') === 'flipOutX'
            ? ' selected' : '';
        $optionCookieBoxAnimationOnFlipOutY = Config::getInstance()->get('cookieBoxAnimationOut') === 'flipOutY'
            ? ' selected' : '';
        $optionCookieBoxAnimationOnLightSpeedOut = Config::getInstance()->get('cookieBoxAnimationOut')
        === 'lightSpeedOut' ? ' selected' : '';
        $optionCookieBoxAnimationOnRotateOut = Config::getInstance()->get('cookieBoxAnimationOut') === 'rotateOut'
            ? ' selected' : '';
        $optionCookieBoxAnimationOnRotateOutDownLeft = Config::getInstance()->get('cookieBoxAnimationOut')
        === 'rotateOutDownLeft' ? ' selected' : '';
        $optionCookieBoxAnimationOnRotateOutDownRight = Config::getInstance()->get('cookieBoxAnimationOut')
        === 'rotateOutDownRight' ? ' selected' : '';
        $optionCookieBoxAnimationOnRotateOutUpLeft = Config::getInstance()->get('cookieBoxAnimationOut')
        === 'rotateOutUpLeft' ? ' selected' : '';
        $optionCookieBoxAnimationOnRotateOutUpRight = Config::getInstance()->get('cookieBoxAnimationOut')
        === 'rotateOutUpRight' ? ' selected' : '';
        $optionCookieBoxAnimationOnSlideOutUp = Config::getInstance()->get('cookieBoxAnimationOut') === 'slideOutUp'
            ? ' selected' : '';
        $optionCookieBoxAnimationOnSlideOutDown = Config::getInstance()->get('cookieBoxAnimationOut') === 'slideOutDown'
            ? ' selected' : '';
        $optionCookieBoxAnimationOnSlideOutLeft = Config::getInstance()->get('cookieBoxAnimationOut') === 'slideOutLeft'
            ? ' selected' : '';
        $optionCookieBoxAnimationOnSlideOutRight = Config::getInstance()->get('cookieBoxAnimationOut')
        === 'slideOutRight' ? ' selected' : '';
        $optionCookieBoxAnimationOnZoomOut = Config::getInstance()->get('cookieBoxAnimationOut') === 'zoomOut'
            ? ' selected' : '';
        $optionCookieBoxAnimationOnZoomOutDown = Config::getInstance()->get('cookieBoxAnimationOut') === 'zoomOutDown'
            ? ' selected' : '';
        $optionCookieBoxAnimationOnZoomOutLeft = Config::getInstance()->get('cookieBoxAnimationOut') === 'zoomOutLeft'
            ? ' selected' : '';
        $optionCookieBoxAnimationOnZoomOutRight = Config::getInstance()->get('cookieBoxAnimationOut') === 'zoomOutRight'
            ? ' selected' : '';
        $optionCookieBoxAnimationOnZoomOutUp = Config::getInstance()->get('cookieBoxAnimationOut') === 'zoomOutUp'
            ? ' selected' : '';
        $optionCookieBoxAnimationOnHinge = Config::getInstance()->get('cookieBoxAnimationOut') === 'hinge' ? ' selected'
            : '';
        $optionCookieBoxAnimationOnRollOut = Config::getInstance()->get('cookieBoxAnimationOut') === 'rollOut'
            ? ' selected' : '';

        $animationPreviewImage = $this->imagePath . '/borlabs-cookie-logo.svg';

        $inputCookieBoxShowLogo = !empty(Config::getInstance()->get('cookieBoxShowLogo')) ? 1 : 0;
        $switchCookieBoxShowLogo = $inputCookieBoxShowLogo ? ' active' : '';
        $inputCookieBoxShowWidget = !empty(Config::getInstance()->get('cookieBoxShowWidget')) ? 1 : 0;
        $switchCookieBoxShowWidget = $inputCookieBoxShowWidget ? ' active' : '';

        $optionCookieBoxWidgetPositionBL = Config::getInstance()->get('cookieBoxWidgetPosition') === 'bottom-left' ? ' selected'
            : '';
        $optionCookieBoxWidgetPositionBR = Config::getInstance()->get('cookieBoxWidgetPosition') === 'bottom-right' ? ' selected'
            : '';

        $inputCookieBoxWidgetColor = esc_attr(
            !empty(Config::getInstance()->get('cookieBoxWidgetColor')) ? Config::getInstance()->get('cookieBoxWidgetColor')
                : ''
        );

        $inputCookieBoxLogo = esc_url(
            !empty(Config::getInstance()->get('cookieBoxLogo')) ? Config::getInstance()->get('cookieBoxLogo')
                : $this->imagePath . '/borlabs-cookie-logo.svg'
        );
        $inputCookieBoxLogoHD = esc_url(
            !empty(Config::getInstance()->get('cookieBoxLogoHD')) ? Config::getInstance()->get('cookieBoxLogoHD')
                : $this->imagePath . '/borlabs-cookie-logo.svg'
        );
        $inputCookieBoxFontFamily = esc_attr(
            !empty(Config::getInstance()->get('cookieBoxFontFamily'))
            && Config::getInstance()->get('cookieBoxFontFamily') !== 'inherit' ? Config::getInstance()->get(
                'cookieBoxFontFamily'
            ) : ''
        );
        $inputCookieBoxFontSize = esc_attr(
            !empty(Config::getInstance()->get('cookieBoxFontSize')) && Config::getInstance()->get('cookieBoxFontSize')
                ? Config::getInstance()->get('cookieBoxFontSize') : ''
        );
        $inputCookieBoxBgColor = esc_attr(
            !empty(Config::getInstance()->get('cookieBoxBgColor')) ? Config::getInstance()->get('cookieBoxBgColor')
                : ''
        );
        $inputCookieBoxTxtColor = esc_attr(
            !empty(Config::getInstance()->get('cookieBoxTxtColor')) ? Config::getInstance()->get('cookieBoxTxtColor')
                : ''
        );
        $inputCookieBoxAccordionBgColor = esc_attr(
            !empty(Config::getInstance()->get('cookieBoxAccordionBgColor')) ? Config::getInstance()->get(
                'cookieBoxAccordionBgColor'
            ) : ''
        );
        $inputCookieBoxAccordionTxtColor = esc_attr(
            !empty(Config::getInstance()->get('cookieBoxAccordionTxtColor')) ? Config::getInstance()->get(
                'cookieBoxAccordionTxtColor'
            ) : ''
        );
        $inputCookieBoxTableBgColor = esc_attr(
            !empty(Config::getInstance()->get('cookieBoxTableBgColor')) ? Config::getInstance()->get(
                'cookieBoxTableBgColor'
            ) : ''
        );
        $inputCookieBoxTableTxtColor = esc_attr(
            !empty(Config::getInstance()->get('cookieBoxTableTxtColor')) ? Config::getInstance()->get(
                'cookieBoxTableTxtColor'
            ) : ''
        );
        $inputCookieBoxTableBorderColor = esc_attr(
            !empty(Config::getInstance()->get('cookieBoxTableBorderColor')) ? Config::getInstance()->get(
                'cookieBoxTableBorderColor'
            ) : ''
        );
        $inputCookieBoxBorderRadius = esc_attr(
            !empty(Config::getInstance()->get('cookieBoxBorderRadius')) ? Config::getInstance()->get(
                'cookieBoxBorderRadius'
            ) : 0
        );
        $inputCookieBoxBtnBorderRadius = esc_attr(
            !empty(Config::getInstance()->get('cookieBoxBtnBorderRadius')) ? Config::getInstance()->get(
                'cookieBoxBtnBorderRadius'
            ) : 0
        );
        $inputCookieBoxCheckboxBorderRadius = esc_attr(
            !empty(Config::getInstance()->get('cookieBoxCheckboxBorderRadius')) ? Config::getInstance()->get(
                'cookieBoxCheckboxBorderRadius'
            ) : 0
        );
        $inputCookieBoxAccordionBorderRadius = esc_attr(
            !empty(Config::getInstance()->get('cookieBoxAccordionBorderRadius')) ? Config::getInstance()->get(
                'cookieBoxAccordionBorderRadius'
            ) : 0
        );
        $inputCookieBoxTableBorderRadius = esc_attr(
            !empty(Config::getInstance()->get('cookieBoxTableBorderRadius')) ? Config::getInstance()->get(
                'cookieBoxTableBorderRadius'
            ) : 0
        );

        $inputCookieBoxBtnFullWidth = !empty(Config::getInstance()->get('cookieBoxBtnFullWidth')) ? 1 : 0;
        $switchCookieBoxBtnFullWidth = $inputCookieBoxBtnFullWidth ? ' active' : '';

        $inputCookieBoxBtnColor = esc_attr(
            !empty(Config::getInstance()->get('cookieBoxBtnColor')) ? Config::getInstance()->get('cookieBoxBtnColor')
                : ''
        );
        $inputCookieBoxBtnHoverColor = esc_attr(
            !empty(Config::getInstance()->get('cookieBoxBtnHoverColor')) ? Config::getInstance()->get(
                'cookieBoxBtnHoverColor'
            ) : ''
        );
        $inputCookieBoxBtnTxtColor = esc_attr(
            !empty(Config::getInstance()->get('cookieBoxBtnTxtColor')) ? Config::getInstance()->get(
                'cookieBoxBtnTxtColor'
            ) : ''
        );
        $inputCookieBoxBtnTxtHoverColor = esc_attr(
            !empty(Config::getInstance()->get('cookieBoxBtnHoverTxtColor')) ? Config::getInstance()->get(
                'cookieBoxBtnHoverTxtColor'
            ) : ''
        );
        $inputCookieBoxRefuseBtnColor = esc_attr(
            !empty(Config::getInstance()->get('cookieBoxRefuseBtnColor')) ? Config::getInstance()->get(
                'cookieBoxRefuseBtnColor'
            ) : ''
        );
        $inputCookieBoxRefuseBtnHoverColor = esc_attr(
            !empty(Config::getInstance()->get('cookieBoxRefuseBtnHoverColor')) ? Config::getInstance()->get(
                'cookieBoxRefuseBtnHoverColor'
            ) : ''
        );
        $inputCookieBoxRefuseBtnTxtColor = esc_attr(
            !empty(Config::getInstance()->get('cookieBoxRefuseBtnTxtColor')) ? Config::getInstance()->get(
                'cookieBoxRefuseBtnTxtColor'
            ) : ''
        );
        $inputCookieBoxRefuseBtnTxtHoverColor = esc_attr(
            !empty(Config::getInstance()->get('cookieBoxRefuseBtnHoverTxtColor')) ? Config::getInstance()->get(
                'cookieBoxRefuseBtnHoverTxtColor'
            ) : ''
        );
        $inputCookieBoxAcceptAllBtnColor = esc_attr(
            !empty(Config::getInstance()->get('cookieBoxAcceptAllBtnColor')) ? Config::getInstance()->get(
                'cookieBoxAcceptAllBtnColor'
            ) : ''
        );
        $inputCookieBoxAcceptAllBtnHoverColor = esc_attr(
            !empty(Config::getInstance()->get('cookieBoxAcceptAllBtnHoverColor')) ? Config::getInstance()->get(
                'cookieBoxAcceptAllBtnHoverColor'
            ) : ''
        );
        $inputCookieBoxAcceptAllBtnTxtColor = esc_attr(
            !empty(Config::getInstance()->get('cookieBoxAcceptAllBtnTxtColor')) ? Config::getInstance()->get(
                'cookieBoxAcceptAllBtnTxtColor'
            ) : ''
        );
        $inputCookieBoxAcceptAllBtnTxtHoverColor = esc_attr(
            !empty(Config::getInstance()->get('cookieBoxAcceptAllBtnHoverTxtColor')) ? Config::getInstance()->get(
                'cookieBoxAcceptAllBtnHoverTxtColor'
            ) : ''
        );
        $inputCookieBoxIndividualSettingsBtnColor = esc_attr(
            !empty(Config::getInstance()->get('cookieBoxIndividualSettingsBtnColor')) ? Config::getInstance()->get(
                'cookieBoxIndividualSettingsBtnColor'
            ) : ''
        );
        $inputCookieBoxIndividualSettingsBtnHoverColor = esc_attr(
            !empty(Config::getInstance()->get('cookieBoxIndividualSettingsBtnHoverColor')) ? Config::getInstance()->get(
                'cookieBoxIndividualSettingsBtnHoverColor'
            ) : ''
        );
        $inputCookieBoxIndividualSettingsBtnTxtColor = esc_attr(
            !empty(Config::getInstance()->get('cookieBoxIndividualSettingsBtnTxtColor')) ? Config::getInstance()->get(
                'cookieBoxIndividualSettingsBtnTxtColor'
            ) : ''
        );
        $inputCookieBoxIndividualSettingsBtnTxtHoverColor = esc_attr(
            !empty(Config::getInstance()->get('cookieBoxIndividualSettingsBtnHoverTxtColor')) ? Config::getInstance()->get(
                'cookieBoxIndividualSettingsBtnHoverTxtColor'
            ) : ''
        );

        $inputCookieBoxBtnSwitchActiveBgColor = esc_attr(
            !empty(Config::getInstance()->get('cookieBoxBtnSwitchActiveBgColor')) ? Config::getInstance()->get(
                'cookieBoxBtnSwitchActiveBgColor'
            ) : ''
        );
        $inputCookieBoxBtnSwitchInactiveBgColor = esc_attr(
            !empty(Config::getInstance()->get('cookieBoxBtnSwitchInactiveBgColor')) ? Config::getInstance()->get(
                'cookieBoxBtnSwitchInactiveBgColor'
            ) : ''
        );
        $inputCookieBoxBtnSwitchActiveColor = esc_attr(
            !empty(Config::getInstance()->get('cookieBoxBtnSwitchActiveColor')) ? Config::getInstance()->get(
                'cookieBoxBtnSwitchActiveColor'
            ) : ''
        );
        $inputCookieBoxBtnSwitchInactiveColor = esc_attr(
            !empty(Config::getInstance()->get('cookieBoxBtnSwitchInactiveColor')) ? Config::getInstance()->get(
                'cookieBoxBtnSwitchInactiveColor'
            ) : ''
        );
        $inputCookieBoxBtnSwitchRound = !empty(Config::getInstance()->get('cookieBoxBtnSwitchRound')) ? 1 : 0;
        $switchCookieBoxBtnSwitchRound = $inputCookieBoxBtnSwitchRound ? ' active' : '';

        $inputCookieBoxCheckboxActiveBgColor = esc_attr(
            !empty(Config::getInstance()->get('cookieBoxCheckboxActiveBgColor')) ? Config::getInstance()->get(
                'cookieBoxCheckboxActiveBgColor'
            ) : ''
        );
        $inputCookieBoxCheckboxActiveBorderColor = esc_attr(
            !empty(Config::getInstance()->get('cookieBoxCheckboxActiveBorderColor')) ? Config::getInstance()->get(
                'cookieBoxCheckboxActiveBorderColor'
            ) : ''
        );
        $inputCookieBoxCheckboxInactiveBgColor = esc_attr(
            !empty(Config::getInstance()->get('cookieBoxCheckboxInactiveBgColor')) ? Config::getInstance()->get(
                'cookieBoxCheckboxInactiveBgColor'
            ) : ''
        );
        $inputCookieBoxCheckboxInactiveBorderColor = esc_attr(
            !empty(Config::getInstance()->get('cookieBoxCheckboxInactiveBorderColor')) ? Config::getInstance()->get(
                'cookieBoxCheckboxInactiveBorderColor'
            ) : ''
        );
        $inputCookieBoxCheckboxDisabledBgColor = esc_attr(
            !empty(Config::getInstance()->get('cookieBoxCheckboxDisabledBgColor')) ? Config::getInstance()->get(
                'cookieBoxCheckboxDisabledBgColor'
            ) : ''
        );
        $inputCookieBoxCheckboxDisabledBorderColor = esc_attr(
            !empty(Config::getInstance()->get('cookieBoxCheckboxDisabledBorderColor')) ? Config::getInstance()->get(
                'cookieBoxCheckboxDisabledBorderColor'
            ) : ''
        );
        $inputCookieBoxCheckboxCheckMarkActiveColor = esc_attr(
            !empty(Config::getInstance()->get('cookieBoxCheckboxCheckMarkActiveColor')) ? Config::getInstance()->get(
                'cookieBoxCheckboxCheckMarkActiveColor'
            ) : ''
        );
        $inputCookieBoxCheckboxCheckMarkDisabledColor = esc_attr(
            !empty(Config::getInstance()->get('cookieBoxCheckboxCheckMarkDisabledColor')) ? Config::getInstance()->get(
                'cookieBoxCheckboxCheckMarkDisabledColor'
            ) : ''
        );

        $inputCookieBoxPrimaryLinkColor = esc_attr(
            !empty(Config::getInstance()->get('cookieBoxPrimaryLinkColor')) ? Config::getInstance()->get(
                'cookieBoxPrimaryLinkColor'
            ) : ''
        );
        $inputCookieBoxPrimaryLinkHoverColor = esc_attr(
            !empty(Config::getInstance()->get('cookieBoxPrimaryLinkHoverColor')) ? Config::getInstance()->get(
                'cookieBoxPrimaryLinkHoverColor'
            ) : ''
        );
        $inputCookieBoxSecondaryLinkColor = esc_attr(
            !empty(Config::getInstance()->get('cookieBoxSecondaryLinkColor')) ? Config::getInstance()->get(
                'cookieBoxSecondaryLinkColor'
            ) : ''
        );
        $inputCookieBoxSecondaryLinkHoverColor = esc_attr(
            !empty(Config::getInstance()->get('cookieBoxSecondaryLinkHoverColor')) ? Config::getInstance()->get(
                'cookieBoxSecondaryLinkHoverColor'
            ) : ''
        );
        $inputCookieBoxRejectionLinkColor = esc_attr(
            !empty(Config::getInstance()->get('cookieBoxRejectionLinkColor')) ? Config::getInstance()->get(
                'cookieBoxRejectionLinkColor'
            ) : ''
        );
        $inputCookieBoxRejectionLinkHoverColor = esc_attr(
            !empty(Config::getInstance()->get('cookieBoxRejectionLinkHoverColor')) ? Config::getInstance()->get(
                'cookieBoxRejectionLinkHoverColor'
            ) : ''
        );

        $inputCookieBoxShowTextDescriptionConfirmAge = !empty(
        Config::getInstance()->get(
            'cookieBoxShowTextDescriptionConfirmAge'
        )
        ) ? 1 : 0;
        $textareaCookieBoxTextDescriptionConfirmAge = esc_textarea(
            !empty(Config::getInstance()->get('cookieBoxTextDescriptionConfirmAge')) ? Config::getInstance()->get(
                'cookieBoxTextDescriptionConfirmAge'
            ) : ''
        );
        $inputCookieBoxShowTextDescriptionTechnology = !empty(
        Config::getInstance()->get(
            'cookieBoxShowTextDescriptionTechnology'
        )
        ) ? 1 : 0;
        $textareaCookieBoxTextDescriptionTechnology = esc_textarea(
            !empty(Config::getInstance()->get('cookieBoxTextDescriptionTechnology')) ? Config::getInstance()->get(
                'cookieBoxTextDescriptionTechnology'
            ) : ''
        );
        $inputCookieBoxShowTextDescriptionPersonalData = !empty(
        Config::getInstance()->get(
            'cookieBoxShowTextDescriptionPersonalData'
        )
        ) ? 1 : 0;
        $textareaCookieBoxTextDescriptionPersonalData = esc_textarea(
            !empty(Config::getInstance()->get('cookieBoxTextDescriptionPersonalData')) ? Config::getInstance()->get(
                'cookieBoxTextDescriptionPersonalData'
            ) : ''
        );
        $inputCookieBoxShowDescriptionMoreInformation = !empty(
        Config::getInstance()->get(
            'cookieBoxShowDescriptionMoreInformation'
        )
        ) ? 1 : 0;
        $textareaCookieBoxTextDescriptionMoreInformation = esc_textarea(
            !empty(Config::getInstance()->get('cookieBoxTextDescriptionMoreInformation')) ? Config::getInstance()->get(
                'cookieBoxTextDescriptionMoreInformation'
            ) : ''
        );
        $inputCookieBoxShowTextDescriptionNoObligation = !empty(
        Config::getInstance()->get(
            'cookieBoxShowTextDescriptionNoObligation'
        )
        ) ? 1 : 0;
        $textareaCookieBoxTextDescriptionNoObligation = esc_textarea(
            !empty(Config::getInstance()->get('cookieBoxTextDescriptionNoObligation')) ? Config::getInstance()->get(
                'cookieBoxTextDescriptionNoObligation'
            ) : ''
        );
        $inputCookieBoxShowTextDescriptionRevoke = !empty(
        Config::getInstance()->get(
            'cookieBoxShowTextDescriptionRevoke'
        )
        ) ? 1 : 0;
        $textareaCookieBoxTextDescriptionRevoke = esc_textarea(
            !empty(Config::getInstance()->get('cookieBoxTextDescriptionRevoke')) ? Config::getInstance()->get(
                'cookieBoxTextDescriptionRevoke'
            ) : ''
        );
        $inputCookieBoxShowTextDescriptionIndividualSettings = !empty(
        Config::getInstance()->get(
            'cookieBoxShowTextDescriptionIndividualSettings'
        )
        ) ? 1 : 0;
        $textareaCookieBoxTextDescriptionIndividualSettings = esc_textarea(
            !empty(Config::getInstance()->get('cookieBoxTextDescriptionIndividualSettings')) ? Config::getInstance()
                ->get('cookieBoxTextDescriptionIndividualSettings') : ''
        );
        $inputCookieBoxShowTextDescriptionNonEUDataTransfer = !empty(
        Config::getInstance()->get(
            'cookieBoxShowTextDescriptionNonEUDataTransfer'
        )
        ) ? 1 : 0;
        $textareaCookieBoxTextDescriptionNonEUDataTransfer = esc_textarea(
            !empty(Config::getInstance()->get('cookieBoxTextDescriptionNonEUDataTransfer')) ? Config::getInstance()
                ->get('cookieBoxTextDescriptionNonEUDataTransfer') : ''
        );

        $inputCookieBoxTextHeadline = esc_attr(
            !empty(Config::getInstance()->get('cookieBoxTextHeadline')) ? Config::getInstance()->get(
                'cookieBoxTextHeadline'
            ) : ''
        );
        $textareaCookieBoxTextDescription = esc_textarea(
            !empty(Config::getInstance()->get('cookieBoxTextDescription')) ? Config::getInstance()->get(
                'cookieBoxTextDescription'
            ) : ''
        );
        $inputCookieBoxTextAcceptButton = esc_attr(
            !empty(Config::getInstance()->get('cookieBoxTextAcceptButton')) ? Config::getInstance()->get(
                'cookieBoxTextAcceptButton'
            ) : ''
        );
        $inputCookieBoxTextManageLink = esc_attr(
            !empty(Config::getInstance()->get('cookieBoxTextManageLink')) ? Config::getInstance()->get(
                'cookieBoxTextManageLink'
            ) : ''
        );
        $inputCookieBoxTextRefuseLink = esc_attr(
            !empty(Config::getInstance()->get('cookieBoxTextRefuseLink')) ? Config::getInstance()->get(
                'cookieBoxTextRefuseLink'
            ) : ''
        );
        $inputCookieBoxTextCookieDetailsLink = esc_attr(
            !empty(Config::getInstance()->get('cookieBoxTextCookieDetailsLink')) ? Config::getInstance()->get(
                'cookieBoxTextCookieDetailsLink'
            ) : ''
        );
        $inputCookieBoxTextPrivacyLink = esc_attr(
            !empty(Config::getInstance()->get('cookieBoxTextPrivacyLink')) ? Config::getInstance()->get(
                'cookieBoxTextPrivacyLink'
            ) : ''
        );
        $inputCookieBoxTextImprintLink = esc_attr(
            !empty(Config::getInstance()->get('cookieBoxTextImprintLink')) ? Config::getInstance()->get(
                'cookieBoxTextImprintLink'
            ) : ''
        );

        $inputCookieBoxPreferenceTextHeadline = esc_attr(
            !empty(Config::getInstance()->get('cookieBoxPreferenceTextHeadline')) ? Config::getInstance()->get(
                'cookieBoxPreferenceTextHeadline'
            ) : ''
        );
        $textareaCookieBoxPreferenceTextDescription = esc_textarea(
            !empty(Config::getInstance()->get('cookieBoxPreferenceTextDescription')) ? Config::getInstance()->get(
                'cookieBoxPreferenceTextDescription'
            ) : ''
        );
        $inputCookieBoxPreferenceTextSaveButton = esc_attr(
            !empty(Config::getInstance()->get('cookieBoxPreferenceTextSaveButton')) ? Config::getInstance()->get(
                'cookieBoxPreferenceTextSaveButton'
            ) : ''
        );
        $inputCookieBoxPreferenceTextAcceptAllButton = esc_attr(
            !empty(Config::getInstance()->get('cookieBoxPreferenceTextAcceptAllButton')) ? Config::getInstance()->get(
                'cookieBoxPreferenceTextAcceptAllButton'
            ) : ''
        );
        $inputCookieBoxPreferenceTextRefuseLink = esc_attr(
            !empty(Config::getInstance()->get('cookieBoxPreferenceTextRefuseLink')) ? Config::getInstance()->get(
                'cookieBoxPreferenceTextRefuseLink'
            ) : ''
        );
        $inputCookieBoxPreferenceTextBackLink = esc_attr(
            !empty(Config::getInstance()->get('cookieBoxPreferenceTextBackLink')) ? Config::getInstance()->get(
                'cookieBoxPreferenceTextBackLink'
            ) : ''
        );
        $inputCookieBoxPreferenceTextSwitchStatusActive = esc_attr(
            !empty(Config::getInstance()->get('cookieBoxPreferenceTextSwitchStatusActive')) ? Config::getInstance()
                ->get('cookieBoxPreferenceTextSwitchStatusActive') : ''
        );
        $inputCookieBoxPreferenceTextSwitchStatusInactive = esc_attr(
            !empty(Config::getInstance()->get('cookieBoxPreferenceTextSwitchStatusInactive')) ? Config::getInstance()
                ->get('cookieBoxPreferenceTextSwitchStatusInactive') : ''
        );
        $inputCookieBoxPreferenceTextShowCookieLink = esc_attr(
            !empty(Config::getInstance()->get('cookieBoxPreferenceTextShowCookieLink')) ? Config::getInstance()->get(
                'cookieBoxPreferenceTextShowCookieLink'
            ) : ''
        );
        $inputCookieBoxPreferenceTextHideCookieLink = esc_attr(
            !empty(Config::getInstance()->get('cookieBoxPreferenceTextHideCookieLink')) ? Config::getInstance()->get(
                'cookieBoxPreferenceTextHideCookieLink'
            ) : ''
        );

        $inputCookieBoxCookieDetailsTableAccept = esc_attr(
            !empty(Config::getInstance()->get('cookieBoxCookieDetailsTableAccept')) ? Config::getInstance()->get(
                'cookieBoxCookieDetailsTableAccept'
            ) : ''
        );
        $inputCookieBoxCookieDetailsTableName = esc_attr(
            !empty(Config::getInstance()->get('cookieBoxCookieDetailsTableName')) ? Config::getInstance()->get(
                'cookieBoxCookieDetailsTableName'
            ) : ''
        );
        $inputCookieBoxCookieDetailsTableProvider = esc_attr(
            !empty(Config::getInstance()->get('cookieBoxCookieDetailsTableProvider')) ? Config::getInstance()->get(
                'cookieBoxCookieDetailsTableProvider'
            ) : ''
        );
        $inputCookieBoxCookieDetailsTablePurpose = esc_attr(
            !empty(Config::getInstance()->get('cookieBoxCookieDetailsTablePurpose')) ? Config::getInstance()->get(
                'cookieBoxCookieDetailsTablePurpose'
            ) : ''
        );
        $inputCookieBoxCookieDetailsTablePrivacyPolicy = esc_attr(
            !empty(Config::getInstance()->get('cookieBoxCookieDetailsTablePrivacyPolicy')) ? Config::getInstance()
                ->get('cookieBoxCookieDetailsTablePrivacyPolicy') : ''
        );
        $inputCookieBoxCookieDetailsTableHosts = esc_attr(
            !empty(Config::getInstance()->get('cookieBoxCookieDetailsTableHosts')) ? Config::getInstance()->get(
                'cookieBoxCookieDetailsTableHosts'
            ) : ''
        );
        $inputCookieBoxCookieDetailsTableCookieName = esc_attr(
            !empty(Config::getInstance()->get('cookieBoxCookieDetailsTableCookieName')) ? Config::getInstance()->get(
                'cookieBoxCookieDetailsTableCookieName'
            ) : ''
        );
        $inputCookieBoxCookieDetailsTableCookieExpiry = esc_attr(
            !empty(Config::getInstance()->get('cookieBoxCookieDetailsTableCookieExpiry')) ? Config::getInstance()->get(
                'cookieBoxCookieDetailsTableCookieExpiry'
            ) : ''
        );

        $inputCookieBoxConsentHistoryTableDate = esc_attr(
            !empty(Config::getInstance()->get('cookieBoxConsentHistoryTableDate')) ? Config::getInstance()->get(
                'cookieBoxConsentHistoryTableDate'
            ) : ''
        );
        $inputCookieBoxConsentHistoryTableVersion = esc_attr(
            !empty(Config::getInstance()->get('cookieBoxConsentHistoryTableVersion')) ? Config::getInstance()->get(
                'cookieBoxConsentHistoryTableVersion'
            ) : ''
        );
        $inputCookieBoxConsentHistoryTableConsents = esc_attr(
            !empty(Config::getInstance()->get('cookieBoxConsentHistoryTableConsents')) ? Config::getInstance()->get(
                'cookieBoxConsentHistoryTableConsents'
            ) : ''
        );

        $textareaCookieBoxCustomCSS = esc_textarea(
            !empty(Config::getInstance()->get('cookieBoxCustomCSS')) ? Config::getInstance()->get('cookieBoxCustomCSS')
                : ''
        );

        $tipsImageCookieBox = $this->imagePath . '/tips-cookie-box.png';
        $tipsImageCookiePreference = $this->imagePath . '/tips-cookie-preference.png';

        include Backend::getInstance()->templatePath . '/cookie-box.html.php';
    }

    /**
     * save function.
     *
     * @param mixed $formData
     */
    public function save($formData)
    {
        $defaultConfig = Config::getInstance()->defaultConfig();
        $updatedConfig = Config::getInstance()->get();

        // General Settings
        $updatedConfig['showCookieBox'] = !empty($formData['showCookieBox']) ? true : false;
        $updatedConfig['showCookieBoxOnLoginPage'] = !empty($formData['showCookieBoxOnLoginPage']) ? true : false;

        $updatedConfig['cookieBoxIntegration'] = 'javascript';

        if (!empty($formData['cookieBoxIntegration'])) {
            if ($formData['cookieBoxIntegration'] === 'html') {
                $updatedConfig['cookieBoxIntegration'] = 'html';
            }
        }

        $updatedConfig['cookieBoxBlocksContent'] = !empty($formData['cookieBoxBlocksContent']) ? true : false;

        $updatedConfig['cookieBoxManageOptionType'] = 'link';

        if (!empty($formData['cookieBoxManageOptionType'])) {
            if ($formData['cookieBoxManageOptionType'] === 'button') {
                $updatedConfig['cookieBoxManageOptionType'] = 'button';
            }
        }

        $updatedConfig['cookieBoxRefuseOptionType'] = 'button';

        if (!empty($formData['cookieBoxRefuseOptionType'])) {
            if ($formData['cookieBoxRefuseOptionType'] === 'link') {
                $updatedConfig['cookieBoxRefuseOptionType'] = 'link';
            }
        }

        $updatedConfig['cookieBoxPreferenceRefuseOptionType'] = 'button';

        if (!empty($formData['cookieBoxPreferenceRefuseOptionType'])) {
            if ($formData['cookieBoxPreferenceRefuseOptionType'] === 'link') {
                $updatedConfig['cookieBoxPreferenceRefuseOptionType'] = 'link';
            }
        }

        $updatedConfig['cookieBoxHideRefuseOption'] = !empty($formData['cookieBoxHideRefuseOption']) ? true : false;

        // Privacy URL
        $updatedConfig['privacyPageId'] = 0;

        if (!empty($formData['privacyPageId'])) {
            $updatedConfig['privacyPageId'] = $formData['privacyPageId'];

            $postData = get_post($updatedConfig['privacyPageId']);

            if (!empty($postData->ID)) {
                $permalink = get_permalink($postData->ID);
                $updatedConfig['privacyPageURL'] = $permalink;
            }
        } else {
            $updatedConfig['privacyPageId'] = 0;
            $updatedConfig['privacyPageURL'] = '';
        }

        if (!empty($formData['enablePrivacyPageCustomURL']) && !empty($formData['privacyPageCustomURL'])) {
            if (filter_var($formData['privacyPageCustomURL'], FILTER_VALIDATE_URL)) {
                $updatedConfig['privacyPageId'] = -1;
                $updatedConfig['privacyPageURL'] = $formData['privacyPageCustomURL'];
                $updatedConfig['privacyPageCustomURL'] = $formData['privacyPageCustomURL'];
            }
        } else {
            $updatedConfig['privacyPageCustomURL'] = '';
        }

        // Imprint URL
        $updatedConfig['imprintPageId'] = 0;

        if (!empty($formData['imprintPageId'])) {
            $updatedConfig['imprintPageId'] = $formData['imprintPageId'];

            $postData = get_post($updatedConfig['imprintPageId']);

            if (!empty($postData->ID)) {
                $permalink = get_permalink($postData->ID);
                $updatedConfig['imprintPageURL'] = $permalink;
            }
        } else {
            $updatedConfig['imprintPageId'] = 0;
            $updatedConfig['imprintPageURL'] = 0;
        }

        if (!empty($formData['enableImprintPageCustomURL']) && !empty($formData['imprintPageCustomURL'])) {
            if (filter_var($formData['imprintPageCustomURL'], FILTER_VALIDATE_URL)) {
                $updatedConfig['imprintPageId'] = -1;
                $updatedConfig['imprintPageURL'] = $formData['imprintPageCustomURL'];
                $updatedConfig['imprintPageCustomURL'] = $formData['imprintPageCustomURL'];
            }
        } else {
            $updatedConfig['imprintPageCustomURL'] = '';
        }

        // Hide Cookie Box on Page
        $updatedConfig['hideCookieBoxOnPages'] = [];

        if (!empty($formData['hideCookieBoxOnPages'])) {
            $formData['hideCookieBoxOnPages'] = stripslashes($formData['hideCookieBoxOnPages']);
            $formData['hideCookieBoxOnPages'] = preg_split('/\r\n|[\r\n]/', $formData['hideCookieBoxOnPages']);

            if (!empty($formData['hideCookieBoxOnPages'])) {
                foreach ($formData['hideCookieBoxOnPages'] as $path) {
                    $path = trim(stripslashes($path));

                    if (!empty($path)) {
                        $updatedConfig['hideCookieBoxOnPages'][] = $path;
                    }
                }
            }
        }

        $updatedConfig['supportBorlabsCookie'] = !empty($formData['supportBorlabsCookie']) ? true : false;

        $updatedConfig['cookieBoxShowAcceptAllButton'] = !empty($formData['cookieBoxShowAcceptAllButton']) ? true
            : false;
        $updatedConfig['cookieBoxIgnorePreSelectStatus'] = !empty($formData['cookieBoxIgnorePreSelectStatus']) ? true
            : false;

        // Appearance Settings
        $updatedConfig['cookieBoxLayout'] = 'box';

        if (!empty($formData['cookieBoxLayout'])) {
            if ($formData['cookieBoxLayout'] === 'bar') {
                $updatedConfig['cookieBoxLayout'] = 'bar';
            }

            if ($formData['cookieBoxLayout'] === 'bar-advanced') {
                $updatedConfig['cookieBoxLayout'] = 'bar-advanced';
            }

            if ($formData['cookieBoxLayout'] === 'bar-slim') {
                $updatedConfig['cookieBoxLayout'] = 'bar-slim';
            }

            if ($formData['cookieBoxLayout'] === 'box-advanced') {
                $updatedConfig['cookieBoxLayout'] = 'box-advanced';
            }

            if ($formData['cookieBoxLayout'] === 'box-plus') {
                $updatedConfig['cookieBoxLayout'] = 'box-plus';
            }

            if ($formData['cookieBoxLayout'] === 'box-slim') {
                $updatedConfig['cookieBoxLayout'] = 'box-slim';
            }
        }

        $updatedConfig['cookieBoxPosition'] = 'top-center';

        if (!empty($formData['cookieBoxPosition'])) {
            $validCookieBoxPositions = [
                'top-left',
                'top-center',
                'top-right',
                'middle-left',
                'middle-center',
                'middle-right',
                'bottom-left',
                'bottom-center',
                'bottom-right',
            ];

            if (in_array($formData['cookieBoxPosition'], $validCookieBoxPositions, true)) {
                $updatedConfig['cookieBoxPosition'] = $formData['cookieBoxPosition'];
            }
        }

        $updatedConfig['cookieBoxCookieGroupJustification'] = 'space-between';

        if (!empty($formData['cookieBoxCookieGroupJustification'])) {
            $validCookieBoxGroupJustifications = [
                'space-around',
                'space-between',
            ];

            if (in_array($formData['cookieBoxCookieGroupJustification'], $validCookieBoxGroupJustifications, true)) {
                $updatedConfig['cookieBoxCookieGroupJustification'] = $formData['cookieBoxCookieGroupJustification'];
            }
        }

        $updatedConfig['cookieBoxAnimation'] = !empty($formData['cookieBoxAnimation']) ? true : false;
        $updatedConfig['cookieBoxAnimationDelay'] = !empty($formData['cookieBoxAnimationDelay']) ? true : false;

        $updatedConfig['cookieBoxAnimationIn'] = 'flipInX';

        $validAnimationIn = [
            'bounce',
            'flash',
            'pulse',
            'rubberBand',
            'shake',
            'swing',
            'tada',
            'wobble',
            'jello',
            'heartBeat',
            'bounceIn',
            'bounceInDown',
            'bounceInLeft',
            'bounceInRight',
            'bounceInUp',
            'fadeIn',
            'fadeInDown',
            'fadeInDownBig',
            'fadeInLeft',
            'fadeInLeftBig',
            'fadeInRight',
            'fadeInRightBig',
            'fadeInUp',
            'fadeInUpBig',
            'flip',
            'flipInX',
            'flipInY',
            'lightSpeedIn',
            'rotateIn',
            'rotateInDownLeft',
            'rotateInDownRight',
            'rotateInUpLeft',
            'rotateInUpRight',
            'slideInUp',
            'slideInDown',
            'slideInLeft',
            'slideInRight',
            'zoomIn',
            'zoomInDown',
            'zoomInLeft',
            'zoomInRight',
            'zoomInUp',
            'jackInTheBox',
            'rollIn',
        ];

        if (
            !empty($formData['cookieBoxAnimationIn'])
            && in_array(
                $formData['cookieBoxAnimationIn'],
                $validAnimationIn,
                true
            )
        ) {
            $updatedConfig['cookieBoxAnimationIn'] = $formData['cookieBoxAnimationIn'];
        }

        $updatedConfig['cookieBoxAnimationOut'] = 'flipOutX';

        $validAnimationOut = [
            'bounceOut',
            'bounceOutDown',
            'bounceOutLeft',
            'bounceOutRight',
            'bounceOutUp',
            'fadeOut',
            'fadeOutDown',
            'fadeOutDownBig',
            'fadeOutLeft',
            'fadeOutLeftBig',
            'fadeOutRight',
            'fadeOutRightBig',
            'fadeOutUp',
            'fadeOutUpBig',
            'flipOutX',
            'flipOutY',
            'lightSpeedOut',
            'rotateOut',
            'rotateOutDownLeft',
            'rotateOutDownRight',
            'rotateOutUpLeft',
            'rotateOutUpRight',
            'slideOutUp',
            'slideOutDown',
            'slideOutLeft',
            'slideOutRight',
            'zoomOut',
            'zoomOutDown',
            'zoomOutLeft',
            'zoomOutRight',
            'zoomOutUp',
            'hinge',
            'rollOut',
        ];

        if (
            !empty($formData['cookieBoxAnimationOut'])
            && in_array(
                $formData['cookieBoxAnimationOut'],
                $validAnimationOut,
                true
            )
        ) {
            $updatedConfig['cookieBoxAnimationOut'] = $formData['cookieBoxAnimationOut'];
        }

        // Overlay
        $updatedConfig['cookieBoxShowWidget'] = !empty($formData['cookieBoxShowWidget']) ? true : false;

        $updatedConfig['cookieBoxWidgetColor'] = !empty($formData['cookieBoxWidgetColor'])
        && Tools::getInstance()->validateHexColor($formData['cookieBoxWidgetColor']) ? $formData['cookieBoxWidgetColor']
            : $defaultConfig['cookieBoxWidgetColor'];

        $updatedConfig['cookieBoxWidgetPosition'] = 'bottom-left';

        if (!empty($formData['cookieBoxWidgetPosition'])) {
            $validCookieBoxWidgetPositions = [
                'bottom-left',
                'bottom-right',
            ];

            if (in_array($formData['cookieBoxWidgetPosition'], $validCookieBoxWidgetPositions, true)) {
                $updatedConfig['cookieBoxWidgetPosition'] = $formData['cookieBoxWidgetPosition'];
            }
        }

        // Logos
        $updatedConfig['cookieBoxShowLogo'] = !empty($formData['cookieBoxShowLogo']) ? true : false;
        $updatedConfig['cookieBoxLogo'] = $this->imagePath . '/borlabs-cookie-logo.svg';
        $updatedConfig['cookieBoxLogoHD'] = $this->imagePath . '/borlabs-cookie-logo.svg';

        if (!empty($formData['cookieBoxLogo'])) {
            $updatedConfig['cookieBoxLogo'] = $formData['cookieBoxLogo'];
        }

        if (!empty($formData['cookieBoxLogoHD'])) {
            $updatedConfig['cookieBoxLogoHD'] = $formData['cookieBoxLogoHD'];
        }

        $updatedConfig['cookieBoxFontFamily'] = !empty($formData['cookieBoxFontFamily']) ? stripslashes(
            $formData['cookieBoxFontFamily']
        ) : $defaultConfig['cookieBoxFontFamily'];
        $updatedConfig['cookieBoxFontSize'] = !empty($formData['cookieBoxFontSize']) ? (int) (
            $formData['cookieBoxFontSize']
        ) : $defaultConfig['cookieBoxFontSize'];

        // Colors
        $updatedConfig['cookieBoxBgColor'] = !empty($formData['cookieBoxBgColor'])
        && Tools::getInstance()->validateHexColor($formData['cookieBoxBgColor']) ? $formData['cookieBoxBgColor']
            : $defaultConfig['cookieBoxBgColor'];
        $updatedConfig['cookieBoxTxtColor'] = !empty($formData['cookieBoxTxtColor'])
        && Tools::getInstance()->validateHexColor($formData['cookieBoxTxtColor']) ? $formData['cookieBoxTxtColor']
            : $defaultConfig['cookieBoxTxtColor'];
        $updatedConfig['cookieBoxAccordionBgColor'] = !empty($formData['cookieBoxAccordionBgColor'])
        && Tools::getInstance()->validateHexColor($formData['cookieBoxAccordionBgColor'])
            ? $formData['cookieBoxAccordionBgColor'] : $defaultConfig['cookieBoxAccordionBgColor'];
        $updatedConfig['cookieBoxAccordionTxtColor'] = !empty($formData['cookieBoxAccordionTxtColor'])
        && Tools::getInstance()->validateHexColor($formData['cookieBoxAccordionTxtColor'])
            ? $formData['cookieBoxAccordionTxtColor'] : $defaultConfig['cookieBoxAccordionTxtColor'];
        $updatedConfig['cookieBoxTableBgColor'] = !empty($formData['cookieBoxTableBgColor'])
        && Tools::getInstance()->validateHexColor($formData['cookieBoxTableBgColor'])
            ? $formData['cookieBoxTableBgColor'] : $defaultConfig['cookieBoxTableBgColor'];
        $updatedConfig['cookieBoxTableTxtColor'] = !empty($formData['cookieBoxTableTxtColor'])
        && Tools::getInstance()->validateHexColor($formData['cookieBoxTableTxtColor'])
            ? $formData['cookieBoxTableTxtColor'] : $defaultConfig['cookieBoxTableTxtColor'];
        $updatedConfig['cookieBoxTableBorderColor'] = !empty($formData['cookieBoxTableBorderColor'])
        && Tools::getInstance()->validateHexColor($formData['cookieBoxTableBorderColor'])
            ? $formData['cookieBoxTableBorderColor'] : $defaultConfig['cookieBoxTableBorderColor'];
        $updatedConfig['cookieBoxBorderRadius'] = isset($formData['cookieBoxBorderRadius']) ? (int) (
            $formData['cookieBoxBorderRadius']
        ) : $defaultConfig['cookieBoxBorderRadius'];
        $updatedConfig['cookieBoxBtnBorderRadius'] = isset($formData['cookieBoxBtnBorderRadius']) ? (int) (
            $formData['cookieBoxBtnBorderRadius']
        ) : $defaultConfig['cookieBoxBtnBorderRadius'];
        $updatedConfig['cookieBoxCheckboxBorderRadius'] = isset($formData['cookieBoxCheckboxBorderRadius']) ? (int) (
            $formData['cookieBoxCheckboxBorderRadius']
        ) : $defaultConfig['cookieBoxCheckboxBorderRadius'];
        $updatedConfig['cookieBoxAccordionBorderRadius'] = isset($formData['cookieBoxAccordionBorderRadius']) ? (int) (
            $formData['cookieBoxAccordionBorderRadius']
        ) : $defaultConfig['cookieBoxAccordionBorderRadius'];
        $updatedConfig['cookieBoxTableBorderRadius'] = isset($formData['cookieBoxTableBorderRadius']) ? (int) (
            $formData['cookieBoxTableBorderRadius']
        ) : $defaultConfig['cookieBoxTableBorderRadius'];

        $updatedConfig['cookieBoxBtnFullWidth'] = !empty($formData['cookieBoxBtnFullWidth']) ? true : false;
        $updatedConfig['cookieBoxBtnColor'] = !empty($formData['cookieBoxBtnColor'])
        && Tools::getInstance()->validateHexColor($formData['cookieBoxBtnColor']) ? $formData['cookieBoxBtnColor']
            : $defaultConfig['cookieBoxBtnColor'];
        $updatedConfig['cookieBoxBtnHoverColor'] = !empty($formData['cookieBoxBtnHoverColor'])
        && Tools::getInstance()->validateHexColor($formData['cookieBoxBtnHoverColor'])
            ? $formData['cookieBoxBtnHoverColor'] : $defaultConfig['cookieBoxBtnHoverColor'];
        $updatedConfig['cookieBoxBtnTxtColor'] = !empty($formData['cookieBoxBtnTxtColor'])
        && Tools::getInstance()->validateHexColor($formData['cookieBoxBtnTxtColor']) ? $formData['cookieBoxBtnTxtColor']
            : $defaultConfig['cookieBoxBtnTxtColor'];
        $updatedConfig['cookieBoxBtnHoverTxtColor'] = !empty($formData['cookieBoxBtnHoverTxtColor'])
        && Tools::getInstance()->validateHexColor($formData['cookieBoxBtnHoverTxtColor'])
            ? $formData['cookieBoxBtnHoverTxtColor'] : $defaultConfig['cookieBoxBtnHoverTxtColor'];
        $updatedConfig['cookieBoxRefuseBtnColor'] = !empty($formData['cookieBoxRefuseBtnColor'])
        && Tools::getInstance()->validateHexColor($formData['cookieBoxRefuseBtnColor'])
            ? $formData['cookieBoxRefuseBtnColor'] : $defaultConfig['cookieBoxRefuseBtnColor'];
        $updatedConfig['cookieBoxRefuseBtnHoverColor'] = !empty($formData['cookieBoxRefuseBtnHoverColor'])
        && Tools::getInstance()->validateHexColor($formData['cookieBoxRefuseBtnHoverColor'])
            ? $formData['cookieBoxRefuseBtnHoverColor'] : $defaultConfig['cookieBoxRefuseBtnHoverColor'];
        $updatedConfig['cookieBoxRefuseBtnTxtColor'] = !empty($formData['cookieBoxRefuseBtnTxtColor'])
        && Tools::getInstance()->validateHexColor($formData['cookieBoxRefuseBtnTxtColor'])
            ? $formData['cookieBoxRefuseBtnTxtColor'] : $defaultConfig['cookieBoxRefuseBtnTxtColor'];
        $updatedConfig['cookieBoxRefuseBtnHoverTxtColor'] = !empty($formData['cookieBoxRefuseBtnHoverTxtColor'])
        && Tools::getInstance()->validateHexColor($formData['cookieBoxRefuseBtnHoverTxtColor'])
            ? $formData['cookieBoxRefuseBtnHoverTxtColor'] : $defaultConfig['cookieBoxRefuseBtnHoverTxtColor'];
        $updatedConfig['cookieBoxIndividualSettingsBtnColor'] = !empty($formData['cookieBoxIndividualSettingsBtnColor'])
        && Tools::getInstance()->validateHexColor($formData['cookieBoxIndividualSettingsBtnColor'])
            ? $formData['cookieBoxIndividualSettingsBtnColor'] : $defaultConfig['cookieBoxIndividualSettingsBtnColor'];
        $updatedConfig['cookieBoxIndividualSettingsBtnHoverColor'] = !empty($formData['cookieBoxIndividualSettingsBtnHoverColor'])
        && Tools::getInstance()->validateHexColor($formData['cookieBoxIndividualSettingsBtnHoverColor'])
            ? $formData['cookieBoxIndividualSettingsBtnHoverColor'] : $defaultConfig['cookieBoxIndividualSettingsBtnHoverColor'];
        $updatedConfig['cookieBoxIndividualSettingsBtnTxtColor'] = !empty($formData['cookieBoxIndividualSettingsBtnTxtColor'])
        && Tools::getInstance()->validateHexColor($formData['cookieBoxIndividualSettingsBtnTxtColor'])
            ? $formData['cookieBoxIndividualSettingsBtnTxtColor'] : $defaultConfig['cookieBoxIndividualSettingsBtnTxtColor'];
        $updatedConfig['cookieBoxIndividualSettingsBtnHoverTxtColor'] = !empty($formData['cookieBoxIndividualSettingsBtnHoverTxtColor'])
        && Tools::getInstance()->validateHexColor($formData['cookieBoxIndividualSettingsBtnHoverTxtColor'])
            ? $formData['cookieBoxIndividualSettingsBtnHoverTxtColor'] : $defaultConfig['cookieBoxIndividualSettingsBtnHoverTxtColor'];
        $updatedConfig['cookieBoxAcceptAllBtnColor'] = !empty($formData['cookieBoxAcceptAllBtnColor'])
        && Tools::getInstance()->validateHexColor($formData['cookieBoxAcceptAllBtnColor'])
            ? $formData['cookieBoxAcceptAllBtnColor'] : $defaultConfig['cookieBoxAcceptAllBtnColor'];
        $updatedConfig['cookieBoxAcceptAllBtnHoverColor'] = !empty($formData['cookieBoxAcceptAllBtnHoverColor'])
        && Tools::getInstance()->validateHexColor($formData['cookieBoxAcceptAllBtnHoverColor'])
            ? $formData['cookieBoxAcceptAllBtnHoverColor'] : $defaultConfig['cookieBoxAcceptAllBtnHoverColor'];
        $updatedConfig['cookieBoxAcceptAllBtnTxtColor'] = !empty($formData['cookieBoxAcceptAllBtnTxtColor'])
        && Tools::getInstance()->validateHexColor($formData['cookieBoxAcceptAllBtnTxtColor'])
            ? $formData['cookieBoxAcceptAllBtnTxtColor'] : $defaultConfig['cookieBoxAcceptAllBtnTxtColor'];
        $updatedConfig['cookieBoxAcceptAllBtnHoverTxtColor'] = !empty($formData['cookieBoxAcceptAllBtnHoverTxtColor'])
        && Tools::getInstance()->validateHexColor($formData['cookieBoxAcceptAllBtnHoverTxtColor'])
            ? $formData['cookieBoxAcceptAllBtnHoverTxtColor'] : $defaultConfig['cookieBoxAcceptAllBtnHoverTxtColor'];

        $updatedConfig['cookieBoxBtnSwitchActiveBgColor'] = !empty($formData['cookieBoxBtnSwitchActiveBgColor'])
        && Tools::getInstance()->validateHexColor($formData['cookieBoxBtnSwitchActiveBgColor'])
            ? $formData['cookieBoxBtnSwitchActiveBgColor'] : $defaultConfig['cookieBoxBtnSwitchActiveBgColor'];
        $updatedConfig['cookieBoxBtnSwitchInactiveBgColor'] = !empty($formData['cookieBoxBtnSwitchInactiveBgColor'])
        && Tools::getInstance()->validateHexColor($formData['cookieBoxBtnSwitchInactiveBgColor'])
            ? $formData['cookieBoxBtnSwitchInactiveBgColor'] : $defaultConfig['cookieBoxBtnSwitchInactiveBgColor'];
        $updatedConfig['cookieBoxBtnSwitchActiveColor'] = !empty($formData['cookieBoxBtnSwitchActiveColor'])
        && Tools::getInstance()->validateHexColor($formData['cookieBoxBtnSwitchActiveColor'])
            ? $formData['cookieBoxBtnSwitchActiveColor'] : $defaultConfig['cookieBoxBtnSwitchActiveColor'];
        $updatedConfig['cookieBoxBtnSwitchInactiveColor'] = !empty($formData['cookieBoxBtnSwitchInactiveColor'])
        && Tools::getInstance()->validateHexColor($formData['cookieBoxBtnSwitchInactiveColor'])
            ? $formData['cookieBoxBtnSwitchInactiveColor'] : $defaultConfig['cookieBoxBtnSwitchInactiveColor'];
        $updatedConfig['cookieBoxBtnSwitchRound'] = !empty($formData['cookieBoxBtnSwitchRound']) ? true : false;
        $updatedConfig['cookieBoxPrimaryLinkColor'] = !empty($formData['cookieBoxPrimaryLinkColor'])
        && Tools::getInstance()->validateHexColor($formData['cookieBoxPrimaryLinkColor'])
            ? $formData['cookieBoxPrimaryLinkColor'] : $defaultConfig['cookieBoxPrimaryLinkColor'];
        $updatedConfig['cookieBoxPrimaryLinkHoverColor'] = !empty($formData['cookieBoxPrimaryLinkHoverColor'])
        && Tools::getInstance()->validateHexColor($formData['cookieBoxPrimaryLinkHoverColor'])
            ? $formData['cookieBoxPrimaryLinkHoverColor'] : $defaultConfig['cookieBoxPrimaryLinkHoverColor'];
        $updatedConfig['cookieBoxSecondaryLinkColor'] = !empty($formData['cookieBoxSecondaryLinkColor'])
        && Tools::getInstance()->validateHexColor($formData['cookieBoxSecondaryLinkColor'])
            ? $formData['cookieBoxSecondaryLinkColor'] : $defaultConfig['cookieBoxSecondaryLinkColor'];
        $updatedConfig['cookieBoxSecondaryLinkHoverColor'] = !empty($formData['cookieBoxSecondaryLinkHoverColor'])
        && Tools::getInstance()->validateHexColor($formData['cookieBoxSecondaryLinkHoverColor'])
            ? $formData['cookieBoxSecondaryLinkHoverColor'] : $defaultConfig['cookieBoxSecondaryLinkHoverColor'];
        $updatedConfig['cookieBoxRejectionLinkColor'] = !empty($formData['cookieBoxRejectionLinkColor'])
        && Tools::getInstance()->validateHexColor($formData['cookieBoxRejectionLinkColor'])
            ? $formData['cookieBoxRejectionLinkColor'] : $defaultConfig['cookieBoxRejectionLinkColor'];
        $updatedConfig['cookieBoxRejectionLinkHoverColor'] = !empty($formData['cookieBoxRejectionLinkHoverColor'])
        && Tools::getInstance()->validateHexColor($formData['cookieBoxRejectionLinkHoverColor'])
            ? $formData['cookieBoxRejectionLinkHoverColor'] : $defaultConfig['cookieBoxRejectionLinkHoverColor'];

        $updatedConfig['cookieBoxCheckboxActiveBgColor'] = !empty($formData['cookieBoxCheckboxActiveBgColor'])
        && Tools::getInstance()->validateHexColor($formData['cookieBoxCheckboxActiveBgColor'])
            ? $formData['cookieBoxCheckboxActiveBgColor'] : $defaultConfig['cookieBoxCheckboxActiveBgColor'];
        $updatedConfig['cookieBoxCheckboxActiveBorderColor'] = !empty($formData['cookieBoxCheckboxActiveBorderColor'])
        && Tools::getInstance()->validateHexColor($formData['cookieBoxCheckboxActiveBorderColor'])
            ? $formData['cookieBoxCheckboxActiveBorderColor'] : $defaultConfig['cookieBoxCheckboxActiveBorderColor'];
        $updatedConfig['cookieBoxCheckboxInactiveBgColor'] = !empty($formData['cookieBoxCheckboxInactiveBgColor'])
        && Tools::getInstance()->validateHexColor($formData['cookieBoxCheckboxInactiveBgColor'])
            ? $formData['cookieBoxCheckboxInactiveBgColor'] : $defaultConfig['cookieBoxCheckboxInactiveBgColor'];
        $updatedConfig['cookieBoxCheckboxInactiveBorderColor']
            = !empty($formData['cookieBoxCheckboxInactiveBorderColor'])
        && Tools::getInstance()->validateHexColor(
            $formData['cookieBoxCheckboxInactiveBorderColor']
        ) ? $formData['cookieBoxCheckboxInactiveBorderColor'] : $defaultConfig['cookieBoxCheckboxInactiveBorderColor'];
        $updatedConfig['cookieBoxCheckboxDisabledBgColor'] = !empty($formData['cookieBoxCheckboxDisabledBgColor'])
        && Tools::getInstance()->validateHexColor($formData['cookieBoxCheckboxDisabledBgColor'])
            ? $formData['cookieBoxCheckboxDisabledBgColor'] : $defaultConfig['cookieBoxCheckboxDisabledBgColor'];
        $updatedConfig['cookieBoxCheckboxDisabledBorderColor']
            = !empty($formData['cookieBoxCheckboxDisabledBorderColor'])
        && Tools::getInstance()->validateHexColor(
            $formData['cookieBoxCheckboxDisabledBorderColor']
        ) ? $formData['cookieBoxCheckboxDisabledBorderColor'] : $defaultConfig['cookieBoxCheckboxDisabledBorderColor'];
        $updatedConfig['cookieBoxCheckboxCheckMarkActiveColor']
            = !empty($formData['cookieBoxCheckboxCheckMarkActiveColor'])
        && Tools::getInstance()->validateHexColor(
            $formData['cookieBoxCheckboxCheckMarkActiveColor']
        ) ? $formData['cookieBoxCheckboxCheckMarkActiveColor']
            : $defaultConfig['cookieBoxCheckboxCheckMarkActiveColor'];
        $updatedConfig['cookieBoxCheckboxCheckMarkDisabledColor']
            = !empty($formData['cookieBoxCheckboxCheckMarkDisabledColor'])
        && Tools::getInstance()->validateHexColor(
            $formData['cookieBoxCheckboxCheckMarkDisabledColor']
        ) ? $formData['cookieBoxCheckboxCheckMarkDisabledColor']
            : $defaultConfig['cookieBoxCheckboxCheckMarkDisabledColor'];

        // Legal Texts
        // Age
        $updatedConfig['cookieBoxShowTextDescriptionConfirmAge']
            = !empty($formData['cookieBoxShowTextDescriptionConfirmAge']) ? true : false;

        if (!empty($updatedConfig['cookieBoxShowTextDescriptionConfirmAge'])) {
            $updatedConfig['cookieBoxTextDescriptionConfirmAge']
                = !empty($formData['cookieBoxTextDescriptionConfirmAge']) ? stripslashes(
                    $formData['cookieBoxTextDescriptionConfirmAge']
                ) : $defaultConfig['cookieBoxTextDescriptionConfirmAge'];
        }
        // Technology
        $updatedConfig['cookieBoxShowTextDescriptionTechnology']
            = !empty($formData['cookieBoxShowTextDescriptionTechnology']) ? true : false;

        if (!empty($updatedConfig['cookieBoxShowTextDescriptionTechnology'])) {
            $updatedConfig['cookieBoxTextDescriptionTechnology']
                = !empty($formData['cookieBoxTextDescriptionTechnology']) ? stripslashes(
                    $formData['cookieBoxTextDescriptionTechnology']
                ) : $defaultConfig['cookieBoxTextDescriptionTechnology'];
        }
        // Personal Data
        $updatedConfig['cookieBoxShowTextDescriptionPersonalData']
            = !empty($formData['cookieBoxShowTextDescriptionPersonalData']) ? true : false;

        if (!empty($updatedConfig['cookieBoxShowTextDescriptionPersonalData'])) {
            $updatedConfig['cookieBoxTextDescriptionPersonalData']
                = !empty($formData['cookieBoxTextDescriptionPersonalData']) ? stripslashes(
                    $formData['cookieBoxTextDescriptionPersonalData']
                ) : $defaultConfig['cookieBoxTextDescriptionPersonalData'];
        }
        // More Information
        $updatedConfig['cookieBoxShowDescriptionMoreInformation']
            = !empty($formData['cookieBoxShowDescriptionMoreInformation']) ? true : false;

        if (!empty($updatedConfig['cookieBoxShowDescriptionMoreInformation'])) {
            $updatedConfig['cookieBoxTextDescriptionMoreInformation']
                = !empty($formData['cookieBoxTextDescriptionMoreInformation']) ? stripslashes(
                    $formData['cookieBoxTextDescriptionMoreInformation']
                ) : $defaultConfig['cookieBoxTextDescriptionMoreInformation'];
        }
        // No Commitment
        $updatedConfig['cookieBoxShowTextDescriptionNoObligation']
            = !empty($formData['cookieBoxShowTextDescriptionNoObligation']) ? true : false;

        if (!empty($updatedConfig['cookieBoxShowTextDescriptionNoObligation'])) {
            $updatedConfig['cookieBoxTextDescriptionNoObligation']
                = !empty($formData['cookieBoxTextDescriptionNoObligation']) ? stripslashes(
                    $formData['cookieBoxTextDescriptionNoObligation']
                ) : $defaultConfig['cookieBoxTextDescriptionNoObligation'];
        }
        // Revoke
        $updatedConfig['cookieBoxShowTextDescriptionRevoke']
            = !empty($formData['cookieBoxShowTextDescriptionRevoke']) ? true : false;

        if (!empty($updatedConfig['cookieBoxShowTextDescriptionRevoke'])) {
            $updatedConfig['cookieBoxTextDescriptionRevoke']
                = !empty($formData['cookieBoxTextDescriptionRevoke']) ? stripslashes(
                    $formData['cookieBoxTextDescriptionRevoke']
                ) : $defaultConfig['cookieBoxTextDescriptionRevoke'];
        }
        // Inidividual Settings
        $updatedConfig['cookieBoxShowTextDescriptionIndividualSettings']
            = !empty($formData['cookieBoxShowTextDescriptionIndividualSettings']) ? true : false;

        if (!empty($updatedConfig['cookieBoxShowTextDescriptionIndividualSettings'])) {
            $updatedConfig['cookieBoxTextDescriptionIndividualSettings']
                = !empty($formData['cookieBoxTextDescriptionIndividualSettings']) ? stripslashes(
                    $formData['cookieBoxTextDescriptionIndividualSettings']
                ) : $defaultConfig['cookieBoxTextDescriptionIndividualSettings'];
        }
        // Non-EU Data Transfer
        $updatedConfig['cookieBoxShowTextDescriptionNonEUDataTransfer']
            = !empty($formData['cookieBoxShowTextDescriptionNonEUDataTransfer']) ? true : false;

        if (!empty($updatedConfig['cookieBoxShowTextDescriptionNonEUDataTransfer'])) {
            $updatedConfig['cookieBoxTextDescriptionNonEUDataTransfer']
                = !empty($formData['cookieBoxTextDescriptionNonEUDataTransfer']) ? stripslashes(
                    $formData['cookieBoxTextDescriptionNonEUDataTransfer']
                ) : $defaultConfig['cookieBoxTextDescriptionNonEUDataTransfer'];
        }

        // Texts
        $updatedConfig['cookieBoxTextHeadline'] = !empty($formData['cookieBoxTextHeadline']) ? stripslashes(
            $formData['cookieBoxTextHeadline']
        ) : $defaultConfig['cookieBoxTextHeadline'];
        $updatedConfig['cookieBoxTextDescription'] = !empty($formData['cookieBoxTextDescription']) ? stripslashes(
            $formData['cookieBoxTextDescription']
        ) : $defaultConfig['cookieBoxTextDescription'];
        $updatedConfig['cookieBoxTextAcceptButton'] = !empty($formData['cookieBoxTextAcceptButton']) ? stripslashes(
            $formData['cookieBoxTextAcceptButton']
        ) : $defaultConfig['cookieBoxTextAcceptButton'];
        $updatedConfig['cookieBoxTextManageLink'] = !empty($formData['cookieBoxTextManageLink']) ? stripslashes(
            $formData['cookieBoxTextManageLink']
        ) : $defaultConfig['cookieBoxTextManageLink'];
        $updatedConfig['cookieBoxTextRefuseLink'] = !empty($formData['cookieBoxTextRefuseLink']) ? stripslashes(
            $formData['cookieBoxTextRefuseLink']
        ) : $defaultConfig['cookieBoxTextRefuseLink'];
        $updatedConfig['cookieBoxTextCookieDetailsLink'] = !empty($formData['cookieBoxTextCookieDetailsLink'])
            ? stripslashes($formData['cookieBoxTextCookieDetailsLink'])
            : $defaultConfig['cookieBoxTextCookieDetailsLink'];
        $updatedConfig['cookieBoxTextPrivacyLink'] = !empty($formData['cookieBoxTextPrivacyLink']) ? stripslashes(
            $formData['cookieBoxTextPrivacyLink']
        ) : $defaultConfig['cookieBoxTextPrivacyLink'];
        $updatedConfig['cookieBoxTextImprintLink'] = !empty($formData['cookieBoxTextImprintLink']) ? stripslashes(
            $formData['cookieBoxTextImprintLink']
        ) : $defaultConfig['cookieBoxTextImprintLink'];

        $updatedConfig['cookieBoxPreferenceTextHeadline'] = !empty($formData['cookieBoxPreferenceTextHeadline'])
            ? stripslashes($formData['cookieBoxPreferenceTextHeadline'])
            : $defaultConfig['cookieBoxPreferenceTextHeadline'];
        $updatedConfig['cookieBoxPreferenceTextDescription'] = !empty($formData['cookieBoxPreferenceTextDescription'])
            ? stripslashes($formData['cookieBoxPreferenceTextDescription'])
            : $defaultConfig['cookieBoxPreferenceTextDescription'];
        $updatedConfig['cookieBoxPreferenceTextSaveButton'] = !empty($formData['cookieBoxPreferenceTextSaveButton'])
            ? stripslashes($formData['cookieBoxPreferenceTextSaveButton'])
            : $defaultConfig['cookieBoxPreferenceTextSaveButton'];
        $updatedConfig['cookieBoxPreferenceTextAcceptAllButton']
            = !empty($formData['cookieBoxPreferenceTextAcceptAllButton']) ? stripslashes(
                $formData['cookieBoxPreferenceTextAcceptAllButton']
            ) : $defaultConfig['cookieBoxPreferenceTextAcceptAllButton'];
        $updatedConfig['cookieBoxPreferenceTextRefuseLink'] = !empty($formData['cookieBoxPreferenceTextRefuseLink'])
            ? stripslashes($formData['cookieBoxPreferenceTextRefuseLink'])
            : $defaultConfig['cookieBoxPreferenceTextRefuseLink'];
        $updatedConfig['cookieBoxPreferenceTextBackLink'] = !empty($formData['cookieBoxPreferenceTextBackLink'])
            ? stripslashes($formData['cookieBoxPreferenceTextBackLink'])
            : $defaultConfig['cookieBoxPreferenceTextBackLink'];
        $updatedConfig['cookieBoxPreferenceTextSwitchStatusActive']
            = !empty($formData['cookieBoxPreferenceTextSwitchStatusActive']) ? stripslashes(
                $formData['cookieBoxPreferenceTextSwitchStatusActive']
            ) : $defaultConfig['cookieBoxPreferenceTextSwitchStatusActive'];
        $updatedConfig['cookieBoxPreferenceTextSwitchStatusInactive']
            = !empty($formData['cookieBoxPreferenceTextSwitchStatusInactive']) ? stripslashes(
                $formData['cookieBoxPreferenceTextSwitchStatusInactive']
            ) : $defaultConfig['cookieBoxPreferenceTextSwitchStatusInactive'];
        $updatedConfig['cookieBoxPreferenceTextShowCookieLink']
            = !empty($formData['cookieBoxPreferenceTextShowCookieLink']) ? stripslashes(
                $formData['cookieBoxPreferenceTextShowCookieLink']
            ) : $defaultConfig['cookieBoxPreferenceTextShowCookieLink'];
        $updatedConfig['cookieBoxPreferenceTextHideCookieLink']
            = !empty($formData['cookieBoxPreferenceTextHideCookieLink']) ? stripslashes(
                $formData['cookieBoxPreferenceTextHideCookieLink']
            ) : $defaultConfig['cookieBoxPreferenceTextHideCookieLink'];

        $updatedConfig['cookieBoxCookieDetailsTableAccept'] = !empty($formData['cookieBoxCookieDetailsTableAccept'])
            ? stripslashes($formData['cookieBoxCookieDetailsTableAccept'])
            : $defaultConfig['cookieBoxCookieDetailsTableAccept'];
        $updatedConfig['cookieBoxCookieDetailsTableName'] = !empty($formData['cookieBoxCookieDetailsTableName'])
            ? stripslashes($formData['cookieBoxCookieDetailsTableName'])
            : $defaultConfig['cookieBoxCookieDetailsTableName'];
        $updatedConfig['cookieBoxCookieDetailsTableProvider']
            = !empty($formData['cookieBoxCookieDetailsTableProvider']) ? stripslashes(
                $formData['cookieBoxCookieDetailsTableProvider']
            ) : $defaultConfig['cookieBoxCookieDetailsTableProvider'];
        $updatedConfig['cookieBoxCookieDetailsTablePurpose'] = !empty($formData['cookieBoxCookieDetailsTablePurpose'])
            ? stripslashes($formData['cookieBoxCookieDetailsTablePurpose'])
            : $defaultConfig['cookieBoxCookieDetailsTablePurpose'];
        $updatedConfig['cookieBoxCookieDetailsTablePrivacyPolicy']
            = !empty($formData['cookieBoxCookieDetailsTablePrivacyPolicy']) ? stripslashes(
                $formData['cookieBoxCookieDetailsTablePrivacyPolicy']
            ) : $defaultConfig['cookieBoxCookieDetailsTablePrivacyPolicy'];
        $updatedConfig['cookieBoxCookieDetailsTableHosts'] = !empty($formData['cookieBoxCookieDetailsTableHosts'])
            ? stripslashes($formData['cookieBoxCookieDetailsTableHosts'])
            : $defaultConfig['cookieBoxCookieDetailsTableHosts'];
        $updatedConfig['cookieBoxCookieDetailsTableCookieName']
            = !empty($formData['cookieBoxCookieDetailsTableCookieName']) ? stripslashes(
                $formData['cookieBoxCookieDetailsTableCookieName']
            ) : $defaultConfig['cookieBoxCookieDetailsTableCookieName'];
        $updatedConfig['cookieBoxCookieDetailsTableCookieExpiry']
            = !empty($formData['cookieBoxCookieDetailsTableCookieExpiry']) ? stripslashes(
                $formData['cookieBoxCookieDetailsTableCookieExpiry']
            ) : $defaultConfig['cookieBoxCookieDetailsTableCookieExpiry'];

        $updatedConfig['cookieBoxConsentHistoryTableDate'] = !empty($formData['cookieBoxConsentHistoryTableDate'])
            ? stripslashes($formData['cookieBoxConsentHistoryTableDate'])
            : $defaultConfig['cookieBoxConsentHistoryTableDate'];
        $updatedConfig['cookieBoxConsentHistoryTableVersion']
            = !empty($formData['cookieBoxConsentHistoryTableVersion']) ? stripslashes(
                $formData['cookieBoxConsentHistoryTableVersion']
            ) : $defaultConfig['cookieBoxConsentHistoryTableVersion'];
        $updatedConfig['cookieBoxConsentHistoryTableConsents']
            = !empty($formData['cookieBoxConsentHistoryTableConsents']) ? stripslashes(
                $formData['cookieBoxConsentHistoryTableConsents']
            ) : $defaultConfig['cookieBoxConsentHistoryTableConsents'];

        $updatedConfig['cookieBoxCustomCSS'] = !empty($formData['cookieBoxCustomCSS']) ? stripslashes(
            $formData['cookieBoxCustomCSS']
        ) : '';

        // Save config
        Config::getInstance()->saveConfig($updatedConfig);

        // Update CSS File
        CSS::getInstance()->save();
    }
}
