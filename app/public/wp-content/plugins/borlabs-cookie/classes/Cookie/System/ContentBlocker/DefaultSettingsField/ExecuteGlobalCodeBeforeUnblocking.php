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

namespace Borlabs\Cookie\System\ContentBlocker\DefaultSettingsField;

use Borlabs\Cookie\Dto\System\SettingsFieldDto;
use Borlabs\Cookie\Dto\System\SettingsFieldTranslationDto;
use Borlabs\Cookie\Enum\System\SettingsFieldDataTypeEnum;
use Borlabs\Cookie\Enum\System\SettingsFieldVisibilityEnum;
use Borlabs\Cookie\Enum\System\ValidatorEnum;
use Borlabs\Cookie\Localization\ContentBlocker\ContentBlockerDefaultSettingsFieldsLocalizationStrings;
use Borlabs\Cookie\System\DefaultSettingsField\DefaultSettingsFieldInterface;

class ExecuteGlobalCodeBeforeUnblocking implements DefaultSettingsFieldInterface
{
    public const KEY = 'execute-global-code-before-unblocking';

    public function get(string $languageCode): SettingsFieldDto
    {
        $executeGlobalCodeBeforeUnblockingTranslation = new SettingsFieldTranslationDto(
            $languageCode,
            ContentBlockerDefaultSettingsFieldsLocalizationStrings::get()['field']['execute-global-code-before-unblocking'],
        );
        $executeGlobalCodeBeforeUnblockingTranslation->hint = ContentBlockerDefaultSettingsFieldsLocalizationStrings::get()['hint']['execute-global-code-before-unblocking'];

        return new SettingsFieldDto(
            self::KEY,
            SettingsFieldDataTypeEnum::BOOLEAN(),
            $executeGlobalCodeBeforeUnblockingTranslation,
            ValidatorEnum::NO_VALIDATION(),
            SettingsFieldVisibilityEnum::EDIT_ONLY(),
            '0',
            'default',
        );
    }
}
