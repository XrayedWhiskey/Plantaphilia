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

use Borlabs\Cookie\Dto\Package\LanguageSpecificSettingsFieldListItemDto;
use Borlabs\Cookie\DtoList\Package\LanguageSpecificSettingsFieldDtoList;
use Borlabs\Cookie\DtoList\System\SettingsFieldDtoList;

final class LanguageSpecificSettingsFieldListTransformer
{
    private SettingsFieldTransformer $settingsFieldTransformer;

    public function __construct(SettingsFieldTransformer $settingsFieldTransformer)
    {
        $this->settingsFieldTransformer = $settingsFieldTransformer;
    }

    public function toDto(array $settingsFields, string $formFieldCollectionName): LanguageSpecificSettingsFieldDtoList
    {
        $preparationList = [];

        foreach ($settingsFields as $settingsField) {
            foreach ($settingsField->translations as $translation) {
                if (!isset($preparationList[$translation->language])) {
                    $preparationList[$translation->language] = new SettingsFieldDtoList();
                }
                $preparationList[$translation->language]->add($this->settingsFieldTransformer->toDto($settingsField, $formFieldCollectionName, $translation->language));
            }
        }

        $list = new LanguageSpecificSettingsFieldDtoList();

        foreach ($preparationList as $languageCode => $settingsFieldsList) {
            $list->add(
                new LanguageSpecificSettingsFieldListItemDto(
                    $languageCode,
                    $settingsFieldsList,
                ),
            );
        }

        return $list;
    }
}
