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

namespace Borlabs\Cookie\Localization\ContentBlocker;

use Borlabs\Cookie\Localization\LocalizationInterface;

use function Borlabs\Cookie\System\WordPressGlobalFunctions\_x;

final class ContentBlockerDefaultSettingsFieldsLocalizationStrings implements LocalizationInterface
{
    /**
     * @return array<array<string>>
     */
    public static function get(): array
    {
        return [
            // Fields
            'field' => [
                'execute-global-code-before-unblocking' => _x(
                    '<translation-key id="Execute-Global-code-before-unblocking">Execute Global code before unblocking</translation-key>',
                    'Backend / Content Blocker / Label',
                    'borlabs-cookie',
                ),
            ],

            // Hint
            'hint' => [
                'execute-global-code-before-unblocking' => _x(
                    'If this option is enabled and a visitor unblocks the content, the JavaScript in the <translation-key id="Global">Global</translation-key> field will be executed before the blocked content is loaded.',
                    'Backend / Content Blocker / Hint',
                    'borlabs-cookie',
                ),
            ],
        ];
    }
}
