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

namespace Borlabs\Cookie\Dto\Config;

use Borlabs\Cookie\Enum\Cookie\SameSiteEnum;

/**
 * The **GeneralDto** class is used as a typed object that is passed within the system.
 *
 * The object contains technical configuration properties related to the Borlabs Cookie plugin and its cookie.
 *
 * @see \Borlabs\Cookie\System\Config\GeneralConfig
 */
final class GeneralDto extends AbstractConfigDto
{
    /**
     * @var bool default: `false`; `true`: In a multisite network all consents are stored in the main site's
     *           consent table
     */
    public bool $aggregateConsents = false;

    /**
     * @var bool default: `false`; `true`: Borlabs Cookie tries to detect the domain and path for the cookie settings
     */
    public bool $automaticCookieDomainAndPath = false;

    /**
     * @var bool default: `false`; `true`: Activates Borlabs Cookie for the frontend
     */
    public bool $borlabsCookieStatus = false;

    /**
     * @var bool default: `true`; `true`: Borlabs Cookie automatically clears the cache from third-party plugins following specific actions within Borlabs Cookie
     *
     * @deprecated 3.4.0 The setting is now part of PluginDto.
     */
    public bool $clearThirdPartyCache = true;

    /**
     * @var string The domain of the Borlabs Cookie cookie. With the domain setting it is possible to distinguish
     *             between subdomains that are in the same namespace. For example: A cookie for .example.org would not be used
     *             on shop.example.org,
     */
    public string $cookieDomain = '';

    /**
     * @var int default: `60`; The number of days the cookie is valid
     */
    public int $cookieLifetime = 60;

    /**
     * @var int default: `60`; The number of days the cookie is valid if consent was given only for essential cookies/services
     */
    public int $cookieLifetimeEssentialOnly = 60;

    /**
     * @var string Default: `/`; The path of the Borlabs Cookie cookie. With the path setting it is possible to
     *             distinguish between multiple installations that share the same domain. For example: A path for /shop would
     *             not be used on /.
     */
    public string $cookiePath = '/';

    /**
     * @var \Borlabs\Cookie\Enum\Cookie\SameSiteEnum default: `Lax`; The SameSite attribute of the Borlabs Cookie cookie
     */
    public SameSiteEnum $cookieSameSite;

    /**
     * @var bool default: `true`; `true`: The Borlabs Cookie cookie is only transmitted via HTTPS
     */
    public bool $cookieSecure = true;

    /**
     * @var bool default: `true`; `true`: The dialog is not displayed for bots (including lighthouse) and the consent
     *           to all cookies/services is given
     */
    public bool $cookiesForBots = true;

    /**
     * @var array list of URLs the consent is transferred to
     */
    public array $crossCookieDomains = [];

    /**
     * @var array list of URLs where Borlabs Cookie is disabled
     */
    public array $disableBorlabsCookieOnPages = [];

    /**
     * @var array list of post types where Borlabs Cookie meta box is available
     *
     * @deprecated 3.4.0 The setting is now part of PluginDto.
     */
    public array $metaBox = [];

    public string $pluginUrl = '';

    /**
     * @var bool default: `false`; `true`: After the consent is given, the page is reloaded
     */
    public bool $reloadAfterOptIn = false;

    /**
     * @var bool default: `true`; `true`: After the consent is revoked, the page is reloaded
     */
    public bool $reloadAfterOptOut = true;

    /**
     * @var bool Default: `false`; `true`: Visitors who send the `Do not Track` signal will not see the dialog. They
     *           will be treated as if they had only given consent to essential cookies/services.
     */
    public bool $respectDoNotTrack = false;

    /**
     * @var bool Default: `false`; `true`: Only for loggend-in users with `manage_borlabs_cookie` capability the Borlabs
     *           Cookie System is active. When `setupMode` is `true`, the option `borlabsCookieStatus` should be `false`.
     */
    public bool $setupMode = false;
}
