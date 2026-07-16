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

namespace Borlabs\Cookie\Localization\Library;

use Borlabs\Cookie\Localization\LocalizationInterface;

use function Borlabs\Cookie\System\WordPressGlobalFunctions\_x;

final class SharedMailLocalizationStrings implements LocalizationInterface
{
    /**
     * @return array<array<string>>
     */
    public static function get(): array
    {
        // @noinspection HtmlUnknownTarget
        return [
            // Text
            'text' => [
                'mailInformation' => _x(
                    'This email is sent from <a href="{{ websiteUrl }}"><strong>{{ websiteName }}</strong></a>.',
                    'Backend / Shared Mail Strings / Text',
                    'borlabs-cookie',
                ),
                'removeFromMailingList' => _x(
                    'You can manage the recipient email addresses for this notification under <translation-key id="Borlabs-Cookie">Borlabs Cookie</translation-key> &raquo; <translation-key id="Navigation-Library">Library</translation-key>, in the "<translation-key id="Auto-Update-Settings">Auto Update Settings</translation-key>" section at the bottom.',
                    'Backend / Shared Mail Strings / Text',
                    'borlabs-cookie',
                ),
            ],
        ];
    }
}
