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

namespace Borlabs\Cookie\System\WordPressAdminDriver;

use Borlabs\Cookie\Adapter\WpFunction;
use Borlabs\Cookie\System\Config\PluginConfig;
use Borlabs\Cookie\System\ResourceEnqueuer\ResourceEnqueuer;
use Borlabs\Cookie\System\Script\BorlabsCookieGlobalsService;

final class WordPressAdminResources
{
    private BorlabsCookieGlobalsService $borlabsCookieGlobalsService;

    private PluginConfig $pluginConfig;

    private ResourceEnqueuer $resourceEnqueuer;

    private WpFunction $wpFunction;

    public function __construct(
        BorlabsCookieGlobalsService $borlabsCookieGlobalsService,
        PluginConfig $pluginConfig,
        ResourceEnqueuer $resourceEnqueuer,
        WpFunction $wpFunction
    ) {
        $this->borlabsCookieGlobalsService = $borlabsCookieGlobalsService;
        $this->pluginConfig = $pluginConfig;
        $this->resourceEnqueuer = $resourceEnqueuer;
        $this->wpFunction = $wpFunction;
    }

    public function register(): void
    {
        $currentScreenData = $this->wpFunction->getCurrentScreen();
        $manifest = json_decode(file_get_contents(BORLABS_COOKIE_PLUGIN_PATH . '/assets/manifest.json', true), true);
        $this->resourceEnqueuer->enqueueStyle('admin-sidebar', 'assets/' . $manifest['scss/admin/wordpress-admin-sidebar.scss']['file']);

        if (is_string($currentScreenData->id) && strpos($currentScreenData->id, 'borlabs-cookie') !== false) {
            $this->resourceEnqueuer->enqueueStyle('admin', 'assets/' . $manifest['scss/admin/wordpress-admin.scss']['file']);
            $this->resourceEnqueuer->enqueueStyle('animate', 'assets/external/animate.css/animate.min.css', [], '4.1.1');
            // TODO: Replace jQuery-multiple-select with a custom solution
            $this->resourceEnqueuer->enqueueScript('jquery-multiselect', 'assets/external/multiselect/js/jquery.multi-select.js', ['jquery'], '4.5.3');
            $this->resourceEnqueuer->enqueueScript('admin', 'assets/' . $manifest['typescript/admin/borlabs-cookie-admin.ts']['file'], ['wp-color-picker'], null, null);

            if ($currentScreenData->base === 'toplevel_page_borlabs-cookie') {
                $this->resourceEnqueuer->enqueueScript('chartjs', 'assets/external/chart.js/dist/chart.umd.js', [], '4.4.0');
            }

            // Color Picker
            $this->wpFunction->wpEnqueueStyle('wp-color-picker');

            // Media Library
            $this->wpFunction->wpEnqueueMedia();

            // CodeMirror
            $this->borlabsCookieGlobalsService->addProperty(
                'codeMirrorHtml',
                $this->wpFunction->wpEnqueueCodeEditor([
                    'type' => 'text/html',
                    'htmlhint' => [
                        'space-tab-mixed-disabled' => false,
                    ],
                ]) ?? [],
            );
            $this->borlabsCookieGlobalsService->addProperty(
                'codeMirrorJavaScript',
                $this->wpFunction->wpEnqueueCodeEditor(['type' => 'text/javascript']) ?? [],
            );
            $this->borlabsCookieGlobalsService->addProperty(
                'codeMirrorCss',
                $this->wpFunction->wpEnqueueCodeEditor(['type' => 'text/css']) ?? [],
            );
        } elseif (
            isset($currentScreenData->post_type, $this->pluginConfig->get()->metaBox[$currentScreenData->post_type])
        ) {
            $this->resourceEnqueuer->enqueueStyle('admin', 'assets/' . $manifest['scss/admin/wordpress-admin.scss']['file']);
        }
    }

    public function transformScriptTagsToModules($tag, $handle)
    {
        if (strpos($handle, 'borlabs-cookie-admin') !== false) {
            $scriptTypeMatches = [];
            preg_match('/type=["\']([^"\']*)["\']/', $tag, $scriptTypeMatches);
            $scriptType = !empty($scriptTypeMatches) && !empty($scriptTypeMatches[1]) ? strtolower($scriptTypeMatches[1]) : null;

            $tag = $scriptType
                ? preg_replace('/type=(["\'])([^"\']*)["\']/', 'type=$1module$1', $tag)
                : str_replace('<script', "<script type='module'", $tag);
        }

        return $tag;
    }
}
