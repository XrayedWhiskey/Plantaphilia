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

namespace Borlabs\Cookie\System\Translator;

use Borlabs\Cookie\ApiClient\TranslatorApiClient;
use Borlabs\Cookie\Dto\Translator\LanguageSpecificKeyValueListItemDto;
use Borlabs\Cookie\Dto\Translator\TargetLanguageDto;
use Borlabs\Cookie\DtoList\System\KeyValueDtoList;
use Borlabs\Cookie\DtoList\Translator\LanguageSpecificKeyValueDtoList;
use Borlabs\Cookie\DtoList\Translator\TargetLanguageEnumDtoList;
use Borlabs\Cookie\Enum\Translator\SourceLanguageEnum;
use Borlabs\Cookie\Enum\Translator\TargetLanguageEnum;
use Borlabs\Cookie\Exception\ApiClient\TranslatorApiClientException;
use Borlabs\Cookie\Exception\HttpClient\ConnectionErrorException;
use Borlabs\Cookie\Exception\HttpClient\ServerErrorException;
use Borlabs\Cookie\System\Log\Log;

final class TranslatorService
{
    private Log $log;

    private TranslatorApiClient $translatorApiClient;

    public function __construct(Log $log, TranslatorApiClient $translatorApiClient)
    {
        $this->log = $log;
        $this->translatorApiClient = $translatorApiClient;
    }

    /**
     * @throws \Borlabs\Cookie\Exception\ApiClient\ApiClientInvalidLicenseException
     */
    public function translate(
        string $sourceLanguage,
        array $targetLanguages,
        KeyValueDtoList $sourceTexts
    ): ?LanguageSpecificKeyValueDtoList {
        $sourceLanguage = strtoupper(substr($sourceLanguage, 0, 2));

        if (SourceLanguageEnum::hasValue($sourceLanguage) === false) {
            return null;
        }

        if (count($sourceTexts->list) === 0) {
            return null;
        }

        $mId = 1;
        $placeholderMap = [];

        foreach ($sourceTexts->list as $sourceText) {
            $sourceText->value = $this->replacePlaceholdersWithMustacheTags($sourceText->value, $mId, $placeholderMap);
        }

        $list = new TargetLanguageEnumDtoList();

        foreach ($targetLanguages as $languageCode) {
            $languageCode = strtoupper(substr($languageCode, 0, 2));

            if (TargetLanguageEnum::hasKey($languageCode) === true) {
                $list->add(new TargetLanguageDto(TargetLanguageEnum::fromKey($languageCode)));
            }
        }

        $targetLanguages = $list;

        try {
            $translations = $this->translatorApiClient->translate(
                SourceLanguageEnum::fromValue($sourceLanguage),
                $targetLanguages,
                $sourceTexts,
            );
        } catch (TranslatorApiClientException $e) {
            return null;
        } catch (ConnectionErrorException $e) {
            $this->log->error('Connection exception in TranslatorService', [
                'exceptionMessage' => $e->getTranslatedMessage(),
            ]);

            return null;
        } catch (ServerErrorException $e) {
            $this->log->error('Server error exception in TranslatorService', [
                'exceptionMessage' => $e->getMessage(),
            ]);

            return null;
        }

        /**
         * @var LanguageSpecificKeyValueListItemDto $translation
         */
        foreach ($translations->list as $translation) {
            /**
             * @var KeyValueDtoList $translationItem
             */
            foreach ($translation->translations->list as &$translationItem) {
                $translationItem->value = $this->restorePlaceholdersFromMustaceTags($translationItem->value, $placeholderMap);
            }
        }

        return $translations;
    }

    private function replacePlaceholdersWithMustacheTags(string $text, int &$id, array &$map): string
    {
        return preg_replace_callback('/{{\s*(.*?)\s*}}/', function ($matches) use (&$map, &$id) {
            $placeholder = $matches[0]; // e.g. {{ name }}
            $map[$id] = $placeholder;

            return '<m id="' . $id++ . '"></m>';
        }, $text);
    }

    private function restorePlaceholdersFromMustaceTags(string $text, array $map): string
    {
        return preg_replace_callback('/<m id="(\d+)"><\/m>/', function ($matches) use ($map) {
            $id = (int) $matches[1];

            return $map[$id] ?? $matches[0]; // fallback to original tag if ID is not found
        }, $text);
    }
}
