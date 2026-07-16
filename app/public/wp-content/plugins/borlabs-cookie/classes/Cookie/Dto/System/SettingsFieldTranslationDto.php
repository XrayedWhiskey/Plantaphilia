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
use Borlabs\Cookie\DtoList\System\KeyValueDtoList;

class SettingsFieldTranslationDto extends AbstractDto
{
    public string $alertMessage = '';

    public string $description = '';

    public string $errorMessage = '';

    public string $field = '';

    public string $hint = '';

    public string $infoMessage = '';

    public string $label;

    public string $language;

    public ?KeyValueDtoList $values = null;

    public string $warningMessage = '';

    public function __construct(
        string $languageCode,
        string $label
    ) {
        $this->language = $languageCode;
        $this->label = $label;
    }
}
