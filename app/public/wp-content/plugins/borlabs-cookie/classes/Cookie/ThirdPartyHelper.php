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

namespace BorlabsCookie\Cookie;

use BorlabsCookie\Cookie\Backend\ContentBlocker as BackendContentBlocker;
use BorlabsCookie\Cookie\Backend\Cookies as BackendCookies;
use BorlabsCookie\Cookie\Backend\CSS;
use BorlabsCookie\Cookie\Frontend\ContentBlocker;
use BorlabsCookie\Cookie\Frontend\Cookies;
use BorlabsCookie\Cookie\Frontend\JavaScript;
use BorlabsCookie\Cookie\Frontend\Shortcode;

class ThirdPartyHelper
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
     * addBlockedContentType function. DEPRECATED.
     *
     * This functions allows you to add a new Blocked Content Type (BCT) to Borlabs Cookie.
     * The function will add your BCT in every language, if the site is using a multilanguage plugin.
     * The function also ensures that your BCT will only be added when it does not exist.
     *
     * @param string $typeId      : only lowercase
     * @param string $name        : title of your BCT
     * @param string $description : optional description
     * @param array  $hosts       : hosts for autodetection (for iframes or oEmbeds)
     * @param string $previewHTML : html code that is shown instead of the blocked content
     * @param string $globalJS    : global JavaScript, loaded once
     * @param string $initJS      : initialization JavaScript, executed everytime when a blocked content gets unblocked
     * @param array  $settings    (default: [])
     * @param bool   $status      (default: false)
     * @param bool   $undeletable (default: false): if true the user can not delete your BCT
     */
    public function addBlockedContentType(
        $typeId,
        $name,
        $description = '',
        $hosts = [],
        $previewHTML = '',
        $globalJS = '',
        $initJS = '',
        $settings = [],
        $status = false,
        $undeletable = false
    ) {
        if (preg_match('/^[a-z\-\_]{3,}$/', $typeId)) {
            $privacyPolicyURL = '';
            $previewCSS = '';

            // Check if my friend WPSL tries to add a Content Blocker
            if ($typeId === 'wpstorelocator') {
                $contentBlockerData = Frontend\ContentBlocker\GoogleMaps::getInstance()->getDefault();
                $privacyPolicyURL = $contentBlockerData['privacyPolicyURL'];
                $previewHTML = $contentBlockerData['previewHTML'];
                $previewCSS = $contentBlockerData['previewCSS'];
                $settings = [];
            }

            return $this->addContentBlocker(
                $typeId,
                $name,
                $description,
                $privacyPolicyURL,
                $hosts,
                $previewHTML,
                $previewCSS,
                $globalJS,
                $initJS,
                $settings,
                $status,
                $undeletable
            );
        }

        return false;
    }

    /**
     * addBlockedContentType function.
     *
     * This functions allows you to add a new Content Blocker (CB) to Borlabs Cookie.
     * The function will add your CB in every language, if the site is using a multilanguage plugin.
     * The function also ensures that your CB will only be added when it does not exist.
     *
     * @param string $contentBlockerId : only lowercase, - and _
     * @param string $name             : title of your CB
     * @param string $description      : optional description
     * @param string $privacyPolicyURL : privacy policy URL description
     * @param array  $hosts            : hosts for autodetection (for iframes or oEmbeds)
     * @param string $previewHTML      : html code that is shown instead of the blocked content
     * @param string $previewCSS       : css code that is used by the content blocker
     * @param string $globalJS         : global JavaScript, loaded once
     * @param string $initJS           : initialization JavaScript, executed everytime when a blocked content gets unblocked
     * @param array  $settings         (default: [])
     * @param bool   $status           (default: false)
     * @param bool   $undeletable      (default: false): if true the user can not delete your CB
     */
    public function addContentBlocker(
        $contentBlockerId,
        $name,
        $description = '',
        $privacyPolicyURL = '',
        $hosts = [],
        $previewHTML = '',
        $previewCSS = '',
        $globalJS = '',
        $initJS = '',
        $settings = [],
        $status = false,
        $undeletable = false
    ) {
        if (preg_match('/^[a-z\-\_]{3,}$/', $contentBlockerId)) {
            $contentBlockerData = [
                'contentBlockerId' => $contentBlockerId,
                'language' => '',
                'name' => $name,
                'description' => $description,
                'privacyPolicyURL' => $privacyPolicyURL,
                'hosts' => $hosts,
                'previewHTML' => $previewHTML,
                'previewCSS' => $previewCSS,
                'globalJS' => $globalJS,
                'initJS' => $initJS,
                'settings' => $settings,
                'status' => $status,
                'undeletable' => $undeletable,
            ];

            // Update Multilanguage
            $languageCodes = [];

            // Polylang
            if (defined('POLYLANG_VERSION')) {
                $polylangLanguages = get_terms('language', ['hide_empty' => false]);

                if (!empty($polylangLanguages)) {
                    foreach ($polylangLanguages as $languageData) {
                        if (!empty($languageData->slug) && is_string($languageData->slug)) {
                            $languageCodes[$languageData->slug] = $languageData->slug;
                        }
                    }
                }
            }

            // WPML
            if (defined('ICL_LANGUAGE_CODE')) {
                $wpmlLanguages = apply_filters('wpml_active_languages', null, []);

                if (!empty($wpmlLanguages)) {
                    foreach ($wpmlLanguages as $languageData) {
                        if (!empty($languageData['code'])) {
                            $languageCodes[$languageData['code']] = $languageData['code'];
                        }
                    }
                }
            }

            if (!empty($languageCodes)) {
                foreach ($languageCodes as $languageCode) {
                    $contentBlockerData['language'] = $languageCode;

                    BackendContentBlocker::getInstance()->add($contentBlockerData);

                    // Update CSS
                    CSS::getInstance()->save($languageCode);
                }
            } else {
                BackendContentBlocker::getInstance()->add($contentBlockerData);

                // Update CSS
                CSS::getInstance()->save();
            }

            return true;
        }

        return false;
    }

    /**
     * blockContent function.
     *
     * Lets you block any content and returns the preview code for the Content Blocker
     *
     * @param mixed  $content          : Your content you want to be blocked
     * @param mixed  $contentBlockerId : The Content Blocker id (content_blocker_id)
     * @param string $title            (default: ''): You can change the title for your blocked content
     */
    public function blockContent($content, $contentBlockerId, $title = '')
    {
        return ContentBlocker::getInstance()->handleContentBlocking($content, '', $contentBlockerId, $title);
    }

    /**
     * blockCookie function.
     *
     * Lets you block code (HTML & JavaScript) which is associated with a cookie.
     * Will be unblocked when the user gives their consent for this cookie.
     *
     * @param mixed $content
     * @param mixed $cookieId
     */
    public function blockCookie($content, $cookieId)
    {
        return Shortcode::getInstance()->handleTypeCookie(['type' => 'cookie', 'id' => $cookieId], $content);
    }

    /**
     * blockCookieGroup function.
     *
     * Lets you block code (HTML & JavaScript) which is associated with a cookie group.
     * Will be unblocked when the user gives their consent for this cookie group.
     *
     * @param mixed $content
     * @param mixed $cookieGroupId
     */
    public function blockCookieGroup($content, $cookieGroupId)
    {
        return Shortcode::getInstance()->handleTypeCookieGroup(
            ['type' => 'cookie-group', 'id' => $cookieGroupId],
            $content
        );
    }

    /**
     * blockIframes function.
     *
     * Detects iframes within a content and blocks them.
     *
     * @param mixed $content
     */
    public function blockIframes($content)
    {
        return ContentBlocker::getInstance()->detectIframes($content);
    }

    /**
     * deleteBlockedContentType function.
     *
     * Deletes a Blocked Content Type by its typeId.
     *
     * @param mixed $typeId
     * @param mixed $contentBlockerId
     */
    public function deleteBlockedContentType($contentBlockerId)
    {
        $this->deleteContentBlocker($contentBlockerId);

        return true;
    }

    /**
     * deleteContentBlocker function.
     *
     * Delete a Content Blocker by its content blocker id (content_blocker_id)
     *
     * @param mixed $contentBlockerId
     */
    public function deleteContentBlocker($contentBlockerId)
    {
        global $wpdb;

        $tableName = $wpdb->prefix . 'borlabs_cookie_legacy_content_blocker';

        $wpdb->query(
            '
            DELETE FROM
                `' . $tableName . "`
            WHERE
                `content_blocker_id` = '" . esc_sql($contentBlockerId) . "'
        "
        );

        return true;
    }

    /**
     * gaveConsent function.
     *
     * @param mixed $cookieId
     */
    public function gaveConsent($cookieId)
    {
        return Cookies::getInstance()->checkConsent($cookieId);
    }

    /**
     * getBlockedContentTypeDataByTypeId function.
     *
     * Get all information about a Blocked Content Type by its typeId
     *
     * @param mixed $typeId
     */
    public function getBlockedContentTypeDataByTypeId($typeId)
    {
        return ContentBlocker::getInstance()->getContentBlockerData($typeId);
    }

    /**
     * getContentBlockerData function.
     *
     * @param mixed $contentBlockerId
     */
    public function getContentBlockerData($contentBlockerId)
    {
        return ContentBlocker::getInstance()->getContentBlockerData($contentBlockerId);
    }

    /**
     * getContentBlockerDataById function.
     *
     * Get all information about a Content Blocker by its id.
     * Use this function during the validation process when a Content Blocker is being edited and about to be saved.
     *
     * @param mixed $Id
     * @param mixed $id
     */
    public function getContentBlockerDataById($id)
    {
        $contentBlockerData = BackendContentBlocker::getInstance()->get($id);

        return [
            'contentBlockerId' => $contentBlockerData->content_blocker_id,
            'name' => $contentBlockerData->name,
            'description' => $contentBlockerData->description,
            'privacyPolicyURL' => $contentBlockerData->privacy_policy_url,
            'hosts' => $contentBlockerData->hosts,
            'previewHTML' => $contentBlockerData->preview_html,
            'previewCSS' => $contentBlockerData->preview_css,
            'globalJS' => $contentBlockerData->global_js,
            'initJS' => $contentBlockerData->init_js,
            'settings' => $contentBlockerData->settings,
        ];
    }

    /**
     * getCookieData function.
     *
     * @param mixed $cookieId
     */
    public function getCookieData($cookieId)
    {
        $cookieData = [];

        $cookieData = BackendCookies::getInstance()->getByCookieId($cookieId);

        if (!empty($cookieData)) {
            $cookieData = [
                'cookieId' => $cookieData->cookie_id,
                'service' => $cookieData->service,
                'name' => $cookieData->name,
                'provider' => $cookieData->provider,
                'purpose' => $cookieData->purpose,
                'privacyPolicyURL' => $cookieData->privacy_policy_url,
                'hosts' => $cookieData->hosts,
                'cookieName' => $cookieData->cookie_name,
                'cookieExpiry' => $cookieData->cookie_expiry,
                'optInJS' => $cookieData->opt_in_js,
                'optOutJS' => $cookieData->opt_out_js,
                'fallbackJS' => $cookieData->fallback_js,
                'settings' => $cookieData->settings,
                'status' => $cookieData->status ? true : false,
            ];
        }

        return $cookieData;
    }

    // BACKWARDS COMPATIBILITY

    /**
     * getCurrentTitleOfBlockedContentType function.
     *
     * This function returns the title of the current blocked content.
     * It is only available and should only be used within the filter "borlabsCookie/bct/modify_content/{typeId}".
     */
    public function getCurrentTitleOfBlockedContentType()
    {
        return $this->getCurrentTitleOfContentBlocker();
    }

    /**
     * getCurrentTitleOfContentBlocker function.
     */
    public function getCurrentTitleOfContentBlocker()
    {
        return ContentBlocker::getInstance()->getCurrentTitle();
    }

    /**
     * setCurrentBlockedContent function.
     *
     * @param mixed $content
     */
    public function setCurrentBlockedContent($content)
    {
        return ContentBlocker::getInstance()->setCurrentBlockedContent($content);
    }

    /**
     * updateBlockedContentTypeJavaScript function.
     *
     * This function lets you update the JavaScript and settings of your Blocked Content Type during the process when
     * WordPress delivers a page. This function does not update the JavaScript and settings of the Blocked Content Type
     * in general!
     *
     * @param mixed  $typeId
     * @param string $globalJS (default: '')
     * @param string $initJS   (default: '')
     * @param mixed  $settings (default: [])
     */
    public function updateBlockedContentTypeJavaScript($typeId, $globalJS = '', $initJS = '', $settings = [])
    {
        return $this->updateContentBlockerJavaScript($typeId, $globalJS, $initJS, $settings);
    }

    /**
     * updateContentBlockerJavaScript function.
     *
     * @param mixed  $contentBlockerId
     * @param string $globalJS         (default: '')
     * @param string $initJS           (default: '')
     * @param mixed  $settings         (default: [])
     */
    public function updateContentBlockerJavaScript($contentBlockerId, $globalJS = '', $initJS = '', $settings = [])
    {
        return JavaScript::getInstance()->addContentBlocker($contentBlockerId, $globalJS, $initJS, $settings);
    }
}
