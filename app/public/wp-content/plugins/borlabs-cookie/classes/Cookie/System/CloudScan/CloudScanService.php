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

namespace Borlabs\Cookie\System\CloudScan;

use Borlabs\Cookie\Adapter\WpFunction;
use Borlabs\Cookie\ApiClient\CloudScanApiClient;
use Borlabs\Cookie\Dto\Adapter\WpGetPostsArgumentDto;
use Borlabs\Cookie\Dto\Adapter\WpGetPostTypeArgumentDto;
use Borlabs\Cookie\Dto\CloudScan\InstalledPluginDto;
use Borlabs\Cookie\Dto\CloudScan\InstalledThemeDto;
use Borlabs\Cookie\Dto\CloudScan\PackageSuggestionDto;
use Borlabs\Cookie\Dto\CloudScan\ScanResponseDto;
use Borlabs\Cookie\DtoList\CloudScan\InstalledPluginDtoList;
use Borlabs\Cookie\DtoList\CloudScan\InstalledThemeDtoList;
use Borlabs\Cookie\DtoList\CloudScan\PackageSuggestionDtoList;
use Borlabs\Cookie\Enum\CloudScan\CloudScanStatusEnum;
use Borlabs\Cookie\Enum\CloudScan\CloudScanTypeEnum;
use Borlabs\Cookie\Enum\PageSelection\KeywordTypeEnum;
use Borlabs\Cookie\Exception\GenericException;
use Borlabs\Cookie\Exception\System\LicenseExpiredException;
use Borlabs\Cookie\Model\CloudScan\CloudScanCookieModel;
use Borlabs\Cookie\Model\CloudScan\CloudScanExternalResourceModel;
use Borlabs\Cookie\Model\CloudScan\CloudScanModel;
use Borlabs\Cookie\Model\CloudScan\CloudScanSuggestionModel;
use Borlabs\Cookie\Repository\CloudScan\CloudScanCookieRepository;
use Borlabs\Cookie\Repository\CloudScan\CloudScanExternalResourceRepository;
use Borlabs\Cookie\Repository\CloudScan\CloudScanRepository;
use Borlabs\Cookie\Repository\CloudScan\CloudScanSuggestionRepository;
use Borlabs\Cookie\Repository\Expression\BinaryOperatorExpression;
use Borlabs\Cookie\Repository\Expression\ListExpression;
use Borlabs\Cookie\Repository\Expression\LiteralExpression;
use Borlabs\Cookie\Repository\Expression\ModelFieldNameExpression;
use Borlabs\Cookie\Repository\Package\PackageRepository;
use Borlabs\Cookie\Support\Sanitizer;
use Borlabs\Cookie\System\Config\DialogSettingsConfig;
use Borlabs\Cookie\System\Language\Language;
use Borlabs\Cookie\System\License\License;
use Borlabs\Cookie\System\Option\Option;
use Borlabs\Cookie\System\PageSelection\PageSelectionService;
use DateTime;
use Exception;

class CloudScanService
{
    private CloudScanApiClient $cloudScanApiClient;

    private CloudScanCookieRepository $cloudScanCookieRepository;

    private CloudScanExternalResourceRepository $cloudScanExternalResourceRepository;

    private CloudScanRepository $cloudScanRepository;

    private CloudScanSuggestionRepository $cloudScanSuggestionRepository;

    private DialogSettingsConfig $dialogSettingsConfig;

    private Language $language;

    private License $license;

    private Option $option;

    private PackageRepository $packageRepository;

    private PageSelectionService $pageSelectionService;

    private WpFunction $wpFunction;

    public function __construct(
        CloudScanApiClient $cloudScanApiClient,
        CloudScanCookieRepository $cloudScanCookieRepository,
        CloudScanExternalResourceRepository $cloudScanExternalResourceRepository,
        CloudScanRepository $cloudScanRepository,
        CloudScanSuggestionRepository $cloudScanSuggestionRepository,
        DialogSettingsConfig $dialogSettingsConfig,
        Language $language,
        License $license,
        Option $option,
        PackageRepository $packageRepository,
        PageSelectionService $pageSelectionService,
        WpFunction $wpFunction
    ) {
        $this->cloudScanApiClient = $cloudScanApiClient;
        $this->cloudScanCookieRepository = $cloudScanCookieRepository;
        $this->cloudScanExternalResourceRepository = $cloudScanExternalResourceRepository;
        $this->cloudScanRepository = $cloudScanRepository;
        $this->cloudScanSuggestionRepository = $cloudScanSuggestionRepository;
        $this->dialogSettingsConfig = $dialogSettingsConfig;
        $this->language = $language;
        $this->license = $license;
        $this->option = $option;
        $this->packageRepository = $packageRepository;
        $this->pageSelectionService = $pageSelectionService;
        $this->wpFunction = $wpFunction;
    }

    public function checkUnfinishedScans()
    {
        $scansOfStatusAnalyzing = $this->cloudScanRepository->getAllOfStatus(CloudScanStatusEnum::SCANNING());

        foreach ($scansOfStatusAnalyzing as $unfinishedScan) {
            try {
                $this->syncScanResult($unfinishedScan->id);
            } catch (GenericException $e) {
                // Note: ignore
            }
        }

        $scansOfStatusAnalyzing = $this->cloudScanRepository->getAllOfStatus(CloudScanStatusEnum::ANALYZING());

        foreach ($scansOfStatusAnalyzing as $unfinishedScan) {
            try {
                $this->syncScanResult($unfinishedScan->id);
            } catch (GenericException $e) {
                // Note: ignore
            }
        }
    }

    /**
     * @throws \Borlabs\Cookie\Exception\ApiClient\CloudScanApiClientException
     * @throws \Borlabs\Cookie\Exception\System\LicenseExpiredException
     * @throws \Borlabs\Cookie\Exception\UnexpectedRepositoryOperationException
     * @throws \Borlabs\Cookie\Exception\HttpClient\ConnectionErrorException
     */
    public function createScan(
        array $urls,
        CloudScanTypeEnum $scanType,
        ?string $httpAuthUsername = null,
        ?string $httpAuthPassword = null
    ): CloudScanModel {
        if (!$this->license->isLicenseValid()) {
            throw new LicenseExpiredException('licenseExpiredFeatureNotAvailable');
        }

        $cloudScanResponse = $this->cloudScanApiClient->requestScanCreation(
            $this->getInstalledPlugins(),
            $this->getInstalledThemes(),
            $urls,
            $scanType,
            $httpAuthUsername,
            $httpAuthPassword,
        );

        $cloudScanModel = new CloudScanModel();
        $cloudScanModel->createdAt = new DateTime();
        $cloudScanModel->externalId = $cloudScanResponse->id;
        $cloudScanModel->pages = $cloudScanResponse->pages;
        $cloudScanModel->status = $cloudScanResponse->status;
        $cloudScanModel->type = $cloudScanResponse->type;

        return $this->cloudScanRepository->insert($cloudScanModel);
    }

    public function getInstalledPlugins(bool $onlyEnabledPlugins = false): InstalledPluginDtoList
    {
        $activePlugins = $this->option->getThirdPartyOption('active_plugins')->value;
        $installedPlugins = new InstalledPluginDtoList();
        $plugins = $this->wpFunction->getPlugins();

        foreach ($plugins as $pluginPath => $plugin) {
            if ($onlyEnabledPlugins && !in_array($pluginPath, $activePlugins, true)) {
                continue;
            }

            $installedPlugins->add(
                new InstalledPluginDto(
                    dirname($pluginPath),
                    $plugin['TextDomain'],
                ),
            );
        }

        return $installedPlugins;
    }

    public function getInstalledThemes(bool $onlyEnabledTheme = false): InstalledThemeDtoList
    {
        $activeTheme = $this->wpFunction->getWpTheme();
        $installedThemes = new InstalledThemeDtoList();
        $themes = $this->wpFunction->getWpThemes();

        foreach ($themes as $themeSlug => $theme) {
            if ($onlyEnabledTheme && $activeTheme->get_template() !== $theme->get_template()) {
                continue;
            }

            $installedThemes->add(
                new InstalledThemeDto(
                    (string) $themeSlug,
                    (string) $theme->get('TextDomain'),
                ),
            );
        }

        return $installedThemes;
    }

    /**
     * @param string $type Possible values: homepage, selection_of_sites_per_post_type, custom
     *
     * @return object[]
     */
    public function getListOfPagesByType(
        string $type,
        bool $enableCustomScanUrl,
        ?string $scanPageUrl = null,
        ?string $customScanUrls = null
    ): array {
        if ($type === 'homepage') {
            return [
                $this->getHomepage(),
            ];
        }

        if ($type === 'selection_of_sites_per_post_type') {
            return $this->getSelectionOfSitesPerPostType();
        }

        if ($type === 'custom') {
            if (!$enableCustomScanUrl && $scanPageUrl !== null) {
                return [
                    (object) [
                        'url' => $scanPageUrl,
                    ],
                ];
            }

            if ($enableCustomScanUrl && $customScanUrls !== null) {
                return array_map(
                    fn ($url) => (object) ['url' => $url],
                    Sanitizer::hostList($customScanUrls, true),
                );
            }
        }

        // Fallback
        return [
            $this->getHomepage(),
        ];
    }

    public function getNotInstalledSuggestedPackages(int $id): ?PackageSuggestionDtoList
    {
        $suggestions = $this->cloudScanSuggestionRepository->find(['cloudScanId' => $id,]);
        $packageKeys = array_column($suggestions, 'borlabsServicePackageKey');
        $packageSuggestionList = new PackageSuggestionDtoList();

        if (count($packageKeys)) {
            $packages = $this->packageRepository->find([
                new BinaryOperatorExpression(
                    new ModelFieldNameExpression('borlabsServicePackageKey'),
                    'IN',
                    new ListExpression(
                        array_map(
                            fn ($packageKey) => new LiteralExpression($packageKey),
                            $packageKeys,
                        ),
                    ),
                ),
            ], [
                'name' => 'ASC',
            ]);

            // Remove installed packages from the list
            $packages = array_filter($packages, function ($package) {
                return $package->installedAt === null;
            });

            foreach ($packages as $package) {
                $packageSuggestionList->add(
                    new PackageSuggestionDto(
                        $package->id,
                        $package->name,
                        $package->type,
                    ),
                );
            }
        }

        return $packageSuggestionList;
    }

    /**
     * @throws \Borlabs\Cookie\Exception\ApiClient\CloudScanApiClientException
     * @throws \Borlabs\Cookie\Exception\UnexpectedRepositoryOperationException
     */
    public function syncScanResult(int $id): ?CloudScanModel
    {
        /** @var null|CloudScanModel $cloudScan */
        $cloudScan = $this->cloudScanRepository->findById($id);

        if ($cloudScan === null) {
            // TODO: exception
            return null;
        }

        try {
            $scanResponse = $this->cloudScanApiClient->getScan($cloudScan->externalId);
        } catch (GenericException $e) {
            return null;
        }

        if ($cloudScan->status->isValue(CloudScanStatusEnum::FINISHED)) {
            return $cloudScan;
        }

        $cloudScan->pages = $scanResponse->pages;
        $cloudScan->status = $scanResponse->status;
        $this->cloudScanRepository->update($cloudScan);
        $this->handleCookies($cloudScan, $scanResponse);
        $this->handleExternalResources($cloudScan, $scanResponse);
        $this->handleSuggestions($cloudScan, $scanResponse);

        return $cloudScan;
    }

    /**
     * @param array<object{ url: string }>  $urls
     *
     * @return array<object{ url: string }>
     */
    private function ensureUrlIsUnique(array $urls): array
    {
        /** @var array<string, string> $uniqueUrls */
        $uniqueUrls = [];

        /** @var array<object{ url: string }> $urlList */
        $urlList = [];

        foreach ($urls as $url) {
            $urlList[$url->url] = $url->url;
        }

        foreach ($urlList as $url) {
            $uniqueUrls[] = (object) ['url' => $url];
        }

        return $uniqueUrls;
    }

    private function getHomepage(): object
    {
        return (object) [
            'url' => $this->wpFunction->getHomeUrl(),
        ];
    }

    private function getOldestAndNewestPageOfEachPosttype(): array
    {
        $pages = [];
        $postTypeArgument = new WpGetPostTypeArgumentDto();
        $postTypeArgument->excludeFromSearch = false;
        $postTypeArgument->public = true;
        $postTypeArgument->showInNavMenus = true;
        $postTypes = $this->wpFunction->getPostTypes($postTypeArgument);

        foreach ($postTypes as $postType) {
            $archiveLink = $this->wpFunction->getPostTypeArchiveLink($postType);

            if ($archiveLink !== null) {
                $pages[] = (object) [
                    'url' => $archiveLink,
                ];
            }

            $postArgument = new WpGetPostsArgumentDto();
            $postArgument->numberPosts = 1;
            $postArgument->postType = [$postType];
            $postArgument->order = 'ASC';
            $oldestPost = $this->wpFunction->getPosts($postArgument);

            if (count($oldestPost) === 1) {
                $tempUrl = $this->wpFunction->getPermalink($oldestPost[0]->ID);

                if ($tempUrl !== null) {
                    $pages[] = (object) [
                        'url' => $tempUrl,
                    ];
                }

                $postArgument = new WpGetPostsArgumentDto();
                $postArgument->numberPosts = 1;
                $postArgument->postType = [$postType];
                $postArgument->order = 'DESC';
                $newestPost = $this->wpFunction->getPosts($postArgument);

                if (count($newestPost) === 1 && $newestPost[0]->ID !== $oldestPost[0]->ID) {
                    $tempUrl = $this->wpFunction->getPermalink($newestPost[0]->ID);

                    if ($tempUrl !== null) {
                        $pages[] = (object) [
                            'url' => $tempUrl,
                        ];
                    }
                }
            }
        }

        return $pages;
    }

    private function getSelectionOfSitesPerPostType(): array
    {
        $pages = $this->getOldestAndNewestPageOfEachPosttype();

        // Loop through all KeywordTypeEnum values
        $keywordTypes = [
            KeywordTypeEnum::CONTACT(),
            KeywordTypeEnum::MAP(),
            KeywordTypeEnum::IMPRINT(),
            KeywordTypeEnum::PRIVACY(),
        ];

        foreach ($keywordTypes as $keywordType) {
            // Special handling for IMPRINT and PRIVACY - only use if corresponding URL is empty
            if ($keywordType->isValue(KeywordTypeEnum::IMPRINT)
                && $this->dialogSettingsConfig->get()->imprintPageUrl !== '') {
                continue;
            }

            if ($keywordType->isValue(KeywordTypeEnum::PRIVACY)
                && $this->dialogSettingsConfig->get()->privacyPageUrl !== '') {
                continue;
            }

            $page = $this->pageSelectionService->findPageUrlByKeywordType(
                $keywordType,
                $this->language->getSelectedLanguageCode(),
            );

            if ($page !== null) {
                $pages[] = (object) ['url' => $page];
            }
        }

        $pages[] = $this->getHomepage();

        // Add URLs if they are configured in the dialog settings
        if ($this->dialogSettingsConfig->get()->imprintPageUrl !== '') {
            $pages[] = (object) ['url' => $this->dialogSettingsConfig->get()->imprintPageUrl];
        }

        if ($this->dialogSettingsConfig->get()->privacyPageUrl !== '') {
            $pages[] = (object) ['url' => $this->dialogSettingsConfig->get()->privacyPageUrl];
        }

        return $this->ensureUrlIsUnique($pages);
    }

    private function handleCookies(CloudScanModel $cloudScan, ScanResponseDto $scanResponseDto): void
    {
        if (count($scanResponseDto->cookies->list) <= 0) {
            return;
        }

        $existingCookies = $this->cloudScanCookieRepository->getByCloudScan($cloudScan);

        foreach ($existingCookies as $existingCookie) {
            $this->cloudScanCookieRepository->delete($existingCookie);
        }

        foreach ($scanResponseDto->cookies->list as $cookie) {
            $cookieModel = new CloudScanCookieModel();
            $cookieModel->borlabsServicePackageKey = $cookie->packageKey;
            $cookieModel->cloudScanId = $cloudScan->id;
            $cookieModel->examples = $cookie->examples;
            $cookieModel->hostname = $cookie->hostname;
            $cookieModel->lifetime = $cookie->lifetime;
            $cookieModel->name = $cookie->name;
            $cookieModel->path = $cookie->path;

            $this->cloudScanCookieRepository->insert($cookieModel);
        }
    }

    private function handleExternalResources(CloudScanModel $cloudScan, ScanResponseDto $scanResponseDto): void
    {
        if (count($scanResponseDto->externalResources->list) <= 0) {
            return;
        }

        $existingExternalResources = $this->cloudScanExternalResourceRepository->getByCloudScan($cloudScan);

        foreach ($existingExternalResources as $existingExternalResource) {
            $this->cloudScanExternalResourceRepository->delete($existingExternalResource);
        }

        foreach ($scanResponseDto->externalResources->list as $externalResource) {
            $externalResourceModel = new CloudScanExternalResourceModel();
            $externalResourceModel->borlabsServicePackageKey = $externalResource->packageKey;
            $externalResourceModel->cloudScanId = $cloudScan->id;
            $externalResourceModel->examples = $externalResource->examples;
            $externalResourceModel->hostname = $externalResource->hostname;

            $this->cloudScanExternalResourceRepository->insert($externalResourceModel);
        }
    }

    private function handleSuggestions(CloudScanModel $cloudScan, ScanResponseDto $scanResponseDto): void
    {
        if (count($scanResponseDto->suggestions->list) <= 0) {
            return;
        }

        $existingSuggestions = $this->cloudScanSuggestionRepository->getByCloudScan($cloudScan);

        foreach ($existingSuggestions as $existingSuggestion) {
            $this->cloudScanSuggestionRepository->delete($existingSuggestion);
        }

        foreach ($scanResponseDto->suggestions->list as $suggestion) {
            $suggestionModel = new CloudScanSuggestionModel();
            $suggestionModel->borlabsServicePackageKey = $suggestion->packageKey;
            $suggestionModel->cloudScanId = $cloudScan->id;
            $suggestionModel->pages = $suggestion->pages;

            $this->cloudScanSuggestionRepository->insert($suggestionModel);
        }
    }
}
