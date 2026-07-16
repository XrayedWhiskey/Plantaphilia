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

namespace Borlabs\Cookie\System\Installer\ServiceGroup\Entry;

use Borlabs\Cookie\Localization\DefaultLocalizationStrings;
use Borlabs\Cookie\Model\ServiceGroup\ServiceGroupModel;
use Borlabs\Cookie\System\Installer\DefaultEntryInterface;
use Borlabs\Cookie\System\Language\Language;

final class UnclassifiedEntry implements DefaultEntryInterface
{
    private DefaultLocalizationStrings $defaultLocalizationStrings;

    private Language $language;

    public function __construct(DefaultLocalizationStrings $defaultLocalizationStrings, Language $language)
    {
        $this->defaultLocalizationStrings = $defaultLocalizationStrings;
        $this->language = $language;
    }

    public function getDefaultModel(?string $languageCode = null): ServiceGroupModel
    {
        if ($languageCode === null) {
            $languageCode = $this->language->getSelectedLanguageCode();
        }

        $model = new ServiceGroupModel();
        $model->description = $this->defaultLocalizationStrings->get()['serviceGroup']['unclassifiedDescription'];
        $model->key = 'unclassified';
        $model->language = $languageCode;
        $model->name = $this->defaultLocalizationStrings->get()['serviceGroup']['unclassifiedName'];
        $model->position = 5;
        $model->preSelected = false;
        $model->status = true;
        $model->undeletable = true;

        return $model;
    }
}
