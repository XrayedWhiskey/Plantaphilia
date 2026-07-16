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

namespace Borlabs\CookieApi\PhpApi;

use Borlabs\CookieApi\PhpApi\Component\ContentBlockerPhpApi;
use Borlabs\CookieApi\PhpApi\Component\GeneralConfigPhpApi;
use Borlabs\CookieApi\PhpApi\Component\LanguagePhpApi;
use Borlabs\CookieApi\PhpApi\Component\PluginConfigPhpApi;

/**
 * @see PhpApi::contentBlockerApi()
 * @see PhpApi::generalConfigApi()
 * @see PhpApi::languagePhpApi()
 * @see PhpApi::pluginConfigApi()
 */
final class PhpApi
{
    private ContentBlockerPhpApi $contentBlockerApi;

    private GeneralConfigPhpApi $generalConfigApi;

    private LanguagePhpApi $languagePhpApi;

    private PluginConfigPhpApi $pluginConfigApi;

    public function __construct(
        ContentBlockerPhpApi $contentBlockerApi,
        GeneralConfigPhpApi $generalConfig,
        LanguagePhpApi $languagePhpApi,
        PluginConfigPhpApi $pluginConfig
    ) {
        $this->contentBlockerApi = $contentBlockerApi;
        $this->generalConfigApi = $generalConfig;
        $this->languagePhpApi = $languagePhpApi;
        $this->pluginConfigApi = $pluginConfig;
    }

    /**
     * @see ContentBlockerPhpApi::blockContent()
     * @see ContentBlockerPhpApi::detectAndBlockIframes()
     */
    public function contentBlockerApi(): ContentBlockerPhpApi
    {
        return $this->contentBlockerApi;
    }

    /**
     * @see GeneralConfigPhpApi::get()
     */
    public function generalConfigApi(): GeneralConfigPhpApi
    {
        return $this->generalConfigApi;
    }

    /**
     * @see LanguagePhpApi::getCurrentLanguageCode()
     */
    public function languagePhpApi(): LanguagePhpApi
    {
        return $this->languagePhpApi;
    }

    /**
     * @see PluginConfigPhpApi::get()
     */
    public function pluginConfigApi(): PluginConfigPhpApi
    {
        return $this->pluginConfigApi;
    }
}
