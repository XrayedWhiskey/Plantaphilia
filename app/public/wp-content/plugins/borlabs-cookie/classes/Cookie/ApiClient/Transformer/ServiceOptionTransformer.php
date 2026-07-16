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
use Borlabs\Cookie\Enum\Service\ServiceOptionEnum;
use Borlabs\Cookie\Model\Service\ServiceOptionModel;

final class ServiceOptionTransformer
{
    use TranslationListTrait;

    public function toModel(object $serviceOption, string $languageCode): ServiceOptionModel
    {
        $translation = $this->getTranslation($serviceOption->translations, $languageCode);

        $model = new ServiceOptionModel();
        $model->language = $languageCode;
        $model->description = $translation->description;
        $model->type = ServiceOptionEnum::fromValue($serviceOption->type);

        return $model;
    }
}
