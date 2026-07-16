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
use Borlabs\Cookie\DtoList\System\KeyValueDtoList;
use Borlabs\Cookie\Model\StyleBlocker\StyleBlockerModel;

final class StyleBlockerTransformer
{
    public function toModel(object $styleBlocker, string $borlabsServicePackageKey): StyleBlockerModel
    {
        $handles = new KeyValueDtoList();

        foreach ($styleBlocker->config->handles as $key => $value) {
            $handles->add(new KeyValueDto((string) $key, $value));
        }

        $phrases = new KeyValueDtoList();

        foreach ($styleBlocker->config->phrases as $key => $value) {
            $phrases->add(new KeyValueDto((string) $key, $value !== '' ? $value : $key));
        }

        $model = new StyleBlockerModel();
        $model->borlabsServicePackageKey = $borlabsServicePackageKey;
        $model->handles = $handles;
        $model->key = $styleBlocker->key;
        $model->name = $styleBlocker->name;
        $model->phrases = $phrases;
        $model->status = true;
        $model->undeletable = true;

        return $model;
    }
}
