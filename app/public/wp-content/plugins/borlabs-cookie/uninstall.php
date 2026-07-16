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

if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}

define('BORLABS_COOKIE_SLUG', 'borlabs-cookie');

if (version_compare(phpversion(), '7.4', '>=')) {
    require_once plugin_dir_path(__FILE__) . '/vendor/autoload.php';

    $container = new \Borlabs\Cookie\Container\Container;
    \Borlabs\Cookie\Container\ApplicationContainer::init($container);
    $container->get(\Borlabs\Cookie\System\Uninstaller\Uninstaller::class)->run();
}
