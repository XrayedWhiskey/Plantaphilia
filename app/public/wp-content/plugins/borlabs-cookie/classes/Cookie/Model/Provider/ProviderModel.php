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

namespace Borlabs\Cookie\Model\Provider;

use Borlabs\Cookie\Model\AbstractModel;
use Borlabs\Cookie\Model\ContentBlocker\ContentBlockerModel;
use Borlabs\Cookie\Model\Service\ServiceModel;

final class ProviderModel extends AbstractModel
{
    /**
     * @var string address of the provider
     */
    public string $address = '';

    public ?string $borlabsServicePackageKey;

    /**
     * @var null|string unique key to identify the provider, provided by Borlabs Service API
     */
    public ?string $borlabsServiceProviderKey;

    /**
     * @var null|ContentBlockerModel[]
     */
    public ?array $contentBlockers;

    /**
     * @var string URL to the cookie policy
     */
    public string $cookieUrl = '';

    /**
     * @var string Description of the provider
     */
    public string $description = '';

    /**
     * @var null|int IAB Vendor ID
     */
    public ?int $iabVendorId;

    public string $key;

    /**
     * @var string language code
     */
    public string $language;

    /**
     * @var string name of the provider
     */
    public string $name;

    /**
     * @var string URL to the opt-out page
     */
    public string $optOutUrl = '';

    /**
     * @var null|array partners
     */
    public ?array $partners = null;

    /**
     * @var string URL to the privacy policy
     */
    public string $privacyUrl = '';

    /**
     * @var ServiceModel[]
     */
    public ?array $services;

    /**
     * @var bool optional; Default: `false`; `true`: Provider cannot be deleted by a user
     */
    public bool $undeletable = false;
}
