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

use Borlabs\Cookie\System\Config\AbstractConfigManagerWithLanguage;
use Borlabs\Cookie\System\Config\ContentBlockerStyleConfig;
use Borlabs\Cookie\System\Config\DialogLocalization;
use Borlabs\Cookie\System\Config\DialogSettingsConfig;
use Borlabs\Cookie\System\Config\DialogStyleConfig;
use Borlabs\Cookie\System\Config\WidgetConfig;

final class ExportService
{
    private ContentBlockerStyleConfig $contentBlockerStyleConfig;

    private DialogLocalization $dialogLocalization;

    private DialogSettingsConfig $dialogSettingsConfig;

    private DialogStyleConfig $dialogStyleConfig;

    private WidgetConfig $widgetConfig;

    public function __construct(
        ContentBlockerStyleConfig $contentBlockerStyleConfig,
        DialogLocalization $dialogLocalization,
        DialogSettingsConfig $dialogSettingsConfig,
        DialogStyleConfig $dialogStyleConfig,
        WidgetConfig $widgetConfig
    ) {
        $this->contentBlockerStyleConfig = $contentBlockerStyleConfig;
        $this->dialogLocalization = $dialogLocalization;
        $this->dialogSettingsConfig = $dialogSettingsConfig;
        $this->dialogStyleConfig = $dialogStyleConfig;
        $this->widgetConfig = $widgetConfig;
    }

    public function getContentBlockerStyleConfigExportData(): string
    {
        return $this->getExportData($this->contentBlockerStyleConfig);
    }

    public function getDialogLocalizationExportData(): string
    {
        return $this->getExportData($this->dialogLocalization);
    }

    public function getDialogSettingsConfigExportData(): string
    {
        return $this->getExportData($this->dialogSettingsConfig);
    }

    public function getDialogStyleConfigExportData(): string
    {
        return $this->getExportData($this->dialogStyleConfig);
    }

    public function getWidgetConfigExportData(): string
    {
        return $this->getExportData($this->widgetConfig);
    }

    private function getExportData(AbstractConfigManagerWithLanguage $configManager): string
    {
        return base64_encode(
            json_encode($configManager->get()),
        );
    }
}
