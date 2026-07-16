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

namespace Borlabs\Cookie\Validator\ContentBlocker;

use Borlabs\Cookie\Adapter\WpFunction;
use Borlabs\Cookie\Localization\ContentBlocker\ContentBlockerCreateEditLocalizationStrings;
use Borlabs\Cookie\Repository\ContentBlocker\ContentBlockerRepository;
use Borlabs\Cookie\System\Message\MessageManager;
use Borlabs\Cookie\Validator\Validator;

/**
 * Class ContentBlockerValidator.
 */
final class ContentBlockerValidator
{
    private ContentBlockerRepository $contentBlockerRepository;

    private Validator $validator;

    private WpFunction $wpFunction;

    public function __construct(
        ContentBlockerRepository $contentBlockerRepository,
        MessageManager $message,
        WpFunction $wpFunction
    ) {
        $this->contentBlockerRepository = $contentBlockerRepository;
        $this->validator = new Validator($message, true);
        $this->wpFunction = $wpFunction;
    }

    public function isValid(array $postData, string $languageCode): bool
    {
        $localization = ContentBlockerCreateEditLocalizationStrings::get();

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
                $this->contentBlockerRepository,
                $languageCode,
            );
            $this->validator->isNotReservedWord(
                $localization['field']['key'],
                $postData['key'],
                ['all', 'cookie', 'thirdparty', 'firstparty'],
            );
        }

        $this->validator->isNotEmptyString($localization['field']['name'], $postData['name']);
        $this->validator = $this->wpFunction->applyFilter('borlabsCookie/contentBlocker/validate', $this->validator, $postData);

        return $this->validator->isValid();
    }
}
