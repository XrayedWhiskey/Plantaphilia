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

namespace Borlabs\Cookie\Dto\Config;

use Borlabs\Cookie\Enum\Library\AutoUpdateIntervalEnum;

/**
 * The **LibraryDto** class is used as a typed object that is passed within the system.
 *
 * The object defines the criteria for updating packages in the library.
 *
 * @see \Borlabs\Cookie\System\Config\LibraryConfig
 */
final class LibraryDto extends AbstractConfigDto
{
    public bool $enableEmailNotificationsForUpdatablePackagesWithAutoUpdateDisabled = false;

    public bool $enableEmailNotificationsForUpdatablePackagesWithAutoUpdateEnabled = true;

    public array $packageAutoUpdateEmailAddresses = [];

    public AutoUpdateIntervalEnum $packageAutoUpdateInterval;

    public string $packageAutoUpdateTime = '08:30';

    public string $packageAutoUpdateTimeSpan = '08:00-09:59';
}
