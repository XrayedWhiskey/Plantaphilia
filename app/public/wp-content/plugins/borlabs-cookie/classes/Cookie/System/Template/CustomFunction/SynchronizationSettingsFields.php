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

namespace Borlabs\Cookie\System\Template\CustomFunction;

use Borlabs\Cookie\Dependencies\Twig\TwigFunction;
use Borlabs\Cookie\Dto\System\KeyValueDto;
use Borlabs\Cookie\DtoList\System\KeyValueDtoList;
use Borlabs\Cookie\Localization\GlobalLocalizationStrings;
use Borlabs\Cookie\System\Language\Language;
use Borlabs\Cookie\System\Template\Template;

final class SynchronizationSettingsFields
{
    private GlobalLocalizationStrings $globalLocalizationStrings;

    private Language $language;

    private Template $template;

    public function __construct(GlobalLocalizationStrings $globalLocalizationStrings, Language $language, Template $template)
    {
        $this->globalLocalizationStrings = $globalLocalizationStrings;
        $this->language = $language;
        $this->template = $template;
    }

    public function register()
    {
        $this->template->getTwig()->addFunction(
            new TwigFunction(
                'synchronizationSettingsFields',
                function (
                    KeyValueDtoList $languageList,
                    array $configuration = []
                ) {
                    $configuration = array_merge(
                        [
                            'collectionName' => '',
                            'hasRepository' => false,
                            'isCreateAction' => false,
                            'withOverwriteConfigurationOption' => true,
                            'withOverwriteTranslationOption' => true,
                        ],
                        $configuration,
                    );

                    $languageListWithoutActiveLanguage = new KeyValueDtoList();

                    foreach ($languageList->list as $language) {
                        if ($language->key !== $this->language->getSelectedLanguageCode()) {
                            $languageListWithoutActiveLanguage->add(new KeyValueDto($language->key, $language->value));
                        }
                    }

                    $languageList = $languageListWithoutActiveLanguage;

                    return $this->template->getEngine()->render(
                        'system/synchronization-settings-fields.html.twig',
                        [
                            'checkboxes' => $languageList->list, // TODO rename
                            'checkedKeys' => array_column($languageList->list, 'key', 'key'), // TODO rename
                            'collectionName' => $configuration['collectionName'],
                            'hasRepository' => $configuration['hasRepository'],
                            'isCreateAction' => $configuration['isCreateAction'],
                            'localized' => [
                                'global' => $this->globalLocalizationStrings->get(),
                            ],
                            'withOverwriteConfigurationOption' => $configuration['withOverwriteConfigurationOption'],
                            'withOverwriteTranslationOption' => $configuration['withOverwriteTranslationOption'],
                        ],
                    );
                },
            ),
        );
    }
}
