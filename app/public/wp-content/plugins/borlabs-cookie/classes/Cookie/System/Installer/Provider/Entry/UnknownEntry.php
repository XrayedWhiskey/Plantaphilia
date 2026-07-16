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

namespace Borlabs\Cookie\System\Installer\Provider\Entry;

use Borlabs\Cookie\Adapter\WpFunction;
use Borlabs\Cookie\Localization\DefaultLocalizationStrings;
use Borlabs\Cookie\Model\Provider\ProviderModel;
use Borlabs\Cookie\System\Installer\DefaultEntryInterface;
use Borlabs\Cookie\System\Language\Language;

final class UnknownEntry implements DefaultEntryInterface
{
    private DefaultLocalizationStrings $defaultLocalizationStrings;

    private Language $language;

    private WpFunction $wpFunction;

    public function __construct(
        DefaultLocalizationStrings $defaultLocalizationStrings,
        Language $language,
        WpFunction $wpFunction
    ) {
        $this->defaultLocalizationStrings = $defaultLocalizationStrings;
        $this->language = $language;
        $this->wpFunction = $wpFunction;
    }

    public function getDefaultModel(?string $languageCode = null): ProviderModel
    {
        if ($languageCode === null) {
            $languageCode = $this->language->getSelectedLanguageCode();
        }

        $model = new ProviderModel();
        $model->address = '-';
        $model->borlabsServiceProviderKey = 'unknown';
        $model->key = 'unknown';
        $model->description = $this->defaultLocalizationStrings->get()['provider']['unknownDescription'];
        $model->language = $languageCode;
        $model->name = $this->defaultLocalizationStrings->get()['provider']['unknownName'];
        $privacyUrl = $this->wpFunction->getPrivacyPolicyUrl();
        $model->privacyUrl = $privacyUrl !== '' ? $privacyUrl : $this->wpFunction->getHomeUrl();
        $model->undeletable = true;

        return $model;
    }
}
