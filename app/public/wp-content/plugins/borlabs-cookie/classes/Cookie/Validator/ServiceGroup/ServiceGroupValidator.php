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

namespace Borlabs\Cookie\Validator\ServiceGroup;

use Borlabs\Cookie\Adapter\WpFunction;
use Borlabs\Cookie\Localization\ServiceGroup\ServiceGroupCreateEditLocalizationStrings;
use Borlabs\Cookie\Repository\ServiceGroup\ServiceGroupRepository;
use Borlabs\Cookie\System\Message\MessageManager;
use Borlabs\Cookie\Validator\Validator;

/**
 * Class ServiceGroupValidator.
 */
final class ServiceGroupValidator
{
    private ServiceGroupRepository $serviceGroupRepository;

    private Validator $validator;

    private WpFunction $wpFunction;

    public function __construct(
        MessageManager $message,
        ServiceGroupRepository $serviceGroupRepository,
        WpFunction $wpFunction
    ) {
        $this->serviceGroupRepository = $serviceGroupRepository;
        $this->validator = new Validator($message, true);
        $this->wpFunction = $wpFunction;
    }

    public function isValid(array $postData, string $languageCode): bool
    {
        $localization = ServiceGroupCreateEditLocalizationStrings::get();

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
                $this->serviceGroupRepository,
                $languageCode,
            );
        }

        $this->validator->isNotEmptyString(
            $localization['field']['name'],
            $postData['name'],
        );
        $this->validator->isIntegerGreaterThan(
            $localization['field']['position'],
            $postData['position'],
            0,
        );

        $this->validator->isNotEmptyString($localization['field']['name'], $postData['name']);
        $this->validator = $this->wpFunction->applyFilter('borlabsCookie/serviceGroup/validate', $this->validator, $postData);

        return $this->validator->isValid();
    }
}
