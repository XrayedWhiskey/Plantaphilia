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

namespace BorlabsCookie\Cookie\Frontend\ContentBlocker;

use BorlabsCookie\Cookie\Frontend\ContentBlocker;

class GoogleMaps
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
            'borlabsCookie/contentBlocker/edit/template/settings/googlemaps',
            [$this, 'additionalSettingsTemplate']
        );
        add_action(
            'borlabsCookie/contentBlocker/edit/template/settings/help/googlemaps',
            [$this, 'additionalSettingsHelpTemplate']
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

    public function additionalSettingsHelpTemplate($data)
    {
        ?>
        <div class="col-12 col-md-4 rounded-right shadow-sm bg-tips text-light">
            <div class="px-3 pt-3 pb-3 mb-4">
                <h3 class="border-bottom mb-3"><?php
                    _ex('Tips', 'Backend / Global / Tips / Headline', 'borlabs-cookie'); ?></h3>
                <h4><?php
                    _ex(
            'Is Google Maps not blocked?',
            'Backend / Content Blocker / Google Maps / Tips / Headline',
            'borlabs-cookie'
        ); ?></h4>
                <p><?php
                    _ex(
            'If you have a plugin that uses the JavaScript API to embed Google Maps, Borlabs Cookie will not be able to block the content. This is due to technical limitations. To fix this the developer must add Borlabs Cookie support. A plugin with this support is WP Store Locator.',
            'Backend / Content Blocker / Google Maps / Tips / Text',
            'borlabs-cookie'
        ); ?></p>
                <p><?php
                    _ex(
            'If you are not using a plugin, you can manually block the content using the short code of the content blocker.',
            'Backend / Content Blocker / Google Maps / Tips / Text',
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
        $inputAPIKey = esc_html(!empty($data->settings['apiKey']) ? $data->settings['apiKey'] : ''); ?>
        <div class="form-group row">
            <label for="name"
                   class="col-sm-4 col-form-label"><?php
                _ex('API Key', 'Backend / Content Blocker / Google Maps / Label', 'borlabs-cookie'); ?></label>
            <div class="col-sm-8">
                <input type="text" class="form-control form-control-sm d-inline-block w-75 mr-2" id="name"
                       name="settings[apiKey]" value="<?php
                echo $inputAPIKey; ?>">
                <span data-toggle="tooltip"
                      title="<?php
                      echo esc_attr_x(
            'Enter your Google API Key.',
            'Backend / Content Blocker / Google Maps / Tooltip',
            'borlabs-cookie'
        ); ?>"><i
                        class="fas fa-lg fa-question-circle text-dark"></i></span>
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
            'contentBlockerId' => 'googlemaps',
            'name' => 'Google Maps',
            'description' => '',
            'privacyPolicyURL' => _x(
                'https://policies.google.com/privacy?hl=en&amp;gl=en',
                'Frontend / Content Blocker / Google Maps / URL',
                'borlabs-cookie'
            ),
            'hosts' => [
                'maps.google.com',
                'www.google.com/maps/',
            ],
            'previewHTML' => '<div class="_brlbs-content-blocker">
	<div class="_brlbs-embed _brlbs-google-maps">
    	<img class="_brlbs-thumbnail" src="%%thumbnail%%" alt="%%name%%">
		<div class="_brlbs-caption">
			<p>' . _x(
                "By loading the map, you agree to Google's privacy policy.",
                'Frontend / Content Blocker / Google Maps / Text',
                'borlabs-cookie'
            ) . '<br><a href="%%privacy_policy_url%%" target="_blank" rel="nofollow noopener noreferrer">' . _x(
                'Learn more',
                'Frontend / Content Blocker / Google Maps / Text',
                'borlabs-cookie'
            ) . '</a></p>
			<p><a class="_brlbs-btn" href="#" data-borlabs-cookie-unblock role="button">' . _x(
                'Load map',
                'Frontend / Content Blocker / Google Maps / Text',
                'borlabs-cookie'
            ) . '</a></p>
			<p><label><input type="checkbox" name="unblockAll" value="1" checked> <small>' . _x(
                'Always unblock Google Maps',
                'Frontend / Content Blocker / Google Maps / Text',
                'borlabs-cookie'
            ) . '</small></label></p>
		</div>
	</div>
</div>',
            'previewCSS' => '.BorlabsCookie ._brlbs-google-maps a._brlbs-btn {
	background: #4285f4;
	border-radius: 3px;
}

.BorlabsCookie ._brlbs-google-maps a._brlbs-btn:hover {
	background: #fff;
	color: #4285f4;
}',
            'globalJS' => '',
            'initJS' => '',
            'settings' => [
                'executeGlobalCodeBeforeUnblocking' => false,
            ],
            'status' => true,
            'undeletable' => true,
        ];
    }

    /**
     * modify function.
     *
     * @param mixed $content
     * @param mixed $atts    (default: [])
     */
    public function modify($content, $atts = [])
    {
        // Get settings of the Content Blocker
        $contentBlockerData = ContentBlocker::getInstance()->getContentBlockerData('googlemaps');

        // Add API key
        if (!empty($contentBlockerData['settings']['apiKey'])) {
            $srcMatch = [];

            preg_match('/src=("|\')([^"\']{1,})(\1)/i', $content, $srcMatch);

            if (!empty($srcMatch[2])) {
                $urlData = parse_url($srcMatch[2]);

                if (!empty($urlData['query'])) {
                    $query = [];

                    parse_str($urlData['query'], $query);

                    $query['key'] = $contentBlockerData['settings']['apiKey'];

                    $newMapURL = str_replace($urlData['query'], http_build_query($query), $srcMatch[2]);

                    $content = str_replace($srcMatch[2], $newMapURL, $content);

                    // Overwrite the old blocked content with the modified version
                    ContentBlocker::getInstance()->setCurrentBlockedContent($content);
                }
            }
        }

        // Default thumbnail
        $thumbnail = BORLABS_COOKIE_PLUGIN_URL . 'assets/images/cb-maps.png';

        // Get the title which was maybe set via title-attribute in a shortcode
        $title = ContentBlocker::getInstance()->getCurrentTitle();

        // If no title was set use the Content Blocker name as title
        if (empty($title)) {
            $title = $contentBlockerData['name'];
        }

        // Replace text variables
        if (!empty($atts)) {
            foreach ($atts as $key => $value) {
                $contentBlockerData['previewHTML'] = str_replace(
                    '%%' . $key . '%%',
                    $value,
                    $contentBlockerData['previewHTML']
                );
            }
        }

        $contentBlockerData['previewHTML'] = str_replace(
            [
                '%%name%%',
                '%%thumbnail%%',
                '%%privacy_policy_url%%',
            ],
            [
                $title,
                $thumbnail,
                $contentBlockerData['privacyPolicyURL'],
            ],
            $contentBlockerData['previewHTML']
        );

        return $contentBlockerData['previewHTML'];
    }
}
