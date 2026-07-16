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

namespace Borlabs\Cookie\Localization\RestClient;

use Borlabs\Cookie\Localization\LocalizationInterface;

use function Borlabs\Cookie\System\WordPressGlobalFunctions\_x;

class RestClientLocalizationStrings implements LocalizationInterface
{
    /**
     * @return array<array<string>>
     */
    public static function get(): array
    {
        return [
            // Alert
            'alert' => [
                'error' => _x(
                    'An error occurred while communicating with the WordPress REST API.',
                    'Backend / Rest Client / Alert',
                    'borlabs-cookie',
                ),
                'restCookieInvalidNonce' => _x(
                    'Your WordPress authentication cookie has expired. Please log out of WordPress and then log in again.',
                    'Backend / Rest Client / Alert',
                    'borlabs-cookie',
                ),
                'restForbidden' => _x(
                    'The WordPress REST API is currently blocked. Please check your security plugins or settings and make sure the REST API is accessible.',
                    'Backend / Rest Client / Alert',
                    'borlabs-cookie',
                ),
            ],

            // Tables
            'table' => [
                'code' => _x(
                    'Code',
                    'Backend / Rest Client / Table Headline',
                    'borlabs-cookie',
                ),
                'httpStatusCode' => _x(
                    'HTTP Status Code',
                    'Backend / Rest Client / Table Headline',
                    'borlabs-cookie',
                ),
                'message' => _x(
                    'Message',
                    'Backend / Rest Client / Table Headline',
                    'borlabs-cookie',
                ),
            ],
        ];
    }
}
