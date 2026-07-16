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
use Borlabs\Cookie\Exception\RestEndpoint\InvalidRemoteIpRestInputException;
use Borlabs\Cookie\System\GeoIp\GeoIp;
use Borlabs\Cookie\System\Language\Language;
use WP_REST_Request;

/**
 * Handles the visibility of the cookie box in different regions.
 */
class DialogVisibilityEndpoint implements RestEndpointInterface
{
    private GeoIp $geoIp;

    private Language $language;

    private WpFunction $wpFunction;

    /**
     * CookieBoxEndpoint constructor.
     */
    public function __construct(
        GeoIp $geoIp,
        Language $language,
        WpFunction $wpFunction
    ) {
        $this->geoIp = $geoIp;
        $this->language = $language;
        $this->wpFunction = $wpFunction;
    }

    /**
     * Registers the REST endpoints.
     */
    public function register(): void
    {
        $this->wpFunction->registerRestRoute(
            RestEndpointManager::NAMESPACE . '/v1',
            '/dialog/visibility',
            [
                'methods' => 'GET',
                'callback' => [$this, 'visibility',],
                'permission_callback' => '__return_true',
            ],
        );
    }

    /**
     * Visibility of the cookie box in the country of the IP address of the user.
     */
    public function visibility(WP_REST_Request $request): array
    {
        $language = $request->get_param('language') ?? $this->language->getCurrentLanguageCode();

        if (!$this->language->isValidLanguageCode($language)) {
            // Fallback, always display dialog if language is invalid
            return ['visible' => true];
        }

        try {
            $showDialogForCurrentUser = $this->geoIp->getShowDialogStatusForCurrentUser($language);
        } catch (InvalidRemoteIpRestInputException $e) {
            // Fallback, always display dialog if IP is invalid
            return ['visible' => true];
        }

        return [
            'visible' => $showDialogForCurrentUser,
        ];
    }
}
