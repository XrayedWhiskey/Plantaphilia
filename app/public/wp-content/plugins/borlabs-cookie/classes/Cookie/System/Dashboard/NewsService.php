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

namespace Borlabs\Cookie\System\Dashboard;

use Borlabs\Cookie\ApiClient\NewsApiClient;
use Borlabs\Cookie\Dto\News\NewsDto;
use Borlabs\Cookie\DtoList\News\NewsListDto;
use Borlabs\Cookie\Exception\ApiClient\NewsApiClientException;
use Borlabs\Cookie\Exception\GenericException;
use Borlabs\Cookie\Support\Converter;
use Borlabs\Cookie\System\Language\Language;
use Borlabs\Cookie\System\Option\Option;

final class NewsService
{
    private Language $language;

    private NewsApiClient $newsApiClient;

    private Option $option;

    public function __construct(
        Language $language,
        NewsApiClient $newsApiClient,
        Option $option
    ) {
        $this->language = $language;
        $this->newsApiClient = $newsApiClient;
        $this->option = $option;
    }

    public function getNews()
    {
        $language = $this->language->getSelectedLanguageCode();
        $newsList = $this->option->getGlobal('News', [])->value;

        if (!isset($newsList->list)) {
            $newsList = new NewsListDto();
        }

        $languageFilteredNews = new NewsListDto(
            array_filter(
                $newsList->list,
                fn (object $newsEntry) => $newsEntry->language === $language,
            ),
        );

        if (count($languageFilteredNews->list) === 0) {
            $languageFilteredNews = new NewsListDto(
                array_filter(
                    $newsList->list,
                    fn (object $newsEntry) => $newsEntry->language === 'en',
                ),
            );
        }

        array_walk($languageFilteredNews->list, [$this, 'parseMessage']);

        return $languageFilteredNews;
    }

    public function updateNews(): void
    {
        $newsLastCheck = $this->option->getGlobal('NewsLastCheck', '00000000');

        if (date('Ymd') > $newsLastCheck->value) {
            try {
                $newsList = $this->newsApiClient->requestNews();
                $this->option->setGlobal('News', $newsList);
                $this->option->setGlobal('NewsLastCheck', date('Ymd'));
            } catch (NewsApiClientException $e) {
            } catch (GenericException $e) {
            }
        }
    }

    private function parseMessage(NewsDto $news)
    {
        $news->message = Converter::markdownToHtml($news->message);
    }
}
