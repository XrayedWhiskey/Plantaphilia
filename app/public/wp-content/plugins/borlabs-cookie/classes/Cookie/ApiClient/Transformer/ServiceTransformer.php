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
use Borlabs\Cookie\DtoList\System\RepeatableSettingsFieldDtoList;
use Borlabs\Cookie\DtoList\System\SettingsFieldDtoList;
use Borlabs\Cookie\Model\Service\ServiceModel;
use Borlabs\Cookie\System\Service\ServiceDefaultSettingsFieldManager;

final class ServiceTransformer
{
    use TranslationListTrait;

    private RepeatableSettingsFieldTransformer $repeatableSettingsFieldTransformer;

    private ServiceDefaultSettingsFieldManager $serviceDefaultSettingsFieldManager;

    private SettingsFieldTransformer $settingsFieldTransformer;

    public function __construct(
        RepeatableSettingsFieldTransformer $repeatableSettingsFieldTransformer,
        ServiceDefaultSettingsFieldManager $serviceDefaultSettingsFieldManager,
        SettingsFieldTransformer $settingsFieldTransformer
    ) {
        $this->repeatableSettingsFieldTransformer = $repeatableSettingsFieldTransformer;
        $this->serviceDefaultSettingsFieldManager = $serviceDefaultSettingsFieldManager;
        $this->settingsFieldTransformer = $settingsFieldTransformer;
    }

    public function toModel(object $service, string $borlabsServicePackageKey, string $languageCode): ServiceModel
    {
        $translation = $this->getTranslation($service->translations, $languageCode);

        $model = new ServiceModel();
        $model->borlabsServicePackageKey = $borlabsServicePackageKey;
        $model->description = !is_null($translation) ? $translation->description : '';
        $model->fallbackCode = $service->config->fallbackCode ?? '';
        $model->key = $service->key;
        $model->language = $languageCode;
        $model->name = $service->name;
        $model->optInCode = $service->config->optInCode ?? '';
        $model->optOutCode = $service->config->optOutCode ?? '';
        $model->position = 1;

        $repeatableSettingsFields = new RepeatableSettingsFieldDtoList();

        foreach ($service->config->repeatableSettingsFields as $repeatableSettingsField) {
            $repeatableSettingsFields->add($this->repeatableSettingsFieldTransformer->toDto($repeatableSettingsField, $model->key, $languageCode));
        }

        $model->repeatableSettingsFields = $repeatableSettingsFields;

        $settingsFields = new SettingsFieldDtoList();

        foreach ($service->config->settingsFields as $settingsField) {
            $settingsFields->add($this->settingsFieldTransformer->toDto($settingsField, $model->key, $languageCode));
        }

        /*
         * The API needs to be able to set default values for our default settings fields,
         * so we'll add tje, later if they're missing.
         */
        $defaultSettingsFields = $this->serviceDefaultSettingsFieldManager->get($languageCode);

        foreach ($defaultSettingsFields->list as $defaultSettingsField) {
            $settingsFields->add($defaultSettingsField, true);
        }

        $model->settingsFields = $settingsFields;
        $model->status = true;
        $model->undeletable = true;

        return $model;
    }
}
