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

class StillInUseModelDeletionException extends TranslatedException
{
    protected const LOCALIZATION_STRING_CLASS = GlobalLocalizationStrings::class;

    public AbstractModel $blockingModel;

    public AbstractModel $modelToDelete;

    private string $blockingModelLabel;

    private string $modelToDeleteLabel;

    public function __construct(AbstractModel $modelToDelete, AbstractModel $blockingModel, string $modelToDeleteLabel, string $blockingModelLabel)
    {
        $this->modelToDelete = $modelToDelete;
        $this->blockingModel = $blockingModel;
        $this->modelToDeleteLabel = $modelToDeleteLabel;
        $this->blockingModelLabel = $blockingModelLabel;
        parent::__construct('modelStillInUse');
    }

    public function getTranslatedMessage(): string
    {
        $modelName = ModelLocalizationStrings::get()['models'][get_class($this->modelToDelete)] ?? get_class($this->modelToDelete);
        $blockingModelName = ModelLocalizationStrings::get()['models'][get_class($this->blockingModel)] ?? get_class($this->blockingModel);

        $this->context = [
            'modelLabel' => $this->modelToDeleteLabel,
            'modelName' => $modelName,
            'blockingModelLabel' => $this->blockingModelLabel,
            'blockingModelName' => $blockingModelName,
        ];

        return parent::getTranslatedMessage();
    }
}
