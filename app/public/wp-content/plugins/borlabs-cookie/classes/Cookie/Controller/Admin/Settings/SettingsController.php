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

namespace Borlabs\Cookie\Controller\Admin\Settings;

use Borlabs\Cookie\Controller\Admin\ControllerInterface;
use Borlabs\Cookie\Dto\System\RequestDto;
use Borlabs\Cookie\Enum\Cookie\SameSiteEnum;
use Borlabs\Cookie\Localization\GlobalLocalizationStrings;
use Borlabs\Cookie\Localization\Settings\SettingsLocalizationStrings;
use Borlabs\Cookie\Localization\ValidatorLocalizationStrings;
use Borlabs\Cookie\Support\Formatter;
use Borlabs\Cookie\Support\Sanitizer;
use Borlabs\Cookie\System\Config\GeneralConfig;
use Borlabs\Cookie\System\Language\Language;
use Borlabs\Cookie\System\Message\MessageManager;
use Borlabs\Cookie\System\Option\Option;
use Borlabs\Cookie\System\Script\ScriptConfigBuilder;
use Borlabs\Cookie\System\Template\Template;
use Borlabs\Cookie\System\ThirdPartyCacheClearer\ThirdPartyCacheClearerManager;
use Exception;

/**
 * Class SettingsController.
 *
 * The **SettingsController** class takes care of displaying the Settings section in the backend.
 * It also processes all requests that can be executed in the Settings section.
 */
final class SettingsController implements ControllerInterface
{
    public const CONTROLLER_ID = 'borlabs-cookie-settings';

    private GeneralConfig $generalConfig;

    private GlobalLocalizationStrings $globalLocalizationStrings;

    private Language $language;

    private MessageManager $messageManager;

    private Option $option;

    private ScriptConfigBuilder $scriptConfigBuilder;

    private Template $template;

    private ThirdPartyCacheClearerManager $thirdPartyCacheClearerManager;

    public function __construct(
        GeneralConfig $generalConfig,
        GlobalLocalizationStrings $globalLocalizationStrings,
        Language $language,
        MessageManager $messageManager,
        Option $option,
        ScriptConfigBuilder $scriptConfigBuilder,
        Template $template,
        ThirdPartyCacheClearerManager $thirdPartyCacheClearerManager
    ) {
        $this->generalConfig = $generalConfig;
        $this->globalLocalizationStrings = $globalLocalizationStrings;
        $this->language = $language;
        $this->messageManager = $messageManager;
        $this->option = $option;
        $this->scriptConfigBuilder = $scriptConfigBuilder;
        $this->template = $template;
        $this->thirdPartyCacheClearerManager = $thirdPartyCacheClearerManager;
    }

    public function reset(): bool
    {
        // Get default settings
        $defaultConfig = $this->generalConfig->defaultConfig();
        // Save config
        $this->generalConfig->save($defaultConfig, $this->language->getSelectedLanguageCode());
        $this->scriptConfigBuilder->updateJavaScriptConfigFileAndIncrementConfigVersion(
            $this->language->getSelectedLanguageCode(),
        );
        $this->thirdPartyCacheClearerManager->clearCache();

        $this->messageManager->success($this->globalLocalizationStrings::get()['alert']['resetSuccessfully']);

        return true;
    }

    /**
     * Is loaded by {@see \Borlabs\Cookie\System\WordPressAdminDriver\ControllerManager::load()} and gets information
     * what about to do.
     *
     * @throws \Borlabs\Cookie\Dependencies\Twig\Error\Error
     * @throws Exception
     */
    public function route(RequestDto $request): ?string
    {
        $action = $request->postData['action'] ?? $request->getData['action'] ?? '';

        if ($action === 'reset') {
            $this->reset();
        }

        if ($action === 'save') {
            $this->save($request->postData);
        }

        return $this->viewOverview();
    }

    /**
     * Updates the configuration.
     *
     * @see \Borlabs\Cookie\System\Config\AbstractConfigManagerWithLanguage
     *
     * @param array<string> $postData
     */
    public function save(array $postData): bool
    {
        $generalConfig = $this->generalConfig->get();

        $postData['crossCookieDomains'] = Sanitizer::hostList($postData['crossCookieDomains'], true);

        if (!empty($postData['cookieDomain'])) {
            $postData['cookieDomain'] = str_replace(
                ['https://', 'http://'],
                '',
                $postData['cookieDomain'],
            );
        }

        if (!empty($postData['updateCookieVersion'])) {
            $installedVersion = $this->option->getGlobal('CookieVersion', 1)->value;
            $this->option->setGlobal('CookieVersion', $installedVersion + 1);
        }

        $generalConfig->borlabsCookieStatus = (bool) $postData['borlabsCookieStatus'];
        $generalConfig->setupMode = (bool) $postData['setupMode'];
        $generalConfig->aggregateConsents = (bool) $postData['aggregateConsents'];
        $generalConfig->cookiesForBots = (bool) $postData['cookiesForBots'];
        $generalConfig->respectDoNotTrack = (bool) $postData['respectDoNotTrack'];
        $generalConfig->reloadAfterOptIn = (bool) $postData['reloadAfterOptIn'];
        $generalConfig->reloadAfterOptOut = (bool) $postData['reloadAfterOptOut'];
        $generalConfig->pluginUrl = rtrim($postData['pluginUrl'], '/');
        $siteURLInfo = parse_url($this->language->getHomeUrlForSelectedLanguageCode());
        $networkDomain = $siteURLInfo['host'];
        $generalConfig->automaticCookieDomainAndPath = (bool) $postData['automaticCookieDomainAndPath'];
        $generalConfig->cookieDomain = !empty($postData['cookieDomain']) ? (string) $postData['cookieDomain']
            : $networkDomain;
        $generalConfig->cookiePath = !empty($postData['cookiePath']) ? (string) $postData['cookiePath'] : '/';
        $generalConfig->cookieSecure = (bool) $postData['cookieSecure'];
        $generalConfig->cookieSameSite = SameSiteEnum::hasValue($postData['cookieSameSite'] ?? '') ? SameSiteEnum::fromValue($postData['cookieSameSite']) : SameSiteEnum::LAX();
        $generalConfig->cookieLifetime = !empty($postData['cookieLifetime']) ? (int) $postData['cookieLifetime'] : 60;
        $generalConfig->cookieLifetimeEssentialOnly = !empty($postData['cookieLifetimeEssentialOnly']) ? (int) $postData['cookieLifetimeEssentialOnly'] : 60;
        $generalConfig->crossCookieDomains = $postData['crossCookieDomains'];

        // Disable Borlabs Cookie on pages
        $generalConfig->disableBorlabsCookieOnPages = [];

        if (!empty($postData['disableBorlabsCookieOnPages'])) {
            $postData['disableBorlabsCookieOnPages'] = stripslashes($postData['disableBorlabsCookieOnPages']);
            $postData['disableBorlabsCookieOnPages'] = preg_split('/\r\n|[\r\n]/', $postData['disableBorlabsCookieOnPages']);

            if (!empty($postData['disableBorlabsCookieOnPages'])) {
                foreach ($postData['disableBorlabsCookieOnPages'] as $path) {
                    $path = trim(stripslashes($path));

                    if (!empty($path)) {
                        $generalConfig->disableBorlabsCookieOnPages[] = $path;
                    }
                }
            }
        }

        // Save config for other languages
        $languages = array_keys(
            array_filter(
                $postData['languages']['configuration'] ?? [],
                fn ($checked) => $checked === '1',
            ),
        );

        $languageUrlList = $this->language->getLanguageUrlList();
        $pluginUrlInfo = parse_url($generalConfig->pluginUrl);

        foreach ($languages as $languageCode) {
            $generalConfigForCurrentLanguage = clone $generalConfig;
            // Update `cookieDomain` and `pluginUrl` based on the settings of the multi-language plugin.
            $localizedSiteUrl = $languageUrlList->getByKey($languageCode);

            if ($localizedSiteUrl !== null) {
                $localizedSiteUrlInfo = parse_url($localizedSiteUrl->value);
                $generalConfigForCurrentLanguage->cookieDomain = $localizedSiteUrlInfo['host'];
                $generalConfigForCurrentLanguage->pluginUrl = $localizedSiteUrlInfo['scheme'] . '://'
                    . $localizedSiteUrlInfo['host']
                    . (isset($localizedSiteUrlInfo['port']) ? ':' . $localizedSiteUrlInfo['port'] : '')
                    . $pluginUrlInfo['path'];
            }

            $this->generalConfig->save($generalConfigForCurrentLanguage, $languageCode);
            $this->scriptConfigBuilder->updateJavaScriptConfigFileAndIncrementConfigVersion(
                $languageCode,
            );
        }

        // Save config for this language. The save routine also updates the current language object.
        $this->generalConfig->save($generalConfig, $this->language->getSelectedLanguageCode());
        $this->scriptConfigBuilder->updateJavaScriptConfigFileAndIncrementConfigVersion(
            $this->language->getSelectedLanguageCode(),
        );
        $this->thirdPartyCacheClearerManager->clearCache();

        $this->messageManager->success($this->globalLocalizationStrings::get()['alert']['savedSuccessfully']);

        return true;
    }

    /**
     * Returns the overview.
     *
     * @throws \Borlabs\Cookie\Dependencies\Twig\Error\Error
     */
    public function viewOverview(): string
    {
        $templateData = [];
        $templateData['controllerId'] = self::CONTROLLER_ID;
        $templateData['localized'] = SettingsLocalizationStrings::get();
        $templateData['localized']['global'] = $this->globalLocalizationStrings::get();
        $templateData['data'] = (array) $this->generalConfig->get();
        $templateData['data']['cookieVersion'] = $this->option->getGlobal('CookieVersion', 1)->value;
        $templateData['doNotTrackIsActive'] = $_SERVER['HTTP_DNT'] ?? false;
        $templateData['enum']['sameSite'] = SameSiteEnum::getLocalizedKeyValueList();
        $templateData['languages'] = $this->language->getLanguageList();

        $siteURLInfo = parse_url($this->language->getHomeUrlForSelectedLanguageCode());
        $networkDomain = $siteURLInfo['host'];
        $networkPath = !empty($siteURLInfo['path']) ? $siteURLInfo['path'] : '/';
        $templateData['localized']['hint']['cookiePath'] = Formatter::interpolate(
            $templateData['localized']['hint']['cookiePath'],
            [
                'networkPath' => $networkPath,
            ],
        );
        // Modify validation messages
        $validationLocalization = ValidatorLocalizationStrings::get();
        $templateData['localized']['validation']['cookieDomain'] = Formatter::interpolate(
            $validationLocalization['isNotEmptyString'],
            ['fieldName' => $templateData['localized']['field']['cookieDomain']],
        );
        $templateData['localized']['validation']['cookiePath'] = Formatter::interpolate(
            $validationLocalization['isNotEmptyString'],
            ['fieldName' => $templateData['localized']['field']['cookiePath']],
        );
        $templateData['localized']['validation']['cookieLifetime'] = Formatter::interpolate(
            $validationLocalization['isNotEmptyString'],
            ['fieldName' => $templateData['localized']['field']['cookieLifetime']],
        );

        $templateData['cookieDomainIsDifferent'] = false;

        if ($networkDomain !== $this->generalConfig->get()->cookieDomain) {
            if (
                strpos($this->generalConfig->get()->cookieDomain, '.') !== 0
                || strpos(
                    $networkDomain,
                    ltrim(
                        $this->generalConfig->get()->cookieDomain,
                        '.',
                    ),
                ) === false
            ) {
                $templateData['cookieDomainIsDifferent'] = true;
            }
        }

        return $this->template->getEngine()->render(
            'settings/settings.html.twig',
            $templateData,
        );
    }
}
