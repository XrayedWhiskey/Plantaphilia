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

use Borlabs\Cookie\Enum\Service\ServiceOptionEnum;
use Borlabs\Cookie\Model\AbstractModel;

final class ServiceOptionModel extends AbstractModel
{
    public string $description;

    public string $language;

    public ?ServiceModel $service;

    public int $serviceId;

    public ServiceOptionEnum $type;
}
