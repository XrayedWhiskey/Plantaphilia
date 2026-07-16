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

namespace Borlabs\Cookie\Model\CloudScan;

use Borlabs\Cookie\DtoList\CloudScan\ExternalResourceExampleDtoList;
use Borlabs\Cookie\Model\AbstractModel;

final class CloudScanExternalResourceModel extends AbstractModel
{
    public ?string $borlabsServicePackageKey = null;

    public CloudScanModel $cloudScan;

    public int $cloudScanId;

    public ?ExternalResourceExampleDtoList $examples;

    public string $hostname;
}
