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

namespace Borlabs\Cookie\Container;

final class ApplicationContainer
{
    private static Container $container;

    public static function get(): Container
    {
        return self::$container;
    }

    public static function init(Container $container)
    {
        self::$container = $container;
    }
}
