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
use Borlabs\Cookie\DtoList\SetupAssistant\LanguageSpecificPageUrlByKeywordTypeDtoList;
use Borlabs\Cookie\System\SetupAssistant\SetupAssistantService;

final class LegalPagesDetectionEndpoint implements RestEndpointInterface
{
    private SetupAssistantService $setupAssistantService;

    private WpFunction $wpFunction;

    public function __construct(
        SetupAssistantService $setupAssistantService,
        WpFunction $wpFunction
    ) {
        $this->setupAssistantService = $setupAssistantService;
        $this->wpFunction = $wpFunction;
    }

    public function detectPages(): LanguageSpecificPageUrlByKeywordTypeDtoList
    {
        return $this->setupAssistantService->detectLegalPages();
    }

    public function register(): void
    {
        $this->wpFunction->registerRestRoute(
            RestEndpointManager::NAMESPACE . '/v1',
            '/setup-assistant/legal-pages-detection',
            [
                'methods' => 'GET',
                'callback' => [$this, 'detectPages'],
                'permission_callback' => function () {
                    return $this->wpFunction->currentUserCan('manage_borlabs_cookie');
                },
            ],
        );
    }
}
