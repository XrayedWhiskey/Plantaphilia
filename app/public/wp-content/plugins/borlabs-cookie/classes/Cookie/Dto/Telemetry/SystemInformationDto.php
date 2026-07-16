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

namespace Borlabs\Cookie\Dto\Telemetry;

use Borlabs\Cookie\Dto\AbstractDto;
use Borlabs\Cookie\DtoList\System\KeyValueDtoList;
use Borlabs\Cookie\Enum\System\AutomaticUpdateEnum;

class SystemInformationDto extends AbstractDto
{
    public string $databaseVersion;

    public string $phpVersion;

    public string $product;

    public AutomaticUpdateEnum $productAutomaticUpdate;

    public bool $productDebugLoggingEnabled;

    public string $productVersion;

    public ?string $wordPressContentUrl;

    public string $wordPressDatabasePrefix;

    public bool $wordPressDebugDisplayEnabled;

    public bool $wordPressDebugEnabled;

    public bool $wordPressDebugLogEnabled;

    public string $wordPressDefaultLanguage;

    public bool $wordPressHasMultilanguagePluginActive;

    public bool $wordPressIsMultisite;

    /**
     * @var bool `true`: Domain-based network; `false`: Path-based network
     */
    public bool $wordPressIsMultisiteSubdomainInstall;

    public KeyValueDtoList $wordPressMultilanguagePluginLanguages;

    public ?string $wordPressMultisiteMainSite;

    public string $wordPressVersion;
}
