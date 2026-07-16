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

namespace Borlabs\Cookie\Localization\CompatibilityPatch;

use Borlabs\Cookie\Localization\LocalizationInterface;

use function Borlabs\Cookie\System\WordPressGlobalFunctions\_x;

/**
 * The **ContentBlockerOverviewLocalizationStrings** class contains various localized strings.
 *
 * @see \Borlabs\Cookie\Localization\CompatibilityPatch\CompatibilityPatchOverviewLocalizationStrings::get()
 */
final class CompatibilityPatchOverviewLocalizationStrings implements LocalizationInterface
{
    /**
     * @return array<array<string>>
     */
    public static function get(): array
    {
        return [
            // Alert messages
            'alert' => [
                'noCompatibilityPatchConfigured' => _x(
                    'No <translation-key id="Compatibility-Patch">Compatibility Patch</translation-key> configured.',
                    'Backend / Compatibility Patches / Alert Message',
                    'borlabs-cookie',
                ),
            ],

            // Breadcrumbs
            'breadcrumb' => [
                'module' => _x(
                    'Compatibility Patches',
                    'Backend / Compatibility Patches / Breadcrumb',
                    'borlabs-cookie',
                ),
            ],

            // Headlines
            'headline' => [
                'compatibilityPatches' => _x(
                    '<translation-key id="Compatibility-Patches">Compatibility Patches</translation-key>',
                    'Backend / Compatibility Patches / Headline',
                    'borlabs-cookie',
                ),
            ],

            // Hint
            'hint' => [
            ],

            // Placeholder
            'placeholder' => [
                'search' => _x(
                    'Search key or file name...',
                    'Backend / Compatibility Patches / Placeholder',
                    'borlabs-cookie',
                ),
            ],

            // Tables
            'table' => [
                'borlabsServicePackageKey' => _x(
                    'Package',
                    'Backend / Compatibility Patches / Table Headline',
                    'borlabs-cookie',
                ),
                'fileName' => _x(
                    'File name',
                    'Backend / Compatibility Patches / Table Headline',
                    'borlabs-cookie',
                ),
                'hash' => _x(
                    'Hash',
                    'Backend / Compatibility Patches / Table Headline',
                    'borlabs-cookie',
                ),
                'id' => _x(
                    'ID',
                    'Backend / Compatibility Patches / Table Headline',
                    'borlabs-cookie',
                ),
                'valid' => _x(
                    'Valid',
                    'Backend / Compatibility Patches / Table Headline',
                    'borlabs-cookie',
                ),
            ],

            // Things to know
            'thingsToKnow' => [
                'details' => _x(
                    'Click on the icon to get detailed information about the <translation-key id="Compatibility-Patch">Compatibility Patch</translation-key>.',
                    'Backend / Compatibility Patches / Things to know',
                    'borlabs-cookie',
                ),
                'headlinePurposeCompatibilityPatches' => _x(
                    'What is the purpose of the <translation-key id="Compatibility-Patches">Compatibility Patches</translation-key> section?',
                    'Backend / Compatibility Patches / Things to know / Headline',
                    'borlabs-cookie',
                ),
                'headlineSymbolsExplained' => _x(
                    'Symbols explained',
                    'Backend / Compatibility Patches / Things to know / Headline',
                    'borlabs-cookie',
                ),
                'invalid' => _x(
                    'The file of the <translation-key id="Compatibility-Patch">Compatibility Patch</translation-key> is invalid. Please reinstall the package via the <translation-key id="Navigation-Library">Library</translation-key> to resolve this issue.',
                    'Backend / Compatibility Patches / Things to know',
                    'borlabs-cookie',
                ),
                'purposeCompatibilityPatchesA' => _x(
                    'In this section, you can see all <translation-key id="Compatibility-Patches">Compatibility Patches</translation-key> that have been installed by packages from the <translation-key id="Navigation-Library">Library</translation-key>.',
                    'Backend / Compatibility Patches / Things to know',
                    'borlabs-cookie',
                ),
                'purposeCompatibilityPatchesB' => _x(
                    'A <translation-key id="Compatibility-Patch">Compatibility Patch</translation-key> is used to enhance compatibility with other <translation-key id="Plugins">Plugins</translation-key>, <translation-key id="Themes">Themes</translation-key> external services.',
                    'Backend / Compatibility Patches / Things to know',
                    'borlabs-cookie',
                ),
                'valid' => _x(
                    'The file of the <translation-key id="Compatibility-Patch">Compatibility Patch</translation-key> is valid.',
                    'Backend / Compatibility Patches / Things to know',
                    'borlabs-cookie',
                ),
            ],
        ];
    }
}
