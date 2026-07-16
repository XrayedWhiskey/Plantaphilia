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

namespace Borlabs\CookieApi\PhpApi\Component;

use Borlabs\Cookie\Support\Transformer;
use Borlabs\Cookie\System\Config\GeneralConfig;
use stdClass;

final class GeneralConfigPhpApi
{
    private GeneralConfig $generalConfig;

    public function __construct(GeneralConfig $generalConfig)
    {
        $this->generalConfig = $generalConfig;
    }

    /**
     * Example:
     * <code>
     * {
     *  "aggregateConsents": false,
     *  "automaticCookieDomainAndPath": false,
     *  "borlabsCookieStatus": false,
     *  "clearThirdPartyCache": true,
     *  "cookieDomain": "domain.internal",
     *  "cookieLifetime": 60,
     *  "cookieLifetimeEssentialOnly": 60,
     *  "cookiePath": "/",
     *  "cookieSameSite": "Lax", // "Lax"|"None",
     *  "cookieSecure": true,
     *  "cookiesForBots": true,
     *  "crossCookieDomains": ["second-domain.internal"],
     *  "disableBorlabsCookieOnPages": ["https://domain.internal/example-page/"],
     *  "metaBox": ["post" => "1"],
     *  "pluginUrl": "https://domain.internal/wp-content/plugins/borlabs-cookie",
     *  "reloadAfterOptIn": false,
     *  "reloadAfterOptOut": true,
     *  "respectDoNotTrack": false,
     *  "setupMode": false,
     * }
     * </code>.
     *
     * @return stdClass{
     *     aggregateConsents: bool,
     *     automaticCookieDomainAndPath: bool,
     *     borlabsCookieStatus: bool,
     *     clearThirdPartyCache: bool,
     *     cookieDomain: string,
     *     cookieLifetime: int,
     *     cookieLifetimeEssentialOnly: int,
     *     cookiePath: string,
     *     cookieSameSite: string,
     *     cookieSecure: bool,
     *     cookiesForBots: bool,
     *     crossCookieDomains: array,
     *     disableBorlabsCookieOnPages: array,
     *     metaBox: array,
     *     pluginUrl: string,
     *     reloadAfterOptIn: bool,
     *     reloadAfterOptOut: bool,
     *     respectDoNotTrack: bool,
     *     setupMode: bool
     * }
     */
    public function get(): stdClass
    {
        return Transformer::objectToStdClass($this->generalConfig->get());
    }
}
