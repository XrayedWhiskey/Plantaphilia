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

class OpenStreetMap
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
     * getDefault function.
     */
    public function getDefault()
    {
        return [
            'contentBlockerId' => 'openstreetmap',
            'name' => 'OpenStreetMap',
            'description' => '',
            'privacyPolicyURL' => _x(
                'https://wiki.osmfoundation.org/wiki/Privacy_Policy',
                'Frontend / Content Blocker / OpenStreetMap / URL',
                'borlabs-cookie'
            ),
            'hosts' => [
                'www.openstreetmap.org',
            ],
            'previewHTML' => '<div class="_brlbs-content-blocker">
	<div class="_brlbs-embed">
    	<img class="_brlbs-thumbnail" src="%%thumbnail%%" alt="%%name%%">
		<div class="_brlbs-caption">
			<p>' . _x(
                "By loading the map, you agree to OpenStreetMap Foundation's privacy policy.",
                'Frontend / Content Blocker / OpenStreetMap / Text',
                'borlabs-cookie'
            ) . '<br><a href="%%privacy_policy_url%%" target="_blank" rel="nofollow noopener noreferrer">' . _x(
                'Learn more',
                'Frontend / Content Blocker / OpenStreetMap / Text',
                'borlabs-cookie'
            ) . '</a></p>
			<p><a class="_brlbs-btn" href="#" data-borlabs-cookie-unblock role="button">' . _x(
                'Load map',
                'Frontend / Content Blocker / OpenStreetMap / Text',
                'borlabs-cookie'
            ) . '</a></p>
			<p><label><input type="checkbox" name="unblockAll" value="1" checked> <small>' . _x(
                'Always unblock OpenStreetMap',
                'Frontend / Content Blocker / OpenStreetMap / Text',
                'borlabs-cookie'
            ) . '</small></label></p>
		</div>
	</div>
</div>',
            'previewCSS' => '',
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
        $contentBlockerData = ContentBlocker::getInstance()->getContentBlockerData('openstreetmap');

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
