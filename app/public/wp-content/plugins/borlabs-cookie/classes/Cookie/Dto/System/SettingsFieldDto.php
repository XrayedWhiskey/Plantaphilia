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
use Borlabs\Cookie\Enum\System\SettingsFieldDataTypeEnum;
use Borlabs\Cookie\Enum\System\SettingsFieldVisibilityEnum;
use Borlabs\Cookie\Enum\System\ValidatorEnum;

final class SettingsFieldDto extends AbstractDto
{
    public SettingsFieldDataTypeEnum $dataType;

    public string $defaultValue = '';

    public ?string $formFieldCollectionName = null;

    public bool $isRequired = false;

    public string $key;

    public int $position = 0;

    public SettingsFieldTranslationDto $translation;

    public string $validationRegex = '';

    public ValidatorEnum $validator;

    public string $value = '';

    public ?KeyValueDtoList $values = null;

    public SettingsFieldVisibilityEnum $visibility;

    public function __construct(
        string $key,
        SettingsFieldDataTypeEnum $dataType,
        SettingsFieldTranslationDto $translation,
        ValidatorEnum $validator,
        SettingsFieldVisibilityEnum $visibility,
        string $defaultValue = '',
        ?string $formFieldCollectionName = null,
        bool $isRequired = false,
        int $position = 0,
        string $validationRegex = '',
        string $value = '',
        ?KeyValueDtoList $values = null
    ) {
        $this->dataType = $dataType;
        $this->defaultValue = $defaultValue;
        $this->formFieldCollectionName = $formFieldCollectionName;
        $this->isRequired = $isRequired;
        $this->key = $key;
        $this->position = $position;
        $this->translation = $translation;
        $this->validator = $validator;
        $this->validationRegex = $validationRegex;
        $this->value = $value;
        $this->values = $values;
        $this->visibility = $visibility;
    }
}
