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

use Borlabs\Cookie\Adapter\WpFunction;
use Borlabs\Cookie\Localization\Service\ServiceCreateEditLocalizationStrings;
use Borlabs\Cookie\Repository\Service\ServiceRepository;
use Borlabs\Cookie\System\Message\MessageManager;
use Borlabs\Cookie\Validator\Validator;

/**
 * Class ServiceValidator.
 */
final class ServiceValidator
{
    private ServiceRepository $serviceRepository;

    private Validator $validator;

    private WpFunction $wpFunction;

    public function __construct(
        MessageManager $messageManager,
        ServiceRepository $serviceRepository,
        WpFunction $wpFunction
    ) {
        $this->serviceRepository = $serviceRepository;
        $this->validator = new Validator($messageManager, true);
        $this->wpFunction = $wpFunction;
    }

    public function isValid(array $postData, string $languageCode): bool
    {
        $localization = ServiceCreateEditLocalizationStrings::get();

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
                $this->serviceRepository,
                $languageCode,
            );
            $this->validator->isNotReservedWord(
                $localization['field']['key'],
                $postData['key'],
                ['all', 'cookie', 'thirdparty', 'firstparty'],
            );
        }

        $this->validator->isNotEmptyString($localization['field']['name'], $postData['name'] ?? '');

        if (!isset($postData['providerKey']) || strlen(trim($postData['providerKey'])) === 0) {
            //$this->validator->isNotEmptyString($localization['field']['providerName'], $postData['providerName'] ?? '');
        }
        $this->validator = $this->wpFunction->applyFilter('borlabsCookie/service/validate', $this->validator, $postData);

        return $this->validator->isValid();
    }
}
