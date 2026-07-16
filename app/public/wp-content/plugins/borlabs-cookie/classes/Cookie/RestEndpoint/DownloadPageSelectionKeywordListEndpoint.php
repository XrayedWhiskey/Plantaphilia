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
use Borlabs\Cookie\Exception\TranslatedException;
use Borlabs\Cookie\System\Log\Log;
use Borlabs\Cookie\System\PageSelection\PageSelectionService;

final class DownloadPageSelectionKeywordListEndpoint implements RestEndpointInterface
{
    private Log $log;

    private PageSelectionService $pageSelectionService;

    private WpFunction $wpFunction;

    public function __construct(
        Log $log,
        PageSelectionService $pageSelectionService,
        WpFunction $wpFunction
    ) {
        $this->log = $log;
        $this->pageSelectionService = $pageSelectionService;
        $this->wpFunction = $wpFunction;
    }

    public function download(): bool
    {
        $status = false;

        try {
            $status = $this->pageSelectionService->downloadKeywordList();
        } catch (TranslatedException $e) {
            $message = $e->getTranslatedMessage();
            $this->log->error($message);
        }

        return $status;
    }

    public function register(): void
    {
        $this->wpFunction->registerRestRoute(
            RestEndpointManager::NAMESPACE . '/v1',
            '/download/page-selection-keyword-list',
            [
                'methods' => 'GET',
                'callback' => [$this, 'download'],
                'permission_callback' => function () {
                    return $this->wpFunction->currentUserCan('manage_borlabs_cookie');
                },
            ],
        );
    }
}
