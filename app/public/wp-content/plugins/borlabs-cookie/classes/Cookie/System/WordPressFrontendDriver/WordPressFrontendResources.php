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
use Borlabs\Cookie\Localization\DebugConsole\DebugConsoleLocalizationStrings;
use Borlabs\Cookie\Repository\Service\ServiceRepository;
use Borlabs\Cookie\System\Config\BackwardsCompatibilityConfig;
use Borlabs\Cookie\System\Config\IabTcfConfig;
use Borlabs\Cookie\System\Config\PluginConfig;
use Borlabs\Cookie\System\FileSystem\CacheFolder;
use Borlabs\Cookie\System\Language\Language;
use Borlabs\Cookie\System\LocalScanner\ScanRequestService;
use Borlabs\Cookie\System\Option\Option;
use Borlabs\Cookie\System\ResourceEnqueuer\ResourceEnqueuer;
use Borlabs\Cookie\System\Script\FallbackCodeManager;
use Borlabs\Cookie\System\Script\ScriptConfigBuilder;
use Borlabs\Cookie\System\Style\StyleBuilder;

class WordPressFrontendResources
{
    public const BORLABS_COOKIE_HANDLES = [
        'borlabs-cookie-config',
        'borlabs-cookie-core',
        'borlabs-cookie-debug-console',
        'borlabs-cookie-legacy-backward-compatibility',
        'borlabs-cookie-noop',
        'borlabs-cookie-prioritize',
        'borlabs-cookie-stub',
    ];

    /**
     * Used by WordPress 6.5 and higher.
     */
    public const BORLABS_COOKIE_MODULES = [
        'borlabs-cookie-core-js-module',
        'borlabs-cookie-debug-console-js-module',
        'borlabs-cookie-legacy-backward-compatibility-module',
    ];

    private BackwardsCompatibilityConfig $backwardsCompatibilityConfig;

    private CacheFolder $cacheFolder;

    private FallbackCodeManager $fallbackCodeManager;

    private IabTcfConfig $iabTcfConfig;

    private Language $language;

    private Option $option;

    private PluginConfig $pluginConfig;

    private ResourceEnqueuer $resourceEnqueuer;

    private ScanRequestService $scanRequestService;

    private ScriptConfigBuilder $scriptConfigBuilder;

    private ServiceRepository $serviceRepository;

    private StyleBuilder $styleBuilder;

    private WpFunction $wpFunction;

    public function __construct(
        BackwardsCompatibilityConfig $backwardsCompatibilityConfig,
        CacheFolder $cacheFolder,
        FallbackCodeManager $fallbackCodeManager,
        IabTcfConfig $iabTcfConfig,
        Language $language,
        Option $option,
        PluginConfig $pluginConfig,
        ResourceEnqueuer $resourceEnqueuer,
        ScanRequestService $scanRequestService,
        ScriptConfigBuilder $scriptConfigBuilder,
        ServiceRepository $serviceRepository,
        StyleBuilder $styleBuilder,
        WpFunction $wpFunction
    ) {
        $this->backwardsCompatibilityConfig = $backwardsCompatibilityConfig;
        $this->cacheFolder = $cacheFolder;
        $this->fallbackCodeManager = $fallbackCodeManager;
        $this->iabTcfConfig = $iabTcfConfig;
        $this->language = $language;
        $this->option = $option;
        $this->pluginConfig = $pluginConfig;
        $this->resourceEnqueuer = $resourceEnqueuer;
        $this->scanRequestService = $scanRequestService;
        $this->scriptConfigBuilder = $scriptConfigBuilder;
        $this->serviceRepository = $serviceRepository;
        $this->styleBuilder = $styleBuilder;
        $this->wpFunction = $wpFunction;
    }

    /**
     * Prevent Cloudflare Rocket Loader from loading JavaScript files from Borlabs Cookie asynchronously.
     *
     * @param mixed $tag
     * @param mixed $handle
     */
    public function addAttributeNoCloudflareAsync($tag, $handle): string
    {
        return $this->addAttributeIfRequired($tag, $handle, 'data-cfasync', 'false');
    }

    /**
     * Prevent WP Rocket from minimizing the JavaScript files of Borlabs Cookie.
     *
     * @param mixed $tag
     * @param mixed $handle
     */
    public function addAttributeNoMinify($tag, $handle): string
    {
        return $this->addAttributeIfRequired($tag, $handle, 'data-no-minify', '1');
    }

    /**
     * Prevent LiteSpeed Cache from optimizing the JavaScript files of Borlabs Cookie.
     *
     * @param mixed $tag
     * @param mixed $handle
     */
    public function addAttributeNoOptimize($tag, $handle): string
    {
        return $this->addAttributeIfRequired($tag, $handle, 'data-no-optimize', '1');
    }

    public function addAttributesToModuleScriptTags($attributes)
    {
        if (isset($attributes['id']) && in_array($attributes['id'], self::BORLABS_COOKIE_MODULES, true)) {
            $attributes['data-cfasync'] = 'false';
            $attributes['data-no-minify'] = '1';
            $attributes['data-no-optimize'] = '1';
        }

        return $attributes;
    }

    public function outputHeadCode(): void
    {
        echo $this->fallbackCodeManager->getFallbackCodes();
    }

    /**
     * Method cannot be removed because it is used by compatibility patches.
     */
    public function registerFooterResources(): void
    {
    }

    public function registerHeadResources(): void
    {
        if ($this->shouldEnqueueCssResources() === false) {
            return;
        }

        if ($this->iabTcfConfig->get()->iabTcfStatus) {
            /*
             * Required for the IAB TCF implementation.
             * Must be loaded in the <head> before the Borlabs Cookie core script.
             * The file is always treated as standard JavaScript (not a module).
             * WordPress loads standard scripts before any JavaScript modules.
             */
            $this->resourceEnqueuer->enqueueScript(
                'stub',
                'assets/javascript/' . 'borlabs-cookie-tcf-stub.min.js',
            );
        }

        $this->registerConfigFile();

        // Avoid cached styles
        $languageCode = $this->language->getCurrentLanguageCode();
        $blogId = $this->wpFunction->getCurrentBlogId();
        $styleVersionOption = $this->option->get('StyleVersion', 1, $languageCode);
        $cssFilePath = $this->cacheFolder->getPath() . '/' . $this->styleBuilder->getCssFileName($blogId, $languageCode);

        // If CSS file does not exist, try to create it on the fly
        if (file_exists($cssFilePath) === false) {
            $this->styleBuilder->buildCssFile($blogId, $languageCode);
        }

        // Check if DEV mode is active or CSS file is still missing
        if (
            defined('BORLABS_COOKIE_DEV_MODE_DISABLE_CSS_CACHING') && constant('BORLABS_COOKIE_DEV_MODE_DISABLE_CSS_CACHING') === true
            || file_exists($cssFilePath) === false
        ) {
            $manifest = json_decode(file_get_contents(BORLABS_COOKIE_PLUGIN_PATH . '/assets/manifest.json', true), true);
            $this->resourceEnqueuer->enqueueStyle('origin', 'assets/' . $manifest['scss/frontend/borlabs-cookie.scss']['file'], null, (string) $styleVersionOption->value);

            $inlineCss = $this->styleBuilder->getDialogCss($languageCode);
            $inlineCss .= $this->styleBuilder->getWidgetVariableCss($languageCode);
            $inlineCss .= $this->styleBuilder->getAnimationCss($languageCode);
            $inlineCss .= $this->styleBuilder->getCustomCss($languageCode);
            $inlineCss .= $this->styleBuilder->getContentBlockerCss($languageCode);
            $inlineCss = $this->styleBuilder->applyCssModifications($inlineCss);
            $this->resourceEnqueuer->enqueueInlineStyle('origin', $inlineCss);

            return;
        }

        $this->resourceEnqueuer->enqueueStyle(
            'custom',
            $this->cacheFolder->getUrl() . '/' . $this->styleBuilder->getCssFileName($blogId, $languageCode),
            null,
            (string) $styleVersionOption->value,
        );
    }

    public function registerJavaScriptModules(): void
    {
        if ($this->shouldEnqueueJavaScriptResources() === false) {
            return;
        }

        $languageCode = $this->language->getCurrentLanguageCode();

        if (count($this->serviceRepository->getPrioritizedServices($languageCode))) {
            // The prioritize script needs to be loaded before the core script and must include the config file as a dependency.
            $this->resourceEnqueuer->enqueueScript(
                'prioritize',
                'assets/javascript/' . 'borlabs-cookie-prioritize.min.js',
                ['borlabs-cookie-config'],
            );
        }

        $needsConfig = false;

        if (
            version_compare((string) $this->wpFunction->getWpVersion(), '6.5', '<')
            || $this->resourceEnqueuer->isClassicScriptEnqueueEnabled()
        ) {
            $needsConfig = true;
        }

        if ($this->shouldEnableDebugConsole()) {
            // The debug console script must include the config file as a dependency.
            $this->resourceEnqueuer->enqueueScriptModule(
                'debug-console',
                'assets/javascript/' . 'borlabs-cookie-debug-console.min.js',
                $needsConfig ? ['borlabs-cookie-config'] : null,
            );
        }

        $borlabsCookieJSFile = 'typescript/frontend/borlabs-cookie.ts';
        $manifest = json_decode(file_get_contents(BORLABS_COOKIE_PLUGIN_PATH . '/assets/manifest.json', true), true);
        $coreDependencies = [];

        if ($needsConfig) {
            $coreDependencies[] = 'borlabs-cookie-config';
        }

        if ($this->iabTcfConfig->get()->iabTcfStatus) {
            $borlabsCookieJSFile = 'typescript/frontend/borlabs-cookie-iabtcf.ts';

            /*
             * In older WordPress versions and on the login page, all JS modules are
             * loaded via wp_enqueue_scripts. Dependencies must be respected, so the
             * stub file has to be enqueued before borlabs-cookie-core.
             */
            if (
                version_compare((string) $this->wpFunction->getWpVersion(), '6.5', '<')
                || $this->resourceEnqueuer->isClassicScriptEnqueueEnabled()
            ) {
                $coreDependencies[] = 'borlabs-cookie-stub';
            }
        }

        $this->resourceEnqueuer->enqueueScriptModule(
            'core',
            'assets/' . $manifest[$borlabsCookieJSFile]['file'],
            $coreDependencies,
        );

        if ($this->backwardsCompatibilityConfig->get()->loadBackwardsCompatibilityJavaScript) {
            $this->resourceEnqueuer->enqueueScriptModule(
                'legacy-backward-compatibility',
                'assets/javascript/' . 'borlabs-cookie-legacy-backward-compatibility.min.js',
                ['borlabs-cookie-core'],
            );
        }
    }

    /**
     * Used by WordPress before 6.5.
     *
     * @param mixed $tag
     * @param mixed $handle
     */
    public function transformScriptTagsToModules($tag, $handle)
    {
        if (
            strpos($handle, 'borlabs-cookie-core') !== false
            || strpos($handle, 'borlabs-cookie-debug-console') !== false
            || strpos($handle, 'borlabs-cookie-legacy-backward-compatibility') !== false
        ) {
            $scriptTypeMatches = [];
            preg_match('/type=["\']([^"\']*)["\']/', $tag, $scriptTypeMatches);
            $scriptType = !empty($scriptTypeMatches) && !empty($scriptTypeMatches[1]) ? strtolower($scriptTypeMatches[1]) : null;

            $tag = $scriptType
                ? preg_replace('/type=(["\'])([^"\']*)["\']/', 'type=$1module$1', $tag)
                : str_replace('<script', "<script type='module'", $tag);
        }

        return $tag;
    }

    private function addAttributeIfRequired($tag, $handle, $attributeName, $attributeValue): string
    {
        if ($this->shouldProcessTag($handle)) {
            return $this->addOrUpdateAttribute($tag, $attributeName, $attributeValue);
        }

        return $tag;
    }

    private function addOrUpdateAttribute($tag, $attributeName, $attributeValue): string
    {
        $pattern = '/' . preg_quote($attributeName) . '=["\']([^"\']*)["\']/';
        $matches = [];
        preg_match($pattern, $tag, $matches);

        if (!empty($matches)) {
            return preg_replace($pattern, $attributeName . '="' . $attributeValue . '"', $tag);
        }

        return str_replace('<script', "<script {$attributeName}=\"{$attributeValue}\"", $tag);
    }

    private function registerConfigFile(): void
    {
        if ($this->shouldEnqueueJavaScriptResources() === false) {
            return;
        }

        $languageCode = $this->language->getCurrentLanguageCode();
        $configVersionOption = $this->option->get('ConfigVersion', 1, $languageCode);
        $configFilePath = $this->cacheFolder->getPath() . '/' . $this->scriptConfigBuilder->getConfigFileName($languageCode);

        // If JavaScript config file does not exist, try to create it on the fly
        if (file_exists($configFilePath) === false) {
            $this->scriptConfigBuilder->buildJavaScriptConfigFile($languageCode);
        }

        $this->resourceEnqueuer->enqueueScript(
            'config',
            $this->cacheFolder->getUrl() . '/' . $this->scriptConfigBuilder->getConfigFileName($languageCode),
            null,
            (string) $configVersionOption->value,
        );

        if ($this->shouldEnableDebugConsole()) {
            $this->resourceEnqueuer->enqueueScript(
                'noop',
                'assets/javascript/' . 'borlabs-cookie-noop.min.js',
            );
            $this->resourceEnqueuer->enqueueLocalizeScript(
                'noop',
                'borlabsCookieDebugConsoleSettings',
                [
                    'configVersion' => (string) $configVersionOption->value,
                    'localizationStrings' => DebugConsoleLocalizationStrings::get(),
                    'pluginVersion' => BORLABS_COOKIE_VERSION,
                ],
            );
        }
    }

    private function shouldEnableDebugConsole(): bool
    {
        $debugConsoleDisabled = $this->wpFunction->applyFilter('borlabsCookie/frontendResources/disableDebugConsole', false);

        return ($this->pluginConfig->get()->enableDebugConsole
            && $this->wpFunction->isUserLoggedIn()
            && $this->wpFunction->currentUserCan('manage_borlabs_cookie')
            && !$debugConsoleDisabled)
            || $this->scanRequestService->withDebugConsole();
    }

    private function shouldEnqueueCssResources(): bool
    {
        $loadingOnRestRequestDisabled = defined('REST_REQUEST') && $this->wpFunction->applyFilter('borlabsCookie/frontendResources/disabledOnRestRequest', true);
        $loadingDisabled = $this->wpFunction->applyFilter('borlabsCookie/frontendResources/disableCssLoading', false);

        return !$loadingOnRestRequestDisabled && !$loadingDisabled;
    }

    private function shouldEnqueueJavaScriptResources(): bool
    {
        $loadingOnRestRequestDisabled = defined('REST_REQUEST') && $this->wpFunction->applyFilter('borlabsCookie/frontendResources/disabledOnRestRequest', true);
        $loadingDisabled = $this->wpFunction->applyFilter('borlabsCookie/frontendResources/disableJavaScriptLoading', false);

        return !$loadingOnRestRequestDisabled && !$loadingDisabled;
    }

    private function shouldProcessTag($handle): bool
    {
        foreach (self::BORLABS_COOKIE_HANDLES as $string) {
            if (strpos($handle, $string) !== false) {
                return true;
            }
        }

        return false;
    }
}
