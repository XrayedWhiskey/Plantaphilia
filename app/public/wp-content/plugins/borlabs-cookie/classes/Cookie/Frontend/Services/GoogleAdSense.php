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

class GoogleAdSense
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
        add_action('borlabsCookie/cookie/edit/template/settings/GoogleAdSense', [$this, 'additionalSettingsTemplate']);
        add_action(
            'borlabsCookie/cookie/edit/template/settings/help/GoogleAdSense',
            [$this, 'additionalSettingsHelpTemplate']
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
     * additionalSettingsHelpTemplate function.
     *
     * @param mixed $data
     */
    public function additionalSettingsHelpTemplate($data)
    {
        ?>
        <div class="col-12 col-md-4 rounded-right shadow-sm bg-tips text-light">
            <div class="px-3 pt-3 pb-3 mb-4">
                <h3 class="border-bottom mb-3"><?php
                    _ex('Tips', 'Backend / Global / Tips / Headline', 'borlabs-cookie'); ?></h3>
                <h4><?php
                    _ex(
            'How do I place an AdSense banner?',
            'Backend / Cookie / Google AdSense / Tips / Headline',
            'borlabs-cookie'
        ); ?></h4>
                <p><?php
                    printf(
            _x(
                'Copy this code to the place where you want the banner to appear: %s',
                'Backend / Cookie / Google AdSense / Tips / Text',
                'borlabs-cookie'
            ),
            sprintf(
                '<span class="code-example">&lt;ins class="adsbygoogle" style="display:inline-block;min-width:320px;max-width:1200px;width:100%%;height:100px" data-ad-client="%s"&gt;&lt;/ins&gt;&lt;script&gt;(adsbygoogle = window.adsbygoogle || []).push({});&lt;/script&gt;</span>',
                !empty($data->settings['caPubId']) ? esc_html($data->settings['caPubId'])
                                : 'ca-pub-XXXXXXXXX'
            )
        ); ?></p>
            </div>
        </div>
        <?php
    }

    /**
     * additionalSettingsTemplate function.
     *
     * @param mixed $data
     */
    public function additionalSettingsTemplate($data)
    {
        $inputCaPubId = esc_html(!empty($data->settings['caPubId']) ? $data->settings['caPubId'] : ''); ?>
        <div class="form-group row">
            <label for="caPubId"
                   class="col-sm-4 col-form-label"><?php
                _ex('Publisher ID', 'Backend / Cookie / Google AdSense / Label', 'borlabs-cookie'); ?></label>
            <div class="col-sm-8">
                <input type="text" class="form-control form-control-sm d-inline-block w-75 mr-2" id="caPubId"
                       name="settings[caPubId]" value="<?php
                echo $inputCaPubId; ?>"
                       placeholder="<?php
                       _ex('Example', 'Backend / Global / Input Placeholder', 'borlabs-cookie'); ?>: ca-pub-123456789"
                       required>
                <span data-toggle="tooltip"
                      title="<?php
                      echo esc_attr_x(
            'Enter your Publisher ID.',
            'Backend / Cookie / Google AdSense / Tooltip',
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
            'cookieId' => 'google-adsense',
            'service' => 'GoogleAdSense',
            'name' => 'Google AdSense',
            'provider' => 'Google Ireland Limited, Gordon House, Barrow Street, Dublin 4, Ireland',
            'purpose' => _x(
                'Cookie by Google used for ad targeting and ad measurement.',
                'Frontend / Cookie / Google AdSense / Text',
                'borlabs-cookie'
            ),
            'privacyPolicyURL' => _x(
                'https://policies.google.com/privacy?hl=en',
                'Frontend / Cookie / Google AdSense / Text',
                'borlabs-cookie'
            ),
            'hosts' => [
                'doubleclick.net',
            ],
            'cookieName' => 'DSID, IDE',
            'cookieExpiry' => _x('1 Year', 'Frontend / Cookie / Google AdSense / Text', 'borlabs-cookie'),
            'optInJS' => $this->optInJS(),
            'optOutJS' => '',
            'fallbackJS' => '',
            'settings' => [
                'blockCookiesBeforeConsent' => false,
                'prioritize' => false,
                'caPubId' => '',
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
        if (!empty($formData['service']) && $formData['service'] === 'GoogleAdSense') {
            if (!empty($formData['settings']['caPubId'])) {
                $formData['settings']['caPubId'] = trim($formData['settings']['caPubId']);
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
<script data-ad-client="%%caPubId%%" async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
EOT;
    }
}
