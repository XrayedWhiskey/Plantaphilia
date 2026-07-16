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

namespace Borlabs\Cookie\Localization;

use function Borlabs\Cookie\System\WordPressGlobalFunctions\_x;

/**
 * The **ValidatorLocalizationStrings** class contains various localized strings.
 *
 * @see \Borlabs\Cookie\Localization\ValidatorLocalizationStrings::get()
 */
final class ValidatorLocalizationStrings implements LocalizationInterface
{
    /**
     * @return array<string>
     */
    public static function get(): array
    {
        return [
            'isBoolean' => _x(
                'The value in the <strong>{{ fieldName }}</strong> field is not valid.',
                'Backend / Validation / Alert Message',
                'borlabs-cookie',
            ),
            'isCertainCharacters' => _x(
                'Invalid <strong>{{ fieldName }}</strong> name. Only use <strong><em>{{ characterPool }}</em></strong>',
                'Backend / Global / Validation Message',
                'borlabs-cookie',
            ),
            'isEnumValue' => _x(
                'The value in the <strong>{{ fieldName }}</strong> field is not valid.',
                'Backend / Validation / Alert Message',
                'borlabs-cookie',
            ),
            'isHexColor' => _x(
                'The value in the <strong>{{ fieldName }}</strong> field is not a valid hex color code.',
                'Backend / Validation / Alert Message',
                'borlabs-cookie',
            ),
            'isIntegerGreaterThan' => _x(
                'The <strong>{{ fieldName }}</strong> has to be an integer bigger than {{ limit }}.',
                'Backend / Global / Validation Message',
                'borlabs-cookie',
            ),
            'isMinLengthCertainCharacters' => _x(
                'Please fill out the field <strong>{{ fieldName }}</strong>. The field must have at least {{ minLength }} characters and contains only characters from <strong><em>{{ characterPool }}</em></strong>.',
                'Backend / Validation / Alert Message',
                'borlabs-cookie',
            ),
            'isNotEmptyString' => _x(
                'Please fill out the field <strong>{{ fieldName }}</strong>.',
                'Backend / Validation / Alert Message',
                'borlabs-cookie',
            ),
            'isNotReservedWord' => _x(
                'Please change the name of the <strong>{{ fieldName }}</strong>. Your selected name for <strong>{{ fieldName }}</strong> is reserved and can not be used.',
                'Backend / Validation / Alert Message',
                'borlabs-cookie',
            ),
            'isStringJSON' => _x(
                'The value in the <strong>{{ fieldName }}</strong> field is not valid JSON.',
                'Backend / Validation / Alert Message',
                'borlabs-cookie',
            ),
            'isUniqueKey' => _x(
                'The <strong>{{ fieldName }}</strong> already exists.',
                'Backend / Validation / Alert Message',
                'borlabs-cookie',
            ),
            'isUrl' => _x(
                'The value in the <strong>{{ fieldName }}</strong> field is not a valid url.',
                'Backend / Validation / Alert Message',
                'borlabs-cookie',
            ),
        ];
    }
}
