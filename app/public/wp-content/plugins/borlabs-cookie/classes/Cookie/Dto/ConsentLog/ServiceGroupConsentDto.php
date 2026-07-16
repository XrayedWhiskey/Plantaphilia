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

namespace Borlabs\Cookie\Dto\ConsentLog;

use Borlabs\Cookie\Dto\AbstractDto;

class ServiceGroupConsentDto extends AbstractDto
{
    /**
     * @var string The `key` of the service group
     */
    public string $key;

    /**
     * @var string[] The array contains the `key` of the services for which the user has given consent
     */
    public array $services;

    public function __construct(
        string $key,
        array $services
    ) {
        $this->key = $key;
        $this->services = $services;
    }
}
