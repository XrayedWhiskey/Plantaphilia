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
use Borlabs\Cookie\Enum\Service\CookiePurposeEnum;
use Borlabs\Cookie\Enum\Service\CookieTypeEnum;
use Borlabs\Cookie\Model\Service\ServiceCookieModel;

final class ServiceCookieTransformer
{
    use TranslationListTrait;

    public function toModel(object $serviceCookie, string $languageCode): ServiceCookieModel
    {
        $translation = $this->getTranslation($serviceCookie->translations, $languageCode);

        $model = new ServiceCookieModel();
        $model->description = $translation->description;
        $model->hostname = $serviceCookie->location->hostname ?? '#';
        $model->lifetime = $translation->lifetime;
        $model->name = $serviceCookie->name;
        $model->path = $serviceCookie->location->path ?? '/';
        $model->purpose = CookiePurposeEnum::fromValue($serviceCookie->purpose);
        $model->type = CookieTypeEnum::fromValue($serviceCookie->type);

        return $model;
    }
}
