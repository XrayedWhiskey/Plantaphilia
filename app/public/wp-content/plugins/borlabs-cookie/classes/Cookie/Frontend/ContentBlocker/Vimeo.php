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
use BorlabsCookie\Cookie\Tools;

class Vimeo
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
     *
     * Register the action hook for additional settings.
     */
    public function __construct()
    {
        add_action('borlabsCookie/contentBlocker/edit/template/settings/vimeo', [$this, 'additionalSettingsTemplate']);
        add_action(
            'borlabsCookie/contentBlocker/edit/template/settings/help/vimeo',
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
            'Video Wrapper',
            'Backend / Content Blocker / Vimeo / Tips / Headline',
            'borlabs-cookie'
        ); ?></h4>
                <p><?php
                    _ex(
            'If the <strong>Video Wrapper</strong> option is enabled, the iframe of the video is placed in a container to prevent problems with the video display, e.g. small video size, wrong aspect ratio or large spacing above the video.',
            'Backend / Content Blocker / Vimeo / Tips / Text',
            'borlabs-cookie'
        ); ?></p>
                <p><?php
                    _ex(
            'For themes that do not load the default Gutenberg CSS, this option must often be activated.',
            'Backend / Content Blocker / Vimeo / Tips / Text',
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
        $inputSaveThumbnails = !empty($data->settings['saveThumbnails']) ? 1 : 0;
        $switchSaveThumbnails = $inputSaveThumbnails ? ' active' : '';

        $inputAutplay = !empty($data->settings['autoplay']) ? 1 : 0;
        $switchAutoplay = $inputAutplay ? ' active' : '';

        $inputVideoWrapper = !empty($data->settings['videoWrapper']) ? 1 : 0;
        $switchVideoWrapper = $inputVideoWrapper ? ' active' : ''; ?>
        <div class="form-group row align-items-center">
            <label
                class="col-sm-4 col-form-label"><?php
                _ex(
            'Save thumbnails locally',
            'Backend / Content Blocker / Vimeo / Label',
            'borlabs-cookie'
        ); ?></label>
            <div class="col-sm-8">
                <button type="button" class="btn btn-sm btn-toggle mr-2<?php
                echo $switchSaveThumbnails; ?>"
                        data-toggle="button" data-switch-target="saveThumbnails" aria-pressed="<?php
                echo $inputSaveThumbnails ? 'true' : 'false'; ?>">
                    <span class="handle"></span>
                </button>
                <input type="hidden" name="settings[saveThumbnails]" id="saveThumbnails"
                       value="<?php
                       echo $inputSaveThumbnails; ?>">
                <span data-toggle="tooltip"
                      title="<?php
                      echo esc_attr_x(
            'Attempts to get the thumbnail of the Vimeo video to save it locally. Your visitor\'s IP-address will not be transferred to Vimeo during this process.',
            'Backend / Content Blocker / Vimeo / Tooltip',
            'borlabs-cookie'
        ); ?>"><i
                        class="fas fa-lg fa-question-circle text-dark"></i></span>
            </div>
        </div>

        <div class="form-group row align-items-center">
            <label for="autoplay"
                   class="col-sm-4 col-form-label"><?php
                _ex('Autoplay', 'Backend / Content Blocker / Vimeo / Label', 'borlabs-cookie'); ?></label>
            <div class="col-sm-8">
                <button type="button" class="btn btn-sm btn-toggle mr-2<?php
                echo $switchAutoplay; ?>"
                        data-toggle="button" data-switch-target="autoplay" aria-pressed="<?php
                echo $inputAutplay ? 'true' : 'false'; ?>">
                    <span class="handle"></span>
                </button>
                <input type="hidden" name="settings[autoplay]" id="autoplay"
                       value="<?php
                       echo $inputAutplay; ?>">
                <span data-toggle="tooltip"
                      title="<?php
                      echo esc_attr_x(
            'The video will play automatically after unlocking. <strong>Warning:</strong> Not recommended when embedding multiple videos on one page.',
            'Backend / Content Blocker / Vimeo / Tooltip',
            'borlabs-cookie'
        ); ?>"><i
                        class="fas fa-lg fa-question-circle text-dark"></i></span>
            </div>
        </div>

        <div class="form-group row align-items-center">
            <label for="videoWrapper"
                   class="col-sm-4 col-form-label"><?php
                _ex('Video Wrapper', 'Backend / Content Blocker / Vimeo / Label', 'borlabs-cookie'); ?></label>
            <div class="col-sm-8">
                <button type="button" class="btn btn-sm btn-toggle mr-2<?php
                echo $switchVideoWrapper; ?>"
                        data-toggle="button" data-switch-target="videoWrapper" aria-pressed="<?php
                echo $inputVideoWrapper ? 'true' : 'false'; ?>">
                    <span class="handle"></span>
                </button>
                <input type="hidden" name="settings[videoWrapper]" id="videoWrapper"
                       value="<?php
                       echo $inputVideoWrapper; ?>">
                <span data-toggle="tooltip"
                      title="<?php
                      echo esc_attr_x(
            'Enable this option if the video is displayed too small, with incorrect aspect ratios, or large spacing.',
            'Backend / Content Blocker / Vimeo / Tooltip',
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
            'contentBlockerId' => 'vimeo',
            'name' => 'Vimeo',
            'description' => '',
            'privacyPolicyURL' => _x(
                'https://vimeo.com/privacy',
                'Frontend / Content Blocker / Vimeo / URL',
                'borlabs-cookie'
            ),
            'hosts' => [
                'vimeo.com',
            ],
            'previewHTML' => '<div class="_brlbs-content-blocker">
	<div class="_brlbs-embed _brlbs-video-vimeo">
    	<img class="_brlbs-thumbnail" src="%%thumbnail%%" alt="%%name%%">
		<div class="_brlbs-caption">
			<p>' . _x(
                "By loading the video, you agree to Vimeo's privacy policy.",
                'Frontend / Content Blocker / Vimeo / Text',
                'borlabs-cookie'
            ) . '<br><a href="%%privacy_policy_url%%" target="_blank" rel="nofollow noopener noreferrer">' . _x(
                'Learn more',
                'Frontend / Content Blocker / Vimeo / Text',
                'borlabs-cookie'
            ) . '</a></p>
			<p><a class="_brlbs-btn _brlbs-icon-play-white" href="#" data-borlabs-cookie-unblock role="button">' . _x(
                'Load video',
                'Frontend / Content Blocker / Vimeo / Text',
                'borlabs-cookie'
            ) . '</a></p>
			<p><label><input type="checkbox" name="unblockAll" value="1" checked> <small>' . _x(
                'Always unblock Vimeo',
                'Frontend / Content Blocker / Vimeo / Text',
                'borlabs-cookie'
            ) . '</small></label></p>
		</div>
	</div>
</div>',
            'previewCSS' => '.BorlabsCookie ._brlbs-video-vimeo a._brlbs-btn {
	background: #00adef;
	border-radius: 20px;
}

.BorlabsCookie ._brlbs-video-vimeo a._brlbs-btn:hover {
	background: #fff;
	color: #00adef;
}

.BorlabsCookie ._brlbs-video-vimeo a._brlbs-btn._brlbs-icon-play-white:hover::before {
	background: url("data:image/svg+xml,%3Csvg version=\'1.1\' xmlns=\'http://www.w3.org/2000/svg\' xmlns:xlink=\'http://www.w3.org/1999/xlink\' x=\'0\' y=\'0\' width=\'78\' height=\'78\' viewBox=\'0, 0, 78, 78\'%3E%3Cg id=\'Layer_1\'%3E%3Cg%3E%3Cpath d=\'M7.5,71.5 L7.5,7.5 L55.5,37.828 L7.5,71.5\' fill=\'%2300adef\'/%3E%3Cpath d=\'M7.5,71.5 L7.5,7.5 L55.5,37.828 L7.5,71.5\' fill-opacity=\'0\' stroke=\'%2300adef\' stroke-width=\'12\' stroke-linecap=\'round\' stroke-linejoin=\'round\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E") no-repeat center;
	background-size: contain;
	content: " ";
}
',
            'globalJS' => '',
            'initJS' => '',
            'settings' => [
                'executeGlobalCodeBeforeUnblocking' => false,
                'saveThumbnails' => false,
                'autoplay' => false,
                'videoWrapper' => false,
            ],
            'status' => true,
            'undeletable' => true,
        ];
    }

    /**
     * getThumbnail function.
     *
     * @param mixed $videoId
     */
    public function getThumbnail($videoId)
    {
        // Default thumbnail in case a thumbnail can not be retrieved
        $thumbnailURL = BORLABS_COOKIE_PLUGIN_URL . 'assets/images/cb-no-thumbnail.png';

        // Path and filename of the requested thumbnail on the HDD
        $filename = ContentBlocker::getInstance()->getCacheFolder() . '/vimeo_' . $videoId . '.jpg';

        // URL of the requested thumbnail
        $webFilename = content_url() . '/cache/borlabs-cookie/vimeo_' . $videoId . '.jpg';

        // Check if thumbnail does not exist
        if (!file_exists($filename)) {
            // Only try to retrieve a thumbnail when the cache folder is writable
            if (is_writable(ContentBlocker::getInstance()->getCacheFolder())) {
                // Get oembed data from vimeo
                $oembedResponse = wp_remote_get(
                    'https://vimeo.com/api/oembed.json?url=' . urlencode('https://vimeo.com/' . $videoId)
                    . '&maxwidth=720'
                );

                if (!empty($oembedResponse['body']) && Tools::getInstance()->isStringJSON($oembedResponse['body'])) {
                    $videoData = json_decode($oembedResponse['body']);

                    // Check if thumbnail is available
                    if (!empty($videoData->thumbnail_url)) {
                        // Get image from Vimeo
                        $thumbnailResponse = wp_remote_get($videoData->thumbnail_url);

                        // Get the content-type, only jpeg is accepted
                        $contentType = wp_remote_retrieve_header($thumbnailResponse, 'content-type');

                        if (
                            !empty($thumbnailResponse) && is_array($thumbnailResponse)
                            && $contentType == 'image/jpeg'
                        ) {
                            // Save thumbnail locally
                            file_put_contents($filename, wp_remote_retrieve_body($thumbnailResponse));

                            // Update the thumbnail URL
                            $thumbnailURL = $webFilename;
                        }
                    }
                }
            }
        } else {
            // Thumbnail is already saved locally
            $thumbnailURL = $webFilename;
        }

        return $thumbnailURL;
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
        $contentBlockerData = ContentBlocker::getInstance()->getContentBlockerData('vimeo');

        // Default thumbnail
        $thumbnail = BORLABS_COOKIE_PLUGIN_URL . 'assets/images/cb-no-thumbnail.png';

        // Check if the thumbnail should be saved locally
        if (!empty($contentBlockerData['settings']['saveThumbnails'])) {
            // Get the video id out of the Vimeo URL
            $videoId = [];
            preg_match('/(video\/)?([0-9]{1,})/', ContentBlocker::getInstance()->getCurrentURL(), $videoId);

            // Try to get the thumbnail from Vimeo
            if (!empty($videoId[2])) {
                $thumbnail = $this->getThumbnail($videoId[2]);
            }
        }

        // Check if autoplay parameter should be added
        if (!empty($contentBlockerData['settings']['autoplay'])) {
            $content = preg_replace_callback(
                '/(\<p\>)?(<iframe.+?(?=<\/iframe>)<\/iframe>){1}(\<\/p\>)?/i',
                function ($tags) {
                    $srcMatch = [];
                    preg_match('/src=("|\')([^"\']{1,})(\1)/i', $tags[2], $srcMatch);

                    if (empty($srcMatch[2])) {
                        return $tags[0];
                    }

                    $urlInfo = parse_url($srcMatch[2]);
                    $query = [];

                    if (isset($urlInfo['query'])) {
                        parse_str($urlInfo['query'], $query);
                    }

                    if (!isset($query['autoplay'])) {
                        $query['autoplay'] = '1';
                    }

                    $tags[0] = str_replace(
                        $srcMatch[2],
                        $urlInfo['scheme'] . '://' . $urlInfo['host'] . $urlInfo['path'] . '?' . http_build_query(
                            $query
                        ),
                        $tags[0]
                    );

                    return $tags[0];
                },
                $content
            );

            // Overwrite the old blocked content with the modified version
            ContentBlocker::getInstance()->setCurrentBlockedContent($content);
        }

        // Fluid width video wrapper
        if (!empty($contentBlockerData['settings']['videoWrapper'])) {
            // Wrap wrap wrape di wrap wa wa wa wrap wrap wrape di wrap - I need more sleep...
            $content = '<div class="_brlbs-fluid-width-video-wrapper">' . $content . '</div>';

            // Overwrite the old blocked content with the modified version
            ContentBlocker::getInstance()->setCurrentBlockedContent($content);
        }

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
