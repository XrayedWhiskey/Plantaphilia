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

namespace Borlabs\Cookie\Validator\Service;

use Borlabs\Cookie\Localization\Service\ServiceOptionCreateEditLocalizationStrings;
use Borlabs\Cookie\System\Message\MessageManager;
use Borlabs\Cookie\Validator\Validator;

/**
 * Class ServiceValidator.
 */
final class ServiceOptionValidator
{
    private Validator $validator;

    public function __construct(
        MessageManager $message
    ) {
        $this->validator = new Validator($message, true);
    }

    public function isValid(array $postData): bool
    {
        $localization = ServiceOptionCreateEditLocalizationStrings::get();

        $this->validator->isNotEmptyString($localization['field']['description'], $postData['description'] ?? '');

        return $this->validator->isValid();
    }
}
