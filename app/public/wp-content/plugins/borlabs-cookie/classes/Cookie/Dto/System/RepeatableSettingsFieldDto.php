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

namespace Borlabs\Cookie\Dto\System;

use Borlabs\Cookie\Dto\AbstractDto;
use Borlabs\Cookie\DtoList\System\SettingsFieldDtoList;
use Borlabs\Cookie\DtoList\System\SettingsFieldDtoListList;

final class RepeatableSettingsFieldDto extends AbstractDto
{
    public string $key;

    public int $position = 0;

    public SettingsFieldDtoList $settingsFieldsDefinition;

    public SettingsFieldDtoListList $settingsFieldsListList;

    public RepeatableSettingsFieldTranslationDto $translation;

    public function __construct(
        string $key,
        SettingsFieldDtoList $settingsFieldsDefinition,
        SettingsFieldDtoListList $settingsFieldsListList,
        RepeatableSettingsFieldTranslationDto $translation,
        int $position = 0
    ) {
        $this->key = $key;
        $this->position = $position;
        $this->settingsFieldsDefinition = $settingsFieldsDefinition;
        $this->settingsFieldsListList = $settingsFieldsListList;
        $this->translation = $translation;
    }
}
