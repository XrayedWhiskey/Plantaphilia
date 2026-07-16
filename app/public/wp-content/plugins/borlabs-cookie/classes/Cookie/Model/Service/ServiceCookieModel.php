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

namespace Borlabs\Cookie\Model\Service;

use Borlabs\Cookie\Enum\Service\CookiePurposeEnum;
use Borlabs\Cookie\Enum\Service\CookieTypeEnum;
use Borlabs\Cookie\Model\AbstractModel;

final class ServiceCookieModel extends AbstractModel
{
    public string $description = '';

    public ?string $hostname = null;

    public string $lifetime = '';

    public string $name;

    public ?string $path = null;

    public CookiePurposeEnum $purpose;

    public ?ServiceModel $service;

    public int $serviceId;

    public CookieTypeEnum $type;
}
