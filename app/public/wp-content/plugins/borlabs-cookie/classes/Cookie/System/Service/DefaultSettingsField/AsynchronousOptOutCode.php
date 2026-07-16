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

namespace Borlabs\Cookie\System\Service\DefaultSettingsField;

use Borlabs\Cookie\Dto\System\SettingsFieldDto;
use Borlabs\Cookie\Dto\System\SettingsFieldTranslationDto;
use Borlabs\Cookie\Enum\System\SettingsFieldDataTypeEnum;
use Borlabs\Cookie\Enum\System\SettingsFieldVisibilityEnum;
use Borlabs\Cookie\Enum\System\ValidatorEnum;
use Borlabs\Cookie\Localization\Service\ServiceDefaultSettingsFieldsLocalizationStrings;
use Borlabs\Cookie\System\DefaultSettingsField\DefaultSettingsFieldInterface;

class AsynchronousOptOutCode implements DefaultSettingsFieldInterface
{
    public const KEY = 'asynchronous-opt-out-code';

    public function get(string $languageCode): SettingsFieldDto
    {
        $prioritizeSettingsFieldTranslation = new SettingsFieldTranslationDto(
            $languageCode,
            ServiceDefaultSettingsFieldsLocalizationStrings::get()['field']['asynchronous-opt-out-code'],
        );
        $prioritizeSettingsFieldTranslation->hint = ServiceDefaultSettingsFieldsLocalizationStrings::get()['hint']['asynchronous-opt-out-code'];

        return new SettingsFieldDto(
            self::KEY,
            SettingsFieldDataTypeEnum::BOOLEAN(),
            $prioritizeSettingsFieldTranslation,
            ValidatorEnum::NO_VALIDATION(),
            SettingsFieldVisibilityEnum::EDIT_ONLY(),
            '0',
            'default',
        );
    }
}
