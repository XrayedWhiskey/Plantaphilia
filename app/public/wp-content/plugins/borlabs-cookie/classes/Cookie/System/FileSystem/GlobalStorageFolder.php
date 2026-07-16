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

namespace Borlabs\Cookie\System\FileSystem;

use Borlabs\Cookie\Adapter\WpFunction;

class GlobalStorageFolder implements FileLocationInterface
{
    protected WpFunction $wpFunction;

    public function __construct(WpFunction $wpFunction)
    {
        $this->wpFunction = $wpFunction;
    }

    public function getPath(): string
    {
        if (defined('BORLABS_COOKIE_STORAGE_PATH')) {
            // @noinspection PhpUndefinedConstantInspection
            return BORLABS_COOKIE_STORAGE_PATH;
        }

        return WP_CONTENT_DIR . '/uploads/' . BORLABS_COOKIE_SLUG;
    }

    public function getRootPath(): string
    {
        return WP_CONTENT_DIR . '/uploads';
    }

    public function getUrl(): string
    {
        if (defined('BORLABS_COOKIE_STORAGE_URL')) {
            // @noinspection PhpUndefinedConstantInspection
            return BORLABS_COOKIE_STORAGE_URL;
        }

        return WP_CONTENT_URL . '/uploads/' . BORLABS_COOKIE_SLUG;
    }
}
