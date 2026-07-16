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

namespace Borlabs\Cookie\Command;

use Borlabs\Cookie\Container\ApplicationContainer;
use Borlabs\Cookie\Container\Container;
use Borlabs\Cookie\Dto\Config\GeneralDto;
use Borlabs\Cookie\Enum\Cookie\SameSiteEnum;
use Borlabs\Cookie\Support\Sanitizer;
use Borlabs\Cookie\Support\Traits\BooleanConvertibleTrait;
use Borlabs\Cookie\System\Config\GeneralConfig;
use Borlabs\Cookie\System\Language\Language;
use Borlabs\Cookie\System\Script\ScriptConfigBuilder;
use Borlabs\Cookie\System\ThirdPartyCacheClearer\ThirdPartyCacheClearerManager;
use WP_CLI;

class GeneralConfigCommand extends AbstractCommand
{
    use BooleanConvertibleTrait;

    /**
     * @const DEFAULT_FIELDS Default fields to display.
     */
    public const DEFAULT_FIELDS = [
        'aggregate-consents',
        'automatic-cookie-domain-and-path',
        'borlabs-cookie-status',
        'cookie-domain',
        'cookie-lifetime',
        'cookie-lifetime-essential-only',
        'cookie-path',
        'cookie-same-site',
        'cookie-secure',
        'cookies-for-bots',
        'cross-cookie-domains',
        'disable-borlabs-cookie-on-pages',
        'plugin-url',
        'reload-after-opt-in',
        'reload-after-opt-out',
        'respect-do-not-track',
        'setup-mode',
    ];

    private Container $container;

    private GeneralConfig $generalConfig;

    private Language $language;

    private ScriptConfigBuilder $scriptConfigBuilder;

    private ThirdPartyCacheClearerManager $thirdPartyCacheClearerManager;

    /**
     * GeneralConfigCommand constructor.
     */
    public function __construct()
    {
        $this->container = ApplicationContainer::get();
        $this->generalConfig = $this->container->get(GeneralConfig::class);
        $this->language = $this->container->get(Language::class);
        $this->scriptConfigBuilder = $this->container->get(ScriptConfigBuilder::class);
        $this->thirdPartyCacheClearerManager = $this->container->get(ThirdPartyCacheClearerManager::class);
    }

    /**
     * This method is for internal use only and is used to map DTO properties to the corresponding CLI fields.
     * Due to limitations in PHPUnit, the method cannot be moved to a trait.
     * It must be public so that it can be called by PHPUnit.
     * To hide the method from WP CLI, a double underscore (__) is used as a prefix.
     */
    public function __mapToCliFields(GeneralDto $generalConfig): array
    {
        return [
            'aggregate-consents' => $generalConfig->aggregateConsents,
            'automatic-cookie-domain-and-path' => $generalConfig->automaticCookieDomainAndPath,
            'borlabs-cookie-status' => $generalConfig->borlabsCookieStatus,
            'cookie-domain' => $generalConfig->cookieDomain,
            'cookie-lifetime' => $generalConfig->cookieLifetime,
            'cookie-lifetime-essential-only' => $generalConfig->cookieLifetimeEssentialOnly,
            'cookie-path' => $generalConfig->cookiePath,
            'cookie-same-site' => $generalConfig->cookieSameSite->value,
            'cookie-secure' => $generalConfig->cookieSecure,
            'cookies-for-bots' => $generalConfig->cookiesForBots,
            'cross-cookie-domains' => $generalConfig->crossCookieDomains,
            'disable-borlabs-cookie-on-pages' => $generalConfig->disableBorlabsCookieOnPages,
            'plugin-url' => $generalConfig->pluginUrl,
            'reload-after-opt-in' => $generalConfig->reloadAfterOptIn,
            'reload-after-opt-out' => $generalConfig->reloadAfterOptOut,
            'respect-do-not-track' => $generalConfig->respectDoNotTrack,
            'setup-mode' => $generalConfig->setupMode,
        ];
    }

    /**
     * Creates or updates the general configuration for the specified language.
     *
     * ## OPTIONS
     *
     * <language>
     * : The language code.
     *
     * [--aggregate-consents=<aggregate-consents>]
     * : Whether to aggregate consent information.
     *
     * [--automatic-cookie-domain-and-path=<automatic-cookie-domain-and-path>]
     * : Whether to automatically set the cookie domain and path.
     *
     * [--borlabs-cookie-status=<borlabs-cookie-status>]
     * : Whether to enable the Borlabs Cookie.
     *
     * [--cookie-domain=<cookie-domain>]
     * : The cookie domain.
     *
     * [--cookie-lifetime=<cookie-lifetime>]
     * : The cookie lifetime.
     *
     * [--cookie-lifetime-essential-only=<cookie-lifetime-essential-only>]
     * : The cookie lifetime when only essential consent has been given.
     *
     * [--cookie-path=<cookie-path>]
     * : The cookie path.
     *
     * [--cookie-same-site=<cookie-same-site>]
     * : The same site setting. Possible values: "None", "Lax".
     *
     * [--cookie-secure=<cookie-secure>]
     * : Whether to use secure cookies.
     *
     * [--cookies-for-bots=<cookies-for-bots>]
     * : Whether the dialog is not displayed for bots (including lighthouse) and the consent to all cookies/services is given.
     *
     * [--cross-cookie-domains=<cross-cookie-domains>]
     * : List of URLs the consent is transferred to.
     *
     * [--disable-borlabs-cookie-on-pages=<disable-borlabs-cookie-on-pages>]
     * : List of URLs where Borlabs Cookie is disabled.
     *
     * [--plugin-url=<plugin-url>]
     * : The plugin URL.
     *
     * [--reload-after-opt-in=<reload-after-opt-in>]
     * : Whether to reload the page after opt-in.
     *
     * [--reload-after-opt-out=<reload-after-opt-out>]
     * : Whether to reload the page after opt-out.
     *
     * [--respect-do-not-track=<respect-do-not-track>]
     * : Whether to respect the Do Not Track setting.
     *
     * [--setup-mode=<setup-mode>]
     * : Whether to enable the setup mode.
     * ---
     *
     * ## EXAMPLES
     *
     *      # Create the general configuration for the language code "en"
     *      $ wp borlabs-cookie general-config createOrUpdate en
     *      Success: Updated general config for language code "en".
     *
     *      # Update the cookie-domain and plugin-url fields for the language code "en"
     *      $ wp borlabs-cookie general-config createOrUpdate en --cookie-domain=example.com --plugin-url=https://example.com/wp-content/plugins/borlabs-cookie
     */
    public function createOrUpdate(array $args, array $assocArgs): void
    {
        $languageCode = $args[0];
        $generalConfig = $this->generalConfig->load($languageCode);
        $generalConfig->aggregateConsents = $this->toBoolean(WP_CLI\Utils\get_flag_value($assocArgs, 'aggregate-consents', $generalConfig->aggregateConsents));
        $generalConfig->automaticCookieDomainAndPath = $this->toBoolean(WP_CLI\Utils\get_flag_value($assocArgs, 'automatic-cookie-domain-and-path', $generalConfig->automaticCookieDomainAndPath));
        $generalConfig->borlabsCookieStatus = $this->toBoolean(WP_CLI\Utils\get_flag_value($assocArgs, 'borlabs-cookie-status', $generalConfig->borlabsCookieStatus));
        $generalConfig->cookieDomain = str_replace(
            ['https://', 'http://'],
            '',
            WP_CLI\Utils\get_flag_value($assocArgs, 'cookie-domain', $generalConfig->cookieDomain),
        );
        $generalConfig->cookieLifetime = (int) WP_CLI\Utils\get_flag_value($assocArgs, 'cookie-lifetime', $generalConfig->cookieLifetime);
        $generalConfig->cookieLifetimeEssentialOnly = (int) WP_CLI\Utils\get_flag_value($assocArgs, 'cookie-lifetime-essential-only', $generalConfig->cookieLifetimeEssentialOnly);
        $generalConfig->cookiePath = WP_CLI\Utils\get_flag_value($assocArgs, 'cookie-path', $generalConfig->cookiePath);

        $cookieSameSite = WP_CLI\Utils\get_flag_value($assocArgs, 'cookie-same-site');

        if (isset($cookieSameSite)) {
            if (SameSiteEnum::hasValue($cookieSameSite)) {
                $generalConfig->cookieSameSite = SameSiteEnum::fromValue($cookieSameSite);
            } else {
                WP_CLI::error(sprintf('Invalid value "%s" for "cookie-same-site" field.', $cookieSameSite));

                return;
            }
        }

        $generalConfig->cookieSecure = $this->toBoolean(WP_CLI\Utils\get_flag_value($assocArgs, 'cookie-secure', $generalConfig->cookieSecure));
        $generalConfig->cookiesForBots = $this->toBoolean(WP_CLI\Utils\get_flag_value($assocArgs, 'cookies-for-bots', $generalConfig->cookiesForBots));

        $crossCookieDomains = WP_CLI\Utils\get_flag_value($assocArgs, 'cross-cookie-domains');

        if (isset($crossCookieDomains)) {
            $generalConfig->crossCookieDomains = Sanitizer::hostList($crossCookieDomains, true);
        }

        $disableBorlabsCookieOnPages = WP_CLI\Utils\get_flag_value($assocArgs, 'disable-borlabs-cookie-on-pages');

        if (isset($disableBorlabsCookieOnPages)) {
            $disableBorlabsCookieOnPages = explode(',', $disableBorlabsCookieOnPages);
            $generalConfig->disableBorlabsCookieOnPages = [];

            foreach ($disableBorlabsCookieOnPages as $path) {
                $path = trim(stripslashes($path));

                if (!empty($path)) {
                    $generalConfig->disableBorlabsCookieOnPages[] = $path;
                }
            }
        }

        $generalConfig->pluginUrl = rtrim(WP_CLI\Utils\get_flag_value($assocArgs, 'plugin-url', $generalConfig->pluginUrl), '/');
        $generalConfig->reloadAfterOptIn = $this->toBoolean(WP_CLI\Utils\get_flag_value($assocArgs, 'reload-after-opt-in', $generalConfig->reloadAfterOptIn));
        $generalConfig->reloadAfterOptOut = $this->toBoolean(WP_CLI\Utils\get_flag_value($assocArgs, 'reload-after-opt-out', $generalConfig->reloadAfterOptOut));
        $generalConfig->respectDoNotTrack = $this->toBoolean(WP_CLI\Utils\get_flag_value($assocArgs, 'respect-do-not-track', $generalConfig->respectDoNotTrack));
        $generalConfig->setupMode = $this->toBoolean(WP_CLI\Utils\get_flag_value($assocArgs, 'setup-mode', $generalConfig->setupMode));
        $success = $this->generalConfig->save($generalConfig, $languageCode);
        $updateConfigAndClearCache = false;

        // The return value $success is `false` if no data has been changed.
        if (!$success && $this->generalConfig->hasConfig($languageCode) === false) {
            WP_CLI::error('The general config for language code "' . $languageCode . '" was not created.');
        } else {
            WP_CLI::success('Updated general config for language code "' . $languageCode . '".');
            $updateConfigAndClearCache = true;
        }

        if ($updateConfigAndClearCache) {
            $this->scriptConfigBuilder->updateJavaScriptConfigFileAndIncrementConfigVersion($languageCode);
            $this->thirdPartyCacheClearerManager->clearCache();
        }
    }

    /**
     * Get the general configuration for the requested language.
     *
     * ## OPTIONS
     *
     * <language>
     * : The language code.
     *
     * [--fields=<fields>]
     * : Limit the output to specific object fields.
     *
     * [--format=<format>]
     * : Render output in a particular format.
     * ---
     * default: table
     * options:
     *   - table
     *   - csv
     *   - json
     *   - yaml
     * ---
     *
     * ## AVAILABLE FIELDS
     *
     * These fields are displayed by default for the general configuration.
     *
     * * aggregate-consents
     * * automatic-cookie-domain-and-path
     * * borlabs-cookie-status
     * * cookie-domain
     * * cookie-lifetime
     * * cookie-lifetime-essential-only
     * * cookie-path
     * * cookie-same-site
     * * cookie-secure
     * * cookies-for-bots
     * * cross-cookie-domains
     * * disable-borlabs-cookie-on-pages
     * * plugin-url
     * * reload-after-opt-in
     * * reload-after-opt-out
     * * respect-do-not-track
     * * setup-mode
     *
     * ## EXAMPLES
     *
     *     # List the general configuration for the language code "en"
     *     $ wp borlabs-cookie general-config get en
     *     +----------------------------------+--------------+
     *     | Field                            | Value        |
     *     +----------------------------------+--------------+
     *     | aggregate-consents               | 1            |
     *     | automatic-cookie-domain-and-path | 1            |
     *     | borlabs-cookie-status            | 1            |
     *     | cookie-domain                    | example.com  |
     *     | ...                              | ...          |
     *     +----------------------------------+--------------+
     *
     *     # List only the cookie-domain and cookie-path fields for the language code "en"
     *     $ wp borlabs-cookie general-config get en --fields=cookie-domain,cookie-path
     *     +----------------------------------+--------------+
     *     | Field                            | Value        |
     *     +----------------------------------+--------------+
     *     | cookie-domain                    | example.com  |
     *     | cookie-path                      | /            |
     *     +----------------------------------+--------------+
     *
     *     # List the general configuration for the language code "en" in JSON format
     *     $ wp borlabs-cookie general-config get en --format=json
     *     {
     *         "aggregate-consents": 1,
     *         "automatic-cookie-domain-and-path": 1,
     *         "borlabs-cookie-status": 1,
     *         "cookie-domain": "example.com",
     *         ...
     *     }
     */
    public function get(array $args, array $assocArgs): void
    {
        $languageCode = $args[0];

        if ($this->generalConfig->hasConfig($languageCode) === false) {
            WP_CLI::error(sprintf('No configuration found for language code "%s".', $languageCode));

            return;
        }

        $generalConfig = $this->generalConfig->load($languageCode);
        $formatter = $this->getFormatter($assocArgs, self::DEFAULT_FIELDS);
        $formatter->display_item($this->__mapToCliFields($generalConfig));
    }

    /**
     * Get the default general configuration for the requested language.
     *
     * ## OPTIONS
     *
     * <language>
     * : The language code.
     *
     * [--fields=<fields>]
     * : Limit the output to specific object fields.
     *
     * [--format=<format>]
     * : Render output in a particular format.
     * ---
     * default: table
     * options:
     *   - table
     *   - csv
     *   - json
     *   - yaml
     * ---
     *
     * ## AVAILABLE FIELDS
     *
     * These fields are displayed by default for the general configuration.
     *
     * * aggregate-consents
     * * automatic-cookie-domain-and-path
     * * borlabs-cookie-status
     * * cookie-domain
     * * cookie-lifetime
     * * cookie-lifetime-essential-only
     * * cookie-path
     * * cookie-same-site
     * * cookie-secure
     * * cookies-for-bots
     * * cross-cookie-domains
     * * disable-borlabs-cookie-on-pages
     * * plugin-url
     * * reload-after-opt-in
     * * reload-after-opt-out
     * * respect-do-not-track
     * * setup-mode
     *
     * ## EXAMPLES
     *
     *     # List the general configuration for the language code "en"
     *     $ wp borlabs-cookie general-config getDefault en
     *     +----------------------------------+--------------+
     *     | Field                            | Value        |
     *     +----------------------------------+--------------+
     *     | aggregate-consents               | 1            |
     *     | automatic-cookie-domain-and-path | 1            |
     *     | borlabs-cookie-status            | 1            |
     *     | cookie-domain                    | example.com  |
     *     | ...                              | ...          |
     *     +----------------------------------+--------------+
     *
     *     # List only the cookie-domain and cookie-path fields for the language code "en"
     *     $ wp borlabs-cookie general-config getDefault en --fields=cookie-domain,cookie-path
     *     +----------------------------------+--------------+
     *     | Field                            | Value        |
     *     +----------------------------------+--------------+
     *     | cookie-domain                    | example.com  |
     *     | cookie-path                      | /            |
     *     +----------------------------------+--------------+
     *
     *     # List the general configuration for the language code "en" in JSON format
     *     $ wp borlabs-cookie general-config getDefault en --format=json
     *     {
     *         "aggregate-consents": 1,
     *         "automatic-cookie-domain-and-path": 1,
     *         "borlabs-cookie-status": 1,
     *         "cookie-domain": "example.com",
     *         ...
     *     }
     */
    public function getDefault(array $args, array $assocArgs): void
    {
        $languageCode = $args[0];

        $generalConfig = $this->language->runInLanguageContext($languageCode, function () {
            return $this->generalConfig->defaultConfig();
        });

        $formatter = $this->getFormatter($assocArgs, self::DEFAULT_FIELDS);
        $formatter->display_item($this->__mapToCliFields($generalConfig));
    }

    /**
     * List all languages that have a general configuration.
     *
     *  ## OPTIONS
     *
     *  [--format=<format>]
     *  : Render output in a particular format.
     *  ---
     *  default: table
     *  options:
     *    - table
     *    - csv
     *    - json
     *    - yaml
     *  ---
     *
     *  ## EXAMPLES
     *
     *     # List all languages that have a general configuration
     *     $ wp borlabs-cookie general-config listConfiguredLanguages
     *     +----------+
     *     | language |
     *     +----------+
     *     | de       |
     *     | en       |
     *     | fr       |
     */
    public function listConfiguredLanguages(array $args, array $assocArgs): void
    {
        $allConfigs = $this->generalConfig->getAllConfigs();
        $languages = [];

        /** @var \Borlabs\Cookie\Dto\System\OptionDto $option */
        foreach ($allConfigs as $option) {
            $languages[] = $option->language;
        }

        $formatter = $this->getFormatter($assocArgs, ['language']);
        $iterator = WP_CLI\Utils\iterator_map($languages, function ($language): array {
            return [
                'language' => $language,
            ];
        });
        $formatter->display_items($iterator);
    }
}
