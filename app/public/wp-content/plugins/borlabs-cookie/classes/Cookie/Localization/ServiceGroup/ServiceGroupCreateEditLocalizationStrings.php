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
 * The **ServiceGroupCreateEditLocalizationStrings** class contains various localized strings.
 *
 * @see \Borlabs\Cookie\Localization\ServiceGroup\ServiceGroupCreateEditLocalizationStrings::get()
 */
final class ServiceGroupCreateEditLocalizationStrings implements LocalizationInterface
{
    /**
     * @return array<array<string>>
     */
    public static function get(): array
    {
        return [
            // Alert messages
            'alert' => [
            ],

            // Breadcrumbs
            'breadcrumb' => [
                'edit' => _x(
                    'Edit: {{ name }}',
                    'Backend / Service Group / Breadcrumb',
                    'borlabs-cookie',
                ),
                'module' => _x(
                    'Service Groups',
                    'Backend / Service Group / Breadcrumb',
                    'borlabs-cookie',
                ),
                'new' => _x(
                    'New',
                    'Backend / Service Group / Breadcrumb',
                    'borlabs-cookie',
                ),
            ],

            // Fields
            'field' => [
                'description' => _x(
                    'Description',
                    'Backend / Service Groups / Label',
                    'borlabs-cookie',
                ),
                'key' => _x(
                    'ID',
                    'Backend / Service Groups / Label',
                    'borlabs-cookie',
                ),
                'name' => _x(
                    'Name',
                    'Backend / Service Groups / Label',
                    'borlabs-cookie',
                ),
                'position' => _x(
                    'Position',
                    'Backend / Service Groups / Label',
                    'borlabs-cookie',
                ),
                'preSelected' => _x(
                    'Pre-selected',
                    'Backend / Service Groups / Label',
                    'borlabs-cookie',
                ),
                'shortcode' => _x(
                    '<translation-key id="Shortcode">Shortcode</translation-key>',
                    'Backend / Service Groups / Label',
                    'borlabs-cookie',
                ),
                'status' => _x(
                    'Status',
                    'Backend / Service Groups / Label',
                    'borlabs-cookie',
                ),
            ],

            // Headlines
            'headline' => [
                'serviceGroupInformation' => _x(
                    'Service Group Information',
                    'Backend / Service Groups / Headline',
                    'borlabs-cookie',
                ),
                'serviceGroupSettings' => _x(
                    'Service Group Settings',
                    'Backend / Service Groups / Headline',
                    'borlabs-cookie',
                ),
            ],

            // Hint
            'hint' => [
                'description' => _x(
                    'Enter a description for this <translation-key id="Service-Group">Service Group</translation-key>. It is displayed to the visitor in the <translation-key id="Dialog">Dialog</translation-key>.',
                    'Backend / Service Groups / Hint',
                    'borlabs-cookie',
                ),
                'key' => _x(
                    '<translation-key id="ID">ID</translation-key> must be set. The <translation-key id="ID">ID</translation-key> must be at least 3 characters long and may only contain: <strong><em>a-z - _</em></strong>',
                    'Backend / Service Groups / Hint',
                    'borlabs-cookie',
                ),
                'name' => _x(
                    'Choose a name for this <translation-key id="Service-Group">Service Group</translation-key>. It is displayed to the visitor in the <translation-key id="Dialog">Dialog</translation-key>.',
                    'Backend / Service Groups / Hint',
                    'borlabs-cookie',
                ),
                'position' => _x(
                    'Determine the position where this <translation-key id="Service-Group">Service Group</translation-key> is displayed. Order follows natural numbers.',
                    'Backend / Service Groups / Hint',
                    'borlabs-cookie',
                ),
                'preSelected' => _x(
                    'If enabled, this <translation-key id="Service-Group">Service Group</translation-key> is pre-selected in the <translation-key id="Dialog">Dialog</translation-key>. The visitor can de-select it.',
                    'Backend / Service Groups / Hint',
                    'borlabs-cookie',
                ),
                'shortcode' => _x(
                    'Use this <translation-key id="Shortcode">Shortcode</translation-key> to unblock JavaScript or content when user opted-in for this <translation-key id="Service-Group">Service Group</translation-key>.',
                    'Backend / Service Groups / Hint',
                    'borlabs-cookie',
                ),
                'status' => _x(
                    'The status of this <translation-key id="Service-Group">Service Group</translation-key>. If enabled it is displayed to the visitor in the <translation-key id="Dialog">Dialog</translation-key>.',
                    'Backend / Service Groups / Hint',
                    'borlabs-cookie',
                ),
            ],

            // Placeholder
            'placeholder' => [
                'blockThis' => _x(
                    '...block this...',
                    'Backend / Service Groups / Input Placeholder',
                    'borlabs-cookie',
                ),
            ],

            // Tables
            'table' => [
            ],

            // Things to know
            'thingsToKnow' => [
                'headlineShortcode' => _x(
                    '<translation-key id="Shortcode">Shortcode</translation-key> explained',
                    'Backend / Service Groups / Things to know / Headline',
                    'borlabs-cookie',
                ),
                'shortcodeExplainedA' => _x(
                    'The <translation-key id="Shortcode">Shortcode</translation-key> can be used to execute custom code once the visitor has given consent to a <translation-key id="Service">Service</translation-key> associated with this type of <translation-key id="Service-Group">Service Group</translation-key>. This can be used, for example, to block a conversion pixel code.',
                    'Backend / Services / Things to know / Text',
                    'borlabs-cookie',
                ),
                'shortcodeExplainedB' => _x(
                    'The <translation-key id="Shortcode">Shortcode</translation-key> for example can be used in the <translation-key id="Meta-Box">Meta Box</translation-key> of <translation-key id="Borlabs-Cookie">Borlabs Cookie</translation-key>. You can find it for example in <translation-key id="Posts">Posts</translation-key> &raquo; <em><translation-key id="Your-Post">Your Post</translation-key></em> &raquo; <translation-key id="Borlabs-Cookie">Borlabs Cookie</translation-key> &raquo; <translation-key id="Custom-Code">Custom Code</translation-key>.',
                    'Backend / Services / Things to know / Text',
                    'borlabs-cookie',
                ),
                'shortcodesAvailableAfterCreation' => _x(
                    'The <translation-key id="Shortcodes">Shortcodes</translation-key> are not available until the <translation-key id="Service-Group">Service Group</translation-key> is created.',
                    'Backend / Services / Things to know / Text',
                    'borlabs-cookie',
                ),
            ],
        ];
    }
}
