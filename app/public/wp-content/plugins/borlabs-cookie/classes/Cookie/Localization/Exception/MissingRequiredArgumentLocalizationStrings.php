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

namespace Borlabs\Cookie\Localization\Exception;

use Borlabs\Cookie\Localization\LocalizationInterface;

class MissingRequiredArgumentLocalizationStrings implements LocalizationInterface
{
    public static function get(): array
    {
        return [
            // Alert messages
            'alert' => [
                'serviceGroupRepositoryMissing' => _x(
                    'The required argument of type "<strong>ServiceGroupRepository</strong>" is missing for the component "<strong>{{ componentName }} <em>({{ componentKey }})</em></strong>" of model "<strong>{{ model }}</strong>". Please copy this error message and contact support.',
                    'Exception',
                    'borlabs-cookie',
                ),
            ],
        ];
    }
}
