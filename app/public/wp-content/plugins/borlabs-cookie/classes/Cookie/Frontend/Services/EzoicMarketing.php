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

class EzoicMarketing
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
        add_action('borlabsCookie/cookie/edit/template/settings/EzoicMarketing', [$this, 'additionalSettingsTemplate']);
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
            'cookieId' => 'ezoic-marketing',
            'service' => 'EzoicMarketing',
            'name' => 'Ezoic - Marketing',
            'provider' => 'Ezoic Inc, 6023 Innovation Way 2nd Floor, Carlsbad, CA 92009, USA',
            'purpose' => _x(
                'Are used to track visitors across websites. The intent is to display ads that are relevant and appealing to the individual user.',
                'Frontend / Cookie / Ezoic - Marketing / Text',
                'borlabs-cookie'
            ),
            'privacyPolicyURL' => _x(
                'https://www.ezoic.com/privacy-policy/',
                'Frontend / Cookie / Ezoic - Marketing / Text',
                'borlabs-cookie'
            ),
            'hosts' => [],
            'cookieName' => 'ez*, _sm_au, cto*, __gads, mind*, _ym_uid, GoogleAdServingTest',
            'cookieExpiry' => _x('1 Year', 'Frontend / Cookie / Ezoic - Marketing / Text', 'borlabs-cookie'),
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
    window.BorlabsEZConsentCategories.marketing = true;
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
    window.BorlabsEZConsentCategories.marketing = false;
}
</script>
EOT;
    }
}
