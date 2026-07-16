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

namespace Borlabs\Cookie\Exception;

use Borlabs\Cookie\Localization\GlobalLocalizationStrings;
use Borlabs\Cookie\Localization\System\ModelLocalizationStrings;
use Borlabs\Cookie\Model\AbstractModel;

class ModelDeletionException extends TranslatedException
{
    protected const LOCALIZATION_STRING_CLASS = GlobalLocalizationStrings::class;

    private AbstractModel $model;

    private string $modelLabel;

    public function __construct(AbstractModel $model, string $modelLabel)
    {
        $this->model = $model;
        $this->modelLabel = $modelLabel;
        parent::__construct('modelDeleteFailed');
    }

    public function getTranslatedMessage(): string
    {
        $modelName = ModelLocalizationStrings::get()['models'][get_class($this->model)] ?? get_class($this->model);

        $this->context = [
            'modelLabel' => $this->modelLabel,
            'modelName' => $modelName,
        ];

        return parent::getTranslatedMessage();
    }
}
