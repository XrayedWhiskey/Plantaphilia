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

use Borlabs\Cookie\ApiClient\Transformer\Traits\TranslationListTrait;
use Borlabs\Cookie\Model\Provider\ProviderModel;

final class ProviderTransformer
{
    use TranslationListTrait;

    public function toModel(object $provider, string $borlabsServicePackageKey, string $languageCode): ProviderModel
    {
        $translation = count($provider->translations) ? $this->getTranslation($provider->translations, $languageCode) : null;
        $model = new ProviderModel();
        $model->address = $provider->address;
        $model->borlabsServicePackageKey = $borlabsServicePackageKey;
        $model->borlabsServiceProviderKey = $provider->key;
        $model->cookieUrl = $translation->cookieUrl ?? '';
        $model->description = $translation->description ?? '';
        $model->iabVendorId = $provider->iabVendorId ?? null;
        $model->key = $provider->key;
        $model->language = $languageCode;
        $model->name = $provider->name;
        $model->optOutUrl = $translation->optOutUrl ?? '';
        $model->partners = $provider->partners;
        $model->privacyUrl = $translation->privacyUrl ?? '';
        $model->undeletable = true;

        return $model;
    }
}
