<?php
/*
 *  Copyright (c) 2026 Borlabs GmbH. All rights reserved.
 *  This file may not be redistributed in whole or significant part.
 *  Content of this file is protected by international copyright laws.
 *
 *  ----------------- Borlabs Cookie IS NOT FREE SOFTWARE -----------------
 *
 *  @copyright Borlabs GmbH, https://borlabs.io
 */

declare(strict_types=1);

namespace Borlabs\Cookie\System\WordPressFrontendDriver;

use Borlabs\Cookie\Adapter\WpFunction;
use Borlabs\Cookie\Dto\System\RequestDto;
use Borlabs\Cookie\Support\Sanitizer;
use Borlabs\Cookie\System\CompatibilityPatch\CompatibilityPatchManager;
use Borlabs\Cookie\System\Config\DialogSettingsConfig;
use Borlabs\Cookie\System\Config\GeneralConfig;
use Borlabs\Cookie\System\ContentBlocker\ContentBlockerManager;
use Borlabs\Cookie\System\CookieBlocker\CookieBlockerService;
use Borlabs\Cookie\System\Dialog\Dialog;
use Borlabs\Cookie\System\Language\Language;
use Borlabs\Cookie\System\License\License;
use Borlabs\Cookie\System\LocalScanner\LocalScanner;
use Borlabs\Cookie\System\LocalScanner\ScanRequestService;
use Borlabs\Cookie\System\ResourceEnqueuer\ResourceEnqueuer;
use Borlabs\Cookie\System\SafeMode\SafeMode;
use Borlabs\Cookie\System\ScriptBlocker\ScriptBlockerManager;
use Borlabs\Cookie\System\Shortcode\ShortcodeHandler;
use Borlabs\Cookie\System\StyleBlocker\StyleBlockerManager;

final class WordPressFrontendInit
{
    private CompatibilityPatchManager $compatibilityPatchManager;

    private ContentBlockerManager $contentBlockerManager;

    private ControllerManager $controllerManager;

    private CookieBlockerService $cookieBlockerService;

    private Dialog $dialog;

    private DialogSettingsConfig $dialogSettingsConfig;

    private GeneralConfig $generalConfig;

    private HtmlOutputManager $htmlOutputManager;

    private Language $language;

    private License $license;

    private LocalScanner $localScanner;

    private OutputBufferManager $outputBufferManager;

    private ResourceEnqueuer $resourceEnqueuer;

    private SafeMode $safeMode;

    private ScanRequestService $scanRequestService;

    private ScriptBlockerManager $scriptBlockerManager;

    private ShortcodeHandler $shortcodeHandler;

    private StyleBlockerManager $styleBlockerManager;

    private WordPressFrontendResources $wordPressFrontendResources;

    private WpFunction $wpFunction;

    public function __construct(
        CompatibilityPatchManager $compatibilityPatchManager,
        ContentBlockerManager $contentBlockerManager,
        ControllerManager $controllerManager,
        CookieBlockerService $cookieBlockerService,
        Dialog $dialog,
        DialogSettingsConfig $dialogSettingsConfig,
        GeneralConfig $generalConfig,
        HtmlOutputManager $htmlOutputManager,
        Language $language,
        License $license,
        LocalScanner $localScanner,
        OutputBufferManager $outputBufferManager,
        ResourceEnqueuer $resourceEnqueuer,
        SafeMode $safeMode,
        ScanRequestService $scanRequestService,
        ScriptBlockerManager $scriptBlockerManager,
        ShortcodeHandler $shortCodeHandler,
        StyleBlockerManager $styleBlockerManager,
        WordPressFrontendResources $wordPressFrontendResources,
        WpFunction $wpFunction
    ) {
        $this->compatibilityPatchManager = $compatibilityPatchManager;
        $this->contentBlockerManager = $contentBlockerManager;
        $this->controllerManager = $controllerManager;
        $this->cookieBlockerService = $cookieBlockerService;
        $this->dialog = $dialog;
        $this->dialogSettingsConfig = $dialogSettingsConfig;
        $this->generalConfig = $generalConfig;
        $this->htmlOutputManager = $htmlOutputManager;
        $this->language = $language;
        $this->license = $license;
        $this->localScanner = $localScanner;
        $this->outputBufferManager = $outputBufferManager;
        $this->resourceEnqueuer = $resourceEnqueuer;
        $this->safeMode = $safeMode;
        $this->scanRequestService = $scanRequestService;
        $this->scriptBlockerManager = $scriptBlockerManager;
        $this->shortcodeHandler = $shortCodeHandler;
        $this->styleBlockerManager = $styleBlockerManager;
        $this->wordPressFrontendResources = $wordPressFrontendResources;
        $this->wpFunction = $wpFunction;
    }

    public function register(): void
    {
        // Set current request
        $request = new RequestDto(
            Sanitizer::requestData($_POST),
            Sanitizer::requestData($_GET),
            Sanitizer::requestData($_SERVER),
        );
        // Detect language and load text domain.
        $this->language->setInitializationSignal();
        $this->language->init();
        $this->language->loadTextDomain();

        if (!$this->license->isPluginUnlocked()) {
            return;
        }

        // Register Frontend Controllers capable of handling the current request
        $this->controllerManager->init();

        // Disable Borlabs Cookie if a scan request requires it or if Borlabs Cookie is disabled for this page.
        if ($this->scanRequestService->noBorlabsCookie() || $this->isBorlabsCookieDisabledForThisPage()) {
            // Hide shortcodes if Borlabs Cookie should be disabled for this request.
            $this->wpFunction->addShortcode('borlabs-cookie', function ($atts, $content = null) {
                return '';
            });

            return;
        }

        // Initialize Borlabs Cookie
        if (
            $this->generalConfig->get()->borlabsCookieStatus
            || (
                $this->generalConfig->get()->setupMode
                && ($this->wpFunction->currentUserCan('manage_borlabs_cookie') || $this->scanRequestService->isScanRequest())
            )
        ) {
            $this->localScanner->init();
            $this->safeMode->handle($request);
            $this->compatibilityPatchManager->loadPatches();

            if ($this->compatibilityPatchManager->shouldSkipInitialization() === true) {
                return;
            }

            $this->compatibilityPatchManager->initPatches();
            $this->contentBlockerManager->init();
            $this->scriptBlockerManager->init();
            $this->styleBlockerManager->init();
            $this->cookieBlockerService->init();

            // Register resources
            $this->wpFunction->addAction('wp_enqueue_scripts', [$this->wordPressFrontendResources, 'registerHeadResources']);
            $this->wpFunction->addAction('wp_enqueue_scripts', [$this->wordPressFrontendResources, 'registerJavaScriptModules']);
            $this->wpFunction->addAction('wp_head', [$this->wordPressFrontendResources, 'outputHeadCode']);

            /*
             * WordPress triggers `wp_start_template_enhancement_output_buffer` via the
             * `wp_before_include_template` action (priority 1000). Inside this callback,
             * WordPress checks whether `wp_should_output_buffer_template_for_enhancement()`
             * returns `true`.
             *
             * If it does, WordPress fires the `wp_template_enhancement_output_buffer_started`
             * action. We hook into this to set `useWordPressOutputBuffer` to `true`, which
             * tells the OutputBufferManager to rely on WordPress’ native output buffer.
             *
             * Later, when `wp_before_include_template` runs again at priority 19021987,
             * the `useWordPressOutputBuffer` flag is already determined. Depending on its
             * value, we either use WordPress’ output buffer or fall back to our own.
             *
             * Note: The `template_redirect` hook fires earlier than `wp_before_include_template`.
             * The `wp_before_include_template` hook was introduced in WordPress 6.9.
             */
            // WordPress 6.9+
            $this->wpFunction->addAction('wp_template_enhancement_output_buffer_started', [$this->outputBufferManager, 'setWordPressOutputBufferStarted',]);
            $this->wpFunction->addAction('wp_before_include_template', function () {
                $this->outputBufferManager->startBuffering();

                if ($this->outputBufferManager->isWordPressOutputBufferActive()) {
                    $this->wpFunction->addFilter('wp_template_enhancement_output_buffer', [$this->htmlOutputManager, 'handleWithWordPressBuffer'], 19021987); // Late but not latest
                } else {
                    $this->wpFunction->addAction('wp_footer', [$this->htmlOutputManager, 'handleWithNativeBuffer'], 19021987); // Late but not latest
                }
            }, 19021987);

            // WordPress <6.9
            if (version_compare((string) $this->wpFunction->getWpVersion(), '6.9', '<')) {
                $this->wpFunction->addAction('template_redirect', function () {
                    $this->outputBufferManager->startBuffering();

                    $this->wpFunction->addAction('wp_footer', [$this->htmlOutputManager, 'handleWithNativeBuffer'], 19021987); // Late but not latest
                }, 19021987);
            }

            $this->wpFunction->addFilter('script_loader_tag', [$this->scriptBlockerManager, 'blockHandle'], 999, 3);
            $this->wpFunction->addFilter('style_loader_tag', [$this->styleBlockerManager, 'blockHandle'], 999, 3);

            // Add Frontend Shortcodes Support
            $this->wpFunction->addShortcode('borlabs-cookie', [$this->shortcodeHandler, 'handle']);

            $this->wpFunction->addFilter('the_content', [$this->contentBlockerManager, 'detectIframes'], 100, 1);
            $this->wpFunction->addFilter('embed_oembed_html', [$this->contentBlockerManager, 'handleOembedBlocking'], 100, 4);
            $this->wpFunction->addFilter('render_block', [$this->contentBlockerManager, 'detectIframes'], 100, 1);
            $this->wpFunction->addFilter('widget_custom_html_content', [$this->contentBlockerManager, 'detectIframes'], 100, 1);
            $this->wpFunction->addFilter('widget_text_content', [$this->contentBlockerManager, 'detectIframes'], 100, 1);
            $this->wpFunction->addFilter('widget_block_content', [$this->contentBlockerManager, 'detectIframes'], 100, 1);
            $this->wpFunction->addFilter('script_loader_tag', [$this->wordPressFrontendResources, 'transformScriptTagsToModules'], 100, 2);
            $this->wpFunction->addFilter('script_loader_tag', [$this->wordPressFrontendResources, 'addAttributeNoCloudflareAsync'], 100, 2);
            $this->wpFunction->addFilter('script_loader_tag', [$this->wordPressFrontendResources, 'addAttributeNoMinify'], 100, 2);
            $this->wpFunction->addFilter('script_loader_tag', [$this->wordPressFrontendResources, 'addAttributeNoOptimize'], 100, 2);
            $this->wpFunction->addFilter('wp_script_attributes', [$this->wordPressFrontendResources, 'addAttributesToModuleScriptTags',], 100);

            // Embed Cookie Box
            $this->wpFunction->addAction('wp_footer', [$this->dialog, 'output']);

            if ($this->dialogSettingsConfig->get()->showDialogOnLoginPage === true) {
                // For WP 6.5: JS modules are deliberately not loaded on login page. We need to include our JS via scripts instead:
                $this->wpFunction->addAction('login_enqueue_scripts', [$this->resourceEnqueuer, 'enableClassicScriptEnqueue']);
                $this->wpFunction->addAction('login_enqueue_scripts', [$this->wordPressFrontendResources, 'registerHeadResources']);
                $this->wpFunction->addAction('login_head', [$this->wordPressFrontendResources, 'registerJavaScriptModules'], 8);
                $this->wpFunction->addAction('login_head', [$this->wordPressFrontendResources, 'outputHeadCode']);
                $this->wpFunction->addAction('login_footer', [$this->dialog, 'output']);
            }
        }
    }

    private function getRequestedUrl(): string
    {
        // Determine the protocol
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            $protocol = 'https://';
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_PROTO'])) {
            // Check if protocol is forwarded by the load balancer
            $protocol = $_SERVER['HTTP_X_FORWARDED_PROTO'] . '://';
        } else {
            $protocol = 'http://';
        }

        // Determine the host (handle forwarded headers if behind a load balancer)
        $host = ''; // command line or cron job

        if (!empty($_SERVER['HTTP_X_FORWARDED_HOST'])) {
            $host = $_SERVER['HTTP_X_FORWARDED_HOST'];
        } elseif (!empty($_SERVER['HTTP_HOST'])) {
            // Use regular host header
            $host = $_SERVER['HTTP_HOST'];
        } elseif (!empty($_SERVER['SERVER_NAME'])) {
            $host = $_SERVER['SERVER_NAME'];
        }

        return $protocol . $host . ($_SERVER['REQUEST_URI'] ?? '');
    }

    private function isBorlabsCookieDisabledForThisPage(): bool
    {
        $status = false;
        $requestedUrl = $this->getRequestedUrl();

        foreach ($this->generalConfig->get()->disableBorlabsCookieOnPages as $url) {
            if (fnmatch($url, $requestedUrl)) {
                $status = true;

                break;
            }
        }

        return $status;
    }
}
