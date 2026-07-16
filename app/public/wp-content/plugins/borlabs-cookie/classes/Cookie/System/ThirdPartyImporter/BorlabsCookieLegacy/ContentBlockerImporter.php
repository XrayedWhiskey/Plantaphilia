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
use Borlabs\Cookie\Dto\System\KeyValueDto;
use Borlabs\Cookie\Dto\ThirdPartyImporter\PreImportMetadataDto;
use Borlabs\Cookie\DtoList\Package\InstallationStatusDtoList;
use Borlabs\Cookie\DtoList\System\KeyValueDtoList;
use Borlabs\Cookie\DtoList\System\SettingsFieldDtoList;
use Borlabs\Cookie\DtoList\ThirdPartyImporter\PreImportMetadataDtoList;
use Borlabs\Cookie\Enum\Package\ComponentTypeEnum;
use Borlabs\Cookie\Enum\Package\InstallationStatusEnum;
use Borlabs\Cookie\Exception\GenericException;
use Borlabs\Cookie\Exception\TranslatedException;
use Borlabs\Cookie\Localization\DefaultLocalizationStrings;
use Borlabs\Cookie\Model\ContentBlocker\ContentBlockerLocationModel;
use Borlabs\Cookie\Model\ContentBlocker\ContentBlockerModel;
use Borlabs\Cookie\Repository\ContentBlocker\ContentBlockerLocationRepository;
use Borlabs\Cookie\Repository\ContentBlocker\ContentBlockerRepository;
use Borlabs\Cookie\Repository\Package\PackageRepository;
use Borlabs\Cookie\Support\Database;
use Borlabs\Cookie\System\ContentBlocker\ContentBlockerDefaultSettingsFieldManager;
use Borlabs\Cookie\System\ContentBlocker\DefaultSettingsField\ExecuteGlobalCodeBeforeUnblocking;
use Borlabs\Cookie\System\Language\Language;
use Borlabs\Cookie\System\Log\Log;
use Borlabs\Cookie\System\Package\PackageManager;
use Borlabs\Cookie\System\Package\Traits\InstallationStatusAnalyzerTrait;
use Borlabs\Cookie\System\ThirdPartyImporter\BorlabsCookieLegacy\Traits\ComponentSettingsTrait;

final class ContentBlockerImporter
{
    use ComponentSettingsTrait;
    use InstallationStatusAnalyzerTrait;

    public const TABLE_NAME = 'borlabs_cookie_legacy_content_blocker';

    private ContentBlockerDefaultSettingsFieldManager $contentBlockerDefaultSettingsFieldManager;

    private ContentBlockerLocationRepository $contentBlockerLocationRepository;

    private ContentBlockerRepository $contentBlockerRepository;

    private DefaultLocalizationStrings $defaultLocalizationStrings;

    private array $ignoredContentBlockers = [
        'default',
    ];

    private Language $language;

    private Log $log;

    /**
     * @var string[] BorlabsCookieLegacyContentBlockerId => BorlabsServicePackageKey
     */
    private $map = [
        'facebook' => 'facebook',
        'googlemaps' => 'google-maps',
        'instagram' => 'instagram',
        'openstreetmap' => 'open-street-map',
        'twitter' => 'x-alias-twitter',
        'vimeo' => 'vimeo',
        'youtube' => 'youtube',
    ];

    private PackageManager $packageManager;

    private PackageRepository $packageRepository;

    private ProviderImporter $providerImporter;

    private WpDb $wpdb;

    public function __construct(
        ContentBlockerDefaultSettingsFieldManager $contentBlockerDefaultSettingsFieldManager,
        ContentBlockerLocationRepository $contentBlockerLocationRepository,
        ContentBlockerRepository $contentBlockerRepository,
        DefaultLocalizationStrings $defaultLocalizationStrings,
        Language $language,
        Log $log,
        PackageManager $packageManager,
        PackageRepository $packageRepository,
        ProviderImporter $providerImporter,
        WpDb $wpdb
    ) {
        $this->contentBlockerDefaultSettingsFieldManager = $contentBlockerDefaultSettingsFieldManager;
        $this->contentBlockerLocationRepository = $contentBlockerLocationRepository;
        $this->contentBlockerRepository = $contentBlockerRepository;
        $this->defaultLocalizationStrings = $defaultLocalizationStrings;
        $this->language = $language;
        $this->log = $log;
        $this->packageManager = $packageManager;
        $this->packageRepository = $packageRepository;
        $this->providerImporter = $providerImporter;
        $this->wpdb = $wpdb;
    }

    public function getPreImportMetadataList(): PreImportMetadataDtoList
    {
        $preImportMetadataList = new PreImportMetadataDtoList(null);
        $contentBlockers = $this->getContentBlockers();

        if ($contentBlockers === null) {
            return $preImportMetadataList;
        }

        foreach ($contentBlockers as $contentBlocker) {
            $preImportMetadataList->add(
                new PreImportMetadataDto(
                    \Borlabs\Cookie\Enum\ThirdPartyImporter\ComponentTypeEnum::CONTENT_BLOCKER(),
                    $contentBlocker->name,
                    $contentBlocker->content_blocker_id,
                    $this->map[$contentBlocker->content_blocker_id] ?? $contentBlocker->content_blocker_id,
                    $contentBlocker->language,
                ),
            );
        }

        return $preImportMetadataList;
    }

    public function importCustom(): InstallationStatusDtoList
    {
        $installationStatusList = new InstallationStatusDtoList(null);
        $legacyContentBlockers = $this->getContentBlockers();

        if ($legacyContentBlockers === null) {
            return $installationStatusList;
        }

        /** @var object{content_blocker_id: string,
         *     global_js: string,
         *     hosts: string[],
         *     init_js: string,
         *     language: string,
         *     name: string,
         *     preview_css: string,
         *     preview_html: string,
         *     privacy_policy_url: string,
         *     settings: array{
         *       executeGlobalCodeBeforeUnblocking: string,
         *     },
         *     status: bool} $legacyContentBlocker */
        foreach ($legacyContentBlockers as $legacyContentBlocker) {
            if (($this->map[$legacyContentBlocker->content_blocker_id] ?? false) || in_array($legacyContentBlocker->content_blocker_id, $this->ignoredContentBlockers, true)) {
                continue;
            }

            $this->log->info(
                '[Import] Custom Content Blocker "{{ contentBlockerName }}" ({{ languageCode }})',
                [
                    'contentBlockerName' => $legacyContentBlocker->name,
                    'languageCode' => $legacyContentBlocker->language,
                    'legacyContentBlocker' => $legacyContentBlocker,
                ],
            );

            $contentBlocker = $this->getOrAddContentBlockerFromLegacyData($legacyContentBlocker);
            $installationStatusList->add(
                new InstallationStatusDto(
                    $contentBlocker ? InstallationStatusEnum::SUCCESS() : InstallationStatusEnum::FAILURE(),
                    ComponentTypeEnum::CONTENT_BLOCKER(),
                    $legacyContentBlocker->content_blocker_id,
                    $legacyContentBlocker->name,
                    $contentBlocker->id ?? -1,
                ),
            );

            if (is_null($contentBlocker)) {
                continue;
            }

            if (count($legacyContentBlocker->hosts) > 0) {
                $this->addContentBlockerLocationsFromLegacyData($contentBlocker, $legacyContentBlocker->hosts);
            }
        }

        return $installationStatusList;
    }

    public function importPreset(): InstallationStatusDtoList
    {
        $installationStatusList = new InstallationStatusDtoList(null);

        foreach ($this->map as $contentBlockerId => $borlabsServicePackageKey) {
            $contentBlockers = $this->getContentBlockersByKey($contentBlockerId);

            if (!isset($contentBlockers)) {
                $this->log->info(
                    '[Import] Content Blocker package "{{ borlabsServicePackageKey }}" was skipped because it is disabled or unavailable',
                    [
                        'borlabsServicePackageKey' => $borlabsServicePackageKey,
                        'contentBlockerId' => $contentBlockerId,
                    ],
                );

                continue;
            }

            $package = $this->packageRepository->getByPackageKey($borlabsServicePackageKey);

            if ($package === null) {
                $this->log->error(
                    '[Import] Content Blocker package "{{ borlabsServicePackageKey }}" not found',
                    [
                        'borlabsServicePackageKey' => $borlabsServicePackageKey,
                        'contentBlockerId' => $contentBlockerId,
                        'contentBlockers' => $contentBlockers,
                    ],
                );
                $installationStatusList->add(
                    new InstallationStatusDto(
                        InstallationStatusEnum::FAILURE(),
                        ComponentTypeEnum::SERVICE(),
                        $borlabsServicePackageKey,
                        $borlabsServicePackageKey . ' (' . $contentBlockerId . ')',
                    ),
                );

                continue;
            }

            $componentSettings = $this->setDefaultComponentSettings($package, array_column($contentBlockers, 'language'));

            $installationStatus = false;

            try {
                $packageInstallationStatusList = $this->packageManager->install($package, $componentSettings);
                $installationStatus = $packageInstallationStatusList ? $this->isInstallationCompletelySuccessful($packageInstallationStatusList) : false;
            } catch (TranslatedException $e) {
                $this->log->error('[Import] Exception in ContentBlockerImporter', [
                    'exceptionMessage' => $e->getTranslatedMessage(),
                    'packageName' => $package->name,
                ]);
            } catch (GenericException $e) {
                $this->log->error('[Import] Generic exception in ContentBlockerImporter', [
                    'exceptionMessage' => $e->getMessage(),
                    'packageName' => $package->name,
                ]);
            }

            $installationStatusList->add(
                new InstallationStatusDto(
                    $installationStatus ? InstallationStatusEnum::SUCCESS() : InstallationStatusEnum::FAILURE(),
                    ComponentTypeEnum::CONTENT_BLOCKER(),
                    $package->borlabsServicePackageKey,
                    $package->name,
                    -1,
                    $installationStatus === false ? ($packageInstallationStatusList ?? null) : null,
                ),
            );

            $this->log->info(
                '[Import] Content Blocker imported via package "{{ packageName }}": {{ status }}',
                [
                    'packageName' => $package->name,
                    'status' => $installationStatus ? 'Yes' : 'No',
                ],
            );
        }

        return $installationStatusList;
    }

    private function addContentBlockerLocationsFromLegacyData(ContentBlockerModel $contentBlocker, array $hosts): void
    {
        foreach ($hosts as $hostname) {
            try {
                $contentBlockerLocation = new ContentBlockerLocationModel();
                $contentBlockerLocation->hostname = $hostname;
                $contentBlockerLocation->path = '/';
                $contentBlockerLocation->contentBlockerId = $contentBlocker->id;
                $this->contentBlockerLocationRepository->insert($contentBlockerLocation);
            } catch (GenericException $e) {
                $this->log->error('[Import] Failed to insert Content Blocker Location.', ['exception' => $e]);
            }
        }
    }

    /**
     * Example
     * <code>
     * [
     *   {
     *     "content_blocker_id": "googlemaps",
     *     "global_js": "",
     *     "hosts": [
     *       ".google.com",
     *     ],
     *     "init_js": "",
     *     "language": "en",
     *     "name": "Google Maps",
     *     "preview_css": "",
     *     "preview_html": "<div>...</div>",
     *     "privacy_policy_url": "https://policies.google.com/privacy?hl=en",
     *     "settings": [
     *       "executeGlobalCodeBeforeUnblocking" => false,
     *     ],
     *     "status": true,
     *   }
     * ]
     * </code>.
     *
     * @return array<object{content_blocker_id: string,
     *       global_js: string,
     *       hosts: string[],
     *       init_js: string,
     *       language: string,
     *       name: string,
     *       preview_css: string,
     *       preview_html: string,
     *       privacy_policy_url: string,
     *       settings: array{
     *         executeGlobalCodeBeforeUnblocking: string,
     *       },
     *       status: bool}>|null
     */
    private function getContentBlockers(): ?array
    {
        if (!Database::tableExists($this->wpdb->prefix . self::TABLE_NAME)) {
            return null;
        }

        $contentBlockers = $this->wpdb->get_results(
            '
            SELECT
                `content_blocker_id`,
                `global_js`,
                `hosts`,
                `init_js`,
                `language`,
                `name`,
                `preview_css`,
                `preview_html`,
                `privacy_policy_url`,
                `settings`,
                `status`
            FROM
                `' . $this->wpdb->prefix . self::TABLE_NAME . '`
            ',
        );

        if (!is_array($contentBlockers) || count($contentBlockers) === 0) {
            return null;
        }

        foreach ($contentBlockers as &$contentBlocker) {
            $contentBlocker->hosts = unserialize($contentBlocker->hosts);
            $contentBlocker->settings = unserialize($contentBlocker->settings);
        }

        return $contentBlockers;
    }

    private function getContentBlockersByKey(string $contentBlockerId): ?array
    {
        if (!Database::tableExists($this->wpdb->prefix . self::TABLE_NAME)) {
            return null;
        }

        $contentBlockers = $this->wpdb->get_results(
            $this->wpdb->prepare(
                '
                SELECT
                    `content_blocker_id`,
                    `language`
                FROM
                    `' . $this->wpdb->prefix . self::TABLE_NAME . '`
                WHERE
                    `content_blocker_id` = %s
                    AND
                    `status` = 1
            ',
                [
                    $contentBlockerId,
                ],
            ),
        );

        return is_array($contentBlockers) ? $contentBlockers : null;
    }

    private function getDefaultLanguageStrings(string $languageCode): KeyValueDtoList
    {
        $this->language->loadTextDomain($languageCode);

        $languageStrings = new KeyValueDtoList();
        $languageStrings->add(
            new KeyValueDto('acceptServiceUnblockContent', $this->defaultLocalizationStrings->get()['contentBlocker']['acceptServiceUnblockContent']),
        );
        $languageStrings->add(
            new KeyValueDto('description', $this->defaultLocalizationStrings->get()['contentBlocker']['description']),
        );
        $languageStrings->add(
            new KeyValueDto('moreInformation', $this->defaultLocalizationStrings->get()['contentBlocker']['moreInformation']),
        );
        $languageStrings->add(
            new KeyValueDto('unblockButton', $this->defaultLocalizationStrings->get()['contentBlocker']['unblockButton']),
        );

        $this->language->unloadBlogLanguage();

        return $languageStrings;
    }

    private function getDefaultPreviewHtml(): string
    {
        return <<<EOT
<div class="brlbs-cmpnt-cb-preset-a">
  <p class="brlbs-cmpnt-cb-description">{{ description }}</p>
  <div class="brlbs-cmpnt-cb-buttons">
    <a class="brlbs-cmpnt-cb-btn" href="#" data-borlabs-cookie-unblock role="button">{{ unblockButton }}</a>
    <a class="brlbs-cmpnt-cb-btn" href="#" data-borlabs-cookie-accept-service role="button" style="display: {{ serviceConsentButtonDisplayValue }}">{{ acceptServiceUnblockContent }}</a>
  </div>
  <a class="brlbs-cmpnt-cb-provider-toggle" href="#" data-borlabs-cookie-show-provider-information role="button">{{ moreInformation }}</a>
'</div>'
EOT;
    }

    /**
     * @param  object{content_blocker_id: string,
     *    global_js: string,
     *    hosts: string[],
     *    init_js: string,
     *    language: string,
     *    name: string,
     *    preview_css: string,
     *    preview_html: string,
     *    privacy_policy_url: string,
     *    settings: array{
     *      executeGlobalCodeBeforeUnblocking: string,
     *    },
     *    status: bool}  $legacyContentBlockerData
     */
    private function getOrAddContentBlockerFromLegacyData(object $legacyContentBlockerData): ?ContentBlockerModel
    {
        // Get or add Provider
        $provider = $this->providerImporter->getOrAddProviderFromLegacyData(
            'IMPORTED ' . $legacyContentBlockerData->name,
            'MISSING',
            $legacyContentBlockerData->privacy_policy_url,
            $legacyContentBlockerData->language,
        );

        if ($provider === null) {
            return null;
        }

        // Add Content Blocker
        $existingModel = $this->contentBlockerRepository->getByKey(
            $legacyContentBlockerData->content_blocker_id,
            $legacyContentBlockerData->language,
        );

        if ($existingModel !== null) {
            return $existingModel;
        }

        $newModel = new ContentBlockerModel();
        $newModel->javaScriptGlobal = $legacyContentBlockerData->global_js;
        $newModel->javaScriptInitialization = $legacyContentBlockerData->init_js;
        $newModel->key = $legacyContentBlockerData->content_blocker_id;
        $newModel->language = $legacyContentBlockerData->language;
        $newModel->languageStrings = $this->getDefaultLanguageStrings($legacyContentBlockerData->language);
        $newModel->name = $legacyContentBlockerData->name;
        $newModel->providerId = $provider->id;
        $newModel->previewHtml = $this->getDefaultPreviewHtml();
        $newModel->settingsFields = $this->contentBlockerDefaultSettingsFieldManager->get($legacyContentBlockerData->language);
        $newModel->settingsFields = $this->migrateLegacySettings($newModel->settingsFields, $legacyContentBlockerData->settings);
        $newModel->status = (bool) $legacyContentBlockerData->status;

        $contentBlockerModel = null;

        try {
            $contentBlockerModel = $this->contentBlockerRepository->insert($newModel);
        } catch (GenericException $e) {
            $this->log->error('[Import] Failed to insert Content Blocker.', ['exception' => $e]);
        }

        return $contentBlockerModel;
    }

    private function migrateLegacySettings(SettingsFieldDtoList $settingsFields, array $legacySettings): SettingsFieldDtoList
    {
        foreach ($settingsFields->list as &$settingsField) {
            if ($settingsField->key === ExecuteGlobalCodeBeforeUnblocking::KEY && isset($legacySettings['executeGlobalCodeBeforeUnblocking'])) {
                $settingsField->value = $legacySettings['executeGlobalCodeBeforeUnblocking'] ? '1' : '0';
            }
        }

        return $settingsFields;
    }
}
