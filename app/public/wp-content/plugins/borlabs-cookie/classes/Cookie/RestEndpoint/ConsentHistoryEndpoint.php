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
use Borlabs\Cookie\System\Consent\ConsentLogService;
use Borlabs\Cookie\System\Language\Language;
use Borlabs\Cookie\System\Log\Log;
use Exception;
use WP_REST_Request;

/**
 * Handles returning of the consent log history.
 */
final class ConsentHistoryEndpoint implements RestEndpointInterface
{
    private ConsentLogService $consentLogService;

    private Language $language;

    private Log $log;

    private WpFunction $wpFunction;

    public function __construct(
        ConsentLogService $consentLogService,
        Language $language,
        Log $log,
        WpFunction $wpFunction
    ) {
        $this->consentLogService = $consentLogService;
        $this->language = $language;
        $this->log = $log;
        $this->wpFunction = $wpFunction;
    }

    public function history(WP_REST_Request $request): array
    {
        $uid = null;

        if (isset($_COOKIE['borlabs-cookie'])) {
            try {
                $pluginCookie = json_decode(stripslashes($_COOKIE['borlabs-cookie']));

                if (isset($pluginCookie->uid)) {
                    $uid = $pluginCookie->uid;
                } else {
                    $this->log->debug('ConsentHistoryEndpoint: Parsed Borlabs Cookie plugin did not contain UID.');
                }
            } catch (Exception $e) {
                // TODO can this Exception ever happen?
                $this->log->debug('ConsentHistoryEndpoint: Parsing of Borlabs Cookie plugin cookie failed.', [
                    'exceptionMessage' => $e->getMessage(),
                ]);
            }
        } else {
            $this->log->debug('ConsentHistoryEndpoint: Borlabs Cookie plugin cookie not found.');
        }

        if (isset($uid)) {
            $language = $request->get_param('language') ?? $this->language->getCurrentLanguageCode();

            if (!$this->language->isValidLanguageCode($language)) {
                $this->log->debug('ConsentHistoryEndpoint: Client provided invalid language code.', [
                    'languageCode' => $language,
                ]);

                return [];
            }

            return $this->consentLogService->getHistory($uid);
        }

        return [];
    }

    public function register(): void
    {
        $this->wpFunction->registerRestRoute(
            RestEndpointManager::NAMESPACE . '/v1',
            '/consent/history',
            [
                'methods' => 'GET',
                'callback' => [$this, 'history'],
                'permission_callback' => '__return_true',
            ],
        );
    }
}
