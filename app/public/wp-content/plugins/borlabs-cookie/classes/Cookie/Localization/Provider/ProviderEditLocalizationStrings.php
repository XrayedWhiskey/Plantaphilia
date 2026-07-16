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

final class ProviderEditLocalizationStrings implements LocalizationInterface
{
    /**
     * @return array<array<string>>
     */
    public static function get(): array
    {
        return [
            // Alert messages
            'alert' => [
                'providerNotOfCurrentLanguage' => _x(
                    'The selected <translation-key id="Provider">Provider</translation-key> is not available in the current language.',
                    'Backend / Providers / Alert',
                    'borlabs-cookie',
                ),
            ],

            // Breadcrumbs
            'breadcrumb' => [
                'edit' => _x(
                    'Edit: {{ name }}',
                    'Backend / Providers / Breadcrumb',
                    'borlabs-cookie',
                ),
                'module' => _x(
                    'Providers',
                    'Backend / Providers / Breadcrumb',
                    'borlabs-cookie',
                ),
                'new' => _x(
                    'New',
                    'Backend / Providers / Breadcrumb',
                    'borlabs-cookie',
                ),
            ],

            // Fields
            'field' => [
                'address' => _x(
                    'Address',
                    'Backend / Providers / Label',
                    'borlabs-cookie',
                ),
                'cookieUrl' => _x(
                    'Cookie URL',
                    'Backend / Providers / Label',
                    'borlabs-cookie',
                ),
                'description' => _x(
                    'Description',
                    'Backend / Providers / Label',
                    'borlabs-cookie',
                ),
                'iabVendorId' => _x(
                    '<translation-key id="IAB-Vendor-ID">IAB Vendor ID</translation-key>',
                    'Backend / Providers / Label',
                    'borlabs-cookie',
                ),
                'key' => _x(
                    'ID',
                    'Backend / Providers / Label',
                    'borlabs-cookie',
                ),
                'name' => _x(
                    'Name',
                    'Backend / Providers / Label',
                    'borlabs-cookie',
                ),
                'optOutUrl' => _x(
                    'Opt-out URL',
                    'Backend / Providers / Label',
                    'borlabs-cookie',
                ),
                'partners' => _x(
                    'Partners',
                    'Backend / Providers / Label',
                    'borlabs-cookie',
                ),
                'privacyUrl' => _x(
                    '<translation-key id="Privacy-Url">Privacy URL</translation-key>',
                    'Backend / Providers / Label',
                    'borlabs-cookie',
                ),
            ],

            // Headlines
            'headline' => [
                'providerInformation' => _x(
                    'Provider Information',
                    'Backend / Providers / Headline',
                    'borlabs-cookie',
                ),
            ],

            // Hint
            'hint' => [
                'iabVendorId' => _x(
                    'You can optionally provide the <translation-key id="IAB-Vendor-ID">IAB Vendor ID</translation-key> to add information about an IAB Vendor that is not part of the IAB TCF standard.',
                    'Backend / Providers / Label',
                    'borlabs-cookie',
                ),
            ],

            // Placeholder
            'placeholder' => [
            ],

            // Tables
            'table' => [
            ],

            // Things to know
            'thingsToKnow' => [
                'headlinePurposeProviders' => _x(
                    'What is the purpose of the <translation-key id="Providers">Providers</translation-key> section?',
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
            ],
        ];
    }
}
