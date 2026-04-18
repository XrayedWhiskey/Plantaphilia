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

use BorlabsCookie\Cookie\Tools;
use stdClass;

class ScriptBlocker
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
     * detectedHandles.
     *
     * (default value: [])
     *
     * @var mixed
     */
    private $detectedHandles
        = [
            'matchedSearchPhrase' => [],
            'notMatchedSearchPhrase' => [],
        ];

    /**
     * detectedJavaScriptTags.
     *
     * @var mixed
     */
    private $detectedJavaScriptTags
        = [
            'matchedSearchPhrase' => [],
            'notMatchedSearchPhrase' => [],
        ];

    /**
     * scriptBlocker.
     *
     * (default value: [])
     *
     * @var mixed
     */
    private $scriptBlocker = [];

    /**
     * searchPhrases.
     *
     * (default value: [])
     *
     * @var mixed
     */
    private $searchPhrases = [];

    /**
     * statusScanActive.
     *
     * (default value: false)
     *
     * @var bool
     */
    private $statusScanActive = false;

    /**
     * wordpressIncludesURL.
     *
     * (default value: '')
     *
     * @var string
     */
    private $wordpressIncludesURL = '';

    /**
     * wordpressPluginsURL.
     *
     * (default value: '')
     *
     * @var string
     */
    private $wordpressPluginsURL = '';

    /**
     * wordpressSiteURL.
     *
     * @var mixed
     */
    private $wordpressSiteURL = '';

    /**
     * wordpressThemesURL.
     *
     * (default value: '')
     *
     * @var string
     */
    private $wordpressThemesURL = '';

    public function __construct()
    {
        // Check if scan is enabled
        if (get_option('BorlabsCookieLegacyScanJavaScripts', false)) {
            // Only scan the selected page
            if (
                !empty($_POST['borlabsCookie']['scanJavaScripts'])
                || !empty($_GET['__borlabsCookieScanJavaScripts'])
            ) {
                $this->statusScanActive = true;

                $this->searchPhrases = get_option('BorlabsCookieLegacyJavaScriptSearchPhrases', false);
            }
        }

        // Get all active script blocker
        $this->getScriptBlocker();

        $this->wordpressIncludesURL = includes_url();
        $this->wordpressPluginsURL = plugins_url();
        $this->wordpressSiteURL = get_site_url();
        $this->wordpressThemesURL = get_theme_root_uri();
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
     * blockHandles function.
     *
     * @param mixed $tag
     * @param mixed $handle
     * @param mixed $src
     */
    public function blockHandles($tag, $handle, $src)
    {
        if (Buffer::getInstance()->isBufferActive()) {
            if (!empty($this->scriptBlocker)) {
                foreach ($this->scriptBlocker as $data) {
                    if (!empty($data->handles)) {
                        if (
                            $handle !== 'borlabs-cookie' && $handle !== 'borlabs-cookie-prioritize'
                            && in_array(
                                $handle,
                                $data->handles,
                                true
                            )
                        ) {
                            $tag = str_replace(
                                [
                                    'text/javascript',
                                    'application/javascript',
                                    '<script',
                                    'src=',
                                ],
                                [
                                    'text/template',
                                    'text/template',
                                    '<script data-borlabs-script-blocker-js-handle="' . $handle
                                    . '" data-borlabs-script-blocker-id="' . $data->scriptBlockerId . '"',
                                    'data-borlabs-script-blocker-src=',
                                ],
                                $tag
                            );
                            // Remove async or defer attribute
                            if (strpos($tag, 'data-borlabs-script-blocker-src=') !== false) {
                                $tag = preg_replace('/\s(defer|async)=[\'"](defer|async)[\'"]/', '', $tag);
                                $tag = preg_replace('/((\s+)(?:defer|async)([\s>]))/', '$3', $tag);
                            }
                        }
                    }
                }
            }
        }

        return $tag;
    }

    /**
     * blockJavaScriptTag function.
     *
     * @param array $matches
     */
    public function blockJavaScriptTag($matches)
    {
        if (empty($this->scriptBlocker)) {
            return $matches[0];
        }

        /** @var string $wholeScriptTag */
        $wholeScriptTag = $matches[0];
        /** @var string $scriptTagSignature */
        $scriptTagSignature = $matches[1];
        /** @var string $scriptTagContent */
        $scriptTagContent = $matches[2];

        foreach ($this->scriptBlocker as $data) {
            if (!empty($data->blockPhrases)) {
                foreach ($data->blockPhrases as $blockPhrase) {
                    if (
                        strpos($wholeScriptTag, $blockPhrase) !== false
                        && strpos(
                            $wholeScriptTag,
                            'borlabsCookieConfig'
                        ) === false
                        && strpos($wholeScriptTag, 'borlabsCookiePrioritized') === false
                        && strpos($wholeScriptTag, 'borlabsCookieContentBlocker') === false
                    ) {
                        // Detect if script is of type javascript
                        $scriptTypeMatches = [];
                        preg_match('/type=["\']([^"\']*)["\']/', $scriptTagSignature, $scriptTypeMatches);
                        $scriptType = !empty($scriptTypeMatches) && !empty($scriptTypeMatches[1]) ? strtolower(
                            $scriptTypeMatches[1]
                        ) : null;

                        // Only <script>-tags without type attribute or with type attribute text/javascript are JavaScript
                        if (
                            $scriptType === null || $scriptType === 'text/javascript'
                            || $scriptType === 'application/javascript'
                        ) {
                            // Add type attribute if missing
                            if ($scriptType === null) {
                                $scriptTagSignature = ' type=\'text/template\'' . $scriptTagSignature;
                            } else {
                                $scriptTagSignature = preg_replace(
                                    '/text\/javascript/',
                                    'text/template',
                                    $scriptTagSignature,
                                    1
                                );
                                $scriptTagSignature = preg_replace(
                                    '/application\/javascript/',
                                    'text/template',
                                    $scriptTagSignature,
                                    1
                                );
                            }

                            // Switch type attribute and add data attribute
                            $scriptTagSignature = ' data-borlabs-script-blocker-id=\'' . $data->scriptBlockerId . '\''
                                . $scriptTagSignature;

                            // Handle script tags with src attribute (externally loaded)
                            $scriptTagSignature = preg_replace(
                                '/(\s)src=(["\'])/',
                                '$1data-borlabs-script-blocker-src=$2',
                                $scriptTagSignature,
                                1
                            );

                            if (strpos($scriptTagSignature, 'data-borlabs-script-blocker-src=') !== false) {
                                // Remove async or defer attribute
                                $scriptTagSignature = preg_replace(
                                    '/(\s+)(defer|async)=[\'"](defer|async|true|)[\'"]/',
                                    '',
                                    $scriptTagSignature
                                );
                                $scriptTagSignature = preg_replace(
                                    '/(\s+)(?:defer|async)/',
                                    '',
                                    $scriptTagSignature
                                );
                            }
                        }
                    }
                }
            }
        }

        return '<script' . $scriptTagSignature . '>' . $scriptTagContent . '</script>';
    }

    /**
     * checkDetectedJavaScriptTags function.
     *
     * @param mixed $tag
     */
    public function checkDetectedJavaScriptTags($tag)
    {
        // Detect if script is of type javascript
        $scriptType = [];
        preg_match('/\<script([^\>]*)type=("|\')([^"\']*)("|\')/Us', $tag[0], $scriptType);

        // Only <script>-tags without type attribute or with type attribute text/javascript are JavaScript
        if (
            empty($scriptType)
            || !empty($scriptType)
            && (strtolower($scriptType[3]) == 'text/javascript'
                || strtolower($scriptType[3]) == 'application/javascript')
        ) {
            $scriptSrc = [];
            preg_match('/<script(.*?)src=("|\')([^"\']*)("|\')/', $tag[0], $scriptSrc);

            $allDetectedHandles = Tools::getInstance()->arrayFlat($this->detectedHandles);

            if (empty($scriptSrc[3]) || !in_array($scriptSrc[3], $allDetectedHandles, true)) {
                $searchPhraseMatch = $this->checkForSearchPhraseMatch($tag[0]);

                if ($searchPhraseMatch['matched']) {
                    $this->detectedJavaScriptTags['matchedSearchPhrase'][] = [
                        'matchedPhrase' => $searchPhraseMatch['matchedPhrase'],
                        'scriptTag' => $tag[0],
                    ];
                } else {
                    $this->detectedJavaScriptTags['notMatchedSearchPhrase'][] = [
                        'scriptTag' => $tag[0],
                    ];
                }
            }
        }
    }

    /**
     * checkForSearchPhraseMatch function.
     *
     * @param mixed $source
     */
    public function checkForSearchPhraseMatch($source)
    {
        $data = [
            'matched' => false,
            'matchedPhrase' => '',
        ];

        if (!empty($this->searchPhrases)) {
            foreach ($this->searchPhrases as $phrase) {
                if (strpos($source, $phrase) !== false) {
                    $data['matched'] = true;
                    $data['matchedPhrase'] = $phrase;

                    break;
                }
            }
        }

        return $data;
    }

    /**
     * detectHandles function.
     *
     * @param mixed $tag
     * @param mixed $handle
     * @param mixed $src
     */
    public function detectHandles($tag, $handle, $src)
    {
        global $wp;

        // Check if scan is enabled
        if ($this->statusScanActive) {
            // Check handle
            $searchPhraseMatch = $this->checkForSearchPhraseMatch($handle);

            $scriptType = '';

            if (strpos($src, $this->wordpressThemesURL) !== false) {
                $scriptType = 'theme';
            } else {
                if (strpos($src, $this->wordpressPluginsURL) !== false) {
                    $scriptType = 'plugin';
                } else {
                    if (strpos($src, $this->wordpressIncludesURL) !== false) {
                        $scriptType = 'core';
                    } else {
                        if (strpos($src, $this->wordpressSiteURL) !== false) {
                            $scriptType = 'other';
                        } else {
                            $scriptType = 'external';
                        }
                    }
                }
            }

            if ($searchPhraseMatch['matched']) {
                $this->detectedHandles['matchedSearchPhrase'][$handle] = [
                    'matchedPhrase' => $searchPhraseMatch['matchedPhrase'],
                    'handle' => $handle,
                    'src' => $src,
                ];
            } else {
                // Fallback - check src
                $searchPhraseMatch = $this->checkForSearchPhraseMatch($src);

                if ($searchPhraseMatch['matched']) {
                    $this->detectedHandles['matchedSearchPhrase'][$handle] = [
                        'matchedPhrase' => $searchPhraseMatch['matchedPhrase'],
                        'handle' => $handle,
                        'src' => $src,
                    ];
                } else {
                    $this->detectedHandles['notMatchedSearchPhrase'][$scriptType][$handle] = [
                        'handle' => $handle,
                        'src' => $src,
                    ];
                }
            }
        }

        return $tag;
    }

    /**
     * detectJavaScriptsTags function.
     */
    public function detectJavaScriptsTags()
    {
        // Check if scan is enabled
        if ($this->statusScanActive) {
            if (Buffer::getInstance()->isBufferActive()) {
                $buffer = &Buffer::getInstance()->getBuffer();

                preg_replace_callback('/<script.*<\/script>/Us', [$this, 'checkDetectedJavaScriptTags'], $buffer);
            }
        }
    }

    /**
     * getScriptBlocker function.
     */
    public function getScriptBlocker()
    {
        global $wpdb;

        if (ScannerRequest::getInstance()->isAuthorized() && ScannerRequest::getInstance()->noScriptBlocker()) {
            return;
        }

        $tableName = $wpdb->prefix . 'borlabs_cookie_legacy_script_blocker';

        $scriptBlocker = $wpdb->get_results(
            '
            SELECT
                `script_blocker_id`,
                `handles`,
                `js_block_phrases`
            FROM
                `' . $tableName . '`
            WHERE
                `status` = 1
        '
        );

        if (!empty($scriptBlocker)) {
            foreach ($scriptBlocker as $key => $data) {
                $this->scriptBlocker[$key] = new stdClass();
                $this->scriptBlocker[$key]->scriptBlockerId = $scriptBlocker[$key]->script_blocker_id;
                $this->scriptBlocker[$key]->handles = unserialize($scriptBlocker[$key]->handles);
                $this->scriptBlocker[$key]->blockPhrases = unserialize($scriptBlocker[$key]->js_block_phrases);
            }
        }
    }

    /**
     * handleJavaScriptTagBlocking function.
     */
    public function handleJavaScriptTagBlocking()
    {
        if (Buffer::getInstance()->isBufferActive()) {
            $buffer = &Buffer::getInstance()->getBuffer();

            $buffer = preg_replace_callback('/<script([^>]*)>(.*)<\/script>/Us', [$this, 'blockJavaScriptTag'], $buffer);

            Buffer::getInstance()->endBuffering();
        }
    }

    /**
     * hasScriptBlocker function.
     */
    public function hasScriptBlocker()
    {
        return !empty($this->scriptBlocker) ? true : false;
    }

    /**
     * isScanActive function.
     */
    public function isScanActive()
    {
        return $this->statusScanActive;
    }

    /**
     * saveDetectedJavaScripts function.
     */
    public function saveDetectedJavaScripts()
    {
        // Check if scan is enabled
        if ($this->statusScanActive) {
            if (
                !empty($this->detectedHandles['matchedSearchPhrase'])
                || !empty($this->detectedHandles['notMatchedSearchPhrase'])
                || !empty($this->detectedJavaScripts['matchedSearchPhrase'])
                || !empty($this->detectedJavaScripts['notMatchedSearchPhrase'])
            ) {
                update_option(
                    'BorlabsCookieLegacyDetectedJavaScripts',
                    [
                        'handles' => $this->detectedHandles,
                        'scriptTags' => $this->detectedJavaScriptTags,
                    ],
                    'no'
                );
            }

            // Disable JavaScript scan
            update_option('BorlabsCookieLegacyScanJavaScripts', false, 'no');
        }
    }
}
