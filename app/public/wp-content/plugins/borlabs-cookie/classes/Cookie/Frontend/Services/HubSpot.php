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

class HubSpot
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
        add_action('borlabsCookie/cookie/edit/template/settings/HubSpot', [$this, 'additionalSettingsTemplate']);
        add_action(
            'borlabsCookie/cookie/edit/template/settings/help/HubSpot',
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
            'Where can I find the Hub id?',
            'Backend / Cookie / HubSpot / Tips / Headline',
            'borlabs-cookie'
        ); ?></h4>
                <p><?php
                    _ex(
            'In HubSpot click on <strong>Settings &gt; Tracking code &gt; WordPress installation &gt; <em>Your Hub id</em></strong>.',
            'Backend / Cookie / HubSpot / Tips / Text',
            'borlabs-cookie'
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
        $inputHubId = esc_html(!empty($data->settings['hubId']) ? $data->settings['hubId'] : ''); ?>
        <div class="form-group row">
            <label for="hubId"
                   class="col-sm-4 col-form-label"><?php
                _ex('Hub id', 'Backend / Cookie / HubSpot / Label', 'borlabs-cookie'); ?></label>
            <div class="col-sm-8">
                <input type="text" class="form-control form-control-sm d-inline-block w-75 mr-2" id="hubId"
                       name="settings[hubId]" value="<?php
                echo $inputHubId; ?>"
                       placeholder="<?php
                       _ex('Example', 'Backend / Global / Input Placeholder', 'borlabs-cookie'); ?>: 1234567"
                       required>
                <span data-toggle="tooltip"
                      title="<?php
                      echo esc_attr_x(
            'Enter your Hub id.',
            'Backend / Cookie / HubSpot / Tooltip',
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
            'cookieId' => 'hubspot',
            'service' => 'HubSpot',
            'name' => 'HubSpot',
            'provider' => 'HubSpot Inc., 25 First Street, 2nd Floor, Cambridge, MA 02141, USA',
            'purpose' => _x(
                'HubSpot is a user database management service provided by HubSpot, Inc. We use HubSpot on this website for our online marketing activities.',
                'Frontend / Cookie / HubSpot / Text',
                'borlabs-cookie'
            ),
            'privacyPolicyURL' => _x(
                'https://legal.hubspot.com/privacy-policy',
                'Frontend / Cookie / HubSpot / Text',
                'borlabs-cookie'
            ),
            'hosts' => [
                'hubspot-avatars.s3.amazonaws.com, *.hubspot.com, hubspot-realtime.ably.io, hubspot-rest.ably.io, js.hs-scripts.com',
            ],
            'cookieName' => '__hs_opt_out, __hs_d_not_track, hs_ab_test, hs-messages-is-open, hs-messages-hide-welcome-message, __hstc, hubspotutk, __hssc, __hssrc, messagesUtk',
            'cookieExpiry' => _x(
                'Session / 30 Minutes / 1 Day / 1 Year / 13 Months',
                'Frontend / Cookie / HubSpot / Text',
                'borlabs-cookie'
            ),
            'optInJS' => $this->optInJS(),
            'optOutJS' => '',
            'fallbackJS' => '',
            'settings' => [
                'blockCookiesBeforeConsent' => false,
                'prioritize' => false,
                'hubId' => '',
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
        if (!empty($formData['service']) && $formData['service'] === 'HubSpot') {
            if (!empty($formData['settings']['hubId'])) {
                $formData['settings']['hubId'] = trim($formData['settings']['hubId']);
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
<!-- Start of HubSpot Embed Code -->
<script type="text/javascript" id="hs-script-loader" src="//js.hs-scripts.com/%%hubId%%.js"></script>
<!-- End of HubSpot Embed Code -->
EOT;
    }
}
