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

class FacebookPixel
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
        add_action('borlabsCookie/cookie/edit/template/settings/FacebookPixel', [$this, 'additionalSettingsTemplate']);
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
        $inputPixelId = esc_html(!empty($data->settings['pixelId']) ? $data->settings['pixelId'] : ''); ?>
        <div class="form-group row">
            <label for="pixelId"
                   class="col-sm-4 col-form-label"><?php
                _ex('Pixel ID', 'Backend / Cookie / Facebook Pixel / Label', 'borlabs-cookie'); ?></label>
            <div class="col-sm-8">
                <input type="text" class="form-control form-control-sm d-inline-block w-75 mr-2" id="pixelId"
                       name="settings[pixelId]" value="<?php
                echo $inputPixelId; ?>"
                       placeholder="<?php
                       _ex('Example', 'Backend / Global / Input Placeholder', 'borlabs-cookie'); ?>: 123456789"
                       required>
                <span data-toggle="tooltip"
                      title="<?php
                      echo esc_attr_x(
            'Enter your Facebook Pixel ID.',
            'Backend / Cookie / Facebook Pixel / Tooltip',
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
        <?php
    }

    /**
     * getDefault function.
     */
    public function getDefault()
    {
        return [
            'cookieId' => 'facebook-pixel',
            'service' => 'FacebookPixel',
            'name' => 'Facebook Pixel',
            'provider' => 'Meta Platforms Ireland Limited, 4 Grand Canal Square, Dublin 2, Ireland',
            'purpose' => _x(
                'Cookie by Facebook used for website analytics, ad targeting, and ad measurement.',
                'Frontend / Cookie / Facebook Pixel / Text',
                'borlabs-cookie'
            ),
            'privacyPolicyURL' => _x(
                'https://www.facebook.com/policies/cookies',
                'Frontend / Cookie / Facebook Pixel / Text',
                'borlabs-cookie'
            ),
            'hosts' => [],
            'cookieName' => '_fbp,act,c_user,datr,fr,m_pixel_ration,pl,presence,sb,spin,wd,xs',
            'cookieExpiry' => _x('Session / 1 Year', 'Frontend / Cookie / Facebook Pixel / Text', 'borlabs-cookie'),
            'optInJS' => $this->optInJS(),
            'optOutJS' => '',
            'fallbackJS' => '',
            'settings' => [
                'blockCookiesBeforeConsent' => false,
                'prioritize' => true,
                'pixelId' => '',
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
        if (!empty($formData['service']) && $formData['service'] === 'FacebookPixel') {
            if (!empty($formData['settings']['pixelId'])) {
                $formData['settings']['pixelId'] = trim($formData['settings']['pixelId']);
            }
        }

        return $formData;
    }

    /**
     * optInJS function.
     */
    private function optInJS()
    {
        return <<<EOT
<!-- Facebook Pixel Code -->
<script>
  !function(f,b,e,v,n,t,s)
  {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
  n.callMethod.apply(n,arguments):n.queue.push(arguments)};
  if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
  n.queue=[];t=b.createElement(e);t.async=!0;
  t.src=v;s=b.getElementsByTagName(e)[0];
  s.parentNode.insertBefore(t,s)}(window, document,'script',
  'https://connect.facebook.net/en_US/fbevents.js');
  fbq('init', '%%pixelId%%');
  fbq('track', 'PageView');
</script>
<!-- End Facebook Pixel Code -->
EOT;
    }
}
