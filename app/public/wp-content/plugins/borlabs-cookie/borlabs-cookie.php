<?php
/*
Plugin Name: Borlabs Cookie
Plugin URI: https://borlabs.io/
Description: Borlabs Cookie helps you make your website GDPR compliant by providing an opt-in option to its visitors.
Author: Borlabs GmbH
Author URI: https://borlabs.io
Version: 3.4
Text Domain: borlabs-cookie
Domain Path: /languages
Requires at least: 4.7
Requires PHP: 7.4
*/

$borlabsCookieLocale = get_locale();

if (empty($borlabsCookieLocale) || strlen($borlabsCookieLocale) <= 1) {
    $borlabsCookieLocale = 'en_US';
}

define('BORLABS_COOKIE_VERSION', '3.4');
define('BORLABS_COOKIE_BUILD', '260206');
define('BORLABS_COOKIE_BASENAME', plugin_basename(__FILE__));
define('BORLABS_COOKIE_SLUG', basename(BORLABS_COOKIE_BASENAME, '.php'));
define('BORLABS_COOKIE_PLUGIN_PATH', rtrim(plugin_dir_path(__FILE__), '/'));
define('BORLABS_COOKIE_PLUGIN_URL', rtrim(plugin_dir_url(__FILE__), '/'));
define('BORLABS_COOKIE_DEFAULT_LANGUAGE', $borlabsCookieLocale);

// Prevent direct access
if (! defined('ABSPATH')) {
    exit;
}

if (!version_compare(phpversion(), '7.4', '>=')) {
    //! Fallback for very old php version
    add_action('admin_notices', function () {
        ?>
        <div class="notice notice-error">
            <p><?php
                _ex(
                    'Your PHP version is <a href="http://php.net/supported-versions.php" rel="nofollow noreferrer" target="_blank">outdated</a> and not supported by Borlabs Cookie. Please disable Borlabs Cookie, upgrade to PHP 7.4 or higher, and enable Borlabs Cookie again. It is necessary to follow these steps in the exact order described.',
                    'Backend / Global / Alert Message',
                    'borlabs-cookie'
                ); ?></p>
        </div>
        <?php
    });

    return;
}

require_once BORLABS_COOKIE_PLUGIN_PATH . '/vendor/autoload.php';
require_once BORLABS_COOKIE_PLUGIN_PATH . '/vendor-prefixed/symfony/polyfill-ctype/bootstrap.php';
require_once BORLABS_COOKIE_PLUGIN_PATH . '/vendor-prefixed/symfony/polyfill-mbstring/bootstrap.php';
require_once BORLABS_COOKIE_PLUGIN_PATH . '/vendor-prefixed/symfony/polyfill-php80/bootstrap.php';

if (defined('BORLABS_COOKIE_DEV_MODE_DISABLE_SSL_VERIFY')
    && constant('BORLABS_COOKIE_DEV_MODE_DISABLE_SSL_VERIFY') === true
) {
    // Allow self-signed certificates
    add_filter('https_ssl_verify', '__return_false');
    // Allow local hosts
    add_filter('http_request_host_is_external', '__return_true');
}

$container = new \Borlabs\Cookie\Container\Container;
\Borlabs\Cookie\Container\ApplicationContainer::init($container);

/* Start registration of Borlabs Cookie components. */
$container->get(\Borlabs\Cookie\System\WordPressGlobalFunctions\WordPressGlobalFunctionService::class)->register();

if (defined('BORLABS_COOKIE_DEV_MODE_ENABLE_HTTP_MOCK_CLIENT')) {
    $container->add(
        \Borlabs\Cookie\HttpClient\HttpClientInterface::class,
        \Borlabs\Cookie\HttpClient\HttpMockClient::class
    );
} else {
    $container->add(
        \Borlabs\Cookie\HttpClient\HttpClientInterface::class,
        \Borlabs\Cookie\HttpClient\HttpClient::class
    );
}

register_activation_hook(
    __FILE__,
    function () use ($container) {
        $container->get(\Borlabs\Cookie\System\Installer\Install::class)->pluginActivated();
    }
);

register_deactivation_hook(
    __FILE__,
    function () use ($container) {
        $container->get(\Borlabs\Cookie\ScheduleEvent\ScheduleEventManager::class)->deregister();
    }
);

/* Init plugin */
if (is_admin()) {
    /* Backend */
    add_action(
        'init',
        function () use ($container) {
            $container->get(\Borlabs\Cookie\System\WordPressAdminDriver\WordPressAdminInit::class)->register();
        }
    );
} else {
    /* Frontend */
    add_action(
        'init',
        function () use ($container) {
            $container->get(\Borlabs\Cookie\System\WordPressFrontendDriver\WordPressFrontendInit::class)->register();
        }
    );
}

/* PHP API for third-party develeoper */
if (!function_exists('borlabsCookieApi')) {
    /**
     * This function can be used by third-party developers to access the PHP API of Borlabs Cookie.
     * It becomes available after the WordPress `init` hook is triggered.
     * If the function is called before the `init` hook, it will return null.
     * If you need to use the function on the `init` hook, you may need to set the priority of your `init` hook registration to `11`.
     *
     * @see \Borlabs\Cookie\Command\CommandInit::init() If you are looking for WP CLI commands, please use the `borlabs-cookie` command.
     *
     *@return \Borlabs\CookieApi\PhpApi\PhpApi|null
     *
     */
    function borlabsCookieApi(): ?\Borlabs\CookieApi\PhpApi\PhpApi {
        return isset($GLOBALS['BorlabsCookieApiFunction']) ? $GLOBALS['BorlabsCookieApiFunction']() : null;
    }

    add_action(
        'init',
        function () use ($container) {
            $GLOBALS['BorlabsCookieApiFunction'] = function() use ($container) {
                return $container->get(\Borlabs\CookieApi\PhpApi\PhpApi::class);
            };
        }
    );
}

/* Init scheduled events */
add_action(
    'init',
    function () use ($container) {
        $container->get(\Borlabs\Cookie\ScheduleEvent\ScheduleEventManager::class)->register();
    }
);

/* Register REST endpoints */
add_action(
    'rest_api_init',
    function () use ($container) {
        $container->get(\Borlabs\Cookie\RestEndpoint\RestEndpointManager::class)->register();
    }
);

/* Update*/
$container->get(\Borlabs\Cookie\System\Updater\Updater::class)->register();

/* Check if the update should be disabled. */
add_filter(
    'auto_update_plugin',
    function (?bool $update = null, $itemUpdateData = null) use ($container) {
        return $container->get(\Borlabs\Cookie\System\Updater\Updater::class)->shouldApplyAutoUpdate($update, $itemUpdateData);
    },
    10,
    2
);

/* Run once the plugin file update process is complete. */
add_action(
    'upgrader_process_complete',
    function ($wpUpgraderInstance, $itemUpdateData) use ($container) {
        $container->get(\Borlabs\Cookie\System\Updater\Updater::class)->fileUpdateComplete($wpUpgraderInstance, $itemUpdateData);
    },
    10,
    2
);

if (defined('WP_CLI') && WP_CLI) {
    $container->get(Borlabs\Cookie\Command\CommandInit::class)->init();
}
