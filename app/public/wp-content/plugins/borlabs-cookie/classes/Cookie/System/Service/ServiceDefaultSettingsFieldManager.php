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

namespace Borlabs\Cookie\System\Service;

use Borlabs\Cookie\System\DefaultSettingsField\DefaultSettingsFieldManager;
use Borlabs\Cookie\System\Service\DefaultSettingsField\AsynchronousOptOutCode;
use Borlabs\Cookie\System\Service\DefaultSettingsField\BlockCookiesBeforeConsent;
use Borlabs\Cookie\System\Service\DefaultSettingsField\DisableCodeExecution;
use Borlabs\Cookie\System\Service\DefaultSettingsField\Prioritize;

final class ServiceDefaultSettingsFieldManager extends DefaultSettingsFieldManager
{
    public array $registry = [
        AsynchronousOptOutCode::class,
        BlockCookiesBeforeConsent::class,
        DisableCodeExecution::class,
        Prioritize::class,
    ];
}
