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

class GoogleAnalytics
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
            'borlabsCookie/cookie/edit/template/settings/GoogleAnalytics',
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
        $inputTrackingId = esc_html(!empty($data->settings['trackingId']) ? $data->settings['trackingId'] : '');
        $inputConsentMode = !empty($data->settings['consentMode']) ? 1 : 0;
        $switchConsentMode = $inputConsentMode ? ' active' : ''; ?>
        <div class="form-group row">
            <label for="trackingId"
                   class="col-sm-4 col-form-label"><?php
                _ex('Tracking ID', 'Backend / Cookie / Google Analytics / Label', 'borlabs-cookie'); ?></label>
            <div class="col-sm-8">
                <input type="text" class="form-control form-control-sm d-inline-block w-75 mr-2" id="trackingId"
                       name="settings[trackingId]" value="<?php
                echo $inputTrackingId; ?>"
                       placeholder="<?php
                       _ex('Example', 'Backend / Global / Input Placeholder', 'borlabs-cookie'); ?>: G-123456789"
                       required>
                <span data-toggle="tooltip"
                      title="<?php
                      echo esc_attr_x(
            'Enter your Google Analytics Tracking ID.',
            'Backend / Cookie / Google Analytics / Tooltip',
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
            <label for="consentMode"
                   class="col-sm-4 col-form-label"><?php
                _ex('Use Consent Mode', 'Backend / Cookie / Google Analytics / Label', 'borlabs-cookie'); ?></label>
            <div class="col-sm-8">
                <button type="button" class="btn btn-sm btn-toggle mr-2<?php
                echo $switchConsentMode; ?>"
                        data-toggle="button" data-switch-target="consentMode" aria-pressed="<?php
                echo $inputConsentMode ? 'true' : 'false'; ?>">
                    <span class="handle"></span>
                </button>
                <input type="hidden" name="settings[consentMode]" id="consentMode"
                       value="<?php
                       echo $inputConsentMode; ?>">
                <span data-toggle="tooltip"
                      title="<?php
                      echo esc_attr_x(
            'The Google Analytics code is always loaded via the <strong>Fallback Code</strong> field with Google Consent Mode defaults set to denied. If the user accepts the Google Analytics Cookie, Google will be informed about your consent to analytics. Be aware that the consent mode only allows consents for categories not services.',
            'Backend / Cookie / Google Analytics / Tooltip',
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
            'cookieId' => 'google-analytics',
            'service' => 'GoogleAnalytics',
            'name' => 'Google Analytics',
            'provider' => 'Google Ireland Limited, Gordon House, Barrow Street, Dublin 4, Ireland',
            'purpose' => _x(
                'Cookie by Google used for website analytics. Generates statistical data on how the visitor uses the website.',
                'Frontend / Cookie / Google Analytics / Text',
                'borlabs-cookie'
            ),
            'privacyPolicyURL' => _x(
                'https://policies.google.com/privacy?hl=en',
                'Frontend / Cookie / Google Analytics / Text',
                'borlabs-cookie'
            ),
            'hosts' => [],
            'cookieName' => '_ga,_gat,_gid',
            'cookieExpiry' => _x('2 Months', 'Frontend / Cookie / Google Analytics / Text', 'borlabs-cookie'),
            'optInJS' => $this->optInJS(),
            'optOutJS' => '',
            'fallbackJS' => $this->fallbackJS(),
            'settings' => [
                'blockCookiesBeforeConsent' => false,
                'prioritize' => true,
                'trackingId' => '',
                'consentMode' => false,
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
        if (!empty($formData['service']) && $formData['service'] === 'GoogleAnalytics') {
            if (!empty($formData['settings']['trackingId'])) {
                $formData['settings']['trackingId'] = trim($formData['settings']['trackingId']);
            }
        }

        return $formData;
    }

    /**
     * optInJS function.
     *
     * @return string
     */
    private function fallbackJS()
    {
        return <<<EOT
<script>
window.dataLayer = window.dataLayer || [];
if (typeof gtag !== 'function') { function gtag(){dataLayer.push(arguments);} }
if('%%consentMode%%' === '1') {
    gtag('consent', 'default', {
       'ad_storage': 'denied',
       'analytics_storage': 'denied'
    });
    gtag("js", new Date());
    gtag("config", "%%trackingId%%", { "anonymize_ip": true });

    (function (w, d, s, i) {
    var f = d.getElementsByTagName(s)[0],
        j = d.createElement(s);
    j.async = true;
    j.src =
        "https://www.googletagmanager.com/gtag/js?id=" + i;
    f.parentNode.insertBefore(j, f);
    })(window, document, "script", "%%trackingId%%");
}
</script>
EOT;
    }

    /**
     * optInJS function.
     *
     * @return string
     */
    private function optInJS()
    {
        return <<<EOT
<script>
window.dataLayer = window.dataLayer || [];
if (typeof gtag !== 'function') { function gtag(){dataLayer.push(arguments);} }
if('%%consentMode%%' === '1') {
	gtag('consent', 'update', {'analytics_storage': 'granted'});
} else {
    (function (w, d, s, i) {
    var f = d.getElementsByTagName(s)[0],
        j = d.createElement(s);
    j.async = true;
    j.src =
        "https://www.googletagmanager.com/gtag/js?id=" + i;
    f.parentNode.insertBefore(j, f);
    })(window, document, "script", "%%trackingId%%");

    gtag("js", new Date());
    gtag("config", "%%trackingId%%", { "anonymize_ip": true });
}
</script>
EOT;
    }
}
