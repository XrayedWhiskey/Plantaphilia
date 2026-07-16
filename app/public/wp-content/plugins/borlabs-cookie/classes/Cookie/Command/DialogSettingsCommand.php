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

use Borlabs\Cookie\Adapter\WpFunction;
use Borlabs\Cookie\Container\ApplicationContainer;
use Borlabs\Cookie\Container\Container;
use Borlabs\Cookie\Dto\Config\DialogSettingsDto;
use Borlabs\Cookie\Support\Sanitizer;
use Borlabs\Cookie\System\Config\DialogSettingsConfig;
use Borlabs\Cookie\System\Language\Language;
use Borlabs\Cookie\System\Script\ScriptConfigBuilder;
use Borlabs\Cookie\System\ThirdPartyCacheClearer\ThirdPartyCacheClearerManager;
use WP_CLI;

class DialogSettingsCommand extends AbstractCommand
{
    /**
     * @const DEFAULT_FIELDS Default fields to display.
     */
    public const DEFAULT_FIELDS = [
        'hide-dialog-on-pages',
        'imprint-page-custom-url',
        'imprint-page-id',
        'imprint-page-url',
        'logo',
        'logo-hd',
        'privacy-page-custom-url',
        'privacy-page-id',
        'privacy-page-url',
    ];

    private Container $container;

    private DialogSettingsConfig $dialogSettingsConfig;

    private Language $language;

    private ScriptConfigBuilder $scriptConfigBuilder;

    private ThirdPartyCacheClearerManager $thirdPartyCacheClearerManager;

    private WpFunction $wpFunction;

    public function __construct()
    {
        $this->container = ApplicationContainer::get();
        $this->dialogSettingsConfig = $this->container->get(DialogSettingsConfig::class);
        $this->language = $this->container->get(Language::class);
        $this->scriptConfigBuilder = $this->container->get(ScriptConfigBuilder::class);
        $this->thirdPartyCacheClearerManager = $this->container->get(ThirdPartyCacheClearerManager::class);
        $this->wpFunction = $this->container->get(WpFunction::class);
    }

    /**
     * This method is for internal use only and is used to map DTO properties to the corresponding CLI fields.
     * Due to limitations in PHPUnit, the method cannot be moved to a trait.
     * It must be public so that it can be called by PHPUnit.
     * To hide the method from WP CLI, a double underscore (__) is used as a prefix.
     */
    public function __mapToCliFields(DialogSettingsDto $dialogSettings): array
    {
        return [
            'hide-dialog-on-pages' => $dialogSettings->hideDialogOnPages,
            'imprint-page-custom-url' => $dialogSettings->imprintPageCustomUrl,
            'imprint-page-id' => $dialogSettings->imprintPageId,
            'imprint-page-url' => $dialogSettings->imprintPageUrl,
            'logo' => $dialogSettings->logo,
            'logo-hd' => $dialogSettings->logoHd,
            'privacy-page-custom-url' => $dialogSettings->privacyPageCustomUrl,
            'privacy-page-id' => $dialogSettings->privacyPageId,
            'privacy-page-url' => $dialogSettings->privacyPageUrl,
        ];
    }

    /**
     * Creates or updates the dialog settings for the requested language. Only settings that contain a URL and need to be adjusted when switching from, for example, a staging environment to a live environment can be changed via CLI. All other settings can only be changed via the user interface.
     *
     * ## OPTIONS
     *
     * <language>
     * : The language code.
     *
     * [--hide-dialog-on-pages=<hide-dialog-on-pages>]
     * : A comma-separated list of URLs where the dialog is not displayed to visitors without consent.
     *
     * [--imprint-page-custom-url=<imprint-page-custom-url>]
     * : The custom URL of the imprint page.
     *
     * [--imprint-page-id=<imprint-page-id>]
     * : The ID of the imprint page.
     *
     * [--logo=<logo>]
     * : The URL of the logo.
     *
     * [--logo-hd=<logo-hd>]
     * : The URL of the logo for high-definition screens.
     *
     * [--privacy-page-custom-url=<privacy-page-custom-url>]
     * : The custom URL of the privacy page.
     *
     * [--privacy-page-id=<privacy-page-id>]
     * : The ID of the privacy page.
     * ---
     *
     * ## EXAMPLES
     *
     *     # Create or update the dialog settings for the language code "en"
     *     $ wp borlabs-cookie dialog-settings createOrUpdate en
     *     Success: Updated dialog settings for language code "en".
     *
     *     # Update the URLs of the imprint and privacy page for the language code "en"
     *     $ wp borlabs-cookie dialog-settings createOrUpdate en --imprint-page-custom-url=https://example.com/imprint --privacy-page-custom-url=https://example.com/privacy
     *
     *     # Update the hide-on-dialog-pages field for the language code "en"
     *     $ wp borlabs-cookie dialog-settings createOrUpdate en --hide-dialog-on-pages=https://example.com/privacy,https://example.com/imprint
     */
    public function createOrUpdate(array $args, array $assocArgs): void
    {
        $languageCode = $args[0];
        $dialogSettingsConfig = $this->dialogSettingsConfig->load($languageCode);

        $hideDialogOnPages = WP_CLI\Utils\get_flag_value($assocArgs, 'hide-dialog-on-pages');

        if (isset($hideDialogOnPages)) {
            $dialogSettingsConfig->hideDialogOnPages = Sanitizer::hostList($hideDialogOnPages, true);
        }

        $imprintPageId = WP_CLI\Utils\get_flag_value($assocArgs, 'imprint-page-id', 0);

        if ((int) ($imprintPageId ?? 0) > 0) {
            $dialogSettingsConfig->imprintPageId = (int) $imprintPageId;
            $dialogSettingsConfig->imprintPageUrl = $this->wpFunction->getPermalink($dialogSettingsConfig->imprintPageId) ?? '';
        }

        $imprintPageCustomUrl = WP_CLI\Utils\get_flag_value($assocArgs, 'imprint-page-custom-url', '');

        if ($imprintPageCustomUrl ?? '' !== '') {
            if (filter_var($imprintPageCustomUrl, FILTER_VALIDATE_URL)) {
                $dialogSettingsConfig->imprintPageId = 0;
                $dialogSettingsConfig->imprintPageUrl = $imprintPageCustomUrl;
                $dialogSettingsConfig->imprintPageCustomUrl = $imprintPageCustomUrl;
            }
        }

        $dialogSettingsConfig->logo = WP_CLI\Utils\get_flag_value($assocArgs, 'logo', $dialogSettingsConfig->logo);
        $dialogSettingsConfig->logoHd = WP_CLI\Utils\get_flag_value($assocArgs, 'logo-hd', $dialogSettingsConfig->logoHd);
        $privacyPageId = WP_CLI\Utils\get_flag_value($assocArgs, 'privacy-page-id', 0);

        if ((int) ($privacyPageId ?? 0) > 0) {
            $dialogSettingsConfig->privacyPageId = (int) $privacyPageId;
            $dialogSettingsConfig->privacyPageUrl = $this->wpFunction->getPermalink($dialogSettingsConfig->privacyPageId) ?? '';
        }

        $privacyPageCustomUrl = WP_CLI\Utils\get_flag_value($assocArgs, 'privacy-page-custom-url', '');

        if ($privacyPageCustomUrl ?? '' !== '') {
            if (filter_var($privacyPageCustomUrl, FILTER_VALIDATE_URL)) {
                $dialogSettingsConfig->privacyPageId = 0;
                $dialogSettingsConfig->privacyPageUrl = $privacyPageCustomUrl;
                $dialogSettingsConfig->privacyPageCustomUrl = $privacyPageCustomUrl;
            }
        }

        $success = $this->dialogSettingsConfig->save($dialogSettingsConfig, $languageCode);
        $updateConfigAndClearCache = false;

        // The return value $success is `false` if no data has been changed.
        if (!$success && $this->dialogSettingsConfig->hasConfig($languageCode) === false) {
            WP_CLI::error('The dialog settings for language code "' . $languageCode . '" was not created.');
        } else {
            WP_CLI::success('Updated dialog settings for language code "' . $languageCode . '".');
            $updateConfigAndClearCache = true;
        }

        if ($updateConfigAndClearCache) {
            $this->scriptConfigBuilder->updateJavaScriptConfigFileAndIncrementConfigVersion($languageCode);
            $this->thirdPartyCacheClearerManager->clearCache();
        }
    }

    /**
     * Get the dialog settings for the requested language. Only settings that contain a URL and need to be adjusted when switching from, for example, a staging environment to a live environment are displayed. All other settings can only be changed via the user interface.
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
     * These fields are displayed by default for the dialog settings.
     *
     * * hide-dialog-on-pages
     * * imprint-page-custom-url
     * * imprint-page-id
     * * imprint-page-url
     * * logo
     * * logo-hd
     * * privacy-page-custom-url
     * * privacy-page-id
     * * privacy-page-url
     *
     * ## EXAMPLES
     *
     *     # List the dialog settings for the language code "en"
     *     $ wp borlabs-cookie dialog-settings get en
     *     +----------------------------------+--------------+
     *     | Field                            | Value        |
     *     +----------------------------------+--------------+
     *     | hide-dialog-on-pages             | []           |
     *     | imprint-page-custom-url          |              |
     *     | imprint-page-id                  | 0            |
     *     | imprint-page-url                 |              |
     *     | logo                             | https://example.com/wp-content/themes/borlabs-cookie/assets/images/borlabs-cookie-logo.svg |
     *     | ...                              | ...          |
     *     +----------------------------------+--------------+
     *
     *     # List only the imprint-page-id and logo fields for the language code "en"
     *     $ wp borlabs-cookie dialog-settings getD en --fields=imprint-page-id,logo
     *     +----------------------------------+--------------+
     *     | Field                            | Value        |
     *     +----------------------------------+--------------+
     *     | imprint-page-id                  | 0            |
     *     | logo                             | https://example.com/wp-content/themes/borlabs-cookie/assets/images/borlabs-cookie-logo.svg |
     *     +----------------------------------+--------------+
     *
     *     # List the dialog settings for the language code "en" in JSON format
     *     $ wp borlabs-cookie dialog-settings get en --format=json
     *     {
     *         "hide-dialog-on-pages": [],
     *         "imprint-page-custom-url": "",
     *         "imprint-page-id": 0,
     *         "imprint-page-url": "",
     *         "logo": "https://example.com/wp-content/themes/borlabs-cookie/assets/images/borlabs-cookie-logo.svg",
     *         ...
     *     }
     */
    public function get(array $args, array $assocArgs): void
    {
        $languageCode = $args[0];

        if ($this->dialogSettingsConfig->hasConfig($languageCode) === false) {
            WP_CLI::error(sprintf('No settings found for language code "%s".', $languageCode));

            return;
        }

        $dialogSettingsConfig = $this->dialogSettingsConfig->load($languageCode);
        $formatter = $this->getFormatter($assocArgs, self::DEFAULT_FIELDS);
        $formatter->display_item($this->__mapToCliFields($dialogSettingsConfig));
    }

    /**
     * Get the default dialog settings for the requested language. Only settings that contain a URL and need to be adjusted when switching from, for example, a staging environment to a live environment are displayed. All other settings can only be changed via the user interface.
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
     * These fields are displayed by default for the dialog settings.
     *
     * * hide-dialog-on-pages
     * * imprint-page-custom-url
     * * imprint-page-id
     * * imprint-page-url
     * * logo
     * * logo-hd
     * * privacy-page-custom-url
     * * privacy-page-id
     * * privacy-page-url
     *
     * ## EXAMPLES
     *
     *     # List the dialog settings for the language code "en"
     *     $ wp borlabs-cookie dialog-settings getDefault en
     *     +----------------------------------+--------------+
     *     | Field                            | Value        |
     *     +----------------------------------+--------------+
     *     | hide-dialog-on-pages             | []           |
     *     | imprint-page-custom-url          |              |
     *     | imprint-page-id                  | 0            |
     *     | imprint-page-url                 |              |
     *     | logo                             | https://example.com/wp-content/themes/borlabs-cookie/assets/images/borlabs-cookie-logo.svg |
     *     | ...                              | ...          |
     *     +----------------------------------+--------------+
     *
     *     # List only the imprint-page-id and logo fields for the language code "en"
     *     $ wp borlabs-cookie dialog-settings getDefault en --fields=imprint-page-id,logo
     *     +----------------------------------+--------------+
     *     | Field                            | Value        |
     *     +----------------------------------+--------------+
     *     | imprint-page-id                  | 0            |
     *     | logo                             | https://example.com/wp-content/themes/borlabs-cookie/assets/images/borlabs-cookie-logo.svg |
     *     +----------------------------------+--------------+
     *
     *     # List the dialog settings for the language code "en" in JSON format
     *     $ wp borlabs-cookie dialog-settings getDefault en --format=json
     *     {
     *         "hide-dialog-on-pages": [],
     *         "imprint-page-custom-url": "",
     *         "imprint-page-id": 0,
     *         "imprint-page-url": "",
     *         "logo": "https://example.com/wp-content/themes/borlabs-cookie/assets/images/borlabs-cookie-logo.svg",
     *         ...
     *     }
     */
    public function getDefault(array $args, array $assocArgs): void
    {
        $languageCode = $args[0];

        $dialogSettingsConfig = $this->language->runInLanguageContext($languageCode, function () {
            return $this->dialogSettingsConfig->defaultConfig();
        });

        $formatter = $this->getFormatter($assocArgs, self::DEFAULT_FIELDS);
        $formatter->display_item($this->__mapToCliFields($dialogSettingsConfig));
    }

    /**
     * List all languages that have a dialog settings configuration.
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
     *     # List all languages that have a dialog settings configuration
     *     $ wp borlabs-cookie dialog-settings listConfiguredLanguages
     *     +----------+
     *     | language |
     *     +----------+
     *     | de       |
     *     | en       |
     *     | fr       |
     */
    public function listConfiguredLanguages(array $args, array $assocArgs): void
    {
        $allConfigs = $this->dialogSettingsConfig->getAllConfigs();
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
