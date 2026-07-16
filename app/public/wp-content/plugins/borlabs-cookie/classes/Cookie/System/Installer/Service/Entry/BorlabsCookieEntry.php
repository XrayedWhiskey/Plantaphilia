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

namespace Borlabs\Cookie\System\Installer\Service\Entry;

use Borlabs\Cookie\Enum\Service\CookiePurposeEnum;
use Borlabs\Cookie\Enum\Service\CookieTypeEnum;
use Borlabs\Cookie\Localization\DefaultLocalizationStrings;
use Borlabs\Cookie\Model\Service\ServiceCookieModel;
use Borlabs\Cookie\Model\Service\ServiceModel;
use Borlabs\Cookie\Repository\Provider\ProviderRepository;
use Borlabs\Cookie\Repository\ServiceGroup\ServiceGroupRepository;
use Borlabs\Cookie\System\Installer\DefaultEntryInterface;
use Borlabs\Cookie\System\Language\Language;
use Borlabs\Cookie\System\Service\ServiceDefaultSettingsFieldManager;

final class BorlabsCookieEntry implements DefaultEntryInterface
{
    private DefaultLocalizationStrings $defaultLocalizationStrings;

    private Language $language;

    private ProviderRepository $providerRepository;

    private ServiceDefaultSettingsFieldManager $serviceDefaultSettingsFieldManager;

    private ServiceGroupRepository $serviceGroupRepository;

    public function __construct(
        DefaultLocalizationStrings $defaultLocalizationStrings,
        Language $language,
        ProviderRepository $providerRepository,
        ServiceDefaultSettingsFieldManager $serviceDefaultSettingsFieldManager,
        ServiceGroupRepository $serviceGroupRepository
    ) {
        $this->defaultLocalizationStrings = $defaultLocalizationStrings;
        $this->language = $language;
        $this->serviceDefaultSettingsFieldManager = $serviceDefaultSettingsFieldManager;
        $this->providerRepository = $providerRepository;
        $this->serviceGroupRepository = $serviceGroupRepository;
    }

    public function getDefaultModel(?string $languageCode = null): ServiceModel
    {
        if ($languageCode === null) {
            $languageCode = $this->language->getSelectedLanguageCode();
        }

        $provider = $this->providerRepository->getByBorlabsServiceProviderKey('default');
        $serviceGroup = $this->serviceGroupRepository->getByKey('essential');
        $model = new ServiceModel();
        $model->description = $this->defaultLocalizationStrings->get()['service']['borlabsCookieDescription'];
        $model->key = 'borlabs-cookie';
        $model->language = $languageCode;
        $model->name = $this->defaultLocalizationStrings->get()['service']['borlabsCookieName'];
        $model->providerId = $provider->id;
        $model->serviceCookies = $this->getDefaultServiceCookies();
        $model->serviceGroup = $serviceGroup;
        $model->settingsFields = $this->serviceDefaultSettingsFieldManager->get($languageCode);
        $model->status = true;
        $model->undeletable = true;

        return $model;
    }

    /**
     * @return ServiceCookieModel[]
     */
    public function getDefaultServiceCookies(): array
    {
        $serviceCookies = [];

        $defaultCookie = new ServiceCookieModel();
        $defaultCookie->description = $this->defaultLocalizationStrings->get()['service']['borlabsCookieServiceCookieDescription'];
        $defaultCookie->hostname = '#';
        $defaultCookie->lifetime = $this->defaultLocalizationStrings->get()['service']['borlabsCookieServiceCookieLifetime'];
        $defaultCookie->name = 'borlabs-cookie';
        $defaultCookie->path = '/';
        $defaultCookie->purpose = CookiePurposeEnum::FUNCTIONAL();
        $defaultCookie->type = CookieTypeEnum::HTTP();
        $serviceCookies[] = $defaultCookie;

        return $serviceCookies;
    }
}
