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

namespace Borlabs\Cookie\Localization\HttpClient;

use Borlabs\Cookie\Localization\LocalizationInterface;

use function Borlabs\Cookie\System\WordPressGlobalFunctions\_x;

class ConnectionErrorLocalizationStrings implements LocalizationInterface
{
    /**
     * @return array<array<string>>
     */
    public static function get(): array
    {
        return [
            // Alert
            'alert' => [
                'cUrlError' => _x(
                    'No connection could be established: "<strong><em>{{ errorMessage }}</em></strong>". Please copy this error message and contact support.',
                    'Backend / HTTP Client / Alert',
                    'borlabs-cookie',
                ),
                'unsupportedMethod' => _x(
                    'The requested method "{{ method }}" is not supported by the HTTP client.',
                    'Backend / HTTP Client / Alert',
                    'borlabs-cookie',
                ),
            ],
        ];
    }
}
