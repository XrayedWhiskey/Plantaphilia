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

namespace Borlabs\Cookie\RestEndpoint;

use Borlabs\Cookie\Adapter\WpFunction;

/**
 * Test if REST API is working.
 */
final class TestEndpoint implements RestEndpointInterface
{
    private WpFunction $wpFunction;

    public function __construct(
        WpFunction $wpFunction
    ) {
        $this->wpFunction = $wpFunction;
    }

    public function register(): void
    {
        $this->wpFunction->registerRestRoute(
            RestEndpointManager::NAMESPACE . '/v1',
            '/test',
            [
                'methods' => 'POST',
                'callback' => fn () => true,
                'permission_callback' => '__return_true',
            ],
        );
    }
}
