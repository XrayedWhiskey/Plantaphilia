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

namespace Borlabs\Cookie\System\WordPressGlobalFunctions;

use Borlabs\Cookie\Container\ApplicationContainer;
use Borlabs\Cookie\Container\Container;

/**
 * This is a helper function and should not be used outside of this file!
 *
 * @internal
 */
function currentContainer(): Container
{
    return ApplicationContainer::get();
}

/**
 * This is a wrapper for the global WordPress function. It should only be used inside our plugin.
 */
function _x(string $text, string $context, string $domain = 'default'): string
{
    return currentContainer()->get(WordPressGlobalFunctionService::class)->_x(...func_get_args());
}
