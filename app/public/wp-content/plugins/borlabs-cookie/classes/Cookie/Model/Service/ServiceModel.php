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

use Borlabs\Cookie\DtoList\System\RepeatableSettingsFieldDtoList;
use Borlabs\Cookie\DtoList\System\SettingsFieldDtoList;
use Borlabs\Cookie\Model\AbstractModel;
use Borlabs\Cookie\Model\Provider\ProviderModel;
use Borlabs\Cookie\Model\ServiceGroup\ServiceGroupModel;

final class ServiceModel extends AbstractModel
{
    public ?string $borlabsServicePackageKey;

    /**
     * @var null|array<\Borlabs\Cookie\Model\ContentBlocker\ContentBlockerModel>
     */
    public ?array $contentBlockers;

    public string $description = '';

    public string $fallbackCode = '';

    public string $key;

    public string $language;

    public string $name;

    public string $optInCode = '';

    public string $optOutCode = '';

    public int $position = 1;

    public ?ProviderModel $provider;

    public int $providerId;

    public ?RepeatableSettingsFieldDtoList $repeatableSettingsFields = null;

    /**
     * @var null|array<\Borlabs\Cookie\Model\Service\ServiceCookieModel>
     */
    public ?array $serviceCookies;

    public ?ServiceGroupModel $serviceGroup;

    public int $serviceGroupId;

    /**
     * @var null|array<\Borlabs\Cookie\Model\Service\ServiceLocationModel>
     */
    public ?array $serviceLocations;

    /**
     * @var null|array<\Borlabs\Cookie\Model\Service\ServiceOptionModel>
     */
    public ?array $serviceOptions;

    public ?SettingsFieldDtoList $settingsFields;

    public bool $status = false;

    public bool $undeletable = false;
}
