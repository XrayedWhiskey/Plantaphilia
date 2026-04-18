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

class Ezoic
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
        add_action('borlabsCookie/cookie/edit/template/settings/Ezoic', [$this, 'additionalSettingsTemplate']);
    }

    public function __clone()
    {
        trigger_error('Cloning is not allowed.', E_USER_ERROR);
    }

    public function __wakeup()
    {
        trigger_error('Unserialize is forbidden.', E_USER_ERROR);
    }

    public function additionalSettingsTemplate($data)
    {
        ?>
        <div class="form-group row">
            <div class="col-sm-8 offset-4">
                <div
                    class="alert alert-warning mt-2"><?php
                    $kbLink = _x(
            'https://borlabs.io/kb/ezoic/',
            'Backend / Cookie / Ezoic / Alert Message',
            'borlabs-cookie'
        );
        printf(
            _x(
                'Your cookie description needs to be updated. Please read <a href="%s" target="_blank" rel="nofollow noopener noreferrer">%s</a>.',
                'Backend / Cookie / Ezoic / Alert Message',
                'borlabs-cookie'
            ),
            $kbLink,
            $kbLink
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
            'cookieId' => 'ezoic',
            'service' => 'Ezoic',
            'name' => 'Ezoic',
            'provider' => 'Ezoic Inc, 6023 Innovation Way 2nd Floor, Carlsbad, CA 92009, USA',
            'purpose' => _x(
                'Necessary for the basic functions of the website.',
                'Frontend / Cookie / Ezoic / Text',
                'borlabs-cookie'
            ),
            'privacyPolicyURL' => _x(
                'https://www.ezoic.com/privacy-policy/',
                'Frontend / Cookie / Ezoic / Text',
                'borlabs-cookie'
            ),
            'hosts' => [],
            'cookieName' => 'ez*, cf*, unique_id, __cf*, __utmt*',
            'cookieExpiry' => _x('1 Year', 'Frontend / Cookie / Ezoic / Text', 'borlabs-cookie'),
            'optInJS' => $this->optInJS(),
            'optOutJS' => '',
            'fallbackJS' => '',
            'settings' => [
                'blockCookiesBeforeConsent' => false,
                'prioritize' => true,
            ],
            'status' => true,
            'undeletetable' => false,
        ];
    }

    /**
     * optInJS function.
     */
    private function optInJS()
    {
        return <<<EOT
<script>
window.BorlabsEZConsentCategories = window.BorlabsEZConsentCategories || {};
window.BorlabsCookieEzoicHandle = function (e) {

    window.BorlabsEZConsentCategories.preferences = window.BorlabsEZConsentCategories.preferences || false;
    window.BorlabsEZConsentCategories.statistics = window.BorlabsEZConsentCategories.statistics || false;
    window.BorlabsEZConsentCategories.marketing = window.BorlabsEZConsentCategories.marketing || false;

    if (typeof BorlabsEZConsentCategories == 'object') {
        var waitForEzoic = function () {
			if (typeof __ezconsent == 'object') {
    			window.ezConsentCategories = window.BorlabsEZConsentCategories;
    			__ezconsent.setEzoicConsentSettings(window.ezConsentCategories);
			} else {
				window.setTimeout(waitForEzoic, 60);
			}
		};

		waitForEzoic();
    }
};

document.addEventListener("borlabs-cookie-prioritized-code-unblocked", window.BorlabsCookieEzoicHandle, false);
document.addEventListener("borlabs-cookie-code-unblocked-after-consent", window.BorlabsCookieEzoicHandle, false);
</script>
EOT;
    }
}
