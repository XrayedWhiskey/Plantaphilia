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
use Borlabs\Cookie\Dto\System\RepeatableSettingsFieldDto;
use Borlabs\Cookie\DtoList\System\SettingsFieldDtoList;
use Borlabs\Cookie\DtoList\System\SettingsFieldDtoListList;

final class RepeatableSettingsFieldTransformer
{
    use TranslationListTrait;

    private RepeatableSettingsFieldTranslationTransformer $repeatableSettingsFieldTranslationTransformer;

    private SettingsFieldTransformer $settingsFieldTransformer;

    public function __construct(
        RepeatableSettingsFieldTranslationTransformer $settingsFieldTranslationTransformer,
        SettingsFieldTransformer $settingsFieldTransformer
    ) {
        $this->repeatableSettingsFieldTranslationTransformer = $settingsFieldTranslationTransformer;
        $this->settingsFieldTransformer = $settingsFieldTransformer;
    }

    public function toDto(object $repeatableSettingsField, string $formFieldCollectionName, string $languageCode): RepeatableSettingsFieldDto
    {
        $settingsFieldsList = new SettingsFieldDtoList();
        $settingsFieldsListList = new SettingsFieldDtoListList();

        foreach ($repeatableSettingsField->settingsFields as $settingsField) {
            $settingsFieldsList->add(
                $this->settingsFieldTransformer->toDto(
                    $settingsField,
                    $formFieldCollectionName,
                    $languageCode,
                ),
            );
        }

        $settingsFieldsListList->add($settingsFieldsList);
        $translation = $this->getTranslation($repeatableSettingsField->translations, $languageCode);

        return new RepeatableSettingsFieldDto(
            $repeatableSettingsField->key,
            $settingsFieldsList,
            $settingsFieldsListList,
            $this->repeatableSettingsFieldTranslationTransformer->toDto($translation),
            $repeatableSettingsField->position,
        );
    }
}
