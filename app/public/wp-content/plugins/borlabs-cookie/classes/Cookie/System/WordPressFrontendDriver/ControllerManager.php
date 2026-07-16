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

namespace Borlabs\Cookie\System\WordPressFrontendDriver;

use Borlabs\Cookie\Container\Container;
use Borlabs\Cookie\Controller\Frontend\CrossDomainCookie\CrossDomainCookieController;
use Borlabs\Cookie\Controller\Frontend\MetaBox\MetaBoxController;
use Borlabs\Cookie\Controller\Frontend\ScanRequest\ScanRequestController;
use Borlabs\Cookie\Dto\System\RequestDto;
use Borlabs\Cookie\Support\Sanitizer;

final class ControllerManager
{
    private Container $container;

    private array $registry = [
        CrossDomainCookieController::class,
        MetaBoxController::class,
        ScanRequestController::class,
    ];

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function init()
    {
        // Sanitize $_POST and $_GET for our system.
        $requestData = new RequestDto(
            Sanitizer::requestData($_POST),
            Sanitizer::requestData($_GET),
            Sanitizer::requestData($_SERVER),
        );

        foreach ($this->registry as $frontendControllerClass) {
            $frontendController = $this->container->get($frontendControllerClass);

            if ($frontendController->shouldHandle($requestData)) {
                $frontendController->handle($requestData);
            }
        }
    }
}
