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

use Borlabs\Cookie\Dto\Package\CompatibilityPatchComponentDto;
use Borlabs\Cookie\Dto\Package\ComponentDto;
use Borlabs\Cookie\Dto\Package\ContentBlockerComponentDto;
use Borlabs\Cookie\Dto\Package\ScriptBlockerComponentDto;
use Borlabs\Cookie\Dto\Package\ServiceComponentDto;
use Borlabs\Cookie\Dto\Package\StyleBlockerComponentDto;
use Borlabs\Cookie\Dto\Package\TranslationDto;
use Borlabs\Cookie\DtoList\Package\CompatibilityPatchComponentDtoList;
use Borlabs\Cookie\DtoList\Package\ContentBlockerComponentDtoList;
use Borlabs\Cookie\DtoList\Package\ScriptBlockerComponentDtoList;
use Borlabs\Cookie\DtoList\Package\ServiceComponentDtoList;
use Borlabs\Cookie\DtoList\Package\StyleBlockerComponentDtoList;
use Borlabs\Cookie\DtoList\Package\TranslationDtoList;
use Borlabs\Cookie\Enum\Package\PackageTypeEnum;
use Borlabs\Cookie\Model\Package\PackageModel;
use Borlabs\Cookie\Support\Traits\VersionNumberTrait;
use DateTime;
use Exception;

final class PackageTransformer
{
    use VersionNumberTrait;

    private LanguageSpecificSettingsFieldListTransformer $languageSpecificSettingsFieldListTransformer;

    public function __construct(LanguageSpecificSettingsFieldListTransformer $languageSpecificSettingsFieldListTransformer)
    {
        $this->languageSpecificSettingsFieldListTransformer = $languageSpecificSettingsFieldListTransformer;
    }

    /**
     * @throws Exception
     */
    public function toModel(object $package): PackageModel
    {
        $components = new ComponentDto();
        $components->compatibilityPatches = $this->makeCompatibilityPatchComponentList($package->components->compatibilityPatches);
        $components->contentBlockers = $this->makeContentBlockerComponentList($package->components->contentBlockers);
        $components->scriptBlockers = $this->makeScriptBlockerComponentList($package->components->scriptBlockers);
        $components->services = $this->makeServiceComponentList($package->components->services);
        $components->styleBlockers = $this->makeStyleBlockerComponentList($package->components->styleBlockers);

        // Translations
        $translationList = new TranslationDtoList();

        foreach ($package->translations as $translationData) {
            $translation = new TranslationDto();
            $translation->description = $translationData->description ?? '';
            $translation->followUp = $translationData->followUp ?? '';
            $translation->language = $translationData->language;
            $translation->preparation = $translationData->preparation ?? '';
            $translationList->add($translation);
        }

        $thumbnail = '';

        if ($package->thumbnail) {
            $thumbnail = $package->thumbnail;
        }

        $model = new PackageModel();
        $model->borlabsServicePackageKey = $package->key;
        $model->borlabsServicePackageSuccessorKey = $package->successorKey;
        $model->borlabsServicePackageVersion = $this->transformToVersionNumberDto($package->version);
        $model->borlabsServiceUpdatedAt = $package->updatedAt !== null ? new DateTime($package->updatedAt) : null;
        $model->components = $components;
        $model->isDeprecated = $package->isDeprecated;
        $model->isFeatured = $package->isFeatured;
        $model->name = $package->name;
        $model->requiredBorlabsCookieVersion = $this->transformToVersionNumberWithHotfixDto($package->requiredBorlabsCookieVersion);
        $model->thumbnail = $thumbnail;
        $model->translations = $translationList;
        $model->type = PackageTypeEnum::fromValue($package->type);
        $model->version = $this->transformToVersionNumberDto($package->version);

        return $model;
    }

    private function makeCompatibilityPatchComponentList(array $compatibilityPatches): CompatibilityPatchComponentDtoList
    {
        $list = new CompatibilityPatchComponentDtoList();

        foreach ($compatibilityPatches as $componentData) {
            $list->add(
                new CompatibilityPatchComponentDto(
                    $componentData->key,
                    $componentData->config->downloadUrl,
                    $componentData->config->hash,
                ),
            );
        }

        return $list;
    }

    private function makeContentBlockerComponentList(array $contentBlockers): ContentBlockerComponentDtoList
    {
        $list = new ContentBlockerComponentDtoList();

        /**
         * @var object{key: string, name: string, config: object{settingsFields: array{}}} $componentData
         */
        foreach ($contentBlockers as $componentData) {
            $list->add(
                new ContentBlockerComponentDto(
                    $componentData->key,
                    $componentData->name,
                    isset($componentData->config->settingsFields) ? $this->languageSpecificSettingsFieldListTransformer->toDto($componentData->config->settingsFields, $componentData->key) : null,
                ),
            );
        }

        return $list;
    }

    private function makeScriptBlockerComponentList(array $scriptBlockers): ScriptBlockerComponentDtoList
    {
        $list = new ScriptBlockerComponentDtoList();

        /**
         * @var object{key: string, name: string} $componentData
         */
        foreach ($scriptBlockers as $componentData) {
            $list->add(
                new ScriptBlockerComponentDto(
                    $componentData->key,
                    $componentData->name,
                ),
            );
        }

        return $list;
    }

    private function makeServiceComponentList(array $services): ServiceComponentDtoList
    {
        $list = new ServiceComponentDtoList();

        /**
         * @var object{key: string, name: string, config: object{settingsFields: array{}}} $componentData
         */
        foreach ($services as $componentData) {
            $list->add(
                new ServiceComponentDto(
                    $componentData->key,
                    $componentData->name,
                    isset($componentData->config->settingsFields) ? $this->languageSpecificSettingsFieldListTransformer->toDto($componentData->config->settingsFields, $componentData->key) : null,
                ),
            );
        }

        return $list;
    }

    private function makeStyleBlockerComponentList(array $styleBlockers): StyleBlockerComponentDtoList
    {
        $list = new StyleBlockerComponentDtoList();

        foreach ($styleBlockers as $componentData) {
            $list->add(
                new StyleBlockerComponentDto(
                    $componentData->key,
                    $componentData->name,
                ),
            );
        }

        return $list;
    }
}
