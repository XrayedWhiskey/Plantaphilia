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

namespace Borlabs\Cookie\Localization\SetupAssistant;

use Borlabs\Cookie\Localization\LocalizationInterface;

use function Borlabs\Cookie\System\WordPressGlobalFunctions\_x;

final class SetupAssistantFollowUpMailLocalizationStrings implements LocalizationInterface
{
    /**
     * @return array<array<string>>
     */
    public static function get(): array
    {
        return [
            // Text
            'text' => [
                'prefaceA' => _x(
                    'During setup, some packages were installed that may require follow-up work.',
                    'Backend / Setup Assistant Follow-up / Text',
                    'borlabs-cookie',
                ),
                'prefaceB' => _x(
                    'Below you will find information on the follow-up steps for the individual packages.',
                    'Backend / Setup Assistant Follow-up / Text',
                    'borlabs-cookie',
                ),
                'prefaceC' => _x(
                    'If two packages mention the same steps, for example updating the CSS or clearing the cache using the same procedure, you only need to perform the step once.',
                    'Backend / Setup Assistant Follow-up / Text',
                    'borlabs-cookie',
                ),
                'subject' => _x(
                    'Borlabs Cookie: Follow-Up Work Needed for Some Packages',
                    'Backend / Setup Assistant Follow-up / Text',
                    'borlabs-cookie',
                ),
            ],
        ];
    }
}
