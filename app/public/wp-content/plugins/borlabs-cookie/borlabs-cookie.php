<?php
/*
Plugin Name: Borlabs Cookie - Cookie Opt-in
Plugin URI: https://borlabs.io/
Description: Borlabs Cookie is an easy to use cookie opt-in and content block solution for WordPress. Create detailed descriptions for cookies and sort them in customizable 'Cookie Groups'. Create specific 'Content Blockers' and block everything from YouTube media to Facebook posts. Let your visitors choose which cookies they want to allow and what content they want to see. Borlabs Cookie helps you to make your website ready for GDPR & ePrivacy regulations.
Author: Borlabs GmbH
Author URI: https://borlabs.io
Version: 2.3
Text Domain: borlabs-cookie
Domain Path: /languages
Requires at least: 4.7
Requires PHP: 7.4
*/

$borlabsCookieWPLANG = get_option('WPLANG', 'en_US');

if (empty($borlabsCookieWPLANG) || strlen($borlabsCookieWPLANG) <= 1) {
    $borlabsCookieWPLANG = 'en';
}

define('BORLABS_COOKIE_VERSION', '2.3');
define('BORLABS_COOKIE_BUILD', '240917');
define('BORLABS_COOKIE_BASENAME', plugin_basename(__FILE__));
define('BORLABS_COOKIE_SLUG', basename(BORLABS_COOKIE_BASENAME, '.php'));
define('BORLABS_COOKIE_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('BORLABS_COOKIE_PLUGIN_URL', plugin_dir_url(__FILE__));

if (defined('BORLABS_COOKIE_IGNORE_ISO_639_1') === false) {
    define('BORLABS_COOKIE_DEFAULT_LANGUAGE', substr($borlabsCookieWPLANG, 0, 2));
} else {
    define('BORLABS_COOKIE_DEFAULT_LANGUAGE', $borlabsCookieWPLANG);
}

// Improving Docker performance on macOS during development
if (BORLABS_COOKIE_BUILD === '000000' && !defined('DISABLE_WP_CRON')) {
    define('DISABLE_WP_CRON', true);
}

// Prevent direct access
if (! defined('ABSPATH')) {
    exit;
}

if (version_compare(phpversion(), '7.2', '>=')) {
    include_once plugin_dir_path(__FILE__) . 'classes/Autoloader.php';

    $Autoloader = new \BorlabsCookie\Autoloader();
    $Autoloader->register();
    $Autoloader->addNamespace('BorlabsCookie', realpath(plugin_dir_path(__FILE__) . '/classes'));

    register_activation_hook(__FILE__, array(\BorlabsCookie\Cookie\Init::getInstance(), 'pluginActivated'));
    register_deactivation_hook(__FILE__, array(\BorlabsCookie\Cookie\Init::getInstance(), 'pluginDeactivated'));

    /* Init plugin */
    if (is_admin()) {
        /* Backend */
        \BorlabsCookie\Cookie\Init::getInstance()->initBackend();
    } else {
        /* Frontend */
        \BorlabsCookie\Cookie\Init::getInstance()->initFrontend();
    }

    /* Update*/
    \BorlabsCookie\Cookie\Init::getInstance()->initUpdateHooks();

    /* Call after upgrade process is complete */
    add_action(
        'upgrader_process_complete',
        function ($upgraderObject, $options) use ($Autoloader) {
            if (
                file_exists(rtrim(BORLABS_COOKIE_PLUGIN_PATH, '/').'/classes/Cookie/Container/Container.php')
                && file_exists(rtrim(BORLABS_COOKIE_PLUGIN_PATH, '/').'/classes/Cookie/Container/ApplicationContainer.php')
                && file_exists(rtrim(BORLABS_COOKIE_PLUGIN_PATH, '/').'/classes/Cookie/System/Updater/Updater.php')
                && !class_exists('\Borlabs\Cookie\Container\Container')
                && !class_exists('\Borlabs\Cookie\Container\ApplicationContainer')
                && !class_exists('\Borlabs\Cookie\System\Updater\Updater')) {
                require_once BORLABS_COOKIE_PLUGIN_PATH . '/vendor/autoload.php';
                require_once BORLABS_COOKIE_PLUGIN_PATH . '/vendor-prefixed/symfony/polyfill-ctype/bootstrap.php';
                require_once BORLABS_COOKIE_PLUGIN_PATH . '/vendor-prefixed/symfony/polyfill-mbstring/bootstrap.php';
                require_once BORLABS_COOKIE_PLUGIN_PATH . '/vendor-prefixed/symfony/polyfill-php80/bootstrap.php';

                spl_autoload_unregister([$Autoloader, 'loadClass']);

                $container = new \Borlabs\Cookie\Container\Container;
                \Borlabs\Cookie\Container\ApplicationContainer::init($container);
                $container->add(
                    \Borlabs\Cookie\HttpClient\HttpClientInterface::class,
                    \Borlabs\Cookie\HttpClient\HttpClient::class
                );
                $language = $container->get(\Borlabs\Cookie\System\Language\Language::class);
                $language->setInitializationSignal();
                $language->init();
                $language->loadTextDomain();

                $container->get(\Borlabs\Cookie\System\WordPressGlobalFunctions\WordpressGlobalFunctionService::class)->register();
                $container->get(\Borlabs\Cookie\System\Updater\Updater::class)->fileUpdateComplete($upgraderObject, $options);

                return;
            }

            \BorlabsCookie\Cookie\Update::getInstance()->upgradeComplete($upgraderObject, $options);
        },
        10,
        2
    );

    /* Fallback if the upgrade of Borlabs Cookie was not initiated via the upgrade process but replaced manually or even worse: via Composer */
    add_action('plugins_loaded', function () {
        $lastVersion = get_option('BorlabsCookieLegacyVersion', false);

        if (!$lastVersion) {
            $lastVersion = get_option('BorlabsCookieVersion', false);
        }

        /* If no last version exists, an upgrade is not needed */
        if ($lastVersion === false) {
            return;
        }

        if (defined('BORLABS_COOKIE_VERSION') && version_compare(BORLABS_COOKIE_VERSION, $lastVersion, '>')) {
            \BorlabsCookie\Cookie\Update::getInstance()->processUpgrade();
        }
    });

    /* Third Party Developer Helper Class Shortcut Function - fun fact: in german this would be a single noun! */
    if (! function_exists('BorlabsCookieHelper')) {
        function BorlabsCookieHelper()
        {
            return \BorlabsCookie\Cookie\ThirdPartyHelper::getInstance();
        }
    }
} else {
    //! Fallback for very old php version
    add_action('admin_notices', function () {
        ?>
        <div class="notice notice-error">
            <p><?php
                _ex(
                    'Your PHP version is <a href="http://php.net/supported-versions.php" rel="nofollow noopener noreferrer" target="_blank">outdated</a> and not supported by Borlabs Cookie. Please disable Borlabs Cookie, upgrade to PHP 7.2 or higher, and enable Borlabs Cookie again. It is necessary to follow these steps in the exact order described.',
                    'Backend / Global / Alert Message',
                    'borlabs-cookie'
                ); ?></p>
        </div>
        <?php
    });
}
?>
