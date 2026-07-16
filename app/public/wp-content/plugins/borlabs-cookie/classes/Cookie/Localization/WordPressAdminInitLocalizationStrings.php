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
 * The **GlobalLocalizationStrings** class contains various localized strings.
 *
 * @see \Borlabs\Cookie\Localization\GlobalLocalizationStrings::get()
 */
final class WordPressAdminInitLocalizationStrings implements LocalizationInterface
{
    /**
     * @return array<array<string>>
     */
    public static function get(): array
    {
        return [
            // Alert messages
            'alert' => [
                'borlabsCookieNotActive' => _x(
                    '<translation-key id="Borlabs-Cookie">Borlabs Cookie</translation-key> is not active. If you want to use <translation-key id="Borlabs-Cookies">Borlabs Cookie\'s</translation-key> features on your website, please activate it under <translation-key id="Navigation-Settings">Settings</translation-key> &raquo; <translation-key id="Navigation-Settings-General-Settings">General Settings</translation-key> &raquo; <translation-key id="Borlabs-Cookie-Status">Borlabs Cookie Status</translation-key>.',
                    'Backend / Init / Alert Message',
                    'borlabs-cookie',
                ),
            ],

            // Options (select | checkbox | radio)
            'option' => [
                'interval5Minutes' => _x(
                    'Every 5 Minutes',
                    'Backend / Init / Option',
                    'borlabs-cookie',
                ),
            ],

            // Plugin links
            'pluginLinks' => [
                'dashboard' => _x(
                    'Dashboard',
                    'Backend / Init / Plugin Links',
                    'borlabs-cookie',
                ),
                'license' => _x(
                    'License',
                    'Backend / Init / Plugin Links',
                    'borlabs-cookie',
                ),
                'setupAssistant' => _x(
                    'Setup Assistant',
                    'Backend / Init / Plugin Links',
                    'borlabs-cookie',
                ),
            ],
        ];
    }
}
