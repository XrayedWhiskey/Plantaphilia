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

use Borlabs\Cookie\DtoList\System\KeyValueDtoList;

/**
 * The **IabTcfDto** class is used as a typed object that is passed within the system.
 *
 * The object contains technical configuration properties related to the Borlabs Cookie plugin and its cookie.
 *
 * @see \Borlabs\Cookie\System\Config\IabTcfDto
 */
final class IabTcfDto extends AbstractConfigDto
{
    public bool $compactLayout = true;

    public array $hostnamesForConsentAddition = [];

    /**
     * @var bool default: `false`; `true`: The IAB TCFv2 is active and replaces the selected layout
     *           {@see \Borlabs\Cookie\Dto\Config\DialogSettingsDto::$layout}
     */
    public bool $iabTcfStatus = false;

    public ?KeyValueDtoList $vendors = null;
}
