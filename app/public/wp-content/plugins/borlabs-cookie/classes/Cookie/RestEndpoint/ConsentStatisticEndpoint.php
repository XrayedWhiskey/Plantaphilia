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
use Borlabs\Cookie\Exception\RestEndpoint\InvalidUserRestInputException;
use Borlabs\Cookie\Support\Transformer;
use Borlabs\Cookie\System\Consent\ConsentLogService;
use Borlabs\Cookie\System\ConsentStatistic\ConsentStatisticService;
use WP_REST_Request;

/**
 * Handles logging of the consent statistics.
 */
final class ConsentStatisticEndpoint implements RestEndpointInterface
{
    private ConsentLogService $consentLogService;

    private ConsentStatisticService $consentStatisticService;

    private WpFunction $wpFunction;

    public function __construct(
        ConsentLogService $consentLogService,
        ConsentStatisticService $consentStatisticService,
        WpFunction $wpFunction
    ) {
        $this->consentLogService = $consentLogService;
        $this->consentStatisticService = $consentStatisticService;
        $this->wpFunction = $wpFunction;
    }

    public function add(WP_REST_Request $request): array
    {
        $requestData = Transformer::buildNestedArray($request->get_params());

        // TODO: Duplicated code from ConsentLogEndpoint.php, can be improved.
        if (!isset($requestData['language'],
            $requestData['consentLog']['uid'],
            $requestData['consentLog']['version'],
            $requestData['consentLog']['borlabsCookieConsentString'],
            $requestData['consentLog']['iabTcfTCString'])
        ) {
            return ['success' => false];
        }

        try {
            $consents = $this->consentLogService->getValidatedServiceGroupConsentList(
                $requestData['language'],
                $requestData['consentLog']['borlabsCookieConsentString'],
            );
        } catch (InvalidUserRestInputException $e) {
            return ['success' => false];
        }

        $this->consentStatisticService->add(
            $consents,
            $requestData['consentLog']['uid'],
            (int) $requestData['consentLog']['version'],
        );

        return ['success' => true];
    }

    public function register(): void
    {
        $this->wpFunction->registerRestRoute(
            RestEndpointManager::NAMESPACE . '/v1',
            '/consent/statistic',
            [
                'methods' => 'POST',
                'callback' => [$this, 'add'],
                'permission_callback' => '__return_true',
            ],
        );
    }
}
