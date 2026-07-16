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

namespace Borlabs\Cookie\Localization\Service;

use Borlabs\Cookie\Localization\LocalizationInterface;

use function Borlabs\Cookie\System\WordPressGlobalFunctions\_x;

/**
 * The **ServiceCreateLocalizationStrings** class contains various localized strings.
 */
final class ServiceOptionCreateEditLocalizationStrings implements LocalizationInterface
{
    /**
     * @return array<array<string>>
     */
    public static function get(): array
    {
        return [
            // Alert messages
            'alert' => [
                'noOptionConfigured' => _x(
                    'No <translation-key id="Option">Option</translation-key> configured.',
                    'Backend / Service Option / Alert Message',
                    'borlabs-cookie',
                ),
            ],

            // Buttons
            'button' => [
                'addOption' => _x(
                    'Add Option',
                    'Backend / Service Option / Button',
                    'borlabs-cookie',
                ),
            ],

            // Fields
            'field' => [
                'description' => _x(
                    'Description',
                    'Backend / Service Option / Label',
                    'borlabs-cookie',
                ),
            ],

            // Headlines
            'headline' => [
                'options' => _x(
                    '<translation-key id="Options">Options</translation-key>',
                    'Backend / Service Option / Headline',
                    'borlabs-cookie',
                ),
            ],

            // Tables
            'table' => [
                'description' => _x(
                    'Description',
                    'Backend / Service Option / Table Headline',
                    'borlabs-cookie',
                ),
                'type' => _x(
                    '<translation-key id="Type">Type</translation-key>',
                    'Backend / Service Option / Table Headline',
                    'borlabs-cookie',
                ),
            ],

            // Things to know
            'thingsToKnow' => [
                'headlineOptionsExplained' => _x(
                    'What is the purpose of the <translation-key id="Options">Options</translation-key> section?',
                    'Backend / Service Option / Things to know / Headline',
                    'borlabs-cookie',
                ),
                'optionsExplainedA' => _x(
                    'With <translation-key id="Options">Options</translation-key> you can provide more information about the <translation-key id="Service">Service</translation-key>. There are six <translation-key id="Types">Types</translation-key> to choose from and you can add as many <translation-key id="Options">Options</translation-key> as you like.',
                    'Backend / Service Option / Things to know / Text',
                    'borlabs-cookie',
                ),
                'optionsExplainedB' => _x(
                    'For example, you could select the &quot;<translation-key id="Location-Processing">Location Processing</translation-key>&quot; <translation-key id="Type">Type</translation-key> and add <strong>United States of America</strong> as description if the <translation-key id="Service">Service</translation-key> processes data in the United States.',
                    'Backend / Service Option / Things to know / Text',
                    'borlabs-cookie',
                ),
            ],
        ];
    }
}
