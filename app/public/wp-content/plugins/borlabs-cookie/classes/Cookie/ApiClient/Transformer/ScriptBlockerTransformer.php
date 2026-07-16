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
use Borlabs\Cookie\Model\ScriptBlocker\ScriptBlockerModel;

final class ScriptBlockerTransformer
{
    public function toModel(object $scriptBlocker, string $borlabsServicePackageKey): ScriptBlockerModel
    {
        $handles = new KeyValueDtoList();

        foreach ($scriptBlocker->config->handles as $key => $value) {
            $handles->add(new KeyValueDto((string) $key, $value));
        }

        $phrases = new KeyValueDtoList();

        foreach ($scriptBlocker->config->phrases as $key => $value) {
            $phrases->add(new KeyValueDto((string) $key, $value !== '' ? $value : $key));
        }

        $onExist = new KeyValueDtoList();

        foreach ($scriptBlocker->config->onExist as $key => $value) {
            $onExist->add(new KeyValueDto($key, $value !== '' ? $value : $key));
        }

        $model = new ScriptBlockerModel();
        $model->borlabsServicePackageKey = $borlabsServicePackageKey;
        $model->handles = $handles;
        $model->key = $scriptBlocker->key;
        $model->name = $scriptBlocker->name;
        $model->onExist = $onExist;
        $model->phrases = $phrases;
        $model->status = true;
        $model->undeletable = true;

        return $model;
    }
}
