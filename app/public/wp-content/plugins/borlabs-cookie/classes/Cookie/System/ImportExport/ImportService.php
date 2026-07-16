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

namespace Borlabs\Cookie\System\ImportExport;

use Borlabs\Cookie\Exception\System\ImportException;
use Borlabs\Cookie\Support\Validator;
use Borlabs\Cookie\System\Config\AbstractConfigManagerWithLanguage;
use Borlabs\Cookie\System\Config\ContentBlockerStyleConfig;
use Borlabs\Cookie\System\Config\DialogLocalization;
use Borlabs\Cookie\System\Config\DialogSettingsConfig;
use Borlabs\Cookie\System\Config\DialogStyleConfig;
use Borlabs\Cookie\System\Config\IabTcfConfig;
use Borlabs\Cookie\System\Config\WidgetConfig;
use Borlabs\Cookie\Validator\Import\ImportValidator;

final class ImportService
{
    private ContentBlockerStyleConfig $contentBlockerStyleConfig;

    private DialogLocalization $dialogLocalization;

    private DialogSettingsConfig $dialogSettingsConfig;

    private DialogStyleConfig $dialogStyleConfig;

    private IabTcfConfig $iabTcfConfig;

    private ImportValidator $importValidator;

    private WidgetConfig $widgetConfig;

    public function __construct(
        ContentBlockerStyleConfig $contentBlockerStyleConfig,
        DialogLocalization $dialogLocalization,
        DialogSettingsConfig $dialogSettingsConfig,
        DialogStyleConfig $dialogStyleConfig,
        IabTcfConfig $iabTcfConfig,
        ImportValidator $importValidator,
        WidgetConfig $widgetConfig
    ) {
        $this->contentBlockerStyleConfig = $contentBlockerStyleConfig;
        $this->dialogLocalization = $dialogLocalization;
        $this->dialogSettingsConfig = $dialogSettingsConfig;
        $this->dialogStyleConfig = $dialogStyleConfig;
        $this->iabTcfConfig = $iabTcfConfig;
        $this->importValidator = $importValidator;
        $this->widgetConfig = $widgetConfig;
    }

    /**
     * @throws \Borlabs\Cookie\Exception\System\ImportException
     */
    public function importContentBlockerStyleConfig(string $base64EncodedConfig, string $languageCode): bool
    {
        return $this->import(
            $this->contentBlockerStyleConfig,
            $base64EncodedConfig,
            $languageCode,
        );
    }

    /**
     * @throws \Borlabs\Cookie\Exception\System\ImportException
     */
    public function importDialogLocalization(string $base64EncodedConfig, string $languageCode): bool
    {
        return $this->import(
            $this->dialogLocalization,
            $base64EncodedConfig,
            $languageCode,
        );
    }

    /**
     * @throws \Borlabs\Cookie\Exception\System\ImportException
     */
    public function importDialogSettingsConfig(string $base64EncodedConfig, string $languageCode): bool
    {
        $iabTcfConfig = $this->iabTcfConfig->get();
        $saveStatus = $this->import(
            $this->dialogSettingsConfig,
            $base64EncodedConfig,
            $languageCode,
        );

        if ($saveStatus && $iabTcfConfig->iabTcfStatus) {
            $dialogSettingsConfig = $this->dialogSettingsConfig->get();
            $dialogSettingsConfig->showBorlabsCookieBranding = true;
            $this->dialogSettingsConfig->save($dialogSettingsConfig, $languageCode);
        }

        return $saveStatus;
    }

    /**
     * @throws \Borlabs\Cookie\Exception\System\ImportException
     */
    public function importDialogStyleConfig(string $base64EncodedConfig, string $languageCode): bool
    {
        return $this->import(
            $this->dialogStyleConfig,
            $base64EncodedConfig,
            $languageCode,
        );
    }

    /**
     * @throws \Borlabs\Cookie\Exception\System\ImportException
     */
    public function importWidgetConfig(string $base64EncodedConfig, string $languageCode): bool
    {
        $iabTcfConfig = $this->iabTcfConfig->get();
        $saveStatus = $this->import(
            $this->widgetConfig,
            $base64EncodedConfig,
            $languageCode,
        );

        if ($saveStatus && $iabTcfConfig->iabTcfStatus) {
            $widgetConfig = $this->widgetConfig->get();
            $widgetConfig->show = true;
            $this->widgetConfig->save($widgetConfig, $languageCode);
        }

        return $saveStatus;
    }

    /**
     * @throws \Borlabs\Cookie\Exception\System\ImportException
     */
    private function getConfigurationToImport(string $base64EncodedConfig): array
    {
        $base64EncodedConfig = base64_decode($base64EncodedConfig, true);

        if (!is_string($base64EncodedConfig)) {
            throw new ImportException('encodingError');
        }

        if (!Validator::isStringJSON($base64EncodedConfig)) {
            throw new ImportException('jsonError', ['error' => json_last_error_msg(), ]);
        }

        return json_decode($base64EncodedConfig, true);
    }

    /**
     * @throws \Borlabs\Cookie\Exception\System\ImportException
     */
    private function import(
        AbstractConfigManagerWithLanguage $configManager,
        string $base64EncodedConfig,
        string $languageCode
    ): bool {
        $configurationToImport = $this->getConfigurationToImport($base64EncodedConfig);
        $currentConfiguration = $configManager->get();
        $updatedConfiguration = $configManager->mapPostDataToProperties(
            $configurationToImport,
            $currentConfiguration,
        );

        return $configManager->save(
            $updatedConfiguration,
            $languageCode,
        );
    }
}
