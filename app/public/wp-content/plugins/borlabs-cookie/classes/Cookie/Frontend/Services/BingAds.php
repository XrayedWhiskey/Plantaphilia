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

class BingAds
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
        add_action('borlabsCookie/cookie/edit/template/settings/BingAds', [$this, 'additionalSettingsTemplate']);
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
        $inputConversionId = esc_html(!empty($data->settings['conversionId']) ? $data->settings['conversionId'] : '');
        $inputConsentMode = !empty($data->settings['consentMode']) ? 1 : 0;
        $switchConsentMode = $inputConsentMode ? ' active' : ''; ?>
        <div class="form-group row">
            <label for="conversionId" class="col-sm-4 col-form-label"><?php _ex('UET Tag ID', 'Backend / Cookie / Bing Ads / Label', 'borlabs-cookie'); ?></label>
            <div class="col-sm-8">
                <input
                    type="text"
                    class="form-control form-control-sm d-inline-block w-75 mr-2"
                    id="conversionId"
                    name="settings[conversionId]"
                    value="<?php echo $inputConversionId; ?>"
                    placeholder="<?php _ex('Example', 'Backend / Global / Input Placeholder', 'borlabs-cookie'); ?>: 123456789" required
                >
                <span
                    data-toggle="tooltip"
                    title="<?php echo esc_attr_x('Enter your UET Tag ID.', 'Backend / Cookie / Bing Ads / Tooltip', 'borlabs-cookie'); ?>"><i class="fas fa-lg fa-question-circle text-dark"></i></span>
                <div class="invalid-feedback"><?php _ex('This is a required field and cannot be empty.', 'Backend / Global / Validation Message', 'borlabs-cookie'); ?></div>
            </div>
        </div>
        <div class="form-group row align-items-center">
            <label for="consentMode" class="col-sm-4 col-form-label"><?php _ex('Use Consent Mode', 'Backend / Cookie / Bing Ads / Label', 'borlabs-cookie'); ?></label>
            <div class="col-sm-8">
                <button type="button" class="btn btn-sm btn-toggle mr-2<?php echo $switchConsentMode; ?>" data-toggle="button" data-switch-target="consentMode" aria-pressed="<?php echo $inputConsentMode ? 'true' : 'false'; ?>"><span class="handle"></span>
                </button>
                <input type="hidden" name="settings[consentMode]" id="consentMode" value="<?php echo $inputConsentMode; ?>">
                <span data-toggle="tooltip" title="<?php echo esc_attr_x('The Microsoft Advertising (Bing.com) UET code is always loaded via the <strong>Fallback Code</strong> field with UET Consent Mode defaults set to denied. If the user accepts the Bing Ads Cookie, Microsoft Advertising will be informed about your consent to ads. Be aware that the consent mode only allows consents for categories not services.', 'Backend / Cookie / Bing Ads / Tooltip', 'borlabs-cookie'); ?>"><i class="fas fa-lg fa-question-circle text-dark"></i></span>
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
            'cookieId' => 'bing-ads',
            'service' => 'BingAds',
            'name' => 'Bing Ads',
            'provider' => 'Microsoft Ireland Operations Limited, One Microsoft Place, South County Business Park, Leopardstown, Dublin, Ireland 18, D18 P521',
            'purpose' => _x(
                'Cookie by Microsoft Advertising (Bing.com) used for conversion tracking of Bing Ads.',
                'Frontend / Cookie / Bing Ads / Text',
                'borlabs-cookie'
            ),
            'privacyPolicyURL' => _x(
                'https://about.ads.microsoft.com/en-us/policies/legal-privacy-and-security',
                'Frontend / Cookie / Bing Ads / Text',
                'borlabs-cookie'
            ),
            'hosts' => [
                '.bing.com',
            ],
            'cookieName' => 'MUID,_uetmsclkid,_uetsid,_uetvid',
            'cookieExpiry' => '1 Year',
            'optInJS' => $this->optInJS(),
            'optOutJS' => '',
            'fallbackJS' => $this->fallbackJS(),
            'settings' => [
                'blockCookiesBeforeConsent' => false,
                'prioritize' => true,
                'conversionId' => '',
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
        if (!empty($formData['service']) && $formData['service'] === 'BingAds') {
            if (!empty($formData['settings']['conversionId'])) {
                $formData['settings']['conversionId'] = trim($formData['settings']['conversionId']);
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
if('%%consentMode%%' === '1') {
    window.uetq = window.uetq || [];
    window.uetq.push('consent', 'default', {
        'ad_storage': 'denied'
    });

    (function(w,d,t,r,u)
    {
        var f,n,i;
        w[u]=w[u]||[],f=function()
        {
            var o={ti:"%%conversionId%%"};
            o.q=w[u],w[u]=new UET(o),w[u].push("pageLoad")
        },
        n=d.createElement(t),n.src=r,n.async=1,n.onload=n.onreadystatechange=function()
        {
            var s=this.readyState;
            s&&s!=="loaded"&&s!=="complete"||(f(),n.onload=n.onreadystatechange=null)
        },
        i=d.getElementsByTagName(t)[0],i.parentNode.insertBefore(n,i)
    })
    (window,document,"script","//bat.bing.com/bat.js","uetq");
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
if('%%consentMode%%' === '1') {
    window.uetq = window.uetq || [];
    window.uetq.push('consent', 'update', {
        'ad_storage': 'granted'
    });
} else {
    (function(w,d,t,r,u)
    {
        var f,n,i;
        w[u]=w[u]||[],f=function()
        {
            var o={ti:"%%conversionId%%"};
            o.q=w[u],w[u]=new UET(o),w[u].push("pageLoad")
        },
        n=d.createElement(t),n.src=r,n.async=1,n.onload=n.onreadystatechange=function()
        {
            var s=this.readyState;
            s&&s!=="loaded"&&s!=="complete"||(f(),n.onload=n.onreadystatechange=null)
        },
        i=d.getElementsByTagName(t)[0],i.parentNode.insertBefore(n,i)
    })
    (window,document,"script","//bat.bing.com/bat.js","uetq");
}
</script>
EOT;
    }
}
