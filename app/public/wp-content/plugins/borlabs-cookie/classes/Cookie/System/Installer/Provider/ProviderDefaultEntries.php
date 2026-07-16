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

namespace Borlabs\Cookie\System\Installer\Provider;

use Borlabs\Cookie\System\Installer\DefaultEntriesManager;
use Borlabs\Cookie\System\Installer\Provider\Entry\UnknownEntry;
use Borlabs\Cookie\System\Installer\Provider\Entry\WebsiteOwnerEntry;

final class ProviderDefaultEntries extends DefaultEntriesManager
{
    public array $registry = [
        UnknownEntry::class,
        WebsiteOwnerEntry::class,
    ];
}
