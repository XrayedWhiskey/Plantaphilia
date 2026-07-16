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

namespace Borlabs\Cookie\Adapter;

use Borlabs\Cookie\Dto\Adapter\WpGetPagesArgumentDto;
use Borlabs\Cookie\Dto\Adapter\WpGetPostsArgumentDto;
use Borlabs\Cookie\Dto\Adapter\WpGetPostTypeArgumentDto;
use Borlabs\Cookie\Dto\Adapter\WpRemoteResponseDto;
use Borlabs\Cookie\Dto\System\KeyValueDto;
use Borlabs\Cookie\DtoList\System\KeyValueDtoList;
use DateTimeZone;
use WP_Error;
use WP_Post;
use WP_Screen;
use WP_Theme;
use WP_User;

final class WpFunction
{
    public function __construct()
    {
    }

    public function addAction(
        string $hookName,
        callable $callback,
        int $priority = 10,
        int $acceptedArguments = 1
    ): bool {
        return add_action($hookName, $callback, $priority, $acceptedArguments);
    }

    public function addFilter(
        string $hookName,
        callable $callback,
        int $priority = 10,
        int $acceptedArguments = 1
    ): bool {
        return add_filter($hookName, $callback, $priority, $acceptedArguments);
    }

    public function addMenuPage(
        string $pageTitle,
        string $menuTitle,
        string $capability,
        string $menuSlug,
        $callback = null,
        $iconUrl = '',
        ?int $position = null
    ): bool {
        return gettype(add_menu_page($pageTitle, $menuTitle, $capability, $menuSlug, $callback, $iconUrl, $position)) === 'string';
    }

    public function addMetaBox(
        string $id,
        string $title,
        callable $callback,
        string $context,
        string $priority,
        ?array $callbackArgs = null,
        $screen = null
    ): void {
        add_meta_box($id, $title, $callback, $screen, $context, $priority, $callbackArgs);
    }

    public function addShortcode(string $tag, callable $callback): bool
    {
        add_shortcode($tag, $callback);

        return true;
    }

    public function addSubMenuPage(
        string $parentSlug,
        string $pageTitle,
        string $menuTitle,
        string $capability,
        string $menuSlug,
        $function = null,
        ?int $position = null
    ): bool {
        return !(add_submenu_page($parentSlug, $pageTitle, $menuTitle, $capability, $menuSlug, $function, $position)) === false;
    }

    public function applyFilter(string $filterName, $content, ?array $attributes = null, ...$args)
    {
        return apply_filters($filterName, $content, $attributes, ...$args);
    }

    /**
     * The WordPress mixed parameter `...$args` is not supported by this method.
     */
    public function currentUserCan(string $capability): bool
    {
        return current_user_can($capability);
    }

    /**
     * This method is a wrapper for the WordPress function `delete_site_option`. This method is used by
     * {@see \Borlabs\Cookie\System\Option\Option}.
     *
     * @param string $option name of the option
     */
    public function deleteGlobalOption(string $option): bool
    {
        return delete_site_option($option);
    }

    /**
     * This method is a wrapper for the WordPress function `delete_option`. This method is used by
     * {@see \Borlabs\Cookie\System\Option\Option}.
     *
     * @param string $option name of the option
     */
    public function deleteOption(string $option): bool
    {
        return delete_option($option);
    }

    public function doAction(string $actionName, ...$args)
    {
        do_action($actionName, ...$args);
    }

    public function doShortcode($content, $ignoreHtml = false): string
    {
        return do_shortcode($content, $ignoreHtml);
    }

    public function escUrl(string $url, ?array $protocols = null): string
    {
        return esc_url($url, $protocols, $context = 'display');
    }

    public function escUrlRaw(string $url, ?array $protocols = null): string
    {
        return esc_url_raw($url, $protocols);
    }

    public function getAdminUrl(string $path, string $scheme = 'admin', ?int $blogId = null): string
    {
        return get_admin_url($blogId, $path, $scheme);
    }

    public function getBlogInfo(string $show = '', string $filter = 'raw'): string
    {
        return get_bloginfo($show, $filter);
    }

    public function getCronArray(): array
    {
        return _get_cron_array();
    }

    public function getCurrentBlogId(): int
    {
        return get_current_blog_id();
    }

    public function getCurrentScreen(): ?WP_Screen
    {
        return get_current_screen();
    }

    public function getHomeUrl(?int $blogId = null, string $path = '', ?string $scheme = null): string
    {
        return get_home_url($blogId, $path, $scheme);
    }

    public function getLocale(): string
    {
        return get_locale();
    }

    /**
     * This method is a wrapper for the WordPress function `get_option`. This method is used by
     * {@see \Borlabs\Cookie\System\Option\Option}.
     *
     * @param string $option  name of the option
     * @param mixed  $default optional; Default: `false`; Default value if the option does not exist
     *
     * @return false|mixed
     */
    public function getOption(string $option, $default = false)
    {
        return get_option($option, $default);
    }

    /**
     * @return null|WP_Post[]
     */
    public function getPages(WpGetPagesArgumentDto $getPagesArgumentDto): ?array
    {
        $pages = get_pages([
            'authors' => $getPagesArgumentDto->authors,
            'child_of' => $getPagesArgumentDto->childOf,
            'exclude' => $getPagesArgumentDto->exclude,
            'exclude_tree' => $getPagesArgumentDto->excludeTree,
            'hierarchical' => $getPagesArgumentDto->hierarchical,
            'include' => $getPagesArgumentDto->include,
            'meta_key' => $getPagesArgumentDto->metaKey,
            'meta_value' => $getPagesArgumentDto->metaValue,
            'number' => $getPagesArgumentDto->number,
            'offset' => $getPagesArgumentDto->offset,
            'parent' => $getPagesArgumentDto->parent,
            'post_status' => $getPagesArgumentDto->postStatus,
            'post_type' => $getPagesArgumentDto->postType,
            'sort_column' => $getPagesArgumentDto->sortColumn,
            'sort_order' => $getPagesArgumentDto->sortOrder,
        ]);

        return $pages !== false ? $pages : null;
    }

    public function getPermalink(int $postId): ?string
    {
        $url = get_permalink($postId);

        return is_string($url) ? $url : null;
    }

    public function getPlugins(): array
    {
        if (!function_exists('get_plugins')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        return get_plugins();
    }

    public function getPostMeta(int $postId, string $metaKey, bool $returnSingleValue = false)
    {
        return get_post_meta($postId, $metaKey, $returnSingleValue);
    }

    public function getPosts(WpGetPostsArgumentDto $getPostsArgument): ?array
    {
        $args = [];
        $args['category'] = $getPostsArgument->category;
        $args['exclude'] = $getPostsArgument->exclude;
        $args['include'] = $getPostsArgument->include;
        $args['numberposts'] = $getPostsArgument->numberPosts;
        $args['order'] = $getPostsArgument->order;
        $args['orderby'] = $getPostsArgument->orderBy;
        $args['post_status'] = $getPostsArgument->postStatus;
        $args['post_type'] = $getPostsArgument->postType;
        $args['suppress_filters'] = $getPostsArgument->suppressFilters;

        return get_posts($args);
    }

    /**
     * Returns the link to the archive page of the given post type.
     * If the post type does not have an archive page, this method returns null.
     * If the archive link not a valid URL (e.g. it contains template syntax "%something%"), this method returns null.
     */
    public function getPostTypeArchiveLink(string $postType): ?string
    {
        $link = get_post_type_archive_link($postType);

        if ($link === false || filter_var($link, FILTER_VALIDATE_URL) === false) {
            return null;
        }

        return $link;
    }

    public function getPostTypes(
        WpGetPostTypeArgumentDto $getPostTypeArgumentDto,
        string $output = 'names',
        string $operator = 'and'
    ): array {
        $args = [];
        $args['can_export'] = $getPostTypeArgumentDto->canExport;
        $args['capabilities'] = $getPostTypeArgumentDto->capabilities;
        $args['capability_type'] = $getPostTypeArgumentDto->capabilityType;
        $args['delete_with_user'] = $getPostTypeArgumentDto->deleteWithUser;
        $args['description'] = $getPostTypeArgumentDto->description;
        $args['exclude_from_search'] = $getPostTypeArgumentDto->excludeFromSearch;
        $args['has_archive'] = $getPostTypeArgumentDto->hasArchive;
        $args['hierarchical'] = $getPostTypeArgumentDto->hierarchical;
        $args['label'] = $getPostTypeArgumentDto->label;
        $args['labels'] = $getPostTypeArgumentDto->labels;
        $args['map_meta_cap'] = $getPostTypeArgumentDto->mapMetaCap;
        $args['menu_icon'] = $getPostTypeArgumentDto->menuIcon;
        $args['menu_position'] = $getPostTypeArgumentDto->menuPosition;
        $args['public'] = $getPostTypeArgumentDto->public;
        $args['publicly_queryable'] = $getPostTypeArgumentDto->publiclyQueryable;
        $args['query_var'] = $getPostTypeArgumentDto->queryVar;
        $args['register_meta_box_cb'] = $getPostTypeArgumentDto->registerMetaBoxCb;
        $args['rest_base'] = $getPostTypeArgumentDto->restBase;
        $args['rest_controller_class'] = $getPostTypeArgumentDto->restControllerClass;
        $args['rest_namespace'] = $getPostTypeArgumentDto->restNamespace;
        $args['rewrite'] = $getPostTypeArgumentDto->rewrite;
        $args['show_in_admin_bar'] = $getPostTypeArgumentDto->showInAdminBar;
        $args['show_in_menu'] = $getPostTypeArgumentDto->showInMenu;
        $args['show_in_nav_menus'] = $getPostTypeArgumentDto->showInNavMenus;
        $args['show_in_rest'] = $getPostTypeArgumentDto->showInRest;
        $args['show_ui'] = $getPostTypeArgumentDto->showUi;
        $args['supports'] = $getPostTypeArgumentDto->supports;
        $args['taxonomies'] = $getPostTypeArgumentDto->taxonomies;
        $args['template_lock'] = $getPostTypeArgumentDto->templateLock;

        // Remove all null values
        $args = array_filter($args, fn ($value) => $value !== null);

        return get_post_types($args, $output, $operator);
    }

    public function getPrivacyPolicyUrl(): string
    {
        return get_privacy_policy_url();
    }

    public function getRestUrl(?int $blogId = null, string $path = '/', string $scheme = 'rest'): string
    {
        return get_rest_url($blogId, $path, $scheme);
    }

    /**
     * This method is a wrapper for the WordPress function `get_site_option`. This method is used by
     * {@see \Borlabs\Cookie\System\Option\Option}.
     *
     * @param string $option  name of the option
     * @param mixed  $default optional; Default: `false`; Default value if the option does not exist
     *
     * @return false|mixed
     */
    public function getSiteOption(string $option, $default = false)
    {
        return get_site_option($option, $default);
    }

    public function getSites(): array
    {
        return get_sites();
    }

    public function getSiteUrl(?int $blogId = null, string $path = '', ?string $scheme = null): string
    {
        return get_site_url($blogId, $path, $scheme);
    }

    public function getThemeRootUri(string $stylesheetOrTemplate = '', string $themeRoot = ''): string
    {
        return get_theme_root_uri($stylesheetOrTemplate, $themeRoot);
    }

    public function getTransient(string $transient)
    {
        return get_transient($transient);
    }

    public function getUserLocale(): string
    {
        return get_user_locale();
    }

    public function getWpTheme(): WP_Theme
    {
        return wp_get_theme();
    }

    /**
     * @return array<string, WP_Theme>
     */
    public function getWpThemes(): array
    {
        return wp_get_themes();
    }

    /**
     * @since 3.3.20 First time this was introduced in Borlabs Cookie.
     */
    public function getWpVersion(): ?string
    {
        if (function_exists('wp_get_wp_version')) {
            return wp_get_wp_version();
        }

        static $wp_version;

        if (!isset($wp_version)) {
            require ABSPATH . WPINC . '/version.php';
        }

        return isset($wp_version) && is_string($wp_version) ? $wp_version : null;
    }

    public function hasAction(string $hookName, ?callable $callback = null): bool
    {
        return has_action($hookName, $callback ?? false) !== false;
    }

    public function includesUrl(string $path = '', ?string $scheme = null): string
    {
        return includes_url($path, $scheme);
    }

    public function isFeed(): bool
    {
        return function_exists('is_feed') && is_feed();
    }

    public function isMultisite(): bool
    {
        return (bool) is_multisite(); // WordPress does not check whether the constant for MULTISTIE is a Boolean value.
    }

    public function isUserLoggedIn(): bool
    {
        return is_user_logged_in();
    }

    public function loadPluginTextdomain($domain, $pluginRelPath = false): bool
    {
        return load_plugin_textdomain($domain, false, $pluginRelPath);
    }

    public function loadTextDomain($domain, $mofile, $locale = null): bool
    {
        return load_textdomain($domain, $mofile, $locale);
    }

    public function numberFormatI18n(float $number, int $decimals = 0): string
    {
        return number_format_i18n($number, $decimals);
    }

    public function pluginsUrl(string $path = '', string $plugin = ''): string
    {
        return plugins_url($path, $plugin);
    }

    public function registerRestRoute(
        string $routeNamespace,
        string $route,
        array $args = []
    ): bool {
        return register_rest_route($routeNamespace, $route, $args);
    }

    public function removeAction(
        string $hookName,
        array $callback,
        int $priority = 10
    ): bool {
        return remove_action($hookName, $callback, $priority);
    }

    /**
     * @since 3.3.15 First time this was introduced in Borlabs Cookie
     */
    public function removeFilter(
        string $hookName,
        array $callback,
        int $priority = 10
    ): bool {
        return remove_filter($hookName, $callback, $priority);
    }

    public function restUrl(): string
    {
        return rest_url();
    }

    public function setTransient(string $transient, $value, int $expiration = 0): bool
    {
        return set_transient($transient, $value, $expiration);
    }

    public function switchToBlog(int $blogId): bool
    {
        return switch_to_blog($blogId);
    }

    public function unloadTextDomain(string $domain): bool
    {
        return unload_textdomain($domain);
    }

    public function unzipFile(string $filePath, string $destinationPath): bool
    {
        return unzip_file($filePath, $destinationPath) === true;
    }

    /**
     * This method is a wrapper for the WordPress function `update_option`. This method is used by
     * {@see \Borlabs\Cookie\System\Option\Option}.
     *
     * @param string $option   name of the option
     * @param mixed  $value    any serializable data
     * @param bool   $autoload optional; Default: `false`; `true`: The option is loaded when WordPress starts up
     */
    public function updateOption(string $option, $value, bool $autoload = false): bool
    {
        return update_option($option, $value, $autoload);
    }

    public function updatePostMeta(int $postId, string $metaKey, string $metaValue, string $previousValue = '')
    {
        return update_post_meta($postId, $metaKey, $metaValue, $previousValue);
    }

    /**
     * This method is a wrapper for the WordPress function `update_site_option`. This method is used by
     * {@see \Borlabs\Cookie\System\Option\Option}.
     *
     * @param string $option name of the option
     * @param mixed  $value  any serializable data
     */
    public function updateSiteOption(string $option, $value): bool
    {
        return update_site_option($option, $value);
    }

    public function wpAddInlineScript(string $handle, string $data, string $position = 'after'): bool
    {
        return wp_add_inline_script($handle, $data, $position);
    }

    public function wpAddInlineStyle(string $handle, string $css): bool
    {
        return wp_add_inline_style($handle, $css);
    }

    public function wpClearScheduledHook(string $eventName, array $args = []): bool
    {
        $status = wp_clear_scheduled_hook($eventName, $args);

        return is_int($status);
    }

    /**
     * @since 3.4 First time this was introduced in Borlabs Cookie.
     */
    public function wpCreateNonce(string $action): string
    {
        return wp_create_nonce($action);
    }

    public function wpDoingAjax(): bool
    {
        return wp_doing_ajax();
    }

    /**
     * This adapter function is required because wp_dropdown_pages is empty when no page/post exists.
     *
     * @param mixed $args
     */
    public function wpDropdownPages($args): string
    {
        $dropdownHtml = wp_dropdown_pages($args);

        if (empty($dropdownHtml)) {
            $dropdownHtml = <<<EOT
<select class="{$args['class']}" id="{$args['id']}" name="{$args['name']}">
    <option value="{$args['option_none_value']}">{$args['show_option_none']}</option>
</select>
EOT;
        }

        return $dropdownHtml;
    }

    public function wpEnqueueCodeEditor(array $args): ?array
    {
        $settings = wp_enqueue_code_editor($args);

        return is_array($settings) ? $settings : null;
    }

    public function wpEnqueueMedia(): void
    {
        wp_enqueue_media();
    }

    public function wpEnqueueScript(
        string $handle,
        ?string $filePath = null,
        ?array $dependencies = null,
        ?string $version = null,
        ?bool $placeInFooter = null
    ): void {
        wp_enqueue_script(
            $handle,
            $filePath ?? '',
            $dependencies ?? [],
            $version ?? false,
            $placeInFooter ?? false,
        );
    }

    /**
     * This method is a wrapper for the WordPress function `wp_enqueue_script_module`, which was added in WordPress 6.5.0.
     * This function incorporates a fallback mechanism for compatibility with older versions of WordPress.
     *
     * # Note:
     * WordPress outputs regular JavaScript files in the `wp_head` action with priority `9`, while JavaScript modules are
     * printed in block themes with priority `10` in `wp_head`, and in non-block themes they are printed in `wp_footer`.
     * JavaScript modules cannot have dependencies on regular JavaScript files; any such dependencies will be ignored
     */
    public function wpEnqueueScriptModule(
        string $id,
        ?string $src = null,
        ?array $dependencies = null,
        ?string $version = null,
        ?array $args = null
    ): void {
        // Check if the function exists, because it was added in WordPress 6.5.0
        if (function_exists('wp_enqueue_script_module')) {
            wp_enqueue_script_module(
                $id,
                $src ?? '',
                $dependencies ?? [],
                $version ?? false,
                $args ?? [],
            );

            return;
        }

        $this->wpEnqueueScript($id, $src, $dependencies, $version);
    }

    public function wpEnqueueStyle(
        string $handle,
        ?string $filePath = null,
        ?array $dependencies = null,
        ?string $version = null,
        ?string $media = null
    ): void {
        wp_enqueue_style(
            $handle,
            $filePath ?? '',
            $dependencies ?? [],
            $version ?? false,
            $media ?? 'all',
        );
    }

    /**
     * @since 3.4 First time this was introduced in Borlabs Cookie.
     */
    public function wpGetCurrentUser(): WP_user
    {
        return wp_get_current_user();
    }

    public function wpLocalizeScript(string $handle, string $objectName, array $l10n): bool
    {
        return wp_localize_script($handle, $objectName, $l10n);
    }

    public function wpMail(
        string $to,
        string $subject,
        string $message,
        array $headers = [],
        array $attachments = []
    ): bool {
        return wp_mail($to, $subject, $message, $headers, $attachments) === true;
    }

    public function wpMkdirP(string $path): bool
    {
        return wp_mkdir_p($path);
    }

    public function wpNextScheduled(string $eventName, array $args = []): bool
    {
        return is_int(wp_next_scheduled($eventName, $args));
    }

    public function wpNonceField(string $action, string $name = '_wpnonce', bool $referer = true)
    {
        return wp_nonce_field($action, $name, $referer, false);
    }

    public function wpNonceUrl(string $actionUrl, string $action, string $name = '_wpnonce')
    {
        return wp_nonce_url($actionUrl, $action, $name);
    }

    public function wpRemoteGet(string $url, array $args = []): WpRemoteResponseDto
    {
        $data = wp_remote_get($url, $args);

        return $this->wpResponseTransformer(
            is_array($data) ? $data : [],
            $data instanceof WP_Error ? $data : null,
        );
    }

    public function wpRemotePost(string $url, array $args = []): WpRemoteResponseDto
    {
        $data = wp_remote_post($url, $args);

        return $this->wpResponseTransformer(
            is_array($data) ? $data : [],
            $data instanceof WP_Error ? $data : null,
        );
    }

    /**
     * @since 3.4 First time this was introduced in Borlabs Cookie.
     */
    public function wpSafeRedirect(string $location, int $status = 302): bool
    {
        return wp_safe_redirect($location, $status);
    }

    public function wpScheduleEvent(
        int $timestamp,
        string $recurrence,
        string $eventName,
        array $args = []
    ): bool {
        return wp_schedule_event($timestamp, $recurrence, $eventName, $args);
    }

    public function wpTimezone(): ?DateTimeZone
    {
        if (function_exists('wp_timezone')) {
            return wp_timezone();
        }

        $gmtOffset = $this->getOption('gmt_offset');

        if ($gmtOffset) {
            $hours = (int) $gmtOffset;
            $minutes = abs(((float) $gmtOffset - $hours) * 60);

            return new DateTimeZone(sprintf('%+03d:%02d', $hours, $minutes));
        }

        return null;
    }

    public function wpVerifyNonce(string $action, string $nonce): bool
    {
        return (bool) wp_verify_nonce($nonce, $action);
    }

    private function wpResponseTransformer(array $response, ?WP_Error $wpError = null): WpRemoteResponseDto
    {
        $wpRemoteResponseDto = new WpRemoteResponseDto();
        $wpRemoteResponseDto->body = $response['body'] ?? null;
        $wpRemoteResponseDto->responseCode = $response['response']['code'] ?? null;
        $wpRemoteResponseDto->responseMessage = $response['response']['message'] ?? null;

        if (isset($response['headers'])) {
            $wpRemoteResponseDto->headers = new KeyValueDtoList();

            foreach ($response['headers'] as $key => $value) {
                $wpRemoteResponseDto->headers->add(
                    new KeyValueDto($key, $value),
                );
            }
        }

        if (isset($response['http_response']) && method_exists($response['http_response'], 'get_response_object')) {
            $responseObject = $response['http_response']->get_response_object();
            $wpRemoteResponseDto->redirected = (bool) ($responseObject->redirects ?? 0);
            $wpRemoteResponseDto->url = $responseObject->url ?? null;
        }

        if (isset($wpError)) {
            $wpRemoteResponseDto->errors = new KeyValueDtoList();

            foreach ($wpError->get_error_codes() as $errorCode) {
                $wpRemoteResponseDto->errors->add(
                    new KeyValueDto($errorCode, $wpError->get_error_message($errorCode)),
                );
            }
        }

        return $wpRemoteResponseDto;
    }
}
