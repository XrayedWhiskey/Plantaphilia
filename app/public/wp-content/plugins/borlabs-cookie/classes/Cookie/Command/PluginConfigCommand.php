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

namespace Borlabs\Cookie\Command;

use Borlabs\Cookie\Container\ApplicationContainer;
use Borlabs\Cookie\Container\Container;
use Borlabs\Cookie\Dto\Config\PluginDto;
use Borlabs\Cookie\Enum\System\AutomaticUpdateEnum;
use Borlabs\Cookie\Enum\System\DisplayModeSettingsEnum;
use Borlabs\Cookie\Enum\System\WordPressAdminSidebarMenuModeEnum;
use Borlabs\Cookie\Support\Traits\BooleanConvertibleTrait;
use Borlabs\Cookie\System\Config\PluginConfig;
use WP_CLI;

class PluginConfigCommand extends AbstractCommand
{
    use BooleanConvertibleTrait;

    /**
     * @const DEFAULT_FIELDS Default fields to display.
     */
    public const DEFAULT_FIELDS = [
        'automatic-update',
        'clear-third-party-cache',
        'display-mode-settings',
        'enable-debug-console',
        'enable-debug-logging',
        'meta-box',
        'wordpress-admin-sidebar-menu-mode',
    ];

    private Container $container;

    private PluginConfig $pluginConfig;

    /**
     * PluginConfigCommand constructor.
     */
    public function __construct()
    {
        $this->container = ApplicationContainer::get();
        $this->pluginConfig = $this->container->get(PluginConfig::class);
    }

    /**
     * This method is for internal use only and is used to map DTO properties to the corresponding CLI fields.
     * Due to limitations in PHPUnit, the method cannot be moved to a trait.
     * It must be public so that it can be called by PHPUnit.
     * To hide the method from WP CLI, a double underscore (__) is used as a prefix.
     */
    public function __mapToCliFields(PluginDto $pluginConfig): array
    {
        return [
            'automatic-update' => $pluginConfig->automaticUpdate->value,
            'clear-third-party-cache' => $pluginConfig->clearThirdPartyCache,
            'display-mode-settings' => $pluginConfig->displayModeSettings->value,
            'enable-debug-console' => $pluginConfig->enableDebugConsole,
            'enable-debug-logging' => $pluginConfig->enableDebugLogging,
            'meta-box' => $pluginConfig->metaBox,
            'wordpress-admin-sidebar-menu-mode' => $pluginConfig->wordPressAdminSidebarMenuMode->value,
        ];
    }

    /**
     * Creates or updates the plugin configuration.
     *
     * ## OPTIONS
     *
     * [--automatic-update=<automatic-update>]
     * : Automatic update channel for the plugin.
     *   Options: auto-update-all, auto-update-minor, auto-update-patch, auto-update-none
     *
     * [--clear-third-party-cache=<clear-third-party-cache>]
     * : Whether third-party caches should be cleared after changes. Accepts: 1/0, true/false
     *
     * [--display-mode-settings=<display-mode-settings>]
     * : How settings are displayed in the backend.
     *   Options: simplified, standard
     *
     * [--enable-debug-console=<enable-debug-console>]
     * : Enable the Borlabs Cookie debug console. Accepts: 1/0, true/false
     *
     * [--enable-debug-logging=<enable-debug-logging>]
     * : Enable debug logging. Accepts: 1/0, true/false
     *
     * [--meta-box=<meta-box>]
     * : Comma-separated list of post types where the Borlabs Cookie meta box should appear. Example: post,page
     *
     * [--wordpress-admin-sidebar-menu-mode=<wordpress-admin-sidebar-menu-mode>]
     * : How navigation items are displayed in the sidebar.
     *   Options: expanded, simplified, standard
     *
     * ## EXAMPLES
     *
     *      # Enable all automatic updates and use the default settings display mode
     *      $ wp borlabs-cookie plugin-config createOrUpdate --automatic-update=auto-update-all --display-mode-settings=standard
     *      Success: Updated plugin config.
     *
     *      # Enable the meta box for posts and pages and turn on the debug console
     *      $ wp borlabs-cookie plugin-config createOrUpdate --meta-box=post,page --enable-debug-console=1
     *      Success: Updated plugin config.
     */
    public function createOrUpdate(array $args, array $assocArgs): void
    {
        $pluginConfig = $this->pluginConfig->load();
        $automaticUpdate = WP_CLI\Utils\get_flag_value($assocArgs, 'automatic-update');

        if (isset($automaticUpdate)) {
            if (AutomaticUpdateEnum::hasValue($automaticUpdate)) {
                $pluginConfig->automaticUpdate = AutomaticUpdateEnum::fromValue($automaticUpdate);
            } else {
                WP_CLI::error(sprintf('Invalid value "%s" for "automatic-update" field.', $automaticUpdate));

                return;
            }
        }

        $displayModeSettings = WP_CLI\Utils\get_flag_value($assocArgs, 'display-mode-settings');

        if (isset($displayModeSettings)) {
            if (DisplayModeSettingsEnum::hasValue($displayModeSettings)) {
                $pluginConfig->displayModeSettings = DisplayModeSettingsEnum::fromValue($displayModeSettings);
            } else {
                WP_CLI::error(sprintf('Invalid value "%s" for "display-mode-settings" field.', $displayModeSettings));

                return;
            }
        }

        $wordPressAdminSidebarMenuMode = WP_CLI\Utils\get_flag_value($assocArgs, 'wordpress-admin-sidebar-menu-mode');

        if (isset($wordPressAdminSidebarMenuMode)) {
            if (WordPressAdminSidebarMenuModeEnum::hasValue($wordPressAdminSidebarMenuMode)) {
                $pluginConfig->wordPressAdminSidebarMenuMode = WordPressAdminSidebarMenuModeEnum::fromValue($wordPressAdminSidebarMenuMode);
            } else {
                WP_CLI::error(sprintf('Invalid value "%s" for "wordpress-admin-sidebar-menu-mode" field.', $wordPressAdminSidebarMenuMode));

                return;
            }
        }

        $metaBox = WP_CLI\Utils\get_flag_value($assocArgs, 'meta-box');

        if (isset($metaBox)) {
            $metaBox = explode(',', $metaBox);
            $pluginConfig->metaBox = [];

            foreach ($metaBox as $postType) {
                $pluginConfig->metaBox[$postType] = '1';
            }
        }

        $pluginConfig->clearThirdPartyCache = $this->toBoolean(WP_CLI\Utils\get_flag_value($assocArgs, 'clear-third-party-cache', $pluginConfig->clearThirdPartyCache));
        $pluginConfig->enableDebugConsole = $this->toBoolean(WP_CLI\Utils\get_flag_value($assocArgs, 'enable-debug-console', $pluginConfig->enableDebugConsole));
        $pluginConfig->enableDebugLogging = $this->toBoolean(WP_CLI\Utils\get_flag_value($assocArgs, 'enable-debug-logging', $pluginConfig->enableDebugLogging));
        $success = $this->pluginConfig->save($pluginConfig);

        // The return value $success is `false` if no data has been changed.
        if (!$success && $this->pluginConfig->hasConfig() === false) {
            WP_CLI::error('The plugin config was not updated.');
        } else {
            WP_CLI::success('Updated plugin config.');
        }
    }

    /**
     * Get the plugin configuration.
     *
     * ## OPTIONS
     *
     * [--fields=<fields>]
     * : Limit the output to specific object fields.
     *
     * [--format=<format>]
     * : Render output in a particular format.
     * ---
     * default: table
     * options:
     *   - table
     *   - csv
     *   - json
     *   - yaml
     * ---
     *
     * ## AVAILABLE FIELDS
     *
     * These fields are displayed by default for the plugin configuration.
     *
     * * automatic-update
     * * clear-third-party-cache
     * * display-mode-settings
     * * enable-debug-console
     * * enable-debug-logging
     * * meta-box
     * * wordpress-admin-sidebar-menu-mode
     *
     * ## EXAMPLES
     *
     *     # List the plugin configuration
     *     $ wp borlabs-cookie plugin-config get
     *     +-----------------------+--------------------+
     *     | Field                 | Value              |
     *     +-----------------------+--------------------+
     *     | automatic-update      | auto-update-all    |
     *     | clear-third-party-cache | 0                |
     *     | display-mode-settings | standard           |
     *     | enable-debug-console  | 0                  |
     *     | enable-debug-logging  | 0                  |
     *     | meta-box              | {"post":"1"}       |
     *     | wordpress-admin-sidebar-menu-mode | standard         |
     *     +-----------------------+--------------------+
     *
     *     # List only the automatic-update and display-mode-settings fields
     *     $ wp borlabs-cookie plugin-config get --fields=automatic-update,display-mode-settings
     *     +-----------------------+--------------------+
     *     | Field                 | Value              |
     *     +-----------------------+--------------------+
     *     | automatic-update      | auto-update-all    |
     *     | display-mode-settings | standard           |
     *     +-----------------------+--------------------+
     *
     *     # List the plugin configuration in JSON format
     *     $ wp borlabs-cookie plugin-config get --format=json
     *     {
     *         "automatic-update": "auto-update-all",
     *         "clear-third-party-cache": 0,
     *         "display-mode-settings": "standard",
     *         "enable-debug-console": 0,
     *         "enable-debug-logging": 0,
     *         "meta-box": {"post":"1"},
     *         "wordpress-admin-sidebar-menu-mode": "standard"
     *     }
     */
    public function get(array $args, array $assocArgs): void
    {
        $pluginConfig = $this->pluginConfig->load();
        $formatter = $this->getFormatter($assocArgs, self::DEFAULT_FIELDS);
        $formatter->display_item($this->__mapToCliFields($pluginConfig));
    }

    /**
     * Get the default plugin configuration.
     *
     * ## OPTIONS
     *
     * [--fields=<fields>]
     * : Limit the output to specific object fields.
     *
     * [--format=<format>]
     * : Render output in a particular format.
     * ---
     * default: table
     * options:
     *   - table
     *   - csv
     *   - json
     *   - yaml
     * ---
     *
     * ## AVAILABLE FIELDS
     *
     * These fields are displayed by default for the plugin configuration.
     *
     * * automatic-update
     * * clear-third-party-cache
     * * display-mode-settings
     * * enable-debug-console
     * * enable-debug-logging
     * * meta-box
     * * wordpress-admin-sidebar-menu-mode
     *
     * ## EXAMPLES
     *
     *     # List the default plugin configuration
     *     $ wp borlabs-cookie plugin-config getDefault
     *     +-----------------------+--------------------+
     *     | Field                 | Value              |
     *     +-----------------------+--------------------+
     *     | automatic-update      | auto-update-all    |
     *     | clear-third-party-cache | 0                |
     *     | display-mode-settings | standard           |
     *     | enable-debug-console  | 0                  |
     *     | enable-debug-logging  | 0                  |
     *     | meta-box              | {"post":"1"}       |
     *     | wordpress-admin-sidebar-menu-mode | standard         |
     *     +-----------------------+--------------------+
     *
     *     # List only the automatic-update and display-mode-settings fields
     *     $ wp borlabs-cookie plugin-config getDefault --fields=automatic-update,display-mode-settings
     *     +-----------------------+--------------------+
     *     | Field                 | Value              |
     *     +-----------------------+--------------------+
     *     | automatic-update      | auto-update-all    |
     *     | display-mode-settings | default            |
     *     +-----------------------+--------------------+
     *
     *     # List the default plugin configuration in JSON format
     *     $ wp borlabs-cookie plugin-config getDefault --format=json
     *     {
     *         "automatic-update": "auto-update-all",
     *         "clear-third-party-cache": 0,
     *         "display-mode-settings": "standard",
     *         "enable-debug-console": 0,
     *         "enable-debug-logging": 0,
     *         "meta-box": {"post":"1"},
     *         "wordpress-admin-sidebar-menu-mode": "standard"
     *     }
     */
    public function getDefault(array $args, array $assocArgs): void
    {
        $formatter = $this->getFormatter($assocArgs, self::DEFAULT_FIELDS);
        $formatter->display_item($this->__mapToCliFields($this->pluginConfig->defaultConfig()));
    }
}
