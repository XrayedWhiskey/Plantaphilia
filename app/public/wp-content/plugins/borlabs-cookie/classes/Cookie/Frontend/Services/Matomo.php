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

class Matomo
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
        add_action('borlabsCookie/cookie/edit/template/settings/Matomo', [$this, 'additionalSettingsTemplate']);
        add_action(
            'borlabsCookie/cookie/edit/template/settings/help/Matomo',
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
                    _ex('Matomo Site ID', 'Backend / Cookie / Matomo / Tips / Headline', 'borlabs-cookie'); ?></h4>
                <p><?php
                    _ex(
            'The <strong>Matomo Site ID</strong> is also called <strong>Website ID</strong> or <strong>ID Site</strong>.',
            'Backend / Cookie / Matomo / Tips / Text',
            'borlabs-cookie'
        ); ?></p>
                <p class="text-center"><?php
                    _ex(
            '<a href="https://matomo.org/faq/general/faq_19212/" target="_blank" rel="nofollow noopener noreferrer" class="text-light">More information about Matomo Site ID <i class="fas fa-external-link-alt"></i></a>.',
            'Backend / Cookie / Matomo / Tips / Text',
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
        $inputMatomoUrl = esc_html(!empty($data->settings['matomoUrl']) ? $data->settings['matomoUrl'] : '');
        $inputMatomoSiteId = esc_html(!empty($data->settings['matomoSiteId']) ? $data->settings['matomoSiteId'] : ''); ?>
        <div class="form-group row">
            <label for="matomoUrl"
                   class="col-sm-4 col-form-label"><?php
                _ex('Matomo URL', 'Backend / Cookie / Matomo / Label', 'borlabs-cookie'); ?></label>
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
            'Backend / Cookie / Matomo / Tooltip',
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
            <label for="matomoSiteId"
                   class="col-sm-4 col-form-label"><?php
                _ex('Matomo Site ID', 'Backend / Cookie / Matomo / Label', 'borlabs-cookie'); ?></label>
            <div class="col-sm-8">
                <input type="text" class="form-control form-control-sm d-inline-block w-75 mr-2" id="matomoSiteId"
                       name="settings[matomoSiteId]" value="<?php
                echo $inputMatomoSiteId; ?>"
                       placeholder="<?php
                       _ex('Example', 'Backend / Global / Input Placeholder', 'borlabs-cookie'); ?>: 1"
                       required>
                <span data-toggle="tooltip"
                      title="<?php
                      echo esc_attr_x(
            'Enter the <strong>Site ID</strong> of the website from Matomo.',
            'Backend / Cookie / Matomo / Tooltip',
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
            'cookieId' => 'matomo',
            'service' => 'Matomo',
            'name' => 'Matomo',
            'provider' => get_bloginfo('name', 'raw'),
            'purpose' => _x(
                'Cookie by Matomo used for website analytics. Generates statistical data on how the visitor uses the website.',
                'Frontend / Cookie / Matomo / Text',
                'borlabs-cookie'
            ),
            'privacyPolicyURL' => $privacyPolicyURL,
            'hosts' => [],
            'cookieName' => '_pk_*.*',
            'cookieExpiry' => _x('13 Months', 'Frontend / Cookie / Matomo / Text', 'borlabs-cookie'),
            'optInJS' => $this->optInJS(),
            'optOutJS' => '',
            'fallbackJS' => '',
            'settings' => [
                'blockCookiesBeforeConsent' => false,
                'prioritize' => true,
                'matomoUrl' => '',
                'matomoSiteId' => '1',
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
        if (!empty($formData['service']) && $formData['service'] === 'Matomo') {
            if (!empty($formData['settings']['matomoUrl'])) {
                $formData['settings']['matomoUrl'] = trim($formData['settings']['matomoUrl']);

                $urlInfo = parse_url($formData['settings']['matomoUrl']);

                $formData['settings']['matomoUrl'] = (!empty($urlInfo['scheme']) ? $urlInfo['scheme'] . '://' : '//')
                    . $urlInfo['host'];

                if (!empty($urlInfo['path'])) {
                    $pathInfo = pathinfo($urlInfo['path']);

                    // Remove filename like index.php
                    if (!empty($pathInfo['extension'])) {
                        $urlInfo['path'] = dirname($urlInfo['path']);
                    }

                    $formData['settings']['matomoUrl'] .= rtrim($urlInfo['path'], '/') . '/';
                } else {
                    $formData['settings']['matomoUrl'] .= '/';
                }
            }

            if (!empty($formData['settings']['matomoSiteId'])) {
                $formData['settings']['matomoSiteId'] = trim($formData['settings']['matomoSiteId']);
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
<!-- Matomo -->
<script type="text/javascript">
var _paq = window._paq || [];
/* tracker methods like "setCustomDimension" should be called before "trackPageView" */
_paq.push(['trackPageView']);
_paq.push(['enableLinkTracking']);
(function() {
var u="%%matomoUrl%%";
_paq.push(['setTrackerUrl', u+'matomo.php']);
_paq.push(['setSiteId', '%%matomoSiteId%%']);
var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
g.type='text/javascript'; g.async=true; g.defer=true; g.src=u+'matomo.js'; s.parentNode.insertBefore(g,s);
})();
</script>
<!-- End Matomo Code -->
EOT;
    }
}
