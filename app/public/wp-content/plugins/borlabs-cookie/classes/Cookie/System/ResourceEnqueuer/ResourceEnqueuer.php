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

namespace Borlabs\Cookie\System\ResourceEnqueuer;

use Borlabs\Cookie\Adapter\WpFunction;

final class ResourceEnqueuer
{
    public const HANDLE_PREFIX = 'borlabs-cookie';

    private bool $useClassicScriptEnqueue = false;

    private WpFunction $wpFunction;

    public function __construct(
        WpFunction $wpFunction
    ) {
        $this->wpFunction = $wpFunction;
    }

    public function enableClassicScriptEnqueue(): void
    {
        $this->useClassicScriptEnqueue = true;
    }

    public function enqueueInlineStyle(
        string $name,
        string $style
    ): void {
        $this->wpFunction->wpAddInlineStyle(
            $this->prefixName($name),
            $style,
        );
    }

    public function enqueueLocalizeScript(
        string $name,
        string $objectName,
        array $data
    ): bool {
        return $this->wpFunction->wpLocalizeScript(
            $this->prefixName($name),
            $objectName,
            $data,
        );
    }

    /**
     * @param string $name the prefix `borlabs-cookie-` is added automatically
     * @param string $path the path to the file relative to the plugin directory or an URL
     */
    public function enqueueScript(
        string $name,
        string $path,
        ?array $dependency = null,
        ?string $version = null,
        ?bool $placeInFooter = null
    ): void {
        $this->wpFunction->wpEnqueueScript(
            $this->prefixName($name),
            $this->resolvePluginUrl($path),
            $dependency,
            BORLABS_COOKIE_VERSION . ($version ? '-' . $version : ''),
            $placeInFooter,
        );
    }

    /**
     * @param string $name the prefix `borlabs-cookie-` is added automatically
     * @param string $path the path to the file relative to the plugin directory or an URL
     */
    public function enqueueScriptModule(
        string $name,
        string $path,
        ?array $dependency = null,
        ?string $version = null
    ) {
        // Check if the function exists, because it was added in WordPress 6.5.0
        if ($this->isClassicScriptEnqueueEnabled()) {
            $this->wpFunction->wpEnqueueScript(
                $this->prefixName($name),
                $this->resolvePluginUrl($path),
                $dependency,
                BORLABS_COOKIE_VERSION . ($version ? '-' . $version : ''),
            );

            return;
        }

        $this->wpFunction->wpEnqueueScriptModule(
            $this->prefixName($name),
            $this->resolvePluginUrl($path),
            $dependency,
            BORLABS_COOKIE_VERSION . ($version ? '-' . $version : ''),
        );
    }

    /**
     * @param string $name the prefix `borlabs-cookie-` is added automatically
     * @param string $path the path to the file relative to the plugin directory or an URL
     */
    public function enqueueStyle(string $name, string $path, ?array $dependency = null, ?string $version = null): void
    {
        $this->wpFunction->wpEnqueueStyle(
            $this->prefixName($name),
            $this->resolvePluginUrl($path),
            $dependency,
            BORLABS_COOKIE_VERSION . ($version ? '-' . $version : ''),
        );
    }

    public function isClassicScriptEnqueueEnabled(): bool
    {
        return $this->useClassicScriptEnqueue;
    }

    private function prefixName(string $name): string
    {
        return self::HANDLE_PREFIX . '-' . $name;
    }

    private function resolvePluginUrl(string $path): string
    {
        return filter_var($path, FILTER_VALIDATE_URL) ? $path : $this->wpFunction->pluginsUrl($path, BORLABS_COOKIE_BASENAME);
    }
}
