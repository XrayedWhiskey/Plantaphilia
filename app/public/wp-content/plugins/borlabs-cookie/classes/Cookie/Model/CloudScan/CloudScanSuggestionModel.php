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

use Borlabs\Cookie\DtoList\CloudScan\SuggestionPagesDtoList;
use Borlabs\Cookie\Model\AbstractModel;
use Borlabs\Cookie\Model\Package\PackageModel;

final class CloudScanSuggestionModel extends AbstractModel
{
    public ?string $borlabsServicePackageKey = null;

    public CloudScanModel $cloudScan;

    public int $cloudScanId;

    public PackageModel $package;

    public ?SuggestionPagesDtoList $pages;
}
