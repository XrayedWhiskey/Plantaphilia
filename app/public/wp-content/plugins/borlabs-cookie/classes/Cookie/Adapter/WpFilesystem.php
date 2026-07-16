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

use WP_Filesystem_Base;

/**
 * Class WpFilesystem.
 */
final class WpFilesystem
{
    public static function getInstance($args = false, $context = false, $allowRelaxedFileOwnership = false): WP_Filesystem_Base
    {
        global $wp_filesystem;

        if (!function_exists('WP_Filesystem')) {
            require ABSPATH . 'wp-admin/includes/file.php';
        }

        WP_Filesystem($args, $context, $allowRelaxedFileOwnership);

        return $wp_filesystem;
    }

    public function __construct()
    {
    }

    public function __call($method, $args)
    {
        return call_user_func_array([self::getInstance(), $method], $args);
    }

    public static function __callStatic($method, $args)
    {
        return forward_static_call_array([self::getInstance(), $method], $args);
    }

    public function __get($varname)
    {
        return self::getInstance()->{$varname};
    }
}
