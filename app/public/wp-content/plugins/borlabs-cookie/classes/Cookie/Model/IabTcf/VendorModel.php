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

namespace Borlabs\Cookie\Model\IabTcf;

use Borlabs\Cookie\Dto\IabTcf\DataRetentionDto;
use Borlabs\Cookie\DtoList\IabTcf\VendorUrlsDtoList;
use Borlabs\Cookie\Model\AbstractModel;

final class VendorModel extends AbstractModel
{
    public int $cookieMaxAgeSeconds;

    public array $dataDeclaration = [];

    public DataRetentionDto $dataRetention;

    public string $deviceStorageDisclosureUrl;

    public array $features = [];

    public array $legIntPurposes = [];

    public string $name;

    public array $purposes = [];

    public array $specialFeatures = [];

    public array $specialPurposes = [];

    public bool $status = false;

    public VendorUrlsDtoList $urls;

    public bool $usesCookies;

    public bool $usesNonCookieAccess;

    public int $vendorId;
}
