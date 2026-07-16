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

namespace Borlabs\Cookie\Exception\ApiClient;

use Borlabs\Cookie\Exception\TranslatedException;

class ApiClientException extends TranslatedException
{
    protected const LOCALIZATION_STRING_CLASS = \Borlabs\Cookie\Localization\ApiClient\ApiClientLocalizationStrings::class;

    /**
     * Gets the Exception message.
     *
     * @return string the Exception message as a string
     */
    public function getTranslatedMessage(): string
    {
        // Notice: We cannot call parent::getTranslatedMessage() since this would change the context of "self::LOCALIZATION_STRING_CLASS" to the parent class.
        if ($this->localizationStringClass !== null) {
            $localizationStrings = call_user_func([$this->localizationStringClass, 'get']);

            if (isset($localizationStrings['alert'][$this->translationKey])) {
                return $this->getTranslationWithContext($localizationStrings['alert'][$this->translationKey]);
            }
        }

        if (isset(call_user_func([static::LOCALIZATION_STRING_CLASS, 'get'])['alert'][$this->translationKey])) {
            return $this->getTranslationWithContext(call_user_func([static::LOCALIZATION_STRING_CLASS, 'get'])['alert'][$this->translationKey]);
        }

        if (isset(call_user_func([self::LOCALIZATION_STRING_CLASS, 'get'])['alert'][$this->translationKey])) {
            return $this->getTranslationWithContext(call_user_func([self::LOCALIZATION_STRING_CLASS, 'get'])['alert'][$this->translationKey]);
        }

        return $this->translationKey;
    }
}
