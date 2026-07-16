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

use Borlabs\Cookie\System\Config\ContentBlockerSettingsConfig;
use Borlabs\Cookie\System\Log\Log;

final class ContentBlockerSettingsConfigImporter
{
    private ContentBlockerSettingsConfig $contentBlockerSettingsConfig;

    private Log $log;

    public function __construct(
        ContentBlockerSettingsConfig $contentBlockerSettingsConfig,
        Log $log
    ) {
        $this->contentBlockerSettingsConfig = $contentBlockerSettingsConfig;
        $this->log = $log;
    }

    public function import($legacyConfigData, string $languageCode)
    {
        $contentBlockerSettingsDto = $this->contentBlockerSettingsConfig->defaultConfig();
        $contentBlockerSettingsDto->excludedHostnames = $legacyConfigData['contentBlockerHostWhitelist'] ?? $contentBlockerSettingsDto->excludedHostnames;
        $contentBlockerSettingsDto->removeIframesInFeeds = (bool) ($legacyConfigData['removeIframesInFeeds'] ?? $contentBlockerSettingsDto->removeIframesInFeeds);
        $contentBlockerSettingsSaveStatus = $this->contentBlockerSettingsConfig->save($contentBlockerSettingsDto, $languageCode);

        $this->log->info(
            '[Import] Content Blocker settings config ({{ languageCode }}) imported: {{ status }}',
            [
                'languageCode' => $languageCode,
                'status' => $contentBlockerSettingsSaveStatus ? 'Yes' : 'No',
            ],
        );
    }
}
