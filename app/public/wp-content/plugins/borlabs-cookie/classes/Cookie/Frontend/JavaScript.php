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

namespace BorlabsCookie\Cookie\Frontend;

use BorlabsCookie\Cookie\Config;
use BorlabsCookie\Cookie\Multilanguage;

class JavaScript
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
     * contentBlocker.
     *
     * (default value: [])
     *
     * @var mixed
     */
    private $contentBlocker = [];

    private $cookiePath;

    private $cookieVersion;

    /**
     * fallbackCode.
     *
     * (default value: [])
     *
     * @var mixed
     */
    private $fallbackCode = [];

    public function __construct()
    {
        // Domain information for javascript cookie
        $siteURL = get_home_url();
        $siteURLInfo = parse_url($siteURL);
        $this->cookiePath = !empty($siteURLInfo['path']) ? $siteURLInfo['path'] : '/';

        if (Config::getInstance()->get('automaticCookieDomainAndPath') === false) {
            $this->cookiePath = Config::getInstance()->get('cookiePath');
        }

        $this->cookieVersion = (int) (get_site_option('BorlabsCookieCookieVersion', 1));
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
     * addContentBlocker function.
     *
     * @param mixed  $contentBlockerId
     * @param string $globalJS         (default: '')
     * @param string $initJS           (default: '')
     * @param mixed  $settings         (default: [])
     */
    public function addContentBlocker($contentBlockerId, $globalJS = '', $initJS = '', $settings = [])
    {
        $settings = apply_filters('borlabsCookie/contentBlocker/modify/settings/' . $contentBlockerId, $settings);

        $this->contentBlocker[$contentBlockerId] = [
            'contentBlockerId' => $contentBlockerId,
            'global' => $globalJS,
            'init' => $initJS,
            'settings' => $settings,
        ];

        return true;
    }

    /**
     * getContentBlockerScriptsData function.
     */
    public function getContentBlockerScriptsData()
    {
        $js = 'var borlabsCookieContentBlocker = {';

        if (!empty($this->contentBlocker)) {
            foreach ($this->contentBlocker as $contentBlockerId => $data) {
                $js .= '"' . $contentBlockerId . '": {';
                $js .= '"id": "' . $contentBlockerId . '",';
                $js .= '"global": function (contentBlockerData) { ' . $data['global'] . ' },';
                $js .= '"init": function (el, contentBlockerData) { ' . $data['init'] . ' },';
                $js .= '"settings": ' . json_encode($data['settings']);
                $js .= '},';
            }

            $js = substr($js, 0, -1);
        }

        $js .= '};';

        return $js;
    }

    /**
     * registerFooter function.
     */
    public function registerFooter()
    {
        if (defined('REST_REQUEST') && apply_filters('borlabsCookie/javascript/disabledOnRestRequest', true)) {
            return;
        }

        wp_enqueue_script(
            'borlabs-cookie',
            BORLABS_COOKIE_PLUGIN_URL . 'assets/javascript/borlabs-cookie.min.js',
            [
                Config::getInstance()->get('jQueryHandle'),
            ],
            BORLABS_COOKIE_VERSION,
            true
        );

        $showCookieBox = Config::getInstance()->get('showCookieBox');

        if (ScannerRequest::getInstance()->isAuthorized() && ScannerRequest::getInstance()->noConsentDialog()) {
            $showCookieBox = false;
        }

        $jsConfig = [
            'ajaxURL' => admin_url('admin-ajax.php'),
            'language' => Multilanguage::getInstance()->getCurrentLanguageCode(),

            'animation' => Config::getInstance()->get('cookieBoxAnimation'),
            'animationDelay' => Config::getInstance()->get('cookieBoxAnimationDelay'),
            'animationIn' => '_brlbs-' . Config::getInstance()->get('cookieBoxAnimationIn'),
            'animationOut' => '_brlbs-' . Config::getInstance()->get('cookieBoxAnimationOut'),
            'blockContent' => Config::getInstance()->get('cookieBoxBlocksContent'),
            'boxLayout' => str_replace(['-slim', '-advanced', '-plus'], '', Config::getInstance()->get('cookieBoxLayout')),
            'boxLayoutAdvanced' => strpos(Config::getInstance()->get('cookieBoxLayout'), '-advanced') || strpos(Config::getInstance()->get('cookieBoxLayout'), '-plus') !== false ? true
                : false,

            'automaticCookieDomainAndPath' => Config::getInstance()->get('automaticCookieDomainAndPath'),
            'cookieDomain' => Config::getInstance()->get('cookieDomain'),
            'cookiePath' => $this->cookiePath,
            'cookieSameSite' => Config::getInstance()->get('cookieSameSite'),
            'cookieSecure' => Config::getInstance()->get('cookieSecure'),
            'cookieLifetime' => Config::getInstance()->get('cookieLifetime'),
            'cookieLifetimeEssentialOnly' => Config::getInstance()->get('cookieLifetimeEssentialOnly'),
            'crossDomainCookie' => Config::getInstance()->get('crossDomainCookie'),

            'cookieBeforeConsent' => Config::getInstance()->get('cookieBeforeConsent'),
            'cookiesForBots' => Config::getInstance()->get('cookiesForBots'),
            'cookieVersion' => $this->cookieVersion,
            'hideCookieBoxOnPages' => Config::getInstance()->get('hideCookieBoxOnPages'),
            'respectDoNotTrack' => Config::getInstance()->get('respectDoNotTrack'),
            'reloadAfterConsent' => Config::getInstance()->get('reloadAfterConsent'),
            'reloadAfterOptOut' => Config::getInstance()->get('reloadAfterOptOut'),
            'showCookieBox' => $showCookieBox,
            'cookieBoxIntegration' => Config::getInstance()->get('cookieBoxIntegration'),

            'ignorePreSelectStatus' => Config::getInstance()->get('cookieBoxIgnorePreSelectStatus'),

            'cookies' => [],
        ];

        $allCookies = Cookies::getInstance()->getAllCookieGroups();
        $cookies = [];

        if (!empty($allCookies)) {
            foreach ($allCookies as $cookieGroupData) {
                // Add all cookie groups to the array which are needed by the JavaScript class
                $jsConfig['cookies'][$cookieGroupData->group_id] = [];

                if (!empty($cookieGroupData->cookies)) {
                    foreach ($cookieGroupData->cookies as $cookieData) {
                        // Add all cookies to the array which are needed by the JavaScript class
                        $jsConfig['cookies'][$cookieGroupData->group_id][] = $cookieData->cookie_id;

                        $cookieData = apply_filters(
                            'borlabsCookie/cookie/modify/code/' . $cookieData->cookie_id,
                            $cookieData
                        );

                        $cookies[$cookieGroupData->group_id][$cookieData->cookie_id] = [
                            'cookieNameList' => CookieBlocker::getInstance()->prepareCookieNamesList(
                                $cookieData->cookie_name
                            ),
                            'settings' => $cookieData->settings,
                        ];

                        if (
                            !empty($cookieData->opt_in_js) || !empty($cookieData->opt_out_js)
                            || !empty($cookieData->fallback_js)
                        ) {
                            $cookies[$cookieGroupData->group_id][$cookieData->cookie_id]['optInJS']
                                = empty($cookieData->settings['prioritize']) ? base64_encode(
                                    do_shortcode($cookieData->opt_in_js)
                                ) : '';
                            $cookies[$cookieGroupData->group_id][$cookieData->cookie_id]['optOutJS'] = base64_encode(
                                do_shortcode($cookieData->opt_out_js)
                            );
                        }
                    }
                }
            }
        }

        $jsConfig = apply_filters('borlabsCookie/settings', $jsConfig);

        wp_localize_script('borlabs-cookie', 'borlabsCookieConfig', $jsConfig);
        wp_localize_script('borlabs-cookie', 'borlabsCookieCookies', $cookies);

        $jsCode = 'document.addEventListener("DOMContentLoaded", function (e) {';
        $jsCode .= "\n" . $this->getContentBlockerScriptsData() . "\n";

        $jsCode .= <<<EOT
    var BorlabsCookieInitCheck = function () {

    if (typeof window.BorlabsCookie === "object" && typeof window.jQuery === "function") {

        if (typeof borlabsCookiePrioritized !== "object") {
            borlabsCookiePrioritized = { optInJS: {} };
        }

        window.BorlabsCookie.init(borlabsCookieConfig, borlabsCookieCookies, borlabsCookieContentBlocker, borlabsCookiePrioritized.optInJS);
    } else {
        window.setTimeout(BorlabsCookieInitCheck, 50);
    }
};

BorlabsCookieInitCheck();
EOT;
        $jsCode .= '});';

        wp_add_inline_script('borlabs-cookie', $jsCode, 'after');
    }

    /**
     * registerHead function.
     */
    public function registerHead()
    {
        $allCookies = Cookies::getInstance()->getAllCookieGroups();
        $prioritizedCodes = [];

        if (!empty($allCookies)) {
            foreach ($allCookies as $cookieGroupData) {
                if (!empty($cookieGroupData->cookies)) {
                    foreach ($cookieGroupData->cookies as $cookieData) {
                        if (!empty($cookieData->opt_in_js) || !empty($cookieData->fallback_js)) {
                            if (!empty($cookieData->settings['prioritize'])) {
                                $prioritizedCodes[$cookieGroupData->group_id][$cookieData->cookie_id] = base64_encode(
                                    do_shortcode($cookieData->opt_in_js)
                                );
                            }

                            $this->fallbackCode[$cookieGroupData->group_id][$cookieData->cookie_id] = do_shortcode(
                                $cookieData->fallback_js
                            );
                        }
                    }
                }
            }
        }

        if (!empty($prioritizedCodes)) {
            if (defined('REST_REQUEST') && apply_filters('borlabsCookie/javascript/disabledOnRestRequest', true)) {
                return;
            }

            wp_enqueue_script(
                'borlabs-cookie-prioritize',
                BORLABS_COOKIE_PLUGIN_URL . 'assets/javascript/borlabs-cookie-prioritize.min.js',
                [],
                BORLABS_COOKIE_VERSION
            );

            wp_localize_script('borlabs-cookie-prioritize', 'borlabsCookiePrioritized', [
                'domain' => Config::getInstance()->get('cookieDomain'),
                'path' => $this->cookiePath,
                'version' => $this->cookieVersion,
                'bots' => Config::getInstance()->get('cookiesForBots'),
                'optInJS' => $prioritizedCodes,
            ]);
        }
    }

    /**
     * registerHeadFallback function.
     */
    public function registerHeadFallback()
    {
        if (defined('REST_REQUEST') && apply_filters('borlabsCookie/javascript/disabledOnRestRequest', true)) {
            return;
        }

        // Fallback code is always executed
        if (!empty($this->fallbackCode)) {
            foreach ($this->fallbackCode as $groupData) {
                foreach ($groupData as $cookieFallbackCode) {
                    echo $cookieFallbackCode;
                }
            }
        }
    }
}
