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
 * The **ServiceOverviewLocalizationStrings** class contains various localized strings.
 */
final class ServiceOverviewLocalizationStrings implements LocalizationInterface
{
    /**
     * @return array<array<string>>
     */
    public static function get(): array
    {
        return [
            // Alert messages
            'alert' => [
                'noServiceConfigured' => _x(
                    'No <translation-key id="Service">Service</translation-key> configured.',
                    'Backend / Services / Alert Message',
                    'borlabs-cookie',
                ),
            ],

            // Breadcrumbs
            'breadcrumb' => [
                'module' => _x(
                    'Services',
                    'Backend / Services / Breadcrumb',
                    'borlabs-cookie',
                ),
            ],

            // Fields
            'field' => [
            ],

            // Headlines
            'headline' => [
                'resetDefaultServices' => _x(
                    'Reset Default Services',
                    'Backend / Services / Headline',
                    'borlabs-cookie',
                ),
            ],

            // Hint
            'hint' => [
                'reset' => _x(
                    'Please confirm that you want to reset all <translation-key id="Service">Service</translation-key> settings. They will be reset to their default settings. Your custom <translation-key id="Services">Services</translation-key> and the <translation-key id="Services">Services</translation-key> installed via the <translation-key id="Navigation-Library">Library</translation-key> are not reset.',
                    'Backend / Services / Hint',
                    'borlabs-cookie',
                ),
            ],

            // Placeholder
            'placeholder' => [
            ],

            // Tables
            'table' => [
                'id' => _x(
                    'ID',
                    'Backend / Services / Table Headline',
                    'borlabs-cookie',
                ),
                'name' => _x(
                    'Name',
                    'Backend / Services / Table Headline',
                    'borlabs-cookie',
                ),
                'position' => _x(
                    'Position',
                    'Backend / Services / Table Headline',
                    'borlabs-cookie',
                ),
                'status' => _x(
                    'Status',
                    'Backend / Services / Table Headline',
                    'borlabs-cookie',
                ),
            ],

            // Things to know
            'thingsToKnow' => [
                'headlinePurposeServices' => _x(
                    'What is the purpose of the <translation-key id="Services">Services</translation-key> section?',
                    'Backend / Services / Things to know / Headline',
                    'borlabs-cookie',
                ),
                'headlineSymbolsExplained' => _x(
                    'Symbols explained',
                    'Backend / Services / Things to know / Headline',
                    'borlabs-cookie',
                ),
                'purposeServicesExplainedA' => _x(
                    'In the <translation-key id="Services">Services</translation-key> section you can document the use of services and their cookies for your visitors, as well as integrate JavaScripts, such as Google Analytics.',
                    'Backend / Services / Things to know / Text',
                    'borlabs-cookie',
                ),
                'purposeServicesExplainedB' => _x(
                    'Because <translation-key id="Borlabs-Cookie">Borlabs Cookie</translation-key> uses the opt-in process, JavaScripts are only executed after the visitor has given their consent to the <translation-key id="Service-Group">Service Group</translation-key> or <translation-key id="Service">Service</translation-key>.',
                    'Backend / Services / Things to know / Text',
                    'borlabs-cookie',
                ),
                'symbolExplainedActive' => _x(
                    'The <translation-key id="Service">Service</translation-key> is active and displayed in the <translation-key id="Dialog">Dialog</translation-key>. Not available for the <translation-key id="Service">Service</translation-key>: <translation-key id="Borlabs-Cookie">Borlabs Cookie</translation-key>.',
                    'Backend / Services / Things to know / Text',
                    'borlabs-cookie',
                ),
                'symbolExplainedDelete' => _x(
                    'Delete the <translation-key id="Service">Service</translation-key>. Not available for the <translation-key id="Service">Service</translation-key>: <translation-key id="Borlabs-Cookie">Borlabs Cookie</translation-key>.',
                    'Backend / Services / Things to know / Text',
                    'borlabs-cookie',
                ),
                'symbolExplainedEdit' => _x(
                    'Edit the <translation-key id="Service">Service</translation-key>.',
                    'Backend / Services / Things to know / Text',
                    'borlabs-cookie',
                ),
                'symbolExplainedInactive' => _x(
                    'The <translation-key id="Service">Service</translation-key> is inactive and not displayed in the <translation-key id="Dialog">Dialog</translation-key>. Not available for the <translation-key id="Service">Service</translation-key>: <translation-key id="Borlabs-Cookie">Borlabs Cookie</translation-key>.',
                    'Backend / Services / Things to know / Text',
                    'borlabs-cookie',
                ),
            ],
        ];
    }
}
