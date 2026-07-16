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

namespace Borlabs\Cookie\System\ContentBlocker;

use Borlabs\Cookie\Adapter\WpFunction;
use Borlabs\Cookie\Model\ContentBlocker\ContentBlockerModel;
use Borlabs\Cookie\Model\Service\ServiceModel;
use Borlabs\Cookie\Repository\ContentBlocker\ContentBlockerRepository;
use Borlabs\Cookie\Repository\Service\ServiceRepository;
use Borlabs\Cookie\Support\Sanitizer;
use Borlabs\Cookie\Support\Searcher;
use Borlabs\Cookie\System\Config\ContentBlockerSettingsConfig;
use Borlabs\Cookie\System\Config\ContentBlockerStyleConfig;
use Borlabs\Cookie\System\Language\Language;
use Borlabs\Cookie\System\LocalScanner\ScanRequestService;

final class ContentBlockerManager
{
    public const IFRAME_DETECTION_REGEX = '/<iframe.*?(?=<\/iframe>)<\/iframe>/is';

    private ContentBlockerSettingsConfig $contentBlockerGeneralConfig;

    private ContentBlockerRepository $contentBlockerRepository;

    /**
     * @var ContentBlockerModel[]
     */
    private array $contentBlockers = [];

    private ContentBlockerStyleConfig $contentBlockerStyleConfig;

    private array $exclusionList = [];

    private Language $language;

    private ScanRequestService $scanRequestService;

    private ServiceRepository $serviceRepository;

    /**
     * @var ServiceModel[]
     */
    private array $services = [];

    private WpFunction $wpFunction;

    public function __construct(
        ContentBlockerSettingsConfig $contentBlockerGeneralConfig,
        ContentBlockerStyleConfig $contentBlockerStyleConfig,
        ContentBlockerRepository $contentBlockerRepository,
        Language $language,
        ScanRequestService $scanRequestService,
        ServiceRepository $serviceRepository,
        WpFunction $wpFunction
    ) {
        $this->contentBlockerGeneralConfig = $contentBlockerGeneralConfig;
        $this->contentBlockerStyleConfig = $contentBlockerStyleConfig;
        $this->contentBlockerRepository = $contentBlockerRepository;
        $this->language = $language;
        $this->scanRequestService = $scanRequestService;
        $this->serviceRepository = $serviceRepository;
        $this->wpFunction = $wpFunction;
    }

    /**
     * @param null|string $htmlContent Due to an unknown error, the WordPress filter `the_content` may return `null`.
     *                                 We think it's a bug in WordPress`s excerpt_remove_footnotes() method in blocks.php.
     * @param null|mixed  $postId
     * @param null|mixed  $field
     */
    public function detectIframes(?string $htmlContent = '', $postId = null, $field = null): string
    {
        if ($htmlContent === null) {
            return '';
        }

        if ($this->wpFunction->isFeed() && $this->contentBlockerGeneralConfig->get()->removeIframesInFeeds) {
            return preg_replace(self::IFRAME_DETECTION_REGEX, '', $htmlContent);
        }

        return preg_replace_callback(
            self::IFRAME_DETECTION_REGEX,
            fn ($matches) => $this->handleIframeBlocking($matches[0]),
            $htmlContent,
        );
    }

    /**
     * Determines the appropriate content blocker based on a given URL by comparing it against registered content
     * blocker locations. Uses Levenshtein distance to find the best matching content blocker.
     *
     * @since 3.3.15 This method was private until Borlabs Cookie version 3.3.15
     *
     * @param string $url URL to check for content blocking
     *
     * @return null|ContentBlockerModel The matching content blocker or null if none found
     */
    public function determineContentBlockerByUrl(string $url): ?ContentBlockerModel
    {
        $urlInfo = parse_url($url);
        $urlToCompare = strtolower(($urlInfo['host'] ?? '') . ($urlInfo['path'] ?? '/'));
        $levensteinDistance = 0;
        $contentBlocker = null;

        foreach ($this->contentBlockers as $contentBlockerModel) {
            foreach ($contentBlockerModel->contentBlockerLocations  as $contentBlockerLocation) {
                $contentBlockerLocation = strtolower($contentBlockerLocation->hostname . '/' . ltrim($contentBlockerLocation->path, '/'));

                if (strpos($urlToCompare, $contentBlockerLocation) === false) {
                    continue;
                }

                $distance = levenshtein($urlToCompare, $contentBlockerLocation);

                if ($distance < $levensteinDistance || ($levensteinDistance === 0 && $contentBlocker === null)) {
                    $levensteinDistance = $distance;
                    $contentBlocker = $contentBlockerModel;
                }
            }
        }

        return $contentBlocker;
    }

    public function getContentBlockerByKey(string $key): ?ContentBlockerModel
    {
        return Searcher::findObject($this->contentBlockers, 'key', $key);
    }

    public function getContentBlockers(): array
    {
        return $this->contentBlockers;
    }

    public function handleContentBlocking(
        string $content,
        ?string $url = null,
        ?string $contentBlockerId = null,
        ?array $attributes = null
    ) {
        if (isset($url) && $this->isHostnameExcluded($url)) {
            return $content;
        }

        if ($this->wpFunction->isFeed() && $this->contentBlockerGeneralConfig->get()->removeIframesInFeeds) {
            return '';
        }

        /** @var ContentBlockerModel $contentBlocker */
        $contentBlocker = null;

        if (isset($contentBlockerId)) {
            $contentBlocker = Searcher::findObject($this->contentBlockers, 'key', $contentBlockerId);
        } elseif (isset($url)) {
            $contentBlocker = $this->determineContentBlockerByUrl($url);
        }

        // Fallback to default ContentBlocker
        if ($contentBlocker === null) {
            $contentBlocker = Searcher::findObject($this->contentBlockers, 'key', 'default');
        }

        // In case default ContentBlocker was disabled
        if ($contentBlocker === null) {
            return $content;
        }

        $contentBlocker = clone $contentBlocker;
        $attributes = array_merge($attributes ?? [], ['url' => $url,]);

        // Allow modification of the ContentBlocker model
        $contentBlocker = $this->wpFunction->applyFilter(
            'borlabsCookie/contentBlocker/blocking/afterDetermination/' . $contentBlocker->key,
            $contentBlocker,
            $attributes,
            $content,
        );
        // Allow modification of the content that is about to be blocked
        $content = $this->wpFunction->applyFilter(
            'borlabsCookie/contentBlocker/blocking/beforeBlocking/' . $contentBlocker->key,
            $content,
            $attributes,
            $contentBlocker,
        );

        $search = array_map(static fn ($value) => '{{ ' . $value . ' }}', array_column($contentBlocker->languageStrings->list, 'key'));
        $search[] = '{{ name }}';
        $search[] = '{{ previewImage }}';
        $search[] = '{{ serviceConsentButtonDisplayValue }}';
        $replace = array_column($contentBlocker->languageStrings->list, 'value');
        $replace[] = $contentBlocker->name;
        $replace[] = $contentBlocker->previewImage;
        $replace[] = isset($contentBlocker->serviceId) && isset($this->services[$contentBlocker->serviceId]) ? 'inherit' : 'none';

        $contentBlocker->previewHtml = str_replace($search, $replace, $contentBlocker->previewHtml);
        $encodedContent = base64_encode($content);
        $additionalCssClass = $this->contentBlockerStyleConfig->get()->useIndividualStyles ? ' brlbs-cmpnt-with-individual-styles' : '';

        $content = <<<EOT
        <div class="brlbs-cmpnt-container brlbs-cmpnt-content-blocker{$additionalCssClass}" data-borlabs-cookie-content-blocker-id="{$contentBlocker->key}" data-borlabs-cookie-content="{$encodedContent}">{$contentBlocker->previewHtml}</div>
EOT;

        // Allow modification of the content after blocking
        $content = $this->wpFunction->applyFilter(
            'borlabsCookie/contentBlocker/blocking/afterBlocking/' . $contentBlocker->key,
            $content,
            $attributes,
        );

        // Remove whitespace to avoid WordPress' automatic br- & p-tags
        return preg_replace('/[\s]+/mu', ' ', $content);
    }

    public function handleIframeBlocking(string $iframeTag): string
    {
        if (strpos($iframeTag, 'data-borlabs-cookie-do-not-block-iframe') !== false) {
            return $iframeTag;
        }

        // Replace data-src & data-lazy-src attributes with src attributes
        $iframeTag = str_replace(['data-src=', 'data-lazy-src=', 'data-attr-src=',], ['src=', 'src=', 'src='], $iframeTag);

        $srcMatches = null;
        preg_match('/src=([\'"])(.+?)\1/i', $iframeTag, $srcMatches);

        if (empty($srcMatches[0]) || $srcMatches[2] === 'about:blank') {
            return $iframeTag;
        }

        if ($this->wpFunction->applyFilter(
            'borlabsCookie/contentBlocker/skipIframeBlocking',
            false,
            [
                'iframeTag' => $iframeTag,
                'srcMatches' => $srcMatches,
            ],
        )) {
            return $iframeTag;
        }

        return $this->handleContentBlocking($iframeTag, $srcMatches[2]);
    }

    public function handleOembedBlocking(string $content): string
    {
        if (preg_match('/<iframe.+?src=[\'"](.+?)[\'"].*?><\/iframe>/i', $content)) {
            return $this->handleIframeBlocking($content);
        }

        return $this->handleContentBlocking($content);
    }

    public function init()
    {
        $this->contentBlockers = [];
        $this->exclusionList = [];
        $this->services = [];

        if ($this->scanRequestService->noContentBlockers()
            || $this->wpFunction->applyFilter('borlabsCookie/contentBlocker/skipInitialization', null) === true
        ) {
            return;
        }

        $siteHost = parse_url($this->wpFunction->getHomeUrl(), PHP_URL_HOST);
        $siteHost = is_string($siteHost) ? strtolower($siteHost) : null;

        if (!empty($siteHost)) {
            $this->exclusionList[$siteHost] = $siteHost;
        }

        foreach ($this->contentBlockerGeneralConfig->get()->excludedHostnames as $exclusion) {
            $exclusion = strtolower($exclusion);
            $this->exclusionList[$exclusion] = $exclusion;
        }

        $contentBlockerModels = $this->contentBlockerRepository->find(
            ['language' => $this->language->getCurrentLanguageCode(),],
            [],
            [],
            ['contentBlockerLocations'],
        );

        foreach ($contentBlockerModels as $contentBlockerModel) {
            if ($contentBlockerModel->status === true) {
                if ($this->scanRequestService->noDefaultContentBlocker() && $contentBlockerModel->key === 'default') {
                    continue;
                }

                $this->contentBlockers[] = $contentBlockerModel;

                continue;
            }

            foreach ($contentBlockerModel->contentBlockerLocations as $contentBlockerLocation) {
                $exclusion = strtolower($contentBlockerLocation->hostname);
                $this->exclusionList[$exclusion] = $exclusion;
            }
        }

        $this->exclusionList = $this->wpFunction->applyFilter(
            'borlabsCookie/contentBlocker/modifyExcludedHostnames',
            $this->exclusionList,
        );
        $this->exclusionList = is_array($this->exclusionList)
            ? Sanitizer::hostArray($this->exclusionList)
            : [];

        $serviceModels = $this->serviceRepository->find(
            ['language' => $this->language->getCurrentLanguageCode(),],
            [],
            [],
            [],
        );

        foreach ($serviceModels as $serviceModel) {
            if ($serviceModel->status === true) {
                $this->services[$serviceModel->id] = $serviceModel;
            }
        }
    }

    private function isHostnameExcluded(string $url): bool
    {
        $urlInfo = parse_url(
            strtolower($url),
        );
        $hostname = $urlInfo['host'] ?? false;

        if ($hostname === false) {
            return false;
        }

        foreach ($this->exclusionList as $exclusion) {
            if (strpos($hostname, $exclusion) !== false) {
                return true;
            }
        }

        return false;
    }
}
