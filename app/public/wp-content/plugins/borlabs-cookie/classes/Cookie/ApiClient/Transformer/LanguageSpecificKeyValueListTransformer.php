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

namespace Borlabs\Cookie\ApiClient\Transformer;

use Borlabs\Cookie\Dto\System\KeyValueDto;
use Borlabs\Cookie\Dto\Translator\LanguageSpecificKeyValueListItemDto;
use Borlabs\Cookie\DtoList\System\KeyValueDtoList;
use Borlabs\Cookie\DtoList\Translator\LanguageSpecificKeyValueDtoList;

final class LanguageSpecificKeyValueListTransformer
{
    public function toDto(object $translations, KeyValueDtoList $sourceTexts): LanguageSpecificKeyValueDtoList
    {
        $translationsPerLanguage = [];
        $translations = (array) $translations;

        foreach ($sourceTexts->list as $sourceText) {
            foreach ($translations as $translation) {
                if ($translation->sourceText === $sourceText->value) {
                    foreach ($translation->translatedTexts as $translatedText) {
                        $languageCode = strtolower(substr($translatedText->language, 0, 2));

                        if (!isset($translationsPerLanguage[$languageCode])) {
                            $translationsPerLanguage[$languageCode] = new KeyValueDtoList();
                        }

                        $translationsPerLanguage[$languageCode]->add(
                            new KeyValueDto(
                                $sourceText->key,
                                $translatedText->text,
                            ),
                        );
                    }
                }
            }
        }

        $list = new LanguageSpecificKeyValueDtoList();

        foreach ($translationsPerLanguage as $languageCode => $translationsList) {
            $list->add(new LanguageSpecificKeyValueListItemDto($languageCode, $translationsList));
        }

        return $list;
    }
}
