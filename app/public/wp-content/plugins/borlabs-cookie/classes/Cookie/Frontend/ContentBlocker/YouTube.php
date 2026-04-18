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

class YouTube
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
        add_action(
            'borlabsCookie/contentBlocker/edit/template/settings/youtube',
            [$this, 'additionalSettingsTemplate']
        );
        add_action(
            'borlabsCookie/contentBlocker/edit/template/settings/help/youtube',
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
            'Thumbnail Sizes',
            'Backend / Content Blocker / YouTube / Tips / Headline',
            'borlabs-cookie'
        ); ?></h4>
                <p><?php
                    _ex(
            '<strong>High Quality</strong>: 480 x 360 px. This size is available in most cases.',
            'Backend / Content Blocker / YouTube / Tips / Text',
            'borlabs-cookie'
        ); ?>
                    <br>
                    <?php
                    _ex(
            '<strong>Medium Quality</strong>: 320 x 180 px.',
            'Backend / Content Blocker / YouTube / Tips / Text',
            'borlabs-cookie'
        ); ?>
                    <br>
                    <?php
                    _ex(
            '<strong>Standard Quality</strong>: 640 x 480 px.',
            'Backend / Content Blocker / YouTube / Tips / Text',
            'borlabs-cookie'
        ); ?>
                    <br>
                    <?php
                    _ex(
            '<strong>Maximum Resolution</strong>: 1280 x 720 px.',
            'Backend / Content Blocker / YouTube / Tips / Text',
            'borlabs-cookie'
        ); ?>
                </p>
                <h4><?php
                    _ex(
            'Video Wrapper',
            'Backend / Content Blocker / YouTube / Tips / Headline',
            'borlabs-cookie'
        ); ?></h4>
                <p><?php
                    _ex(
            'If the <strong>Video Wrapper</strong> option is enabled, the iframe of the video is placed in a container to prevent problems with the video display, e.g. small video size, wrong aspect ratio or large spacing above the video.',
            'Backend / Content Blocker / YouTube / Tips / Text',
            'borlabs-cookie'
        ); ?></p>
                <p><?php
                    _ex(
            'For themes that do not load the default Gutenberg CSS, this option must often be activated.',
            'Backend / Content Blocker / YouTube / Tips / Text',
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

        $optionThumbnailQualityHQDefault = !empty($data->settings['thumbnailQuality'])
        && $data->settings['thumbnailQuality'] === 'hqdefault' ? ' selected' : '';
        $optionThumbnailQualityMQDefault = !empty($data->settings['thumbnailQuality'])
        && $data->settings['thumbnailQuality'] === 'mqdefault' ? ' selected' : '';
        $optionThumbnailQualitySDDefault = !empty($data->settings['thumbnailQuality'])
        && $data->settings['thumbnailQuality'] === 'sddefault' ? ' selected' : '';
        $optionThumbnailQualityMaxResDefault = !empty($data->settings['thumbnailQuality'])
        && $data->settings['thumbnailQuality'] === 'maxresdefault' ? ' selected' : '';

        $inputChangeURLToNoCookie = !empty($data->settings['changeURLToNoCookie']) ? 1 : 0;
        $switchChangeURLToNoCookie = $inputChangeURLToNoCookie ? ' active' : '';

        $inputAutplay = !empty($data->settings['autoplay']) ? 1 : 0;
        $switchAutoplay = $inputAutplay ? ' active' : '';

        $inputVideoWrapper = !empty($data->settings['videoWrapper']) ? 1 : 0;
        $switchVideoWrapper = $inputVideoWrapper ? ' active' : ''; ?>
        <div class="form-group row align-items-center">
            <label for="saveThumbnails"
                   class="col-sm-4 col-form-label"><?php
                _ex(
            'Save thumbnails locally',
            'Backend / Content Blocker / YouTube / Label',
            'borlabs-cookie'
        ); ?></label>
            <div class="col-sm-8">
                <button type="button" class="btn btn-sm btn-toggle mr-2<?php
                echo $switchSaveThumbnails; ?>"
                        data-toggle="button" data-switch-target="saveThumbnails"
                        aria-pressed="<?php
                        echo $inputSaveThumbnails ? 'true' : 'false'; ?>">
                    <span class="handle"></span>
                </button>
                <input type="hidden" name="settings[saveThumbnails]" id="saveThumbnails"
                       value="<?php
                       echo $inputSaveThumbnails; ?>">
                <span data-toggle="tooltip"
                      title="<?php
                      echo esc_attr_x(
            'Attempts to get the thumbnail of the YouTube video to save it locally. Your visitor\'s IP-address will not be transferred to YouTube during this process.',
            'Backend / Content Blocker / YouTube / Tooltip',
            'borlabs-cookie'
        ); ?>"><i
                        class="fas fa-lg fa-question-circle text-dark"></i></span>
            </div>
        </div>

        <div class="form-group row">
            <label for="cookieBoxShow"
                   class="col-sm-4 col-form-label"><?php
                _ex(
            'Thumbnail size &amp; quality',
            'Backend / Content Blocker / YouTube / Label',
            'borlabs-cookie'
        ); ?></label>
            <div class="col-sm-8">
                <select class="form-control form-control form-control-sm d-inline-block w-75 mr-2"
                        name="settings[thumbnailQuality]" id="settings[thumbnailQuality]">
                    <option<?php
                    echo $optionThumbnailQualityHQDefault; ?>
                        value="hqdefault"><?php
                        _ex(
            'High Quality',
            'Backend / Content Blocker / YouTube / Select Option',
            'borlabs-cookie'
        ); ?></option>
                    <option<?php
                    echo $optionThumbnailQualityMQDefault; ?>
                        value="mqdefault"><?php
                        _ex(
            'Medium Quality',
            'Backend / Content Blocker / YouTube / Select Option',
            'borlabs-cookie'
        ); ?></option>
                    <option<?php
                    echo $optionThumbnailQualitySDDefault; ?>
                        value="sddefault"><?php
                        _ex(
            'Standard Quality',
            'Backend / Content Blocker / YouTube / Select Option',
            'borlabs-cookie'
        ); ?></option>
                    <option<?php
                    echo $optionThumbnailQualityMaxResDefault; ?>
                        value="maxresdefault"><?php
                        _ex(
            'Maximum Resolution',
            'Backend / Content Blocker / YouTube / Select Option',
            'borlabs-cookie'
        ); ?></option>
                </select>
                <span data-toggle="tooltip"
                      title="<?php
                      echo esc_attr_x(
            'If the thumbnail in the requested quality is not available the <strong>High Quality</strong> quality is used.',
            'Backend / Content Blocker / YouTube / Tooltip',
            'borlabs-cookie'
        ); ?>"><i
                        class="fas fa-lg fa-question-circle text-dark"></i></span>
            </div>
        </div>

        <div class="form-group row align-items-center">
            <label for="changeURLToNoCookie"
                   class="col-sm-4 col-form-label"><?php
                _ex(
            'Change URL to youtube-nocookie.com',
            'Backend / Content Blocker / YouTube / Label',
            'borlabs-cookie'
        ); ?></label>
            <div class="col-sm-8">
                <button type="button" class="btn btn-sm btn-toggle mr-2<?php
                echo $switchChangeURLToNoCookie; ?>"
                        data-toggle="button" data-switch-target="changeURLToNoCookie"
                        aria-pressed="<?php
                        echo $inputChangeURLToNoCookie ? 'true' : 'false'; ?>">
                    <span class="handle"></span>
                </button>
                <input type="hidden" name="settings[changeURLToNoCookie]" id="changeURLToNoCookie"
                       value="<?php
                       echo $inputChangeURLToNoCookie; ?>">
                <span data-toggle="tooltip"
                      title="<?php
                      echo esc_attr_x(
            'The YouTube URL of the iframe will be changed to www.youtube-nocookie.com.',
            'Backend / Content Blocker / YouTube / Tooltip',
            'borlabs-cookie'
        ); ?>"><i
                        class="fas fa-lg fa-question-circle text-dark"></i></span>
            </div>
        </div>

        <div class="form-group row align-items-center">
            <label for="autoplay"
                   class="col-sm-4 col-form-label"><?php
                _ex('Autoplay', 'Backend / Content Blocker / YouTube / Label', 'borlabs-cookie'); ?></label>
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
            'Backend / Content Blocker / YouTube / Tooltip',
            'borlabs-cookie'
        ); ?>"><i
                        class="fas fa-lg fa-question-circle text-dark"></i></span>
            </div>
        </div>

        <div class="form-group row align-items-center">
            <label for="videoWrapper"
                   class="col-sm-4 col-form-label"><?php
                _ex('Video Wrapper', 'Backend / Content Blocker / YouTube / Label', 'borlabs-cookie'); ?></label>
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
            'Backend / Content Blocker / YouTube / Tooltip',
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
            'contentBlockerId' => 'youtube',
            'name' => 'YouTube',
            'description' => '',
            'privacyPolicyURL' => _x(
                'https://policies.google.com/privacy?hl=en&amp;gl=en',
                'Frontend / Content Blocker / YouTube / URL',
                'borlabs-cookie'
            ),
            'hosts' => [
                'youtube.com',
                'youtu.be',
                'youtube-nocookie.com',
                'youtube.',
            ],
            'previewHTML' => '<div class="_brlbs-content-blocker">
	<div class="_brlbs-embed _brlbs-video-youtube">
    	<img class="_brlbs-thumbnail" src="%%thumbnail%%" alt="%%name%%">
		<div class="_brlbs-caption">
			<p>' . _x(
                "By loading the video, you agree to YouTube's privacy policy.",
                'Frontend / Content Blocker / YouTube / Text',
                'borlabs-cookie'
            ) . '<br><a href="%%privacy_policy_url%%" target="_blank" rel="nofollow noopener noreferrer">' . _x(
                'Learn more',
                'Frontend / Content Blocker / YouTube / Text',
                'borlabs-cookie'
            ) . '</a></p>
			<p><a class="_brlbs-btn _brlbs-icon-play-white" href="#" data-borlabs-cookie-unblock role="button">' . _x(
                'Load video',
                'Frontend / Content Blocker / YouTube / Text',
                'borlabs-cookie'
            ) . '</a></p>
			<p><label><input type="checkbox" name="unblockAll" value="1" checked> <small>' . _x(
                'Always unblock YouTube',
                'Frontend / Content Blocker / YouTube / Text',
                'borlabs-cookie'
            ) . '</small></label></p>
		</div>
	</div>
</div>',
            'previewCSS' => '.BorlabsCookie ._brlbs-video-youtube a._brlbs-btn {
	background: #ff0000;
	border-radius: 20px;
}

.BorlabsCookie ._brlbs-video-youtube a._brlbs-btn:hover {
	background: #fff;
	color: red;
}

.BorlabsCookie ._brlbs-video-youtube a._brlbs-btn._brlbs-icon-play-white:hover::before {
	background: url("data:image/svg+xml,%3Csvg version=\'1.1\' xmlns=\'http://www.w3.org/2000/svg\' xmlns:xlink=\'http://www.w3.org/1999/xlink\' x=\'0\' y=\'0\' width=\'78\' height=\'78\' viewBox=\'0, 0, 78, 78\'%3E%3Cg id=\'Layer_1\'%3E%3Cg%3E%3Cpath d=\'M7.5,71.5 L7.5,7.5 L55.5,37.828 L7.5,71.5\' fill=\'%23ff0000\'/%3E%3Cpath d=\'M7.5,71.5 L7.5,7.5 L55.5,37.828 L7.5,71.5\' fill-opacity=\'0\' stroke=\'%23ff0000\' stroke-width=\'12\' stroke-linecap=\'round\' stroke-linejoin=\'round\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E") no-repeat center;
	background-size: contain;
	content: " ";
}
',
            'globalJS' => '',
            'initJS' => '',
            'settings' => [
                'executeGlobalCodeBeforeUnblocking' => false,
                'changeURLToNoCookie' => true,
                'saveThumbnails' => false,
                'autoplay' => false,
                'thumbnailQuality' => 'maxresdefault',
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
        /*
            Plan B: https://www.googleapis.com/youtube/v3/videos?id={VIDEO_ID}&key={API_KEY}&part=snippet&fields=items/snippet/thumbnails
            Plan C: https://www.youtube.com/oembed?url=http%3A//youtube.com/watch%3Fv%3DM3r2XDceM6A&format=json
        */

        // Get settings of the Blocked Content Type
        $contentBlockerData = ContentBlocker::getInstance()->getContentBlockerData('youtube');

        // Default thumbnail in case a thumbnail can not be retrieved
        $thumbnailURL = BORLABS_COOKIE_PLUGIN_URL . 'assets/images/cb-no-thumbnail.png';

        // Set a default thumbnail quality if no quality was defined (which should not happen...)
        $thumbnailQuality = 'hqdefault';

        // Overwrite the default thumbnail quality with the configured one
        if (!empty($contentBlockerData['settings']['thumbnailQuality'])) {
            $thumbnailQuality = $contentBlockerData['settings']['thumbnailQuality'];
        }

        // Path and filename of the requested thumbnail on the HDD
        $filename = ContentBlocker::getInstance()->getCacheFolder() . '/' . $videoId . '_' . $thumbnailQuality . '.jpg';

        // URL of the requested thumbnail
        $webFilename = content_url() . '/cache/borlabs-cookie/' . $videoId . '_' . $thumbnailQuality . '.jpg';

        // Check if thumbnail does not exist
        if (!file_exists($filename)) {
            // Only try to retrieve a thumbnail when the cache folder is writable
            if (is_writable(ContentBlocker::getInstance()->getCacheFolder())) {
                // Get image from YouTube in the requested quality
                $response = wp_remote_get('https://img.youtube.com/vi/' . $videoId . '/' . $thumbnailQuality . '.jpg');

                // Check if YouTube has the requested quality, if not it returns 404
                $httpStatus = wp_remote_retrieve_response_code($response);

                // If YouTube does not have the requested quality and the configured quality is not hqdefault try to get hqdefault
                if ($httpStatus === 404 && $thumbnailQuality !== 'hqdefault') {
                    // Get image from YouTube in hqdefault quality
                    $response = wp_remote_get('https://img.youtube.com/vi/' . $videoId . '/hqdefault.jpg');

                    // Update the http status code - if again 404 the default thumbnail will be used
                    $httpStatus = wp_remote_retrieve_response_code($response);
                }

                // Get the content-type, only jpeg is accepted
                $contentType = wp_remote_retrieve_header($response, 'content-type');

                if (!empty($response) && is_array($response) && $httpStatus === 200 && $contentType == 'image/jpeg') {
                    // Save thumbnail locally, in the case of the use of the fallback quality we do not change the filename
                    file_put_contents($filename, wp_remote_retrieve_body($response));

                    // Update the thumbnail URL
                    $thumbnailURL = $webFilename;
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
        $contentBlockerData = ContentBlocker::getInstance()->getContentBlockerData('youtube');

        // Check if the URL should be changed to youtube-nocookie.com
        if (!empty($contentBlockerData['settings']['changeURLToNoCookie']) || !empty($atts['changeURLToNoCookie'])) {
            // Replace the host with the www.youtube-nocookie.com host
            // The host is not the oEmbed host like youtu.be - it is always www.youtube.com
            $content = str_replace('www.youtube.com', 'www.youtube-nocookie.com', $content);

            // Overwrite the old blocked content with the modified version
            ContentBlocker::getInstance()->setCurrentBlockedContent($content);
        }

        $youTubeURLQuery = [];
        // Third Party support
        if (isset($atts['rel'])) {
            $youTubeURLQuery['rel'] = (int) ($atts['rel']);
        }

        if (isset($atts['enablejsapi'])) {
            $youTubeURLQuery['enablejsapi'] = (int) ($atts['enablejsapi']);
        }

        if (isset($atts['origin'])) {
            $youTubeURLQuery['origin'] = urlencode($atts['origin']);
        }

        if (isset($atts['controls'])) {
            $youTubeURLQuery['controls'] = (int) ($atts['controls']);
        }

        if (isset($atts['playsinline'])) {
            $youTubeURLQuery['playsinline'] = (int) ($atts['playsinline']);
        }

        if (isset($atts['modestbranding'])) {
            $youTubeURLQuery['modestbranding'] = (int) ($atts['modestbranding']);
        }

        if (!empty($contentBlockerData['settings']['autoplay'])) {
            $youTubeURLQuery['autoplay'] = $contentBlockerData['settings']['autoplay'];
        }

        if (isset($atts['autoplay'])) {
            $youTubeURLQuery['autoplay'] = (int) ($atts['autoplay']);
        }

        if (isset($atts['loop'])) {
            $youTubeURLQuery['loop'] = (int) ($atts['loop']);
            $urlInfo = parse_url(ContentBlocker::getInstance()->getCurrentURL());

            if (!empty($urlInfo['query'])) {
                $query = [];
                parse_str($urlInfo['query'], $query);

                if (!empty($query['v'])) {
                    $youTubeURLQuery['playlist'] = $query['v'];
                    $query = [];
                }
            }
        }

        if (isset($atts['start'])) {
            $youTubeURLQuery['start'] = (int) ($atts['start']);
        }

        if (isset($atts['end'])) {
            $youTubeURLQuery['end'] = (int) ($atts['end']);
        }

        // Check if autoplay parameter should be added
        if (count($youTubeURLQuery)) {
            $content = preg_replace_callback(
                '/(\<p\>)?(<iframe.+?(?=<\/iframe>)<\/iframe>){1}(\<\/p\>)?/i',
                function ($tags) use ($youTubeURLQuery) {
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
                    $query = array_merge($query, $youTubeURLQuery);

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

        // Default thumbnail
        $thumbnail = BORLABS_COOKIE_PLUGIN_URL . 'assets/images/cb-no-thumbnail.png';

        // Check if the thumbnail should be saved locally
        if (!empty($contentBlockerData['settings']['saveThumbnails'])) {
            // Get the video id out of the YouTube URL
            $videoId = [];
            preg_match(
                '/(\.[a-z]{2,}\/)(embed\/|watch\?v=)?([a-zA-z0-9\-_]{1,})/',
                ContentBlocker::getInstance()->getCurrentURL(),
                $videoId
            );

            // Try to get the thumbnail from YouTube
            if (!empty($videoId[3])) {
                $thumbnail = $this->getThumbnail($videoId[3]);
            }
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
