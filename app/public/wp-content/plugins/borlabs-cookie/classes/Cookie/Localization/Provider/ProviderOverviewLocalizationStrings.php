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

namespace Borlabs\Cookie\Localization\Provider;

use Borlabs\Cookie\Localization\LocalizationInterface;

use function Borlabs\Cookie\System\WordPressGlobalFunctions\_x;

final class ProviderOverviewLocalizationStrings implements LocalizationInterface
{
    /**
     * @return array<array<string>>
     */
    public static function get(): array
    {
        return [
            // Alert messages
            'alert' => [
                'noProviderConfigured' => _x(
                    'No <translation-key id="Provider">Provider</translation-key> configured.',
                    'Backend / Providers / Alert Message',
                    'borlabs-cookie',
                ),
            ],

            // Breadcrumbs
            'breadcrumb' => [
                'module' => _x(
                    'Providers',
                    'Backend / Providers / Breadcrumb',
                    'borlabs-cookie',
                ),
            ],

            // Fields
            'field' => [
            ],

            // Headlines
            'headline' => [
                'providers' => _x(
                    'Providers',
                    'Backend / Providers / Headline',
                    'borlabs-cookie',
                ),
                'resetDefaultProviders' => _x(
                    'Reset Default Providers',
                    'Backend / Providers / Headline',
                    'borlabs-cookie',
                ),
            ],

            // Hint
            'hint' => [
                'reset' => _x(
                    'Please confirm that you want to reset the <translation-key id="Providers">Providers</translation-key>. They will be reset to their default settings. Your custom <translation-key id="Providers">Providers</translation-key> and the <translation-key id="Providers">Providers</translation-key> installed via the <translation-key id="Navigation-Library">Library</translation-key> are not reset.',
                    'Backend / Providers / Hint',
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
                    'Backend / Providers / Table Headline',
                    'borlabs-cookie',
                ),
                'name' => _x(
                    'Name',
                    'Backend / Providers / Table Headline',
                    'borlabs-cookie',
                ),
            ],

            // Things to know
            'thingsToKnow' => [
                'headlinePurposeProviders' => _x(
                    'What is the purpose of the <translation-key id="Providers">Providers</translation-key> section?',
                    'Backend / Providers / Things to know / Headline',
                    'borlabs-cookie',
                ),
                'headlineSymbolsExplained' => _x(
                    'Symbols explained',
                    'Backend / Providers / Things to know / Headline',
                    'borlabs-cookie',
                ),
                'purposeProvidersExplainedA' => _x(
                    'For legal compliance, every <translation-key id="Service">Service</translation-key> or <translation-key id="Content-Blocker">Content Blocker</translation-key> must be linked to a specific <translation-key id="Provider">Provider</translation-key>.',
                    'Backend / Providers / Things to know / Text',
                    'borlabs-cookie',
                ),
                'purposeProvidersExplainedB' => _x(
                    'The <translation-key id="Provider">Provider</translation-key> is the company that delivers the <translation-key id="Service">Service</translation-key> (e.g., Google Analytics) or the content (e.g., YouTube videos).',
                    'Backend / Providers / Things to know / Text',
                    'borlabs-cookie',
                ),
                'purposeProvidersExplainedC' => _x(
                    'Visitors should receive clear information about the <translation-key id="Provider">Provider</translation-key> responsible for the <translation-key id="Service">Service</translation-key> or blocked content.',
                    'Backend / Providers / Things to know / Text',
                    'borlabs-cookie',
                ),
                'symbolExplainedDelete' => _x(
                    'Delete the <translation-key id="Provider">Provider</translation-key>. Not available for the <translation-key id="Provider">Provider</translation-key>: <translation-key id="Owner-of-this-website">Owner of this website</translation-key>.',
                    'Backend / Providers / Things to know / Text',
                    'borlabs-cookie',
                ),
                'symbolExplainedEdit' => _x(
                    'Edit the <translation-key id="Provider">Provider</translation-key>.',
                    'Backend / Providers / Things to know / Text',
                    'borlabs-cookie',
                ),
            ],
        ];
    }
}
