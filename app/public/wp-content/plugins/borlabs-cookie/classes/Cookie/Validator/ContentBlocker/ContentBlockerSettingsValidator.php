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

use Borlabs\Cookie\Localization\GlobalLocalizationStrings;
use Borlabs\Cookie\System\Message\MessageManager;
use Borlabs\Cookie\Validator\Validator;

/**
 * Class ContentBlockerValidator.
 */
// TODO Not used at the moment
final class ContentBlockerSettingsValidator
{
    /**
     * @var \Borlabs\Cookie\Validator\Validator
     */
    private $validator;

    public function __construct(MessageManager $message)
    {
        $this->validator = new Validator($message, true);
    }

    public function isValid(array $postData): bool
    {
        $localization = GlobalLocalizationStrings::get();
        $this->validator->isHexColor(
            $localization['field']['backgroundColor'],
            $postData['contentBlockerBackgroundColor'],
        );
        $this->validator->isHexColor($localization['field']['textColor'], $postData['contentBlockerTextColor']);
        $this->validator->isHexColor($localization['field']['buttonColor'], $postData['contentBlockerButtonColor']);
        $this->validator->isHexColor(
            $localization['field']['buttonColor'],
            $postData['contentBlockerButtonColorHover'],
        );
        $this->validator->isHexColor(
            $localization['field']['buttonTextColor'],
            $postData['contentBlockerButtonTextColor'],
        );
        $this->validator->isHexColor(
            $localization['field']['buttonTextColor'],
            $postData['contentBlockerButtonTextColorHover'],
        );
        $this->validator->isHexColor($localization['field']['linkColor'], $postData['contentBlockerLinkColor']);
        $this->validator->isHexColor($localization['field']['linkColor'], $postData['contentBlockerLinkColorHover']);

        return $this->validator->isValid();
    }
}
