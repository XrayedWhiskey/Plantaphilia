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

namespace Borlabs\Cookie\Localization\ServiceGroup;

use Borlabs\Cookie\Localization\LocalizationInterface;

use function Borlabs\Cookie\System\WordPressGlobalFunctions\_x;

/**
 * The **ServiceGroupGeneralLocalizationStrings** class contains various localized strings.
 *
 * @see \Borlabs\Cookie\Localization\ServiceGroup\ServiceGroupGeneralLocalizationStrings::get()
 */
final class ServiceGroupGeneralLocalizationStrings implements LocalizationInterface
{
    /**
     * @return array<array<string>>
     */
    public static function get(): array
    {
        return [
            // Alert messages
            'alert' => [
                'cannotDeleteServiceGroupWithService' => _x(
                    'Could not delete <translation-key id="Service-Group">Service Group</translation-key> because <translation-key id="Service-Group">Service Group</translation-key> is linked with <translation-key id="Services">Services</translation-key>.',
                    'Backend / Service Groups / Alert Message',
                    'borlabs-cookie',
                ),
            ],

            // Breadcrumbs
            'breadcrumb' => [
            ],

            // Fields
            'field' => [
            ],

            // Headlines
            'headline' => [
            ],

            // Hint
            'hint' => [
            ],

            // Placeholder
            'placeholder' => [
            ],

            // Tables
            'table' => [
            ],

            // Things to know
            'thingsToKnow' => [
            ],
        ];
    }
}
