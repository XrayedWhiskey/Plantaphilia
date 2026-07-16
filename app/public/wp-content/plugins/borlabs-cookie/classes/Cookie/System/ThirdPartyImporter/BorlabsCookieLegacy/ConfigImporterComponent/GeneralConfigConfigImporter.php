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

use Borlabs\Cookie\Enum\Cookie\SameSiteEnum;
use Borlabs\Cookie\System\Config\GeneralConfig;
use Borlabs\Cookie\System\Log\Log;

final class GeneralConfigConfigImporter
{
    private GeneralConfig $generalConfig;

    private Log $log;

    public function __construct(GeneralConfig $generalConfig, Log $log)
    {
        $this->generalConfig = $generalConfig;
        $this->log = $log;
    }

    public function import($legacyConfigData, string $languageCode)
    {
        $generalConfigDto = $this->generalConfig->defaultConfig();
        $generalConfigDto->aggregateConsents = (bool) ($legacyConfigData['aggregateCookieConsent'] ?? $generalConfigDto->aggregateConsents);
        $generalConfigDto->automaticCookieDomainAndPath = (bool) ($legacyConfigData['automaticCookieDomainAndPath'] ?? $generalConfigDto->automaticCookieDomainAndPath);
        $generalConfigDto->borlabsCookieStatus = (bool) ($legacyConfigData['cookieStatus'] ?? $generalConfigDto->borlabsCookieStatus);
        $generalConfigDto->cookieDomain = (string) ($legacyConfigData['cookieDomain'] ?? $generalConfigDto->cookieDomain);
        $generalConfigDto->cookieLifetime = (int) ($legacyConfigData['cookieLifetime'] ?? $generalConfigDto->cookieLifetime);
        $generalConfigDto->cookieLifetimeEssentialOnly = (int) ($legacyConfigData['cookieLifetimeEssentialOnly'] ?? $generalConfigDto->cookieLifetimeEssentialOnly);
        $generalConfigDto->cookiePath = (string) ($legacyConfigData['cookiePath'] ?? $generalConfigDto->cookiePath);
        $generalConfigDto->cookieSameSite = SameSiteEnum::fromValue($legacyConfigData['cookieSameSite'] ?? $generalConfigDto->cookieSameSite->value);
        $generalConfigDto->cookieSecure = (bool) ($legacyConfigData['cookieSecure'] ?? $generalConfigDto->cookieSecure);
        $generalConfigDto->cookiesForBots = (bool) ($legacyConfigData['cookiesForBots'] ?? $generalConfigDto->cookiesForBots);
        $generalConfigDto->crossCookieDomains = (array) ($legacyConfigData['crossDomainCookie'] ?? $generalConfigDto->crossCookieDomains);
        $generalConfigDto->reloadAfterOptIn = (bool) ($legacyConfigData['reloadAfterConsent'] ?? $generalConfigDto->reloadAfterOptIn);
        $generalConfigDto->reloadAfterOptOut = (bool) ($legacyConfigData['reloadAfterOptOut'] ?? $generalConfigDto->reloadAfterOptOut);
        $generalConfigDto->respectDoNotTrack = (bool) ($legacyConfigData['respectDoNotTrack'] ?? $generalConfigDto->respectDoNotTrack);
        $generalConfigDto->setupMode = (bool) ($legacyConfigData['setupMode'] ?? $generalConfigDto->setupMode);
        $generalConfigSaveStatus = $this->generalConfig->save($generalConfigDto, $languageCode);

        $this->log->info(
            '[Import] General config ({{ languageCode }}) imported: {{ status }}',
            [
                'languageCode' => $languageCode,
                'status' => $generalConfigSaveStatus ? 'Yes' : 'No',
            ],
        );
    }
}
