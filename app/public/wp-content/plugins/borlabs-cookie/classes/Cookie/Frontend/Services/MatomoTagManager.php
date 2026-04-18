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

use BorlabsCookie\Cookie\Config;

class MatomoTagManager
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
            'borlabsCookie/cookie/edit/template/settings/MatomoTagManager',
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
        $inputMatomoUrl = esc_html(!empty($data->settings['matomoUrl']) ? $data->settings['matomoUrl'] : '');
        $inputContainerId = esc_html(!empty($data->settings['containerId']) ? $data->settings['containerId'] : ''); ?>
        <div class="form-group row">
            <label for="matomoUrl"
                   class="col-sm-4 col-form-label"><?php
                _ex('Matomo URL', 'Backend / Cookie / Matomo Tag Manager / Label', 'borlabs-cookie'); ?></label>
            <div class="col-sm-8">
                <input type="text" class="form-control form-control-sm d-inline-block w-75 mr-2" id="matomoUrl"
                       name="settings[matomoUrl]" value="<?php
                echo $inputMatomoUrl; ?>"
                       placeholder="<?php
                       _ex(
            'Example',
            'Backend / Global / Input Placeholder',
            'borlabs-cookie'
        ); ?>: https://analytics.example.com/matomo/"
                       required>
                <span data-toggle="tooltip"
                      title="<?php
                      echo esc_attr_x(
            'Enter the URL of your Matomo installation.',
            'Backend / Cookie / Matomo Tag Manager / Tooltip',
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

        <div class="form-group row">
            <label for="containerId"
                   class="col-sm-4 col-form-label"><?php
                _ex('Container ID', 'Backend / Cookie / Matomo Tag Manager / Label', 'borlabs-cookie'); ?></label>
            <div class="col-sm-8">
                <input type="text" class="form-control form-control-sm d-inline-block w-75 mr-2" id="containerId"
                       name="settings[containerId]" value="<?php
                echo $inputContainerId; ?>"
                       placeholder="<?php
                       _ex('Example', 'Backend / Global / Input Placeholder', 'borlabs-cookie'); ?>: O3NBs12ab"
                       required>
                <span data-toggle="tooltip"
                      title="<?php
                      echo esc_attr_x(
            'Enter the container ID.',
            'Backend / Cookie / Matomo Tag Manager / Tooltip',
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
        $privacyPolicyURL = '';

        if (!empty(Config::getInstance()->get('privacyPageURL'))) {
            $privacyPolicyURL = Config::getInstance()->get('privacyPageURL');
        }

        if (!empty(Config::getInstance()->get('privacyPageCustomURL'))) {
            $privacyPolicyURL = Config::getInstance()->get('privacyPageCustomURL');
        }

        return [
            'cookieId' => 'matomo-tag-manager',
            'service' => 'MatomoTagManager',
            'name' => 'Matomo Tag Manager',
            'provider' => get_bloginfo('name', 'raw'),
            'purpose' => _x(
                'Matomo Tag Manager is used to control advanced script and event handling.',
                'Frontend / Cookie / Matomo Tag Manager / Text',
                'borlabs-cookie'
            ),
            'privacyPolicyURL' => $privacyPolicyURL,
            'hosts' => [],
            'cookieName' => '',
            'cookieExpiry' => '',
            'optInJS' => $this->optInJS(),
            'optOutJS' => '',
            'fallbackJS' => '',
            'settings' => [
                'blockCookiesBeforeConsent' => false,
                'prioritize' => true,
                'matomoUrl' => '',
                'containerId' => '',
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
        if (!empty($formData['service']) && $formData['service'] === 'MatomoTagManager') {
            if (!empty($formData['settings']['matomoUrl'])) {
                $formData['settings']['matomoUrl'] = trim($formData['settings']['matomoUrl']);

                $urlInfo = parse_url($formData['settings']['matomoUrl']);

                $formData['settings']['matomoUrl'] = (!empty($urlInfo['scheme']) ? $urlInfo['scheme'] . '://' : '//')
                    . $urlInfo['host'];

                if (!empty($urlInfo['path'])) {
                    $formData['settings']['matomoUrl'] .= rtrim($urlInfo['path'], '/') . '/';
                } else {
                    $formData['settings']['matomoUrl'] .= '/';
                }
            }

            if (!empty($formData['settings']['containerId'])) {
                $formData['settings']['containerId'] = trim($formData['settings']['containerId']);
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
<!-- Matomo Tag Manager -->
<script type="text/javascript">
var _mtm = _mtm || [];
_mtm.push({'mtm.startTime': (new Date().getTime()), 'event': 'mtm.Start'});
var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
g.type='text/javascript'; g.async=true; g.defer=true; g.src='%%matomoUrl%%/js/container_%%containerId%%.js'; s.parentNode.insertBefore(g,s);
</script>
<!-- End Matomo Tag Manager -->
EOT;
    }
}
