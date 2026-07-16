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

use Borlabs\Cookie\Localization\ContentBlocker\ContentBlockerLanguageStringCreateEditLocalizationStrings;
use Borlabs\Cookie\System\Message\MessageManager;
use Borlabs\Cookie\Validator\Validator;

/**
 * Class ContentBlockerValidator.
 */
final class ContentBlockerLanguageStringValidator
{
    /**
     * @var \Borlabs\Cookie\Validator\Validator
     */
    private $validator;

    public function __construct(
        MessageManager $messageManager
    ) {
        $this->validator = new Validator($messageManager, true);
    }

    public function isValid(array $postData): bool
    {
        $localization = ContentBlockerLanguageStringCreateEditLocalizationStrings::get();

        if (isset($postData['languageStrings']) && is_array($postData['languageStrings'])) {
            foreach ($postData['languageStrings'] as $index => $languageString) {
                $this->validator->isMinLengthCertainCharacters(
                    $localization['field']['key'] . ' (#' . ($index + 1) . ')',
                    $languageString['key'],
                    1,
                    'a-zA-Z\-\_',
                );
                $this->validator->isNotEmptyString(
                    $localization['field']['text'] . ' (#' . ($index + 1) . ')',
                    $languageString['value'],
                );
                $this->validator->isNotReservedWord(
                    $localization['field']['text'] . ' (#' . ($index + 1) . ')',
                    $languageString['value'],
                    ['name', 'previewImage', 'serviceConsentButtonDisplayValue'],
                );
            }
        }

        return $this->validator->isValid();
    }
}
