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

use Borlabs\Cookie\Localization\Service\ServiceCookieCreateEditLocalizationStrings;
use Borlabs\Cookie\System\Message\MessageManager;
use Borlabs\Cookie\Validator\Validator;

/**
 * Class ServiceValidator.
 */
final class ServiceCookieValidator
{
    private Validator $validator;

    public function __construct(
        MessageManager $message
    ) {
        $this->validator = new Validator($message, true);
    }

    public function isValid(array $postData): bool
    {
        $localization = ServiceCookieCreateEditLocalizationStrings::get();

        $this->validator->isNotEmptyString($localization['field']['lifetime'], $postData['lifetime'] ?? '');
        $this->validator->isNotEmptyString($localization['field']['name'], $postData['name'] ?? '');

        return $this->validator->isValid();
    }
}
