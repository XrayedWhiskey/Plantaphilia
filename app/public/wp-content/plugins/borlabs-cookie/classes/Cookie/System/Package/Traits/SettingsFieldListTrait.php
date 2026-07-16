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

namespace Borlabs\Cookie\System\Package\Traits;

use Borlabs\Cookie\Dto\Package\ContentBlockerComponentDto;
use Borlabs\Cookie\Dto\Package\ServiceComponentDto;
use Borlabs\Cookie\DtoList\System\RepeatableSettingsFieldDtoList;
use Borlabs\Cookie\DtoList\System\SettingsFieldDtoList;
use Borlabs\Cookie\DtoList\System\SettingsFieldDtoListList;
use Borlabs\Cookie\Enum\System\SettingsFieldDataTypeEnum;
use Borlabs\Cookie\Exception\EntryNotFoundException;
use Borlabs\Cookie\Exception\IncompatibleTypeException;
use Borlabs\Cookie\Exception\MissingRequiredArgumentException;
use Borlabs\Cookie\Repository\ContentBlocker\ContentBlockerRepository;
use Borlabs\Cookie\Repository\Service\ServiceRepository;
use Borlabs\Cookie\Repository\ServiceGroup\ServiceGroupRepository;
use Borlabs\Cookie\Support\Searcher;

trait SettingsFieldListTrait
{
    /**
     * @param $componentData ContentBlockerComponentDto|ServiceComponentDto
     * @param $repository ContentBlockerRepository|ServiceRepository
     *
     * @throws EntryNotFoundException
     * @throws IncompatibleTypeException
     * @throws MissingRequiredArgumentException
     */
    private function mergeComponentSettingsWithCustomSettings(object $componentData, object $repository, ?ServiceGroupRepository $serviceGroupRepository = null): void
    {
        if (!$componentData instanceof ContentBlockerComponentDto && !$componentData instanceof ServiceComponentDto) {
            throw new IncompatibleTypeException();
        }

        if (!$repository instanceof ContentBlockerRepository && !$repository instanceof ServiceRepository) {
            throw new IncompatibleTypeException();
        }

        $components = $repository->getAllByKey($componentData->key);

        if ($components === null) {
            return;
        }

        foreach ($components as $component) {
            // Get the language
            $componentDataSettingsField = Searcher::findObject(
                $componentData->languageSpecificSetupSettingsFieldsList->list ?? [],
                'language',
                $component->language,
            );

            // Check if the language is missing in the package
            if (!isset($componentDataSettingsField)) {
                // Check if en language settings fields are available
                $componentDataSettingsField = Searcher::findObject(
                    $componentData->languageSpecificSetupSettingsFieldsList->list ?? [],
                    'language',
                    'en',
                );

                if (!isset($componentDataSettingsField)) {
                    continue;
                }

                // Clone the english settings fields
                $newLanguage = clone $componentDataSettingsField;
                $newLanguage->language = $component->language;
                $componentData->languageSpecificSetupSettingsFieldsList->add($newLanguage);

                // Get the reference of the language object
                $componentDataSettingsField = Searcher::findObject(
                    $componentData->languageSpecificSetupSettingsFieldsList->list ?? [],
                    'language',
                    $component->language,
                );
            }

            foreach ($componentDataSettingsField->settingsFields->list as $settingsField) {
                if ($settingsField->dataType->is(SettingsFieldDataTypeEnum::SYSTEM_SERVICE_GROUP())) {
                    if ($serviceGroupRepository === null) {
                        throw new MissingRequiredArgumentException('serviceGroupRepositoryMissing', ['componentKey' => $component->key, 'componentName' => $component->name, 'model' => $repository::MODEL, ]);
                    }

                    foreach ($component->settingsFields->list as $componentSettingsField) {
                        if ($settingsField->key === $componentSettingsField->key) {
                            $serviceGroup = $this->serviceGroupRepository->getByKey($componentSettingsField->value, $component->language);

                            if ($serviceGroup === null) {
                                throw new EntryNotFoundException('entryWithKeyOfModelNotFoundForLanguage', ['key' => $component->key, 'language' => $component->language, 'model' => $serviceGroupRepository::MODEL, ]);
                            }

                            $settingsField->value = $serviceGroup->key;

                            break;
                        }
                    }

                    continue;
                }

                foreach ($component->settingsFields->list as $componentSettingsField) {
                    if ($settingsField->key === $componentSettingsField->key) {
                        $settingsField->value = $componentSettingsField->value;

                        break;
                    }
                }
            }
        }
    }

    private function migrateSettingsFieldValues(SettingsFieldDtoList $defaultSettings, SettingsFieldDtoList $customSettings): SettingsFieldDtoList
    {
        foreach ($defaultSettings->list as $key => $defaultSettingsField) {
            foreach ($customSettings->list as $customSettingsField) {
                if ($defaultSettingsField->key === $customSettingsField->key) {
                    $defaultSettings->list[$key]->value = $customSettingsField->value;

                    // Ensure that a required field with a default value does not have an empty value field
                    if ($defaultSettings->list[$key]->isRequired && empty($customSettingsField->value) && !empty($defaultSettings->list[$key]->defaultValue)) {
                        $defaultSettings->list[$key]->value = $defaultSettings->list[$key]->defaultValue;
                    }
                }
            }
        }

        return $defaultSettings;
    }

    private function updateRepeatableSettingsValuesFromFormFields(RepeatableSettingsFieldDtoList $repeatableSettings, array $repeatableFormFields): RepeatableSettingsFieldDtoList
    {
        foreach ($repeatableSettings->list as $repeatableSettingsField) {
            if (isset($repeatableFormFields[$repeatableSettingsField->key])) {
                $repeatableSettingsField->settingsFieldsListList = new SettingsFieldDtoListList();

                foreach ($repeatableFormFields[$repeatableSettingsField->key] as $formFieldValues) {
                    $repeatableSettingsField->settingsFieldsListList->add(
                        $this->updateSettingsValuesFromFormFields(
                            clone $repeatableSettingsField->settingsFieldsDefinition,
                            $formFieldValues,
                        ),
                    );
                }
            }
        }

        return $repeatableSettings;
    }

    private function updateSettingsValuesFromFormFields(SettingsFieldDtoList $settings, array $formFieldValues): SettingsFieldDtoList
    {
        foreach ($settings->list as $settingsField) {
            if (isset($formFieldValues[$settingsField->key])) {
                $settingsField->value = $formFieldValues[$settingsField->key];
            }
        }

        return $settings;
    }
}
