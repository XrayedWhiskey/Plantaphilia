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

namespace Borlabs\Cookie\System\ThirdPartyImporter\BorlabsCookieLegacy\ConfigImporterComponent;

use Borlabs\Cookie\System\Config\WidgetConfig;
use Borlabs\Cookie\System\Log\Log;

final class WidgetConfigImporter
{
    private Log $log;

    private WidgetConfig $widgetConfig;

    public function __construct(
        Log $log,
        WidgetConfig $widgetConfig
    ) {
        $this->log = $log;
        $this->widgetConfig = $widgetConfig;
    }

    public function import($legacyConfigData, string $languageCode)
    {
        $widgetConfigDto = $this->widgetConfig->defaultConfig();
        $widgetConfigDto->show = (bool) ($legacyConfigData['cookieBoxShowWidget'] ?? $widgetConfigDto->show);
        $widgetConfigSaveStatus = $this->widgetConfig->save($widgetConfigDto, $languageCode);

        $this->log->info(
            '[Import] Widget config ({{ languageCode }}) imported: {{ status }}',
            [
                'languageCode' => $languageCode,
                'status' => $widgetConfigSaveStatus ? 'Yes' : 'No',
            ],
        );
    }
}
