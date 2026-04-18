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

class GoogleTagManagerConsent
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
            'borlabsCookie/cookie/edit/template/settings/GoogleTagManagerConsent',
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
        $switchLoadBeforeConsent = $inputLoadBeforeConsent ? ' active' : '';
        $optionConsentModeAdStorage = !empty($data->settings['consentMode'])
        && $data->settings['consentMode'] === 'ad_storage' ? ' selected ' : '';
        $optionConsentModeAnalyticsStorage = !empty($data->settings['consentMode'])
        && $data->settings['consentMode'] === 'analytics_storage' ? ' selected ' : '';
        $optionConsentModeFunctionalityStorage = !empty($data->settings['consentMode'])
        && $data->settings['consentMode'] === 'functionality_storage' ? ' selected ' : '';
        $optionConsentModePersonalizationStorage = !empty($data->settings['consentMode'])
        && $data->settings['consentMode'] === 'personalization_storage' ? ' selected ' : '';
        $optionConsentModeSecurityStorage = !empty($data->settings['consentMode'])
        && $data->settings['consentMode'] === 'security_storage' ? ' selected ' : ''; ?>
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
        } ?>
        <div class="form-group row">
            <label for="consentMode"
                   class="col-sm-4 col-form-label"><?php
                _ex('Consent Mode', 'Backend / Cookie / Google Tag Manager / Label', 'borlabs-cookie'); ?></label>
            <div class="col-sm-8">
                <select class="form-control form-control form-control-sm d-inline-block w-75 mr-2"
                        name="settings[consentMode]" id="settings[consentMode]">
                    <option<?php
                    echo $optionConsentModeAdStorage; ?>
                        value="ad_storage">ad_storage
                    </option>
                    <option<?php
                    echo $optionConsentModeAnalyticsStorage; ?>
                        value="analytics_storage">analytics_storage
                    </option>
                    <option<?php
                    echo $optionConsentModeFunctionalityStorage; ?>
                        value="functionality_storage">functionality_storage
                    </option>
                    <option<?php
                    echo $optionConsentModePersonalizationStorage; ?>
                        value="personalization_storage">personalization_storage
                    </option>
                    <option<?php
                    echo $optionConsentModeSecurityStorage; ?>
                        value="security_storage">security_storage
                    </option>
                </select>
                <span data-toggle="tooltip"
                      title="<?php
                      echo esc_attr_x(
                'Select the Consent Mode for which you want to obtain consent. For more information, see the Google Analytics Help.',
                'Backend / Cookie / Google Tag Manager / Tooltip',
                'borlabs-cookie'
            ); ?>"><i
                        class="fas fa-lg fa-question-circle text-dark"></i></span>
            </div>
        </div>
        <?php
    }

    /**
     * getDefault function.
     */
    public function getDefault()
    {
        return [
            'cookieId' => 'google-tag-manager-consent',
            'service' => 'GoogleTagManagerConsent',
            'name' => 'Google Tag Manager - Consent',
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
        if (!empty($formData['service']) && $formData['service'] === 'GoogleTagManagerWithConsentMode') {
            if (!empty($formData['settings']['gtmId'])) {
                $formData['settings']['gtmId'] = trim($formData['settings']['gtmId']);
            }
        }

        return $formData;
    }

    /**
     * fallbackJS function.
     */
    private function fallbackJS()
    {
        return <<<EOT
<!-- Google Tag Manager -->
<script>
window.dataLayer = window.dataLayer || [];
if (typeof gtag !== 'function') { function gtag(){dataLayer.push(arguments);} }
if ('%%loadBeforeConsent%%' === '1' && typeof window.google_tag_manager==='undefined' && !document.querySelector('#brlbs-gtm')) {
    gtag('consent', 'default', {
      'ad_storage': 'denied',
      'analytics_storage': 'denied',
      'functionality_storage': 'denied',
      'personalization_storage': 'denied',
      'security_storage': 'denied',
    });
    if ('%%consentMode%%' === 'ad_storage') {
        gtag('set', 'ads_data_redaction', true);
        gtag('set', 'url_passthrough', true);
    }
    (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({"gtm.start":
    new Date().getTime(),event:"gtm.js"});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!="dataLayer"?"&l="+l:"";j.async=true;j.src=
    "https://www.googletagmanager.com/gtm.js?id="+i+dl;j.id='brlbs-gtm';f.parentNode.insertBefore(j,f);
    })(window,document,"script","dataLayer","%%gtmId%%");
}
</script>
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
<script>
window.dataLayer = window.dataLayer || [];
if (typeof gtag !== 'function') { function gtag(){dataLayer.push(arguments);} }
if ('%%loadBeforeConsent%%' !== '1' && typeof window.google_tag_manager==='undefined' && !document.querySelector('#brlbs-gtm')) {
    gtag('consent', 'default', {
      'ad_storage': 'denied',
      'analytics_storage': 'denied',
      'functionality_storage': 'denied',
      'personalization_storage': 'denied',
      'security_storage': 'denied',
    });
    if ('%%consentMode%%' === 'ad_storage') {
        gtag('set', 'ads_data_redaction', true);
        gtag('set', 'url_passthrough', true);
    }
    (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({"gtm.start":
    new Date().getTime(),event:"gtm.js"});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!="dataLayer"?"&l="+l:"";j.async=true;j.src=
    "https://www.googletagmanager.com/gtm.js?id="+i+dl;j.id='brlbs-gtm';f.parentNode.insertBefore(j,f);
    })(window,document,"script","dataLayer","%%gtmId%%");
}
gtag('consent', 'update', {
    '%%consentMode%%': 'granted'
});
dataLayer.push({
	'event': 'borlabsCookieOptIn'
});
</script>
<!-- End Google Tag Manager -->
EOT;
    }
}
