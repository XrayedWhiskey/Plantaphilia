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

namespace Borlabs\Cookie\System\Script;

use Borlabs\Cookie\Adapter\WpFunction;
use Borlabs\Cookie\Dto\Config\DialogSettingsDto;
use Borlabs\Cookie\Localization\DefaultLocalizationStrings;
use Borlabs\Cookie\Model\ContentBlocker\ContentBlockerModel;
use Borlabs\Cookie\Model\Provider\ProviderModel;
use Borlabs\Cookie\Model\Service\ServiceModel;
use Borlabs\Cookie\Repository\ContentBlocker\ContentBlockerRepository;
use Borlabs\Cookie\Repository\Expression\BinaryOperatorExpression;
use Borlabs\Cookie\Repository\Expression\LiteralExpression;
use Borlabs\Cookie\Repository\Expression\ModelFieldNameExpression;
use Borlabs\Cookie\Repository\Provider\ProviderRepository;
use Borlabs\Cookie\Repository\RepositoryQueryBuilderWithRelations;
use Borlabs\Cookie\Repository\Service\ServiceRepository;
use Borlabs\Cookie\Repository\ServiceGroup\ServiceGroupRepository;
use Borlabs\Cookie\Support\Converter;
use Borlabs\Cookie\Support\Sanitizer;
use Borlabs\Cookie\System\Config\BackwardsCompatibilityConfig;
use Borlabs\Cookie\System\Config\DialogLocalization;
use Borlabs\Cookie\System\Config\DialogSettingsConfig;
use Borlabs\Cookie\System\Config\DialogStyleConfig;
use Borlabs\Cookie\System\Config\GeneralConfig;
use Borlabs\Cookie\System\Config\IabTcfConfig;
use Borlabs\Cookie\System\Config\WidgetConfig;
use Borlabs\Cookie\System\FileSystem\FileManager;
use Borlabs\Cookie\System\FileSystem\GlobalStorageFolder;
use Borlabs\Cookie\System\Language\Language;
use Borlabs\Cookie\System\Option\Option;
use Borlabs\Cookie\System\Script\Traits\RepeatableSettingsTrait;

final class ScriptConfigBuilder
{
    use RepeatableSettingsTrait;

    private BackwardsCompatibilityConfig $backwardsCompatibilityConfig;

    private ContentBlockerRepository $contentBlockerRepository;

    private DialogLocalization $dialogLocalization;

    private DialogSettingsConfig $dialogSettingsConfig;

    private DialogStyleConfig $dialogStyleConfig;

    private FileManager $fileManager;

    private GeneralConfig $generalConfig;

    private GlobalStorageFolder $globalStorageFolder;

    private IabTcfConfig $iabTcfConfig;

    private Language $language;

    private Option $option;

    private ProviderRepository $providerRepository;

    private ServiceGroupRepository $serviceGroupRepository;

    private ServiceRepository $serviceRepository;

    private WidgetConfig $widgetConfig;

    private WpFunction $wpFunction;

    public function __construct(
        BackwardsCompatibilityConfig $backwardsCompatibilityConfig,
        ContentBlockerRepository $contentBlockerRepository,
        DialogLocalization $dialogLocalization,
        DialogSettingsConfig $dialogSettingsConfig,
        DialogStyleConfig $dialogStyleConfig,
        FileManager $fileManager,
        GeneralConfig $generalConfig,
        GlobalStorageFolder $globalStorageFolder,
        IabTcfConfig $iabTcfConfig,
        Language $language,
        Option $option,
        ProviderRepository $providerRepository,
        ServiceGroupRepository $serviceGroupRepository,
        ServiceRepository $serviceRepository,
        WidgetConfig $widgetConfig,
        WpFunction $wpFunction
    ) {
        $this->backwardsCompatibilityConfig = $backwardsCompatibilityConfig;
        $this->contentBlockerRepository = $contentBlockerRepository;
        $this->dialogLocalization = $dialogLocalization;
        $this->dialogSettingsConfig = $dialogSettingsConfig;
        $this->dialogStyleConfig = $dialogStyleConfig;
        $this->fileManager = $fileManager;
        $this->generalConfig = $generalConfig;
        $this->globalStorageFolder = $globalStorageFolder;
        $this->iabTcfConfig = $iabTcfConfig;
        $this->language = $language;
        $this->option = $option;
        $this->providerRepository = $providerRepository;
        $this->serviceGroupRepository = $serviceGroupRepository;
        $this->serviceRepository = $serviceRepository;
        $this->widgetConfig = $widgetConfig;
        $this->wpFunction = $wpFunction;
    }

    public function buildJavaScriptConfigFile(string $languageCode): bool
    {
        $defaultLocalizationStrings = DefaultLocalizationStrings::get();
        $dialogSettings = $this->dialogSettingsConfig->load($languageCode);
        $dialogStyleConfig = $this->dialogStyleConfig->load($languageCode);
        $generalConfig = $this->generalConfig->load($languageCode);
        $iabTcfConfig = $this->iabTcfConfig->load($languageCode);
        $widgetConfig = $this->widgetConfig->load($languageCode);

        // Privacy Policy Link
        $dialogPrivacyLink = '';

        if (!empty($dialogSettings->privacyPageUrl)) {
            $dialogPrivacyLink = $dialogSettings->privacyPageUrl;
        }

        if (!empty($dialogSettings->privacyPageCustomUrl)) {
            $dialogPrivacyLink = $dialogSettings->privacyPageCustomUrl;
        }

        // Imprint Link
        $dialogImprintLink = '';

        if (!empty($dialogSettings->imprintPageUrl)) {
            $dialogImprintLink = $dialogSettings->imprintPageUrl;
        }

        if (!empty($dialogSettings->imprintPageCustomUrl)) {
            $dialogImprintLink = $dialogSettings->imprintPageCustomUrl;
        }

        $brightBackground = false;
        $bgColorHSL = Converter::hexToHsl($dialogStyleConfig->dialogBackgroundColor);

        if (isset($bgColorHSL[2]) && $bgColorHSL[2] <= 50) {
            $brightBackground = true;
        }

        // Support Borlabs Cookie
        $supportBorlabsCookie = $dialogSettings->showBorlabsCookieBranding;
        $supportBorlabsCookieLogo = '';

        if ($supportBorlabsCookie) {
            if ($brightBackground) {
                $supportBorlabsCookieLogo = $generalConfig->pluginUrl . '/assets/images/borlabs-cookie-icon-white.svg';
            } else {
                $supportBorlabsCookieLogo = $generalConfig->pluginUrl . '/assets/images/borlabs-cookie-icon-black.svg';
            }
        }

        // Logo
        $dialogLogo = $dialogSettings->logo;
        $dialogLogoHd = $dialogSettings->logoHd;
        $dialogLogoSrcSet = [];
        $dialogLogoSrcSet[] = $dialogLogo;

        if (!empty($dialogLogoHd)) {
            $dialogLogoSrcSet[] = $dialogLogoHd . ' 2x';
        }

        $iabTcfHostnamesForConsentAddition = $iabTcfConfig->hostnamesForConsentAddition;
        $iabTcfHostnamesForConsentAddition = $this->wpFunction->applyFilter(
            'borlabsCookie/scriptBuilder/iabTcf/modifyHostnamesForConsentAddition',
            $iabTcfHostnamesForConsentAddition,
        );

        $settings = [
            'automaticCookieDomainAndPath' => $generalConfig->automaticCookieDomainAndPath,
            'backwardsCompatibility' => $this->backwardsCompatibilityConfig->get()->loadBackwardsCompatibilityJavaScript,
            'cookieCrossCookieDomains' => $generalConfig->crossCookieDomains,
            'cookieDomain' => $generalConfig->cookieDomain,
            'cookieLifetime' => $generalConfig->cookieLifetime,
            'cookieLifetimeEssentialOnly' => $generalConfig->cookieLifetimeEssentialOnly,
            'cookiePath' => $generalConfig->cookiePath,
            'cookieSameSite' => (string) $generalConfig->cookieSameSite,
            'cookieSecure' => $generalConfig->cookieSecure,
            'cookieVersion' => (int) $this->option->getGlobal('CookieVersion', 1)->value,
            'cookiesForBots' => $generalConfig->cookiesForBots,

            'dialogAnimation' => $dialogSettings->animation,
            'dialogAnimationDelay' => $dialogSettings->animationDelay,
            'dialogAnimationIn' => $dialogSettings->animationIn,
            'dialogAnimationOut' => $dialogSettings->animationOut,
            'dialogButtonDetailsOrder' => $this->handleButtonOrder(
                $dialogSettings,
                $dialogSettings->buttonDetailsOrder,
                false,
                $iabTcfConfig->iabTcfStatus,
            ),
            'dialogButtonEntranceOrder' => $this->handleButtonOrder(
                $dialogSettings,
                $dialogSettings->buttonEntranceOrder,
                true,
            ),
            'dialogButtonSwitchRound' => $dialogSettings->buttonSwitchRound,
            'dialogEnableBackdrop' => $dialogSettings->enableBackdrop,
            'dialogGeoIpActive' => $dialogSettings->geoIpActive,
            'dialogGeoIpCachingMode' => $dialogSettings->geoIpCachingMode,
            'dialogHideDialogOnPages' => Sanitizer::hostArray(
                array_merge(
                    $dialogSettings->hideDialogOnPages,
                    [
                        $dialogSettings->privacyPageUrl,
                        $dialogSettings->imprintPageUrl,
                    ],
                ),
                true,
            ),
            'dialogImprintLink' => $dialogImprintLink,
            'dialogLanguageOptions' => $dialogSettings->languageOptions->list ?? [],
            'dialogLayout' => $dialogSettings->layout,
            'dialogLegalInformationDescriptionConfirmAgeStatus' => $dialogSettings->legalInformationDescriptionConfirmAgeStatus,
            'dialogLegalInformationDescriptionIndividualSettingsStatus' => $dialogSettings->legalInformationDescriptionIndividualSettingsStatus,
            'dialogLegalInformationDescriptionMoreInformationStatus' => $dialogSettings->legalInformationDescriptionMoreInformationStatus,
            'dialogLegalInformationDescriptionNonEuDataTransferStatus' => $dialogSettings->legalInformationDescriptionNonEuDataTransferStatus,
            'dialogLegalInformationDescriptionNoObligationStatus' => $dialogSettings->legalInformationDescriptionNoObligationStatus,
            'dialogLegalInformationDescriptionPersonalDataStatus' => $dialogSettings->legalInformationDescriptionPersonalDataStatus,
            'dialogLegalInformationDescriptionRevokeStatus' => $dialogSettings->legalInformationDescriptionRevokeStatus,
            'dialogLegalInformationDescriptionTechnologyStatus' => $dialogSettings->legalInformationDescriptionTechnologyStatus,
            'dialogLogoSrcSet' => $dialogLogoSrcSet,
            'dialogPosition' => $dialogSettings->position,
            'dialogPrivacyLink' => $dialogPrivacyLink,
            'dialogServiceGroupJustification' => $dialogSettings->serviceGroupJustification,
            'dialogShowAcceptAllButton' => $dialogSettings->showAcceptAllButton,
            'dialogShowAcceptOnlyEssentialButton' => $dialogSettings->showAcceptOnlyEssentialButton,
            'dialogShowCloseButton' => $dialogSettings->showCloseButton,
            'dialogShowDialog' => $dialogSettings->showDialog,
            'dialogShowDialogAfterUserInteraction' => $dialogSettings->showDialogAfterUserInteraction,
            'dialogShowSaveButton' => $dialogSettings->showSaveButton,
            'showHeadlineSeparator' => $dialogSettings->showHeadlineSeparator,
            'dialogShowLogo' => $dialogSettings->showLogo,
            'dialogSupportBorlabsCookieLogo' => $supportBorlabsCookieLogo,
            'dialogSupportBorlabsCookieStatus' => $dialogSettings->showBorlabsCookieBranding,
            'dialogSupportBorlabsCookieText' => $defaultLocalizationStrings['dialog']['supportBorlabsCookieText'],
            'dialogSupportBorlabsCookieUrl' => $defaultLocalizationStrings['dialog']['supportBorlabsCookieUrl'],
            'dialogUid' => $defaultLocalizationStrings['dialog']['uid'],
            'dialogUseBaseFontSizeForDescriptions' => $dialogSettings->useBaseFontSizeForDescriptions,

            'globalStorageUrl' => $this->globalStorageFolder->getUrl(),
            'iabTcfCompactLayout' => $iabTcfConfig->compactLayout,
            'iabTcfHostnamesForConsentAddition' => Sanitizer::hostArray($iabTcfHostnamesForConsentAddition),
            'iabTcfStatus' => $iabTcfConfig->iabTcfStatus,
            'language' => $languageCode,
            'pluginUrl' => $generalConfig->pluginUrl,
            'pluginVersion' => BORLABS_COOKIE_VERSION,
            'production' => defined('BORLABS_COOKIE_DEV_MODE_ENABLE_JAVASCRIPT_LOGS') && constant('BORLABS_COOKIE_DEV_MODE_ENABLE_JAVASCRIPT_LOGS') === true ? false : true,
            'reloadAfterOptIn' => $generalConfig->reloadAfterOptIn,
            'reloadAfterOptOut' => $generalConfig->reloadAfterOptOut,
            'respectDoNotTrack' => $generalConfig->respectDoNotTrack,

            'widgetIcon' => $generalConfig->pluginUrl . '/assets/images/' . $widgetConfig->icon,
            'widgetPosition' => $widgetConfig->position,
            'widgetShow' => $iabTcfConfig->iabTcfStatus ? true : $widgetConfig->show,

            'wpRestURL' => $this->wpFunction->escUrlRaw($this->wpFunction->restUrl()),
        ];

        // Update `globalStorageUrl` when a multi-language plugin is active with domain mapping to prevent CORS issues.
        if ($this->language->isMultilanguagePluginActive()) {
            $languageUrlList = $this->language->getLanguageUrlList();
            $localizedSiteUrl = $languageUrlList->getByKey($languageCode);

            if ($localizedSiteUrl !== null) {
                $globalStorageUrlInfo = parse_url($settings['globalStorageUrl']);
                $localizedSiteUrlInfo = parse_url($localizedSiteUrl->value);
                $wpRestUrlInfo = parse_url($settings['wpRestURL']);
                $settings['globalStorageUrl'] =
                    $localizedSiteUrlInfo['scheme'] . '://'
                    . $localizedSiteUrlInfo['host']
                    . (isset($localizedSiteUrlInfo['port']) ? ':' . $localizedSiteUrlInfo['port'] : '')
                    . $globalStorageUrlInfo['path'];
                $settings['wpRestURL'] = $localizedSiteUrlInfo['scheme'] . '://'
                    . $localizedSiteUrlInfo['host']
                    . (isset($localizedSiteUrlInfo['port']) ? ':' . $localizedSiteUrlInfo['port'] : '')
                    . $wpRestUrlInfo['path'];
            }
        }

        $globalStrings = $this->language->runInLanguageContext($languageCode, function () use ($languageCode) {
            return (array) $this->dialogLocalization->load($languageCode);
        });
        $globalStrings['entranceHeadline'] = esc_attr($globalStrings['entranceHeadline']);
        $globalStrings['entranceDescription'] = do_shortcode($globalStrings['entranceDescription']);
        $globalStrings['detailsHeadline'] = esc_attr($globalStrings['detailsHeadline']);
        $globalStrings['detailsDescription'] = do_shortcode($globalStrings['detailsDescription']);

        $borlabsCookieConfig = [
            'contentBlockers' => $this->getContentBlockers($languageCode),
            'globalStrings' => $globalStrings,
            'providers' => $this->getProviders($languageCode),
            'serviceGroups' => $this->getServiceGroups($languageCode),
            'services' => $this->getServices($languageCode),
            'settings' => $settings,
            'tcfVendors' => array_map(fn ($data) => (int) $data->key, $iabTcfConfig->vendors->list ?? []),
        ];

        $assetTimestamp = '';

        if (defined('BORLABS_COOKIE_DEV_MODE_ENABLE_ASSET_TIMESTAMPS') && constant('BORLABS_COOKIE_DEV_MODE_ENABLE_ASSET_TIMESTAMPS') === true) {
            $assetTimestamp = '/* ' . date('Y-m-d H:i:s') . ' */';
        }

        $borlabsCookieConfig = $this->wpFunction->applyFilter(
            'borlabsCookie/scriptBuilder/modifyConfigBeforeSaving',
            $borlabsCookieConfig,
            ['languageCode' => $languageCode],
        );

        return $this->fileManager->cacheFile(
            $this->getConfigFileName($languageCode),
            $assetTimestamp .
            'var borlabsCookieConfig = (function () { return JSON.parse("' . addslashes(
                json_encode($borlabsCookieConfig),
            ) . '"); })();',
        ) !== null;
    }

    public function getConfigFileName(string $languageCode): string
    {
        return 'borlabs-cookie-config-' . $languageCode . '.json.js';
    }

    public function updateJavaScriptConfigFileAndIncrementConfigVersion(string $languageCode): bool
    {
        // Build JavaScript config file
        if (!$this->buildJavaScriptConfigFile($languageCode)) {
            return false;
        }

        // Update the JavaScript config version to bypass cached config.
        $configVersionOption = $this->option->get(
            'ConfigVersion',
            1,
            $languageCode,
        );
        $this->option->set(
            'ConfigVersion',
            (int) $configVersionOption->value + 1,
            false,
            $languageCode,
        );

        return true;
    }

    private function getContentBlockerData(ContentBlockerModel $contentBlocker): array
    {
        return [
            'description' => esc_html($contentBlocker->description),
            'hosts' => $this->getContentBlockerLocations($contentBlocker),
            'id' => esc_html($contentBlocker->key),
            'javaScriptGlobal' => esc_html($contentBlocker->javaScriptGlobal),
            'javaScriptInitialization' => esc_html($contentBlocker->javaScriptInitialization),
            'name' => esc_html($contentBlocker->name),
            'providerId' => esc_html($contentBlocker->provider->key),
            'repeatableSettings' => $contentBlocker->repeatableSettingsFields ? $this->transformRepeatableSettingsToArray($contentBlocker->repeatableSettingsFields) : [],
            'serviceId' => isset($contentBlocker->service->key) ? esc_html($contentBlocker->service->key) : null,
            'settings' => array_column($contentBlocker->settingsFields->list, 'value', 'key'),
        ];
    }

    private function getContentBlockerLocations(ContentBlockerModel $contentBlocker): array
    {
        $contentBlockerLocations = [];

        foreach ($contentBlocker->contentBlockerLocations as $contentBlockerLocation) {
            $contentBlockerLocations[] = [
                'hostname' => esc_html($contentBlockerLocation->hostname),
            ];
        }

        return $contentBlockerLocations;
    }

    private function getContentBlockers(string $languageCode): array
    {
        $contentBlockers = [];
        $contentBlockerModels = $this->contentBlockerRepository->find(
            [
                'language' => $languageCode,
                'status' => 1,
            ],
            ['name' => 'ASC',],
            [],
            ['contentBlockerLocations', 'provider', 'service'],
        );

        if (empty($contentBlockerModels)) {
            return $contentBlockers;
        }

        foreach ($contentBlockerModels as $contentBlocker) {
            $contentBlockers[$contentBlocker->key] = $this->getContentBlockerData($contentBlocker);
        }

        return $contentBlockers;
    }

    private function getProviderData(ProviderModel $provider): array
    {
        return [
            'address' => esc_html($provider->address),
            'contentBlockerIds' => array_map(function (ContentBlockerModel $contentBlocker) {
                return $contentBlocker->status ? $contentBlocker->key : null;
            }, $provider->contentBlockers),
            'cookieUrl' => esc_url($provider->cookieUrl),
            'description' => esc_html($provider->description),
            'iabVendorId' => $provider->iabVendorId,
            'id' => esc_html($provider->key),
            'name' => esc_html($provider->name),
            'optOutUrl' => esc_html($provider->optOutUrl),
            'partners' => esc_html(implode(', ', $provider->partners ?? [])),
            'privacyUrl' => esc_url($provider->privacyUrl),
            'serviceIds' => array_map(function (ServiceModel $service) {
                return $service->status ? $service->key : null;
            }, $provider->services),
        ];
    }

    private function getProviders(string $languageCode): array
    {
        $providers = [];
        $providerModels = $this->providerRepository->find(
            ['language' => $languageCode,],
            ['name' => 'ASC',],
            [],
            [
                'contentBlockers' => function (RepositoryQueryBuilderWithRelations $queryBuilder) {
                    $queryBuilder->andWhere(new BinaryOperatorExpression(
                        new ModelFieldNameExpression('status'),
                        '=',
                        new LiteralExpression(1),
                    ));
                },
                'services' => function (RepositoryQueryBuilderWithRelations $queryBuilder) {
                    $queryBuilder->addWith('serviceGroup', function (RepositoryQueryBuilderWithRelations $queryBuilder) {
                        $queryBuilder->andWhere(new BinaryOperatorExpression(
                            new ModelFieldNameExpression('status'),
                            '=',
                            new LiteralExpression(1),
                        ));
                    });
                    $queryBuilder->andWhere(new BinaryOperatorExpression(
                        new ModelFieldNameExpression('status'),
                        '=',
                        new LiteralExpression(1),
                    ));
                },
            ],
        );

        foreach ($providerModels as $provider) {
            $servicesWithActiveServiceGroup = array_filter(
                $provider->services,
                function (ServiceModel $service) {
                    return $service->serviceGroup ?? null;
                },
            );

            if (
                count($provider->contentBlockers) === 0
                && (
                    count($provider->services) === 0
                    || count($servicesWithActiveServiceGroup) === 0
                )
                && $provider->iabVendorId === null
            ) {
                continue;
            }

            $providers[$provider->key] = $this->getProviderData($provider);
        }

        return $providers;
    }

    private function getServiceCookies(ServiceModel $service): array
    {
        $serviceCookies = [];

        foreach ($service->serviceCookies as $serviceCookie) {
            $serviceCookies[] = [
                'description' => esc_html($serviceCookie->description),
                'hostname' => esc_html($serviceCookie->hostname),
                'lifetime' => esc_html($serviceCookie->lifetime),
                'name' => esc_html($serviceCookie->name),
                'purpose' => esc_html($serviceCookie->purpose),
                'type' => esc_html($serviceCookie->type),
            ];
        }

        return $serviceCookies;
    }

    private function getServiceData(ServiceModel $service): array
    {
        $repeatableSettings = $service->repeatableSettingsFields ? $this->transformRepeatableSettingsToArray($service->repeatableSettingsFields) : [];
        $searchAndReplace = [
            'search' => array_map(
                static fn ($value) => '{{ ' . $value . ' }}',
                array_column($service->settingsFields->list ?? [], 'key'),
            ),
            'replace' => array_column($service->settingsFields->list ?? [], 'value'),
        ];
        $searchAndReplace['search'][] = '{{ key }}';
        $searchAndReplace['replace'][] = $service->key;

        foreach ($repeatableSettings as $key => $settings) {
            $searchAndReplace['search'][] = '{{ ' . $key . ' }}';
            $searchAndReplace['replace'][] = json_encode($settings);
        }

        $searchAndReplace = $this->wpFunction->applyFilter(
            'borlabsCookie/scriptBuilder/service/modifyPlaceholders/' . $service->key,
            $searchAndReplace,
        );
        $optInCode = $this->wpFunction->applyFilter(
            'borlabsCookie/scriptBuilder/service/modifyOptInCode/',
            $service->optInCode,
        );
        $optInCode = $this->wpFunction->applyFilter(
            'borlabsCookie/scriptBuilder/service/modifyOptInCode/' . $service->key,
            $optInCode,
        );
        $optOutCode = $this->wpFunction->applyFilter(
            'borlabsCookie/scriptBuilder/service/modifyOptOutCode/',
            $service->optOutCode,
        );
        $optOutCode = $this->wpFunction->applyFilter(
            'borlabsCookie/scriptBuilder/service/modifyOptOutCode/' . $service->key,
            $optOutCode,
        );
        $settings = array_column($service->settingsFields->list, 'value', 'key');

        if (isset($settings['disable-code-execution']) && $settings['disable-code-execution'] === '1') {
            $optInCode = '';
            $optOutCode = '';
        }

        return [
            'cookies' => $this->getServiceCookies($service),
            'description' => esc_html($service->description),
            'hosts' => $this->getServiceLocations($service),
            'id' => esc_html($service->key),
            'name' => esc_html($service->name),
            'optInCode' => $optInCode !== '' ? base64_encode(do_shortcode(str_replace($searchAndReplace['search'], $searchAndReplace['replace'], $optInCode))) : '',
            'optOutCode' => $optOutCode !== '' ? base64_encode(do_shortcode(str_replace($searchAndReplace['search'], $searchAndReplace['replace'], $optOutCode))) : '',
            'options' => $this->getServiceOptions($service),
            'providerId' => esc_html($service->provider->key),
            'repeatableSettings' => $repeatableSettings,
            'serviceGroupId' => esc_html($service->serviceGroup->key),
            'settings' => $settings,
        ];
    }

    private function getServiceGroups(string $languageCode): array
    {
        $serviceGroups = [];
        $serviceGroupModels = $this->serviceGroupRepository->find(
            [
                'language' => $languageCode,
                'status' => 1,
            ],
            ['position' => 'ASC',],
            [],
            ['services'],
        );

        if (empty($serviceGroupModels)) {
            return $serviceGroups;
        }

        foreach ($serviceGroupModels as $serviceGroupData) {
            if (count($serviceGroupData->services) === 0) {
                continue;
            }

            // Only add service group when there are services with status true
            $serviceIds = array_map(
                fn ($service) => $service->key,
                array_filter(
                    $serviceGroupData->services,
                    fn ($service) => $service->status === true,
                ),
            );

            if (count($serviceIds) === 0) {
                continue;
            }

            $serviceGroups[$serviceGroupData->key] = [
                'description' => nl2br(esc_html($serviceGroupData->description)),
                'id' => $serviceGroupData->key,
                'name' => esc_html($serviceGroupData->name),
                'preSelected' => !empty($serviceGroupData->preSelected),
                // Make sure that the index starts with 0, otherwise we will get an object instead of an array when converting back from JSON
                'serviceIds' => array_values($serviceIds),
            ];
        }

        return $serviceGroups;
    }

    private function getServiceLocations(ServiceModel $service): array
    {
        $serviceLocations = [];

        foreach ($service->serviceLocations as $serviceLocation) {
            $serviceLocations[] = [
                'hostname' => esc_html($serviceLocation->hostname),
            ];
        }

        return $serviceLocations;
    }

    private function getServiceOptions(ServiceModel $service): array
    {
        $serviceOptions = [];

        foreach ($service->serviceOptions as $serviceOption) {
            $serviceOptions[] = [
                'name' => esc_html($serviceOption->description),
                'type' => esc_html($serviceOption->type),
            ];
        }

        return $serviceOptions;
    }

    private function getServices(string $languageCode): array
    {
        $services = [];
        $serviceModels = $this->serviceRepository->find(
            [
                'language' => $languageCode,
                'status' => 1,
            ],
            [
                'position' => 'ASC',
                'name' => 'ASC',
            ],
            [],
            [
                'provider',
                'serviceCookies',
                'serviceGroup' => function (RepositoryQueryBuilderWithRelations $queryBuilder) {
                    $queryBuilder->andWhere(new BinaryOperatorExpression(
                        new ModelFieldNameExpression('status'),
                        '=',
                        new LiteralExpression(1),
                    ));
                },
                'serviceLocations',
                'serviceOptions',
            ],
        );

        if (empty($serviceModels)) {
            return $services;
        }

        foreach ($serviceModels as $serviceData) {
            if (!isset($serviceData->serviceGroup)) {
                continue;
            }

            $services[$serviceData->key] = $this->getServiceData($serviceData);
        }

        return $services;
    }

    private function handleButtonOrder(
        DialogSettingsDto $dialogSettings,
        array $buttonOrder,
        bool $isButtonEntranceOrder = false,
        bool $iabTcfEnabled = false
    ): array {
        $newButtonOrder = [];
        $index = 0;

        foreach ($buttonOrder as $button) {
            if (($button === 'all' && $dialogSettings->showAcceptAllButton === true)
                || ($button === 'essential' && ($dialogSettings->showAcceptOnlyEssentialButton === true || ($isButtonEntranceOrder === false && $iabTcfEnabled)))
                || ($button === 'save' && ($dialogSettings->showSaveButton === true || $isButtonEntranceOrder === false))
                || $button !== 'essential') {
                $newButtonOrder[$index] = $button;
                ++$index;
            }
        }

        return $newButtonOrder;
    }
}
