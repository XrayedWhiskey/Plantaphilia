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

namespace Borlabs\Cookie\System\ThirdPartyImporter\BorlabsCookieLegacy;

use Borlabs\Cookie\Adapter\WpDb;
use Borlabs\Cookie\Dto\Package\InstallationStatusDto;
use Borlabs\Cookie\Dto\ThirdPartyImporter\PreImportMetadataDto;
use Borlabs\Cookie\DtoList\Package\InstallationStatusDtoList;
use Borlabs\Cookie\DtoList\System\SettingsFieldDtoList;
use Borlabs\Cookie\DtoList\ThirdPartyImporter\PreImportMetadataDtoList;
use Borlabs\Cookie\Enum\Package\ComponentTypeEnum;
use Borlabs\Cookie\Enum\Package\InstallationStatusEnum;
use Borlabs\Cookie\Enum\Service\CookiePurposeEnum;
use Borlabs\Cookie\Enum\Service\CookieTypeEnum;
use Borlabs\Cookie\Enum\System\SettingsFieldDataTypeEnum;
use Borlabs\Cookie\Exception\GenericException;
use Borlabs\Cookie\Exception\TranslatedException;
use Borlabs\Cookie\Model\Service\ServiceCookieModel;
use Borlabs\Cookie\Model\Service\ServiceLocationModel;
use Borlabs\Cookie\Model\Service\ServiceModel;
use Borlabs\Cookie\Repository\Package\PackageRepository;
use Borlabs\Cookie\Repository\Service\ServiceCookieRepository;
use Borlabs\Cookie\Repository\Service\ServiceLocationRepository;
use Borlabs\Cookie\Repository\Service\ServiceRepository;
use Borlabs\Cookie\Repository\ServiceGroup\ServiceGroupRepository;
use Borlabs\Cookie\Support\Database;
use Borlabs\Cookie\System\Log\Log;
use Borlabs\Cookie\System\Package\PackageManager;
use Borlabs\Cookie\System\Package\Traits\InstallationStatusAnalyzerTrait;
use Borlabs\Cookie\System\Service\DefaultSettingsField\AsynchronousOptOutCode;
use Borlabs\Cookie\System\Service\DefaultSettingsField\BlockCookiesBeforeConsent;
use Borlabs\Cookie\System\Service\DefaultSettingsField\Prioritize;
use Borlabs\Cookie\System\Service\ServiceDefaultSettingsFieldManager;
use Borlabs\Cookie\System\ThirdPartyImporter\BorlabsCookieLegacy\Traits\ComponentSettingsTrait;

final class ServiceImporter
{
    use ComponentSettingsTrait;
    use InstallationStatusAnalyzerTrait;

    public const TABLE_NAME = 'borlabs_cookie_legacy_cookies';

    private array $ignoredServices = [
        'borlabs-cookie',
        'ezoic',
        'ezoic-marketing',
        'ezoic-preferences',
        'ezoic-statistics',
        'facebook', // Service of Content Blocker
        'googlemaps', // Service of Content Blocker
        'instagram', // Service of Content Blocker
        'openstreetmap', // Service of Content Blocker
        'twitter', // Service of Content Blocker
        'vimeo', // Service of Content Blocker
        'youtube', // Service of Content Blocker
    ];

    private Log $log;

    /**
     * @var string[] BorlabsCookieLegacyCookieId => BorlabsServicePackageKey
     */
    private array $map = [
        'bing-ads' => 'microsoft-advertising',
        'facebook-pixel' => 'meta-pixel-package',
        'google-ads' => 'google-ads',
        'google-adsense' => 'google-adsense',
        'google-analytics' => 'google-analytics',
        'google-tag-manager' => 'google-tag-manager',
        'google-tag-manager-consent' => 'google-tag-manager',
        'hotjar' => 'hotjar',
        'hubspot' => 'hubspot',
        'matomo' => 'matomo',
        'matomo-tag-manager' => 'matomo-tag-manager',
        'polylang' => 'polylang',
        'tidio' => 'tidio',
        'tikto-pixel' => 'tiktok-pixel',
        'userlike' => 'userlike',
        'woocommerce' => 'woocommerce',
        'wpml' => 'wpml',
    ];

    private PackageManager $packageManager;

    private PackageRepository $packageRepository;

    private ProviderImporter $providerImporter;

    private ServiceCookieRepository $serviceCookieRepository;

    private ServiceDefaultSettingsFieldManager $serviceDefaultSettingsFieldManager;

    private ServiceGroupImporter $serviceGroupImporter;

    private ServiceGroupRepository $serviceGroupRepository;

    private ServiceLocationRepository $serviceLocationRepository;

    private ServiceRepository $serviceRepository;

    /**
     * Example
     * <code>
     * [
     *     BorlabsServicePackageKey => [
     *         BorlabsServiceServiceKey => [
     *             BorlabsCookieLegacySettingsKey => BorlabsServiceSettingsKey
     *         ]
     *     ]
     * ]
     * </code>.
     *
     * @var array[]
     */
    private array $serviceSettingsMap = [
        'google-ads' => [
            'google-ads' => [
                'consentMode' => 'google-ads-consent-mode',
                'conversionId' => 'google-ads-conversion-id',
            ],
        ],
        'google-adsense' => [
            'google-ad-sense' => [
                'caPubId' => 'google-adsense-publisher-id',
            ],
        ],
        'google-analytics' => [
            'google-analytics' => [
                'consentMode' => 'google-analytics-consent-mode',
                'trackingId' => 'google-analytics-tracking-id',
            ],
        ],
        'google-tag-manager' => [
            'google-tag-manager' => [
                'consentMode' => 'google-tag-manager-cm-active',
                'gtmId' => 'google-tag-manager-id',
            ],
        ],
        'hotjar' => [
            'hotjar' => [
                'siteId' => 'hotjar-site-id',
            ],
        ],
        'hubspot' => [
            'hubspot' => [
                'hubId' => 'hubspot-hub-id',
            ],
        ],
        'matomo' => [
            'matomo' => [
                'matomoSiteId' => 'matomo-site-id',
                'matomoUrl' => 'matomo-url',
            ],
        ],
        'matomo-tag-manager' => [
            'matomo-tag-manager' => [
                'containerId' => 'matomo-container-id',
                'matomoUrl' => 'matomo-url',
            ],
        ],
        'meta-pixel-package' => [
            'meta-pixel' => [
                'pixelId' => 'pixel-id',
            ],
        ],
        'microsoft-advertising' => [
            'microsoft-advertising' => [
                'conversionId' => 'microsoft-ads-conversion-id',
            ],
        ],
        'tiktok-pixel' => [
            'tiktok-pixel' => [
                'pixelId' => 'tiktok-pixel-id',
            ],
        ],
        'userlike' => [
            'userlike' => [
                'secret' => 'userlike-secret',
            ],
        ],
    ];

    private WpDb $wpdb;

    public function __construct(
        Log $log,
        PackageManager $packageManager,
        PackageRepository $packageRepository,
        ProviderImporter $providerImporter,
        ServiceCookieRepository $serviceCookieRepository,
        ServiceDefaultSettingsFieldManager $serviceDefaultSettingsFieldManager,
        ServiceGroupImporter $serviceGroupImporter,
        ServiceGroupRepository $serviceGroupRepository,
        ServiceLocationRepository $serviceLocationRepository,
        ServiceRepository $serviceRepository,
        WpDb $wpdb
    ) {
        $this->log = $log;
        $this->packageManager = $packageManager;
        $this->packageRepository = $packageRepository;
        $this->providerImporter = $providerImporter;
        $this->serviceCookieRepository = $serviceCookieRepository;
        $this->serviceDefaultSettingsFieldManager = $serviceDefaultSettingsFieldManager;
        $this->serviceGroupImporter = $serviceGroupImporter;
        $this->serviceGroupRepository = $serviceGroupRepository;
        $this->serviceLocationRepository = $serviceLocationRepository;
        $this->serviceRepository = $serviceRepository;
        $this->wpdb = $wpdb;
    }

    public function getPreImportMetadataList(): PreImportMetadataDtoList
    {
        $preImportMetadataList = new PreImportMetadataDtoList(null);
        $services = $this->getServices();

        if ($services === null) {
            return $preImportMetadataList;
        }

        foreach ($services as $service) {
            $preImportMetadataList->add(
                new PreImportMetadataDto(
                    \Borlabs\Cookie\Enum\ThirdPartyImporter\ComponentTypeEnum::SERVICE(),
                    $service->name,
                    $service->cookie_id,
                    (
                        isset($this->map[$service->cookie_id]) && isset($this->serviceSettingsMap[$this->map[$service->cookie_id]])
                        ? key($this->serviceSettingsMap[$this->map[$service->cookie_id]])
                        : $service->cookie_id
                    ),
                    $service->language,
                ),
            );
        }

        return $preImportMetadataList;
    }

    public function importCustom(): InstallationStatusDtoList
    {
        $installationStatusList = new InstallationStatusDtoList(null);
        $legacyServices = $this->getServices();

        if ($legacyServices === null) {
            return $installationStatusList;
        }

        /** @var object{
         *     cookie_id: string,
         *     language: string,
         *     service: string,
         *     name: string,
         *     provider: string,
         *     purpose: string,
         *     privacy_policy_url: string,
         *     hosts: string[],
         *     cookie_name: string,
         *     cookie_expiry: string,
         *     opt_in_js: string,
         *     opt_out_js: string,
         *     fallback_js: string,
         *     settings: array{
         *       asyncOptOutCode: string,
         *       blockCookiesBeforeConsent: string,
         *       prioritize: string,
         *     },
         *     position: int,
         *     status: bool,
         *     group_id: string
         * } $legacyService
         */
        foreach ($legacyServices as $legacyService) {
            if (($this->map[$legacyService->cookie_id] ?? false) || in_array($legacyService->cookie_id, $this->ignoredServices, true)) {
                continue;
            }

            $this->log->info(
                '[Import] Custom Service "{{ serviceName }}" ({{ languageCode }})',
                [
                    'languageCode' => $legacyService->language,
                    'legacyService' => $legacyService,
                    'serviceName' => $legacyService->name,
                ],
            );

            $service = $this->getOrAddServiceFromLegacyData($legacyService);
            $installationStatusList->add(
                new InstallationStatusDto(
                    $service ? InstallationStatusEnum::SUCCESS() : InstallationStatusEnum::FAILURE(),
                    ComponentTypeEnum::SERVICE(),
                    $legacyService->cookie_id,
                    $legacyService->name,
                    $service->id ?? -1,
                ),
            );

            if (is_null($service)) {
                continue;
            }

            $this->addServiceCookiesFromLegacyData($service, $legacyService);

            if (count($legacyService->hosts) > 0) {
                $this->addServiceLocationsFromLegacyData($service, $legacyService->hosts);
            }
        }

        return $installationStatusList;
    }

    public function importPreset(): InstallationStatusDtoList
    {
        $installationStatusList = new InstallationStatusDtoList(null);

        foreach ($this->map as $cookieId => $borlabsServicePackageKey) {
            $services = $this->getServicesByKey($cookieId);

            if (!isset($services)) {
                $this->log->info(
                    '[Import] Service package "{{ borlabsServicePackageKey }}" was skipped because it is disabled or unavailable',
                    [
                        'borlabsServicePackageKey' => $borlabsServicePackageKey,
                        'cookieId' => $cookieId,
                    ],
                );

                continue;
            }

            $package = $this->packageRepository->getByPackageKey($borlabsServicePackageKey);

            if ($package === null) {
                $this->log->error(
                    '[Import] Service package "{{ borlabsServicePackageKey }}" not found',
                    [
                        'borlabsServicePackageKey' => $borlabsServicePackageKey,
                        'cookieId' => $cookieId,
                        'services' => $services,
                    ],
                );
                $installationStatusList->add(
                    new InstallationStatusDto(
                        InstallationStatusEnum::FAILURE(),
                        ComponentTypeEnum::SERVICE(),
                        $borlabsServicePackageKey,
                        $borlabsServicePackageKey . ' (' . $cookieId . ')',
                    ),
                );

                continue;
            }

            $componentSettings = $this->setDefaultComponentSettings($package, array_column($services, 'language'));

            // Loop through Services of the package
            $type = 'service';

            foreach ($package->components->services->list ?? [] as $component) {
                // Find mapping info for the Service or skip.
                $serviceMappingInfo = $this->serviceSettingsMap[$borlabsServicePackageKey][$component->key] ?? [];

                if (!isset($component->languageSpecificSetupSettingsFieldsList->list)) {
                    continue;
                }

                // Loop through the language specific settings fields list of the Service
                foreach ($component->languageSpecificSetupSettingsFieldsList->list as $settingsFieldsList) {
                    $languageCode = $settingsFieldsList->language;

                    if (!isset($componentSettings['settingsForLanguage'][$languageCode][$type][$component->key])) {
                        continue;
                    }

                    $legacyServiceData = $this->getLegacyServiceDataOfLanguage($services, $languageCode);

                    if ($legacyServiceData === null) {
                        continue;
                    }

                    // If the Package includes multiple Services, the Service Group for all Services is set to the same value as defined by $legacyServiceData.
                    $componentSettings['settingsForLanguage'][$languageCode][$type][$component->key] = $this->setServiceGroupFromLegacyData(
                        $componentSettings['settingsForLanguage'][$languageCode][$type][$component->key],
                        $legacyServiceData,
                        $settingsFieldsList->settingsFields,
                    );

                    if (count($serviceMappingInfo) === 0) {
                        continue;
                    }

                    $componentSettings['settingsForLanguage'][$languageCode][$type][$component->key] = $this->mergeComponentSettingsWithLegacyData(
                        $componentSettings['settingsForLanguage'][$languageCode][$type][$component->key],
                        $legacyServiceData,
                        $serviceMappingInfo,
                    );
                }
            }

            $installationStatus = false;

            try {
                $packageInstallationStatusList = $this->packageManager->install($package, $componentSettings);
                $installationStatus = $packageInstallationStatusList ? $this->isInstallationCompletelySuccessful($packageInstallationStatusList) : false;
            } catch (TranslatedException $e) {
                $this->log->error('[Import] Exception in ServiceImporter', [
                    'exceptionMessage' => $e->getTranslatedMessage(),
                    'packageName' => $package->name,
                ]);
            } catch (GenericException $e) {
                $this->log->error('[Import] Generic exception in ServiceImporter', [
                    'exceptionMessage' => $e->getMessage(),
                    'packageName' => $package->name,
                ]);
            }

            $installationStatusList->add(
                new InstallationStatusDto(
                    $installationStatus ? InstallationStatusEnum::SUCCESS() : InstallationStatusEnum::FAILURE(),
                    ComponentTypeEnum::SERVICE(),
                    $package->borlabsServicePackageKey,
                    $package->name,
                    -1,
                    $installationStatus === false ? ($packageInstallationStatusList ?? null) : null,
                ),
            );

            $this->log->info(
                '[Import] Service imported via package "{{ packageName }}": {{ status }}',
                [
                    'componentSettings' => $componentSettings,
                    'packageName' => $package->name,
                    'services' => $services,
                    'status' => $installationStatus ? 'Yes' : 'No',
                ],
            );
        }

        return $installationStatusList;
    }

    /**
     * @param object{
     *   cookie_id: string,
     *   language: string,
     *   service: string,
     *   name: string,
     *   provider: string,
     *   purpose: string,
     *   privacy_policy_url: string,
     *   hosts: string[],
     *   cookie_name: string,
     *   cookie_expiry: string,
     *   opt_in_js: string,
     *   opt_out_js: string,
     *   fallback_js: string,
     *   settings: array{
     *     asyncOptOutCode: string,
     *     blockCookiesBeforeConsent: string,
     *     prioritize: string,
     *   },
     *   position: int,
     *   status: bool,
     *   group_id: string
     * } $legacyService
     */
    private function addServiceCookiesFromLegacyData(ServiceModel $service, object $legacyService)
    {
        $cookieNames = explode(',', $legacyService->cookie_name);
        $cookieLifetimes = explode(',', $legacyService->cookie_expiry);

        foreach ($cookieNames as $index => $cookieName) {
            try {
                $cookieLifetime = $cookieLifetimes[$index] ?? $cookieLifetime ?? 'MISSING';
                $cookieName = trim($cookieName);
                $serviceCookie = new ServiceCookieModel();
                $serviceCookie->hostname = '#';
                $serviceCookie->lifetime = $cookieLifetime;
                $serviceCookie->name = $cookieName;
                $serviceCookie->path = '/';
                $serviceCookie->purpose = CookiePurposeEnum::TRACKING();
                $serviceCookie->serviceId = $service->id;
                $serviceCookie->type = CookieTypeEnum::HTTP();
                $this->serviceCookieRepository->insert($serviceCookie);
            } catch (GenericException $e) {
                $this->log->error('[Import] Failed to insert Service Cookie.', ['exception' => $e]);
            }
        }
    }

    private function addServiceLocationsFromLegacyData(ServiceModel $service, array $hosts): void
    {
        foreach ($hosts as $hostname) {
            try {
                $serviceLocation = new ServiceLocationModel();
                $serviceLocation->hostname = $hostname;
                $serviceLocation->path = '/';
                $serviceLocation->serviceId = $service->id;
                $this->serviceLocationRepository->insert($serviceLocation);
            } catch (GenericException $e) {
                $this->log->error('[Import] Failed to insert Service Location.', ['exception' => $e]);
            }
        }
    }

    /**
     * @param array<object{cookie_id: string, language: string, settings: string, group_id: string}> $legacyServices
     *
     * @return object{cookie_id: string, language: string, settings: mixed, group_id: string}|null
     */
    private function getLegacyServiceDataOfLanguage(array $legacyServices, string $languageCode): ?object
    {
        /** @var object{cookie_id: string, language: string, settings: string, group_id: string} $legacyService */
        foreach ($legacyServices as $legacyService) {
            if ($legacyService->language === $languageCode) {
                return $legacyService;
            }
        }

        return null;
    }

    /**
     * @param object{cookie_id: string,
     *      language: string,
     *      service: string,
     *      name: string,
     *      provider: string,
     *      purpose: string,
     *      privacy_policy_url: string,
     *      hosts: string[],
     *      cookie_name: string,
     *      cookie_expiry: string,
     *      opt_in_js: string,
     *      opt_out_js: string,
     *      fallback_js: string,
     *      settings: array{
     *        asyncOptOutCode: string,
     *        blockCookiesBeforeConsent: string,
     *        prioritize: string,
     *      },
     *      position: int,
     *      status: bool,
     *      group_id: string} $legacyServiceData
     */
    private function getOrAddServiceFromLegacyData(object $legacyServiceData): ?ServiceModel
    {
        // Get or add Provider
        $provider = $this->providerImporter->getOrAddProviderFromLegacyData(
            $legacyServiceData->provider,
            $legacyServiceData->purpose,
            $legacyServiceData->privacy_policy_url,
            $legacyServiceData->language,
        );

        if ($provider === null) {
            return null;
        }

        // Get Service Group
        $serviceGroup = $this->serviceGroupRepository->getByKey(
            $legacyServiceData->group_id,
            $legacyServiceData->language,
        );

        if ($serviceGroup === null) {
            return null;
        }

        // Add Service
        $existingModel = $this->serviceRepository->getByKey(
            $legacyServiceData->cookie_id,
            $legacyServiceData->language,
        );

        if ($existingModel !== null) {
            return $existingModel;
        }

        $newModel = new ServiceModel();
        $newModel->description = $legacyServiceData->purpose;
        $newModel->fallbackCode = $legacyServiceData->fallback_js;
        $newModel->key = $legacyServiceData->cookie_id;
        $newModel->language = $legacyServiceData->language;
        $newModel->name = $legacyServiceData->name;
        $newModel->optInCode = $legacyServiceData->opt_in_js;
        $newModel->optOutCode = $legacyServiceData->opt_out_js;
        $newModel->position = (int) $legacyServiceData->position;
        $newModel->providerId = $provider->id;
        $newModel->serviceGroupId = $serviceGroup->id;
        $newModel->settingsFields = $this->serviceDefaultSettingsFieldManager->get($legacyServiceData->language);
        $newModel->settingsFields = $this->migrateLegacySettings($newModel->settingsFields, $legacyServiceData->settings);
        $newModel->status = (bool) $legacyServiceData->status;

        $serviceModel = null;

        try {
            $serviceModel = $this->serviceRepository->insert($newModel);
        } catch (GenericException $e) {
            $this->log->error('[Import] Failed to insert Service.', ['exception' => $e]);
        }

        return $serviceModel;
    }

    /**
     * Example
     * <code>
     * [
     *   {
     *     "cookie_id": "google-analytics",
     *     "language": "en",
     *     "service": "GoogleAnalytics",
     *     "name": "Google Analytics",
     *     "provider": "Google LLC, 1600 Amphitheatre Parkway, Mountain View, CA 94043, USA",
     *     "purpose": "Cookie by Google used for website analytics. Generates statistical data on how the visitor uses the website.",
     *     "privacy_policy_url": "https://policies.google.com/privacy?hl=en",
     *     "hosts": [
     *       ".google-analytics.com",
     *       ".googletagmanager.com",
     *     ],
     *     "cookie_name": "_ga,_gat,_gid",
     *     "cookie_expiry": "2 Years",
     *     "opt_in_js": "",
     *     "opt_out_js": "",
     *     "fallback_js": "",
     *     "settings": [
     *       "asyncOptOutCode" => false,
     *       "blockCookiesBeforeConsent" => false,
     *       "prioritize" => true,
     *       "trackingId" => "UA-12345678-1"
     *     ],
     *     "position": 1,
     *     "status": true,
     *     "group_id": "statistics".
     *   }
     * ]
     * </code>.
     *
     * @return array<object{cookie_id: string,
     *       language: string,
     *       service: string,
     *       name: string,
     *       provider: string,
     *       purpose: string,
     *       privacy_policy_url: string,
     *       hosts: string[],
     *       cookie_name: string,
     *       cookie_expiry: string,
     *       opt_in_js: string,
     *       opt_out_js: string,
     *       fallback_js: string,
     *       settings: array{
     *         asyncOptOutCode: string,
     *         blockCookiesBeforeConsent: string,
     *         prioritize: string,
     *       },
     *       position: int,
     *       status: bool,
     *       group_id: string}>|null
     */
    private function getServices(): ?array
    {
        if (!Database::tableExists($this->wpdb->prefix . self::TABLE_NAME) || !Database::tableExists($this->wpdb->prefix . $this->serviceGroupImporter::TABLE_NAME)) {
            return null;
        }

        $services = $this->wpdb->get_results(
            '
            SELECT
                c.`cookie_id`,
                c.`language`,
                c.`service`,
                c.`name`,
                c.`provider`,
                c.`purpose`,
                c.`privacy_policy_url`,
                c.`hosts`,
                c.`cookie_name`,
                c.`cookie_expiry`,
                c.`opt_in_js`,
                c.`opt_out_js`,
                c.`fallback_js`,
                c.`settings`,
                c.`position`,
                c.`status`,
                g.`group_id`
            FROM
                `' . $this->wpdb->prefix . self::TABLE_NAME . '` as c
            INNER JOIN
                `' . $this->wpdb->prefix . $this->serviceGroupImporter::TABLE_NAME . '` as g
                ON (
                    c.`cookie_group_id` = g.`id`
                )
            ',
        );

        if (!is_array($services) || count($services) === 0) {
            return null;
        }

        foreach ($services as &$service) {
            $service->hosts = unserialize($service->hosts);
            $service->settings = unserialize($service->settings);
        }

        return $services;
    }

    /**
     * @return array<object{cookie_id: string, language: string, settings: string, group_id: string}>|null
     */
    private function getServicesByKey(string $cookieId): ?array
    {
        if (!Database::tableExists($this->wpdb->prefix . self::TABLE_NAME) || !Database::tableExists($this->wpdb->prefix . $this->serviceGroupImporter::TABLE_NAME)) {
            return null;
        }

        $services = $this->wpdb->get_results(
            $this->wpdb->prepare(
                '
                SELECT
                    c.`cookie_id`,
                    c.`language`,
                    c.`settings`,
                    g.`group_id`
                FROM
                    `' . $this->wpdb->prefix . self::TABLE_NAME . '` as c
                INNER JOIN
                    `' . $this->wpdb->prefix . $this->serviceGroupImporter::TABLE_NAME . '` as g
                    ON (
                        c.`cookie_id` = %s
                        AND
                        c.`cookie_group_id` = g.`id`
                        AND
                        c.`status` = 1
                    )
            ',
                [
                    $cookieId,
                ],
            ),
        );

        if (!is_array($services) || count($services) === 0) {
            return null;
        }

        foreach ($services as &$service) {
            $service->settings = unserialize($service->settings);
        }

        return $services;
    }

    /**
     * @param  object{cookie_id: string, language: string, settings: mixed, group_id: string}  $legacyServiceData
     *
     * @return array<string, string>
     */
    private function mergeComponentSettingsWithLegacyData(array $componentSettings, object $legacyServiceData, array $serviceMappingInfo): array
    {
        foreach ($serviceMappingInfo as $legacySettingsKey => $settingsKey) {
            $legacyServiceSettingsValue = $legacyServiceData->settings[$legacySettingsKey] ?? null;

            if ($legacyServiceSettingsValue === null) {
                continue;
            }

            $componentSettings[$settingsKey] = (string) $legacyServiceSettingsValue;
        }

        return $componentSettings;
    }

    private function migrateLegacySettings(SettingsFieldDtoList $settingsFields, array $legacySettings): SettingsFieldDtoList
    {
        foreach ($settingsFields->list as &$settingsField) {
            if ($settingsField->key === AsynchronousOptOutCode::KEY && isset($legacySettings['asyncOptOutCode'])) {
                $settingsField->value = $legacySettings['asyncOptOutCode'] ? '1' : '0';
            } elseif ($settingsField->key === BlockCookiesBeforeConsent::KEY && isset($legacySettings['blockCookiesBeforeConsent'])) {
                $settingsField->value = $legacySettings['blockCookiesBeforeConsent'] ? '1' : '0';
            } elseif ($settingsField->key === Prioritize::KEY && isset($legacySettings['prioritize'])) {
                $settingsField->value = $legacySettings['prioritize'] ? '1' : '0';
            }
        }

        return $settingsFields;
    }

    /**
     * @param  object{cookie_id: string, language: string, settings: mixed, group_id: string}  $legacyServiceData
     *
     * @return array<string, string>
     */
    private function setServiceGroupFromLegacyData(array $componentSettings, object $legacyServiceData, SettingsFieldDtoList $settingsFields): array
    {
        foreach ($settingsFields->list as $settingsField) {
            if ($settingsField->dataType->is(SettingsFieldDataTypeEnum::SYSTEM_SERVICE_GROUP())) {
                $componentSettings[$settingsField->key] = $legacyServiceData->group_id;
            }
        }

        return $componentSettings;
    }
}
