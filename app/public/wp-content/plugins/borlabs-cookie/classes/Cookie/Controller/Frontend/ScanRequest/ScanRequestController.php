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

namespace Borlabs\Cookie\Controller\Frontend\ScanRequest;

use Borlabs\Cookie\Controller\Frontend\ControllerInterface;
use Borlabs\Cookie\Dto\System\RequestDto;
use Borlabs\Cookie\System\LocalScanner\ScanRequestService;

final class ScanRequestController implements ControllerInterface
{
    private ScanRequestService $scanRequestService;

    public function __construct(
        ScanRequestService $scanRequestService
    ) {
        $this->scanRequestService = $scanRequestService;
    }

    public function handle(RequestDto $request): void
    {
        $this->scanRequestService->init($request);
    }

    public function shouldHandle(RequestDto $request): bool
    {
        return !(
            !isset($request->getData['__borlabsCookieScannerConfig'])
            || !is_array($request->getData['__borlabsCookieScannerConfig'])
            || !isset($request->getData['__borlabsCookieSignature'])
        );
    }
}
