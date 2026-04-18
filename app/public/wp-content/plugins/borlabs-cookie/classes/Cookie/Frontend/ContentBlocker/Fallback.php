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

class Fallback
{
    private static $instance;

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

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
            'contentBlockerId' => 'default',
            'name' => _x('Default', 'Frontend / Content Blocker / Default / Text', 'borlabs-cookie'),
            'description' => _x(
                'The <strong><em>Default</em> Content Blocker</strong> is a special type that is always used when no specific <strong>Content Blocker</strong> was found.<br>Therefore it is not possible to use the <strong>Unblock all</strong> feature.',
                'Frontend / Content Blocker / Default / Alert Message',
                'borlabs-cookie'
            ),
            'privacyPolicyURL' => '',
            'hosts' => [],
            'previewHTML' => '<div class="_brlbs-content-blocker">
    <div class="_brlbs-default">
        <p>' . _x(
                'Click on the button to load the content from %%name%%.',
                'Frontend / Content Blocker / Default / Text',
                'borlabs-cookie'
            ) . '</p>
        <p><a class="_brlbs-btn" href="#" data-borlabs-cookie-unblock role="button">' . _x(
                'Load content',
                'Frontend / Content Blocker / Default / Text',
                'borlabs-cookie'
            ) . '</a></p>
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
        $contentBlockerData = ContentBlocker::getInstance()->getContentBlockerData('default');

        // Get the title which was maybe set via title-attribute in a shortcode
        $title = ContentBlocker::getInstance()->getCurrentTitle();

        // If no title was set use the Content Blocker name as title
        if (empty($title)) {
            $title = parse_url(ContentBlocker::getInstance()->getCurrentURL(), PHP_URL_HOST);
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
                '%%privacy_policy_url%%',
            ],
            [
                $title,
                $contentBlockerData['privacyPolicyURL'],
            ],
            $contentBlockerData['previewHTML']
        );

        return $contentBlockerData['previewHTML'];
    }
}
