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

namespace Borlabs\Cookie\Model\ServiceGroup;

use Borlabs\Cookie\Model\AbstractModel;
use Borlabs\Cookie\Model\Service\ServiceModel;

/**
 * Class ServiceGroup.
 */
final class ServiceGroupModel extends AbstractModel
{
    public string $description = '';

    public string $key;

    public string $language;

    public string $name;

    public int $position = 1;

    public bool $preSelected = false;

    /**
     * @var ServiceModel[]
     */
    public $services;

    public bool $status = false;

    public bool $undeletable = false;
}
