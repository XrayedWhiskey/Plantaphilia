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

namespace Borlabs\Cookie\Controller\Frontend;

use Borlabs\Cookie\Dto\System\RequestDto;

interface ControllerInterface
{
    public function handle(RequestDto $request): void;

    public function shouldHandle(RequestDto $request): bool;
}
