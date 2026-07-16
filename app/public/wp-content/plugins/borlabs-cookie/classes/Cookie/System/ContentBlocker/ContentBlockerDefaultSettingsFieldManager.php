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

namespace Borlabs\Cookie\System\ContentBlocker;

use Borlabs\Cookie\System\ContentBlocker\DefaultSettingsField\ExecuteGlobalCodeBeforeUnblocking;
use Borlabs\Cookie\System\DefaultSettingsField\DefaultSettingsFieldManager;

final class ContentBlockerDefaultSettingsFieldManager extends DefaultSettingsFieldManager
{
    public array $registry = [
        ExecuteGlobalCodeBeforeUnblocking::class,
    ];
}
