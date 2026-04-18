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

class EzoicPreferences
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
            'borlabsCookie/cookie/edit/template/settings/EzoicPreferences',
            [$this, 'additionalSettingsTemplate']
        );
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
            'cookieId' => 'ezoic-preferences',
            'service' => 'EzoicPreferences',
            'name' => 'Ezoic - Preferences',
            'provider' => 'Ezoic Inc, 6023 Innovation Way 2nd Floor, Carlsbad, CA 92009, USA',
            'purpose' => _x(
                'Remember information that changes the behavior or appearance of the site, such as your preferred language or the region in which you are located.',
                'Frontend / Cookie / Ezoic - Preferences / Text',
                'borlabs-cookie'
            ),
            'privacyPolicyURL' => _x(
                'https://www.ezoic.com/privacy-policy/',
                'Frontend / Cookie / Ezoic - Preferences / Text',
                'borlabs-cookie'
            ),
            'hosts' => [],
            'cookieName' => 'ez*, sitespeed_preview, FTNT*, SITESERVER, SL*, speed_no_process, GED_PLAYLIST_ACTIVITY, __guid',
            'cookieExpiry' => _x('1 Year', 'Frontend / Cookie / Ezoic - Preferences / Text', 'borlabs-cookie'),
            'optInJS' => $this->optInJS(),
            'optOutJS' => $this->optOutJS(),
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
if (typeof window.BorlabsEZConsentCategories == 'object') {
    window.BorlabsEZConsentCategories.preferences = true;
}
</script>
EOT;
    }

    /**
     * optOutJS function.
     */
    private function optOutJS()
    {
        return <<<EOT
<script>
if (typeof window.BorlabsEZConsentCategories == 'object') {
    window.BorlabsEZConsentCategories.preferences = false;
}
</script>
EOT;
    }
}
