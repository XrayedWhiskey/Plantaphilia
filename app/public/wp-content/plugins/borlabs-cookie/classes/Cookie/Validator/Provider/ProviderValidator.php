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

namespace Borlabs\Cookie\Validator\Provider;

use Borlabs\Cookie\Adapter\WpFunction;
use Borlabs\Cookie\Localization\Provider\ProviderEditLocalizationStrings;
use Borlabs\Cookie\Repository\Provider\ProviderRepository;
use Borlabs\Cookie\System\Message\MessageManager;
use Borlabs\Cookie\Validator\Validator;

final class ProviderValidator
{
    private ProviderRepository $providerRepository;

    private Validator $validator;

    private WpFunction $wpFunction;

    public function __construct(
        MessageManager $message,
        ProviderRepository $providerRepository
    ) {
        $this->providerRepository = $providerRepository;
        $this->validator = new Validator($message, true);
        $this->wpFunction = new WpFunction();
    }

    public function isValid(array $postData, string $languageCode): bool
    {
        $localization = ProviderEditLocalizationStrings::get();

        if ($postData['id'] === '-1') {
            $this->validator->isMinLengthCertainCharacters(
                $localization['field']['key'],
                $postData['key'],
                3,
                'a-z\-\_',
            );
            $this->validator->isUniqueKey(
                $localization['field']['key'],
                $postData['key'],
                'key',
                $this->providerRepository,
                $languageCode,
            );
        }

        $this->validator->isNotEmptyString($localization['field']['address'], $postData['address']);
        $this->validator->isNotEmptyString($localization['field']['description'], $postData['description']);
        $this->validator->isNotEmptyString($localization['field']['name'], $postData['name']);
        $this->validator->isUrl($localization['field']['privacyUrl'], $postData['privacyUrl']);

        $this->validator = $this->wpFunction->applyFilter('borlabsCookie/provider/validate', $this->validator, $postData);

        return $this->validator->isValid();
    }
}
