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
 * The **ServiceGroupOverviewLocalizationStrings** class contains various localized strings.
 *
 * @see \Borlabs\Cookie\Localization\ServiceGroup\ServiceGroupOverviewLocalizationStrings::get()
 */
final class ServiceGroupOverviewLocalizationStrings implements LocalizationInterface
{
    /**
     * @return array<array<string>>
     */
    public static function get(): array
    {
        return [
            // Alert messages
            'alert' => [
                'noServiceGroupConfigured' => _x(
                    'No <translation-key id="Service-Group">Service Group</translation-key> configured.',
                    'Backend / Service Group / Alert Message',
                    'borlabs-cookie',
                ),
            ],

            // Breadcrumbs
            'breadcrumb' => [
                'module' => _x(
                    'Service Groups',
                    'Backend / Service Group / Breadcrumb',
                    'borlabs-cookie',
                ),
            ],

            // Fields
            'field' => [
                'confirmReset' => _x(
                    'Confirm Reset',
                    'Backend / Global / Label',
                    'borlabs-cookie',
                ),
                'confirmResetConfirmation' => _x(
                    'Confirmed',
                    'Backend / Global / Text',
                    'borlabs-cookie',
                ),
            ],

            // Headlines
            'headline' => [
                'resetDefaultServiceGroups' => _x(
                    'Reset Default Service Groups',
                    'Backend / Service Groups / Headline',
                    'borlabs-cookie',
                ),
                'serviceGroups' => _x(
                    'Service Groups',
                    'Backend / Service Groups / Headline',
                    'borlabs-cookie',
                ),
            ],

            // Hint
            'hint' => [
                'reset' => _x(
                    'Please confirm that you want to reset the <translation-key id="Service-Groups">Service Groups</translation-key>. They will be reset to their default settings. Your custom <translation-key id="Service-Groups">Service Groups</translation-key> remain unchanged.',
                    'Backend / Service Groups / Hint',
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
                    'Backend / Service Groups / Table Headline',
                    'borlabs-cookie',
                ),
                'name' => _x(
                    'Name',
                    'Backend / Service Groups / Table Headline',
                    'borlabs-cookie',
                ),
                'position' => _x(
                    'Position',
                    'Backend / Service Groups / Table Headline',
                    'borlabs-cookie',
                ),
                'status' => _x(
                    'Status',
                    'Backend / Service Groups / Table Headline',
                    'borlabs-cookie',
                ),
            ],

            // Things to know
            'thingsToKnow' => [
                'headlinePurposeServiceGroups' => _x(
                    'What is the purpose of the <translation-key id="Service-Groups">Service Groups</translation-key> section?',
                    'Backend / Service Groups / Things to know / Headline',
                    'borlabs-cookie',
                ),
                'headlineSymbolsExplained' => _x(
                    'Symbols explained',
                    'Backend / Service Groups / Things to know / Headline',
                    'borlabs-cookie',
                ),
                'purposeServiceGroupsExplained' => _x(
                    '<translation-key id="Services">Services</translation-key> can be grouped thematically into <translation-key id="Service-Groups">Service Groups</translation-key> which are displayed to the visitor. Unused <translation-key id="Service-Groups">Service Groups</translation-key> can be disabled, new ones can be added. The <translation-key id="Service-Group">Service Group</translation-key>: <translation-key id="Essential">Essential</translation-key> cannot be deactivated. All <translation-key id="Services">Services</translation-key> belonging to <translation-key id="Essential">Essential</translation-key> are always issued. An active <translation-key id="Service-Group">Service Group</translation-key> is only displayed in the <translation-key id="Dialog">Dialog</translation-key>, if at least one active <translation-key id="Service">Service</translation-key> is assigned to it.',
                    'Backend / Service Groups / Things to know / Text',
                    'borlabs-cookie',
                ),
                'symbolExplainedActive' => _x(
                    'The <translation-key id="Service-Group">Service Group</translation-key> is active and displayed in the <translation-key id="Dialog">Dialog</translation-key>. Not available for the <translation-key id="Service-Group">Service Group</translation-key>: <translation-key id="Essential">Essential</translation-key>.',
                    'Backend / Service Groups / Things to know / Text',
                    'borlabs-cookie',
                ),
                'symbolExplainedDelete' => _x(
                    'Delete the <translation-key id="Service-Group">Service Group</translation-key>. Not available for default <translation-key id="Service-Groups">Service Groups</translation-key>.',
                    'Backend / Service Groups / Things to know / Text',
                    'borlabs-cookie',
                ),
                'symbolExplainedEdit' => _x(
                    'Edit the <translation-key id="Service-Group">Service Group</translation-key>.',
                    'Backend / Service Groups / Things to know / Text',
                    'borlabs-cookie',
                ),
                'symbolExplainedInactive' => _x(
                    'The <translation-key id="Service-Group">Service Group</translation-key> is inactive and not displayed in the <translation-key id="Dialog">Dialog</translation-key>. Not available for the <translation-key id="Service-Group">Service Group</translation-key>: <translation-key id="Essential">Essential</translation-key>.',
                    'Backend / Service Groups / Things to know / Text',
                    'borlabs-cookie',
                ),
            ],
        ];
    }
}
