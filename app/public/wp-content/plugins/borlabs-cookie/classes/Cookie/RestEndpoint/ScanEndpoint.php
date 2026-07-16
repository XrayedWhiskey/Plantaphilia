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
use Borlabs\Cookie\Dto\ApiClient\ServiceResponseDto;
use Borlabs\Cookie\HttpClient\HttpClient;
use WP_REST_Request;

final class ScanEndpoint implements RestEndpointInterface
{
    private HttpClient $httpClient;

    private WpFunction $wpFunction;

    public function __construct(
        HttpClient $httpClient,
        WpFunction $wpFunction
    ) {
        $this->httpClient = $httpClient;
        $this->wpFunction = $wpFunction;
    }

    public function register(): void
    {
        $this->wpFunction->registerRestRoute(
            RestEndpointManager::NAMESPACE . '/v1',
            '/scan/',
            [
                'methods' => 'POST',
                'callback' => [$this, 'scan'],
                'permission_callback' => function () {
                    return $this->wpFunction->currentUserCan('manage_borlabs_cookie');
                },
            ],
        );
    }

    public function scan(WP_REST_Request $request): ServiceResponseDto
    {
        if ($request->get_param('signedUrl') === null) {
            return new ServiceResponseDto(false, 0, '', (object) []);
        }

        return $this->httpClient->get($request->get_param('signedUrl'));
    }
}
