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

namespace Borlabs\Cookie\Localization\MetaBox;

use Borlabs\Cookie\Localization\LocalizationInterface;

use function Borlabs\Cookie\System\WordPressGlobalFunctions\_x;

final class MetaBoxEditLocalizationStrings implements LocalizationInterface
{
    /**
     * @return array<array<string>>
     */
    public static function get(): array
    {
        return [
            // Fields
            'field' => [
                'customCode' => _x(
                    '<translation-key id="Custom-Code">Custom Code</translation-key>',
                    'Backend / MetaBox / Field',
                    'borlabs-cookie',
                ),
            ],

            // Headlines
            'headline' => [
                'borlabsCookieMetaBox' => _x(
                    'Borlabs Cookie - Meta Box',
                    'Backend / MetaBox / Headline',
                    'borlabs-cookie',
                ),
            ],

            // Hint
            'hint' => [
                'customCode' => _x(
                    'The code you enter here will only run on this page. Extend your code by wrapping it with our blocking <translation-key id="Shortcodes">Shortcodes</translation-key> to ensure that it only executes after you\'ve received consent. This functionality proves useful in scenarios such as needing to trigger a conversion pixel exclusively on this page.',
                    'Backend / MetaBox / Hint',
                    'borlabs-cookie',
                ),
            ],
        ];
    }
}
