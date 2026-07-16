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

use Borlabs\Cookie\Localization\LocalizationInterface;
use Borlabs\Cookie\Support\Formatter;

class TranslatedException extends GenericException
{
    /**
     * @var class-string<LocalizationInterface>
     */
    protected const LOCALIZATION_STRING_CLASS = \Borlabs\Cookie\Localization\GlobalLocalizationStrings::class;

    protected ?array $context;

    /**
     * @var null|class-string<LocalizationInterface>
     */
    protected ?string $localizationStringClass;

    protected string $translationKey;

    /**
     * @param null|class-string<LocalizationInterface> $localizationStringClass
     */
    public function __construct(string $translationKey, ?array $context = null, ?string $localizationStringClass = null)
    {
        $this->translationKey = $translationKey;
        $this->localizationStringClass = $localizationStringClass;
        $this->context = $context;
        parent::__construct(($localizationStringClass !== null ? $localizationStringClass . ':' : '') . $this->translationKey);
    }

    /**
     * Gets the Exception message.
     *
     * @return string the Exception message as a string
     */
    public function getTranslatedMessage(): string
    {
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

    /**
     * Converts the exception to an array.
     * This is useful for logging purposes.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'exceptionClass' => static::class,
            'message' => $this->getTranslatedMessage(),
            'translationKey' => $this->translationKey,
            'context' => $this->context,
        ];
    }

    protected function getTranslationWithContext(string $translation): string
    {
        if ($this->context !== null) {
            return Formatter::interpolate($translation, $this->context);
        }

        return $translation;
    }
}
