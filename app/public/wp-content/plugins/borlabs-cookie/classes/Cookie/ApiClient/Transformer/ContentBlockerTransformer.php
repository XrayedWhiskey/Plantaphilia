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
use Borlabs\Cookie\Dto\System\ExternalFileDto;
use Borlabs\Cookie\Dto\System\KeyValueDto;
use Borlabs\Cookie\DtoList\System\KeyValueDtoList;
use Borlabs\Cookie\DtoList\System\RepeatableSettingsFieldDtoList;
use Borlabs\Cookie\DtoList\System\SettingsFieldDtoList;
use Borlabs\Cookie\Model\ContentBlocker\ContentBlockerModel;
use Borlabs\Cookie\System\ContentBlocker\ContentBlockerDefaultSettingsFieldManager;
use Borlabs\Cookie\System\FileSystem\FileManager;

final class ContentBlockerTransformer
{
    use TranslationListTrait;

    private ContentBlockerDefaultSettingsFieldManager $contentBlockerDefaultSettingsFieldManager;

    private FileManager $fileManager;

    private RepeatableSettingsFieldTransformer $repeatableSettingsFieldTransformer;

    private SettingsFieldTransformer $settingsFieldTransformer;

    public function __construct(
        ContentBlockerDefaultSettingsFieldManager $contentBlockerDefaultSettingsFieldManager,
        FileManager $fileManager,
        RepeatableSettingsFieldTransformer $repeatableSettingsFieldTransformer,
        SettingsFieldTransformer $settingsFieldTransformer
    ) {
        $this->contentBlockerDefaultSettingsFieldManager = $contentBlockerDefaultSettingsFieldManager;
        $this->fileManager = $fileManager;
        $this->repeatableSettingsFieldTransformer = $repeatableSettingsFieldTransformer;
        $this->settingsFieldTransformer = $settingsFieldTransformer;
    }

    public function toModel(object $contentBlocker, string $borlabsServicePackageKey, string $languageCode): ContentBlockerModel
    {
        $previewImage = '';

        if ($contentBlocker->config->previewImage) {
            $externalFile = new ExternalFileDto($contentBlocker->config->previewImage);
            $file = $this->fileManager->storeExternalFile($externalFile);
            $previewImage = $file->fileName ?? '';
        }

        $translation = $this->getTranslation($contentBlocker->translations, $languageCode);
        $languageStrings = new KeyValueDtoList();

        foreach ($translation->languageStrings as $key => $value) {
            $languageStrings->add(new KeyValueDto($key, $value));
        }

        $model = new ContentBlockerModel();
        $model->borlabsServicePackageKey = $borlabsServicePackageKey;
        $model->description = !is_null($translation) ? $translation->description : '';
        $model->javaScriptGlobal = $contentBlocker->config->javaScriptGlobal ?? '';
        $model->javaScriptInitialization = $contentBlocker->config->javaScriptInitialization ?? '';
        $model->key = $contentBlocker->key;
        $model->language = $languageCode;
        $model->languageStrings = $languageStrings;
        $model->name = $contentBlocker->name;
        $model->previewCss = $contentBlocker->config->previewCss ?? '';
        $model->previewHtml = $contentBlocker->config->previewHtml ?? '';
        $model->previewImage = $previewImage !== '' ? $this->fileManager->getStorageFolder()->getUrl() . '/' . $previewImage : $previewImage;

        $repeatableSettingsFields = new RepeatableSettingsFieldDtoList();

        foreach ($contentBlocker->config->repeatableSettingsFields as $repeatableSettingsField) {
            $repeatableSettingsFields->add($this->repeatableSettingsFieldTransformer->toDto($repeatableSettingsField, $model->key, $languageCode));
        }

        $model->repeatableSettingsFields = $repeatableSettingsFields;

        $settingsFields = new SettingsFieldDtoList();

        foreach ($contentBlocker->config->settingsFields as $settingsField) {
            $settingsFields->add($this->settingsFieldTransformer->toDto($settingsField, $model->key, $languageCode));
        }

        /*
         * The API needs to be able to set default values for our default settings field,
         * so we'll add it later if it's missing.
         */
        $defaultSettingsFields = $this->contentBlockerDefaultSettingsFieldManager->get($languageCode);

        foreach ($defaultSettingsFields->list as $defaultSettingsField) {
            $settingsFields->add($defaultSettingsField, true);
        }

        $model->settingsFields = $settingsFields;
        $model->status = true;
        $model->undeletable = true;

        return $model;
    }
}
