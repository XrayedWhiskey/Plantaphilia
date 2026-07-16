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

namespace Borlabs\Cookie\Controller\Frontend\MetaBox;

use Borlabs\Cookie\Controller\Frontend\ControllerInterface;
use Borlabs\Cookie\Dto\System\RequestDto;
use Borlabs\Cookie\System\MetaBox\MetaBoxService;

final class MetaBoxController implements ControllerInterface
{
    private MetaBoxService $metaBoxService;

    public function __construct(MetaBoxService $metaBoxService)
    {
        $this->metaBoxService = $metaBoxService;
    }

    public function handle(RequestDto $request): void
    {
        $this->metaBoxService->init();
    }

    public function shouldHandle(RequestDto $request): bool
    {
        // At this time there is no information available about the contribution, so we have to return true
        return true;
    }
}
