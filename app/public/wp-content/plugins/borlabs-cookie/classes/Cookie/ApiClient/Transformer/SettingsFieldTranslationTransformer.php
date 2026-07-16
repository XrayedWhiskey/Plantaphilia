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

namespace Borlabs\Cookie\ApiClient\Transformer;

use Borlabs\Cookie\Dto\System\KeyValueDto;
use Borlabs\Cookie\Dto\System\SettingsFieldTranslationDto;
use Borlabs\Cookie\DtoList\System\KeyValueDtoList;

final class SettingsFieldTranslationTransformer
{
    public function toDto(object $settingsFieldTranslation): SettingsFieldTranslationDto
    {
        $values = new KeyValueDtoList();

        if (isset($settingsFieldTranslation->values)) {
            foreach ($settingsFieldTranslation->values as $key => $value) {
                $values->add(new KeyValueDto($key, $value));
            }
        }

        $dto = new SettingsFieldTranslationDto(
            $settingsFieldTranslation->language,
            $settingsFieldTranslation->label,
        );
        $dto->alertMessage = $settingsFieldTranslation->alertMessage ?? '';
        $dto->description = $settingsFieldTranslation->description ?? '';
        $dto->errorMessage = $settingsFieldTranslation->errorMessage ?? '';
        $dto->field = $settingsFieldTranslation->field ?? '';
        $dto->hint = $settingsFieldTranslation->hint ?? '';
        $dto->infoMessage = $settingsFieldTranslation->infoMessage ?? '';
        $dto->values = $values;
        $dto->warningMessage = $settingsFieldTranslation->warningMessage ?? '';

        return $dto;
    }
}
