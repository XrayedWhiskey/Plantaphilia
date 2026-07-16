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

class EntryNotFoundLocalizationStrings implements LocalizationInterface
{
    public static function get(): array
    {
        return [
            // Alert messages
            'alert' => [
                'entryWithKeyOfModelNotFoundForLanguage' => _x(
                    'Entry with key {{ key }} of model {{ model }} not found for language {{ language }}.',
                    'Exception',
                    'borlabs-cookie',
                ),
            ],
        ];
    }
}
