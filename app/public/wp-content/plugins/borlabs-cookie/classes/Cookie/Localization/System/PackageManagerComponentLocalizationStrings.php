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

namespace Borlabs\Cookie\Localization\System;

use Borlabs\Cookie\Localization\LocalizationInterface;

final class PackageManagerComponentLocalizationStrings implements LocalizationInterface
{
    /**
     * @return array<array<string>>
     */
    public static function get(): array
    {
        return [
            'alert' => [
                'keyAlreadyInUse' => _x(
                    'A <strong>{{ resource }}</strong> with the key <strong><em>{{ key }}</em></strong> already exists. Please delete the <strong>{{ resource }}</strong> first.',
                    'Backend / Package Manager Components / Alert',
                    'borlabs-cookie',
                ),
            ],
        ];
    }
}
