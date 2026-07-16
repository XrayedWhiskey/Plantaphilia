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

use Borlabs\Cookie\ApiClient\Transformer\Traits\TranslationListTrait;
use Borlabs\Cookie\Dto\System\KeyValueDto;
use Borlabs\Cookie\Dto\System\SettingsFieldDto;
use Borlabs\Cookie\DtoList\System\KeyValueDtoList;
use Borlabs\Cookie\Enum\System\SettingsFieldDataTypeEnum;
use Borlabs\Cookie\Enum\System\SettingsFieldVisibilityEnum;
use Borlabs\Cookie\Enum\System\ValidatorEnum;

final class SettingsFieldTransformer
{
    use TranslationListTrait;

    private SettingsFieldTranslationTransformer $settingsFieldTranslationTransformer;

    public function __construct(SettingsFieldTranslationTransformer $settingsFieldTranslationTransformer)
    {
        $this->settingsFieldTranslationTransformer = $settingsFieldTranslationTransformer;
    }

    public function toDto(object $settingsField, string $formFieldCollectionName, string $languageCode): SettingsFieldDto
    {
        $translation = $this->getTranslation($settingsField->translations, $languageCode);
        $values = new KeyValueDtoList();

        if (isset($settingsField->values)) {
            foreach ($settingsField->values as $key => $value) {
                $values->add(new KeyValueDto($key, $value));
            }
        }

        $validationRegex = $settingsField->validationRegex ?? '';

        // Remove / from start and end of regex
        if (preg_match('/^\/.*\/$/', $validationRegex)) {
            $validationRegex = substr($validationRegex, 1, -1);
        }

        return new SettingsFieldDto(
            $settingsField->key,
            SettingsFieldDataTypeEnum::fromValue($settingsField->dataType),
            $this->settingsFieldTranslationTransformer->toDto($translation),
            ValidatorEnum::fromValue($settingsField->validator),
            SettingsFieldVisibilityEnum::fromValue($settingsField->visibility),
            $settingsField->defaultValue ?? '',
            $formFieldCollectionName,
            (bool) $settingsField->isRequired,
            $settingsField->position,
            $validationRegex,
            $settingsField->defaultValue ?? '',
            $values,
        );
    }
}
