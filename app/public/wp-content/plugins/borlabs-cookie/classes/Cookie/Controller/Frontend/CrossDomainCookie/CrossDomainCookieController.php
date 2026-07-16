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

namespace Borlabs\Cookie\Controller\Frontend\CrossDomainCookie;

use Borlabs\Cookie\Controller\Frontend\ControllerInterface;
use Borlabs\Cookie\Dto\System\RequestDto;
use Borlabs\Cookie\System\CrossDomainCookie\CrossDomainCookieService;

class CrossDomainCookieController implements ControllerInterface
{
    private CrossDomainCookieService $crossDomainCookieService;

    public function __construct(CrossDomainCookieService $crossDomainCookieService)
    {
        $this->crossDomainCookieService = $crossDomainCookieService;
    }

    public function handle(RequestDto $request): void
    {
        $this->crossDomainCookieService->init($request);
    }

    public function shouldHandle(RequestDto $request): bool
    {
        return isset(
            $request->getData['__borlabsCookieCrossDomainCookie'],
            $request->getData['__borlabsCookieLanguage'],
            $request->getData['__borlabsCookieCookieData']
        );
    }
}
