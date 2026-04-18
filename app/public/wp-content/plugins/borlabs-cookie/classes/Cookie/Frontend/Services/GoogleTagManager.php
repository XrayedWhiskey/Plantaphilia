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

namespace BorlabsCookie\Cookie\Frontend\Services;

class GoogleTagManager
{
    private static $instance;

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * __construct function.
     */
    public function __construct()
    {
        add_action(
            'borlabsCookie/cookie/edit/template/settings/GoogleTagManager',
            [$this, 'additionalSettingsTemplate']
        );
        add_action('borlabsCookie/cookie/save', [$this, 'save']);
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
     * additionalSettingsTemplate function.
     *
     * @param mixed $data
     */
    public function additionalSettingsTemplate($data)
    {
        $inputGtmId = esc_html(!empty($data->settings['gtmId']) ? $data->settings['gtmId'] : '');
        $inputLoadBeforeConsent = !empty($data->settings['loadBeforeConsent']) ? 1 : 0;
        $switchLoadBeforeConsent = $inputLoadBeforeConsent ? ' active' : ''; ?>
        <div class="form-group row">
            <label for="gtmId"
                   class="col-sm-4 col-form-label"><?php
                _ex('GTM ID', 'Backend / Cookie / Google Tag Manager / Label', 'borlabs-cookie'); ?></label>
            <div class="col-sm-8">
                <input type="text" class="form-control form-control-sm d-inline-block w-75 mr-2" id="gtmId"
                       name="settings[gtmId]" value="<?php
                echo $inputGtmId; ?>"
                       placeholder="<?php
                       _ex('Example', 'Backend / Global / Input Placeholder', 'borlabs-cookie'); ?>: GTM-1234"
                       required>
                <span data-toggle="tooltip"
                      title="<?php
                      echo esc_attr_x(
            'Enter your Google Tag Manager ID.',
            'Backend / Cookie / Google Tag Manager / Tooltip',
            'borlabs-cookie'
        ); ?>"><i
                        class="fas fa-lg fa-question-circle text-dark"></i></span>
                <div
                    class="invalid-feedback"><?php
                    _ex(
            'This is a required field and cannot be empty.',
            'Backend / Global / Validation Message',
            'borlabs-cookie'
        ); ?></div>
            </div>
        </div>
        <div class="form-group row align-items-center">
            <label for="loadBeforeConsent"
                   class="col-sm-4 col-form-label"><?php
                _ex(
            'Load before Consent',
            'Backend / Cookie / Google Tag Manager / Label',
            'borlabs-cookie'
        ); ?></label>
            <div class="col-sm-8">
                <button type="button" class="btn btn-sm btn-toggle mr-2<?php
                echo $switchLoadBeforeConsent; ?>"
                        data-toggle="button" data-switch-target="loadBeforeConsent" aria-pressed="<?php
                echo $inputLoadBeforeConsent ? 'true' : 'false'; ?>">
                    <span class="handle"></span>
                </button>
                <input type="hidden" name="settings[loadBeforeConsent]" id="loadBeforeConsent"
                       value="<?php
                       echo $inputLoadBeforeConsent; ?>">
                <span data-toggle="tooltip"
                      title="<?php
                      echo esc_attr_x(
            'The Google Tag Manager code is always loaded via the <strong>Fallback Code</strong> field.',
            'Backend / Cookie / Google Tag Manager / Tooltip',
            'borlabs-cookie'
        ); ?>"><i
                        class="fas fa-lg fa-question-circle text-dark"></i></span>
            </div>
        </div>
        <?php
        if ($inputLoadBeforeConsent) {
            ?>
            <div class="form-group row align-items-center">
                <div class="col-sm-8 offset-4">
                    <div class="alert alert-danger mt-2">
                        <?php
                        _ex(
                '<strong>Load before Consent</strong> may violate applicable laws.',
                'Backend / Cookie / Alert Message',
                'borlabs-cookie'
            ); ?>
                        <?php
                        _ex(
                'The code is loaded via the <strong>Fallback Code</strong> field, from which a visitor cannot opt-out.',
                'Backend / Cookie / Alert Message',
                'borlabs-cookie'
            ); ?>
                        <?php
                        _ex(
                'Please inform yourself in advance about the legal situation that applies to you.',
                'Backend / Cookie / Alert Message',
                'borlabs-cookie'
            ); ?>
                    </div>
                </div>
            </div>
            <?php
        }
    }

    /**
     * getDefault function.
     */
    public function getDefault()
    {
        return [
            'cookieId' => 'google-tag-manager',
            'service' => 'GoogleTagManager',
            'name' => 'Google Tag Manager',
            'provider' => 'Google Ireland Limited, Gordon House, Barrow Street, Dublin 4, Ireland',
            'purpose' => _x(
                'Cookie by Google used to control advanced script and event handling.',
                'Frontend / Cookie / Google Tag Manager / Text',
                'borlabs-cookie'
            ),
            'privacyPolicyURL' => _x(
                'https://policies.google.com/privacy?hl=en',
                'Frontend / Cookie / Google Tag Manager / Text',
                'borlabs-cookie'
            ),
            'hosts' => [],
            'cookieName' => '_ga,_gat,_gid',
            'cookieExpiry' => _x('2 Years', 'Frontend / Cookie / Google Tag Manager / Text', 'borlabs-cookie'),
            'optInJS' => $this->optInJS(),
            'optOutJS' => '',
            'fallbackJS' => $this->fallbackJS(),
            'settings' => [
                'blockCookiesBeforeConsent' => false,
                'prioritize' => true,
                'gtmId' => '',
                'loadViaFallback' => false,
            ],
            'status' => true,
            'undeletetable' => false,
        ];
    }

    /**
     * save function.
     *
     * @param mixed $formData
     */
    public function save($formData)
    {
        if (!empty($formData['service']) && $formData['service'] === 'GoogleTagManager') {
            if (!empty($formData['settings']['gtmId'])) {
                $formData['settings']['gtmId'] = trim($formData['settings']['gtmId']);
            }
        }

        return $formData;
    }

    /**
     * optInJS function.
     */
    private function fallbackJS()
    {
        return <<<EOT
<!-- Google Tag Manager -->
<script>if('%%loadBeforeConsent%%'==='1'){(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({"gtm.start":
new Date().getTime(),event:"gtm.js"});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!="dataLayer"?"&l="+l:"";j.async=true;j.src=
"https://www.googletagmanager.com/gtm.js?id="+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,"script","dataLayer","%%gtmId%%");}</script>
<!-- End Google Tag Manager -->
EOT;
    }

    /**
     * optInJS function.
     */
    private function optInJS()
    {
        return <<<EOT
<!-- Google Tag Manager -->
<script>if('%%loadBeforeConsent%%'!=='1'){(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({"gtm.start":
new Date().getTime(),event:"gtm.js"});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!="dataLayer"?"&l="+l:"";j.async=true;j.src=
"https://www.googletagmanager.com/gtm.js?id="+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,"script","dataLayer","%%gtmId%%");}</script>
<!-- End Google Tag Manager -->
EOT;
    }
}
