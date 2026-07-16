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

namespace Borlabs\Cookie\System\PageSelection;

use Borlabs\Cookie\Adapter\WpDb;
use Borlabs\Cookie\Adapter\WpFunction;
use Borlabs\Cookie\ApiClient\PageSelectionApiClient;
use Borlabs\Cookie\Dto\System\ExternalFileDto;
use Borlabs\Cookie\Enum\PageSelection\KeywordTypeEnum;
use Borlabs\Cookie\Exception\GenericException;
use Borlabs\Cookie\System\FileSystem\FileManager;
use Borlabs\Cookie\System\License\License;

final class PageSelectionService
{
    public const KEYWORD_LIST_FILE_NAME = 'page-selection-keyword-list.json';

    private FileManager $fileManager;

    private License $license;

    private PageSelectionApiClient $pageSelectionApiClient;

    private WpDb $wpdb;

    private WpFunction $wpFunction;

    public function __construct(
        FileManager $fileManager,
        License $license,
        PageSelectionApiClient $pageSelectionApiClient,
        WpDb $wpdb,
        WpFunction $wpFunction
    ) {
        $this->fileManager = $fileManager;
        $this->license = $license;
        $this->pageSelectionApiClient = $pageSelectionApiClient;
        $this->wpdb = $wpdb;
        $this->wpFunction = $wpFunction;
    }

    /**
     * @throws \Borlabs\Cookie\Exception\ApiClient\ApiClientException
     * @throws \Borlabs\Cookie\Exception\ApiClient\ApiClientInvalidLicenseException
     * @throws \Borlabs\Cookie\Exception\HttpClient\ConnectionErrorException
     */
    public function downloadKeywordList(): bool
    {
        if ($this->license->get() === null) {
            return false;
        }

        $attachment = $this->pageSelectionApiClient->requestPageSelectionKeywordListAttachmentData();

        return $this->fileManager->storeExternalFileGlobally(new ExternalFileDto($attachment->downloadUrl), self::KEYWORD_LIST_FILE_NAME)
            ? true
            : false;
    }

    public function findPageUrlByKeywordType(KeywordTypeEnum $keywordType, string $languageCode): ?string
    {
        $keywordList = $this->getListByKeywordTypeAndLanguage($keywordType, $languageCode);

        if ($keywordList === null) {
            return null;
        }

        $priorityOrderedKeywords = $this->buildPriorityOrderedKeywords($keywordList);

        foreach ($priorityOrderedKeywords as $keyword) {
            $matchingPage = $this->findBestMatchingPage($keyword);

            if ($matchingPage !== null) {
                return $this->wpFunction->getPermalink((int) $matchingPage->ID);
            }
        }

        return null;
    }

    public function getListByKeywordTypeAndLanguage(KeywordTypeEnum $keywordType, string $languageCode): ?array
    {
        $keywordList = $this->getKeywordList();

        if ($keywordList === null) {
            return null;
        }

        if (!isset($keywordList->{$languageCode}->{$keywordType->value})) {
            return null;
        }

        return $keywordList->{$languageCode}->{$keywordType->value};
    }

    private function buildPriorityOrderedKeywords(array $keywordList): array
    {
        $priorityMap = [];

        foreach ($keywordList as $keywordData) {
            $priorityMap[$keywordData->p] = $keywordData->k;
        }

        krsort($priorityMap);

        return $priorityMap;
    }

    private function findBestMatchingPage(string $keyword): ?object
    {
        $searchResults = $this->wpdb->get_results(
            $this->wpdb->prepare(
                '
            SELECT `ID`, `post_name` FROM
                `' . $this->wpdb->posts . '`
            WHERE
                `post_type` = \'page\'
                AND `post_status` = \'publish\'
                AND `post_name` LIKE \'%s\'
           ',
                [
                    '%' . $keyword . '%',
                ],
            ),
        );

        if (!is_array($searchResults) || count($searchResults) === 0) {
            return null;
        }

        return $this->findExactOrFirstMatch($searchResults, $keyword);
    }

    private function findExactOrFirstMatch(array $pages, string $keyword): ?object
    {
        $firstMatch = null;

        foreach ($pages as $page) {
            if ($page->post_name === $keyword) {
                return $page; // Exact match takes precedence
            }

            if ($firstMatch === null) {
                $firstMatch = $page; // Remember first partial match
            }
        }

        return $firstMatch;
    }

    private function getKeywordList(): ?object
    {
        $file = $this->fileManager->getGloballyStoredFileContent(self::KEYWORD_LIST_FILE_NAME);

        if ($file === null) {
            try {
                $this->downloadKeywordList();
            } catch (GenericException $e) {
            }

            $file = $this->fileManager->getGloballyStoredFileContent(self::KEYWORD_LIST_FILE_NAME);

            if ($file === null) {
                return null;
            }
        }

        return json_decode($file, false);
    }
}
