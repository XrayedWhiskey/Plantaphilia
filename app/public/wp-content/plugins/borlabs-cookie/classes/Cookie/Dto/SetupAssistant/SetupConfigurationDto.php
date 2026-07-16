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

namespace Borlabs\Cookie\Dto\SetupAssistant;

use Borlabs\Cookie\Dto\AbstractDto;
use Borlabs\Cookie\Dto\Config\DialogStyleDto;
use Borlabs\Cookie\DtoList\Config\LanguageOptionDtoList;
use Borlabs\Cookie\DtoList\System\KeyValueDtoList;
use Borlabs\Cookie\Enum\SetupAssistant\SetupTypeEnum;
use Borlabs\Cookie\Enum\System\DisplayModeSettingsEnum;
use Borlabs\Cookie\Enum\System\WordPressAdminSidebarMenuModeEnum;
use DateTime;

final class SetupConfigurationDto extends AbstractDto
{
    public int $cloudScanId;

    public ?DialogStyleDto $dialogStyle = null;

    public DisplayModeSettingsEnum $displayModeSettings;

    public ?KeyValueDtoList $imprintPages = null;

    public ?LanguageOptionDtoList $languageOptions = null;

    public string $layout;

    public string $logo;

    public ?KeyValueDtoList $privacyPages = null;

    public string $selectedLanguageCode;

    public DateTime $setupFinishedAt;

    public DateTime $setupStartedAt;

    public ?SetupTypeEnum $setupType = null;

    public bool $showLogo;

    public WordPressAdminSidebarMenuModeEnum $wordPressAdminSidebarMenuMode;
}
