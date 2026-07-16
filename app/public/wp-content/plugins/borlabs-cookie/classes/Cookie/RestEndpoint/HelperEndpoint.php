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
use WP_REST_Request;

/**
 * @deprecated 3.4.0 This class is deprecated and will be removed in a future version
 */
final class HelperEndpoint implements RestEndpointInterface
{
    private WpFunction $wpFunction;

    public function __construct(WpFunction $wpFunction)
    {
        $this->wpFunction = $wpFunction;
    }

    public function getPermalinkById(WP_REST_Request $request): array
    {
        $postId = $request->get_param('post_id');

        return ['permalink' => $this->wpFunction->getPermalink($postId)];
    }

    public function register(): void
    {
        $this->wpFunction->registerRestRoute(
            RestEndpointManager::NAMESPACE . '/v1',
            '/helper/permalink/(?P<post_id>\d+)',
            [
                'methods' => 'GET',
                'callback' => [$this, 'getPermalinkById'],
                'args' => [
                    'post_id' => [
                        'required' => true,
                        'sanitize_callback' => 'absint',
                    ],
                ],
                'permission_callback' => function () {
                    return $this->wpFunction->currentUserCan('manage_borlabs_cookie');
                },
            ],
        );
    }
}
