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

namespace Borlabs\Cookie\Localization\Dialog;

use Borlabs\Cookie\Localization\LocalizationInterface;

use function Borlabs\Cookie\System\WordPressGlobalFunctions\_x;

/**
 * The **DialogSettingsLocalizationStrings** class contains various localized strings.
 *
 * @see \Borlabs\Cookie\Localization\Dialog\DialogAppearanceLocalizationStrings::get()
 */
final class DialogAppearanceLocalizationStrings implements LocalizationInterface
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
                'module' => _x(
                    '<translation-key id="Dialog-Appearance">Dialog - Appearance</translation-key>',
                    'Backend / Dialog Appearance / Breadcrumb',
                    'borlabs-cookie',
                ),
            ],

            // Buttons
            'button' => [
                /** @see SetupAssistantLocalizationStrings::class The identical string is used here. */
                'applyColors' => _x(
                    'Apply Colors',
                    'Backend / Dialog Appearance / Button',
                    'borlabs-cookie',
                ),
                /** @see SetupAssistantLocalizationStrings::class The identical string is used here. */
                'updatePreview' => _x(
                    'Update Preview',
                    'Backend / Dialog Appearance / Button',
                    'borlabs-cookie',
                ),
            ],

            // Fields
            'field' => [
                'active' => _x(
                    'Active',
                    'Backend / Dialog Appearance / Label',
                    'borlabs-cookie',
                ),
                'backdrop' => _x(
                    '<translation-key id="Backdrop">Backdrop</translation-key>',
                    'Backend / Dialog Appearance / Label',
                    'borlabs-cookie',
                ),
                'backgroundColor' => _x(
                    'Background Color',
                    'Backend / Dialog Appearance / Label',
                    'borlabs-cookie',
                ),
                'borderColor' => _x(
                    'Border Color',
                    'Backend / Dialog Appearance / Label',
                    'borlabs-cookie',
                ),
                'borderRadius' => _x(
                    'Border Radius',
                    'Backend / Dialog Appearance / Label',
                    'borlabs-cookie',
                ),
                'bottom' => _x(
                    'Bottom',
                    'Backend / Dialog Appearance / Label',
                    'borlabs-cookie',
                ),
                'bottomLeft' => _x(
                    'Bottom Left',
                    'Backend / Dialog Appearance / Label',
                    'borlabs-cookie',
                ),
                'bottomRight' => _x(
                    'Bottom Right',
                    'Backend / Dialog Appearance / Label',
                    'borlabs-cookie',
                ),
                'button' => _x(
                    'Button',
                    'Backend / Dialog Appearance / Label',
                    'borlabs-cookie',
                ),
                'buttonColor' => _x(
                    'Button Color',
                    'Backend / Dialog Appearance / Label',
                    'borlabs-cookie',
                ),
                'buttonTextColor' => _x(
                    'Button Text Color',
                    'Backend / Dialog Appearance / Label',
                    'borlabs-cookie',
                ),
                'card' => _x(
                    'Card',
                    'Backend / Dialog Appearance / Label',
                    'borlabs-cookie',
                ),
                'checkbox' => _x(
                    'Checkbox',
                    'Backend / Global / Text',
                    'borlabs-cookie',
                ),
                /** @see SetupAssistantLocalizationStrings::class The identical string is used here. */
                'colorAssistantAccentColor' => _x(
                    '<translation-key id="Accent-Color">Accent Color</translation-key>',
                    'Backend / Dialog Appearance / Label',
                    'borlabs-cookie',
                ),
                /** @see SetupAssistantLocalizationStrings::class The identical string is used here. */
                'colorAssistantContrastColor' => _x(
                    '<translation-key id="Contrast-Color">Contrast Color</translation-key>',
                    'Backend / Dialog Appearance / Label',
                    'borlabs-cookie',
                ),
                /** @see SetupAssistantLocalizationStrings::class The identical string is used here. */
                'colorAssistantDialogBackgroundColor' => _x(
                    '<translation-key id="Dialog-Background-Color">Dialog Background Color</translation-key>',
                    'Backend / Dialog Appearance / Label',
                    'borlabs-cookie',
                ),
                /** @see SetupAssistantLocalizationStrings::class The identical string is used here. */
                'colorAssistantTextMode' => _x(
                    '<translation-key id="Text-Mode">Text Mode</translation-key>',
                    'Backend / Dialog Appearance / Label',
                    'borlabs-cookie',
                ),
                'customCss' => _x(
                    'CSS',
                    'Backend / Dialog Appearance / Label',
                    'borlabs-cookie',
                ),
                'default' => _x(
                    'Default',
                    'Backend / Dialog Appearance / Label',
                    'borlabs-cookie',
                ),
                'dialog' => _x(
                    'Dialog',
                    'Backend / Dialog Appearance / Label',
                    'borlabs-cookie',
                ),
                'dialogBorderRadius' => _x(
                    'Dialog Border Radius',
                    'Backend / Dialog Appearance / Label',
                    'borlabs-cookie',
                ),
                'dialogCardBorderRadius' => _x(
                    'Card Border Radius',
                    'Backend / Dialog Appearance / Label',
                    'borlabs-cookie',
                ),
                'dialogCardListPaddingMediumScreen' => _x(
                    'Card List Padding Medium Screen',
                    'Backend / Dialog Appearance / Label',
                    'borlabs-cookie',
                ),
                'dialogCardListPaddingSmallScreen' => _x(
                    'Card List Padding Small Screen',
                    'Backend / Dialog Appearance / Label',
                    'borlabs-cookie',
                ),
                'dialogButtonAcceptAllColor' => _x(
                    'Accept All - Button Color',
                    'Backend / Dialog Appearance / Label',
                    'borlabs-cookie',
                ),
                'dialogButtonAcceptAllTextColor' => _x(
                    'Accept All - Button Text Color',
                    'Backend / Dialog Appearance / Label',
                    'borlabs-cookie',
                ),
                'dialogButtonAcceptOnlyEssentialColor' => _x(
                    'Accept Only Essential - Button Color',
                    'Backend / Dialog Appearance / Label',
                    'borlabs-cookie',
                ),
                'dialogButtonAcceptOnlyEssentialTextColor' => _x(
                    'Accept Only Essential - Button Text Color',
                    'Backend / Dialog Appearance / Label',
                    'borlabs-cookie',
                ),
                'dialogButtonBorderRadius' => _x(
                    'Button Border Radius',
                    'Backend / Dialog Appearance / Label',
                    'borlabs-cookie',
                ),
                'dialogButtonCloseColor' => _x(
                    'Close - Button Color',
                    'Backend / Dialog Appearance / Label',
                    'borlabs-cookie',
                ),
                'dialogButtonCloseTextColor' => _x(
                    'Close - Button Text Color',
                    'Backend / Dialog Appearance / Label',
                    'borlabs-cookie',
                ),
                'dialogButtonPreferencesColor' => _x(
                    'Privacy Preferences - Button Color',
                    'Backend / Dialog Appearance / Label',
                    'borlabs-cookie',
                ),
                'dialogButtonPreferencesTextColor' => _x(
                    'Privacy Preferences - Button Text Color',
                    'Backend / Dialog Appearance / Label',
                    'borlabs-cookie',
                ),
                'dialogButtonSaveConsentColor' => _x(
                    'Save Consent - Button Color',
                    'Backend / Dialog Appearance / Label',
                    'borlabs-cookie',
                ),
                'dialogButtonSaveConsentTextColor' => _x(
                    'Save Consent - Button Text Color',
                    'Backend / Dialog Appearance / Label',
                    'borlabs-cookie',
                ),
                'dialogButtonSelectionColor' => _x(
                    'Selection - Button Color',
                    'Backend / Dialog Appearance / Label',
                    'borlabs-cookie',
                ),
                'dialogButtonSelectionTextColor' => _x(
                    'Selection - Button Text Color',
                    'Backend / Dialog Appearance / Label',
                    'borlabs-cookie',
                ),
                'dialogCheckMark' => _x(
                    'Check Mark',
                    'Backend / Dialog Appearance / Label',
                    'borlabs-cookie',
                ),
                'dialogCheckboxActive' => _x(
                    'Checkbox Active',
                    'Backend / Dialog Appearance / Label',
                    'borlabs-cookie',
                ),
                'dialogCheckboxBorderRadius' => _x(
                    'Checkbox Border Radius',
                    'Backend / Dialog Appearance / Label',
                    'borlabs-cookie',
                ),
                'dialogCheckboxDisabled' => _x(
                    'Checkbox Disabled',
                    'Backend / Dialog Appearance / Label',
                    'borlabs-cookie',
                ),
                'dialogCheckboxInactive' => _x(
                    'Checkbox Inactive',
                    'Backend / Dialog Appearance / Label',
                    'borlabs-cookie',
                ),
                'dialogControlElement' => _x(
                    'Control Element',
                    'Backend / Dialog Appearance / Label',
                    'borlabs-cookie',
                ),
                'dialogFooter' => _x(
                    'Dialog Footer',
                    'Backend / Dialog Appearance / Label',
                    'borlabs-cookie',
                ),
                'dialogListBorderRadius' => _x(
                    'List Border Radius',
                    'Backend / Dialog Appearance / Label',
                    'borlabs-cookie',
                ),
                'dialogListItemControlElement' => _x(
                    'List Item Control Element',
                    'Backend / Dialog Appearance / Label',
                    'borlabs-cookie',
                ),
                'dialogListItemControlElementSeparatorColor' => _x(
                    'List Item Control Element Separator Color',
                    'Backend / Dialog Appearance / Label',
                    'borlabs-cookie',
                ),
                'dialogListItemEven' => _x(
                    'List Item Even',
                    'Backend / Dialog Appearance / Label',
                    'borlabs-cookie',
                ),
                'dialogListItemOdd' => _x(
                    'List Item Odd',
                    'Backend / Dialog Appearance / Label',
                    'borlabs-cookie',
                ),
                'dialogListItemSeparatorColor' => _x(
                    'List Item Separator Color',
                    'Backend / Dialog Appearance / Label',
                    'borlabs-cookie',
                ),
                'dialogListItemSeparatorWidth' => _x(
                    'List Item Separator Width',
                    'Backend / Dialog Appearance / Label',
                    'borlabs-cookie',
                ),
                'dialogListPaddingMediumScreen' => _x(
                    'List Padding Medium Screen',
                    'Backend / Dialog Appearance / Label',
                    'borlabs-cookie',
                ),
                'dialogListPaddingSmallScreen' => _x(
                    'List Padding Small Screen',
                    'Backend / Dialog Appearance / Label',
                    'borlabs-cookie',
                ),
                'dialogLinkPrimaryColor' => _x(
                    'Primary Link Color',
                    'Backend / Dialog Appearance / Label',
                    'borlabs-cookie',
                ),
                'dialogLinkSecondaryColor' => _x(
                    'Secondary Link Color',
                    'Backend / Dialog Appearance / Label',
                    'borlabs-cookie',
                ),
                'dialogSearchBarInput' => _x(
                    'Search Input',
                    'Backend / Dialog Appearance / Label',
                    'borlabs-cookie',
                ),
                'dialogSearchBarInputBorderColor' => _x(
                    'Search Input Border Color',
                    'Backend / Dialog Appearance / Label',
                    'borlabs-cookie',
                ),
                'dialogSearchBarInputBorderRadius' => _x(
                    'Search Input Border Radius',
                    'Backend / Dialog Appearance / Label',
                    'borlabs-cookie',
                ),
                'dialogSearchBarInputBorderWidth' => _x(
                    'Search Input Border Width',
                    'Backend / Dialog Appearance / Label',
                    'borlabs-cookie',
                ),
                'dialogSeparatorColor' => _x(
                    'Separator Color',
                    'Backend / Dialog Appearance / Label',
                    'borlabs-cookie',
                ),
                'dialogSwitchButtonBackgroundColor' => _x(
                    'Switch Button Background Color',
                    'Backend / Dialog Appearance / Label',
                    'borlabs-cookie',
                ),
                'dialogSwitchButtonColor' => _x(
                    'Switch Button Color',
                    'Backend / Dialog Appearance / Label',
                    'borlabs-cookie',
                ),
                'dialogTabBarTabBackgroundColor' => _x(
                    'Background Color',
                    'Backend / Dialog Appearance / Label',
                    'borlabs-cookie',
                ),
                'dialogTabBarTabBorderColorTopBottomActive' => _x(
                    'Border Color Top Bottom Active',
                    'Backend / Dialog Appearance / Label',
                    'borlabs-cookie',
                ),
                'dialogTabBarTabBorderColorLeftRightActive' => _x(
                    'Border Color Left Right Active',
                    'Backend / Dialog Appearance / Label',
                    'borlabs-cookie',
                ),
                'dialogTabBarTabBorderColorTopBottomInactive' => _x(
                    'Border Color Top Bottom Inactive',
                    'Backend / Dialog Appearance / Label',
                    'borlabs-cookie',
                ),
                'dialogTabBarTabBorderColorLeftRightInactive' => _x(
                    'Border Color Left Right Inactive',
                    'Backend / Dialog Appearance / Label',
                    'borlabs-cookie',
                ),
                'dialogTabBarTabBorderRadiusActive' => _x(
                    'Tab Border Radius Active',
                    'Backend / Dialog Appearance / Label',
                    'borlabs-cookie',
                ),
                'dialogTabBarTabBorderRadiusInactive' => _x(
                    'Tab Border Radius Inactive',
                    'Backend / Dialog Appearance / Label',
                    'borlabs-cookie',
                ),
                'dialogTabBarTabBorderWidthActive' => _x(
                    'Tab Border Width Active',
                    'Backend / Dialog Appearance / Label',
                    'borlabs-cookie',
                ),
                'dialogTabBarTabBorderWidthInactive' => _x(
                    'Tab Border Width Inactive',
                    'Backend / Dialog Appearance / Label',
                    'borlabs-cookie',
                ),
                'dialogTabBarTabTextColor' => _x(
                    'Text Color',
                    'Backend / Dialog Appearance / Label',
                    'borlabs-cookie',
                ),
                'dialogTableBorderRadius' => _x(
                    'Table Border Radius',
                    'Backend / Dialog Appearance / Label',
                    'borlabs-cookie',
                ),
                'dialogTableCellPadding' => _x(
                    'Table Cell Padding',
                    'Backend / Dialog Appearance / Label',
                    'borlabs-cookie',
                ),
                'dialogTableRowBorderColor' => _x(
                    'Table Row Border Color',
                    'Backend / Dialog Appearance / Label',
                    'borlabs-cookie',
                ),
                'dialogTableRowEven' => _x(
                    'Table Row Even',
                    'Backend / Dialog Appearance / Label',
                    'borlabs-cookie',
                ),
                'dialogTableRowOdd' => _x(
                    'Table Row Odd',
                    'Backend / Dialog Appearance / Label',
                    'borlabs-cookie',
                ),
                'disabled' => _x(
                    'Disabled',
                    'Backend / Dialog Appearance / Label',
                    'borlabs-cookie',
                ),
                'focus' => _x(
                    'Focus',
                    'Backend / Dialog Appearance / Label',
                    'borlabs-cookie',
                ),
                'fontFamily' => _x(
                    'Font Family',
                    'Backend / Dialog Appearance / Label',
                    'borlabs-cookie',
                ),
                'fontSize' => _x(
                    '<translation-key id="Font-Size-Base">Font Size (Base)</translation-key>',
                    'Backend / Dialog Appearance / Label',
                    'borlabs-cookie',
                ),
                'hover' => _x(
                    'Hover',
                    'Backend / Dialog Appearance / Label',
                    'borlabs-cookie',
                ),
                'inactive' => _x(
                    'Inactive',
                    'Backend / Dialog Appearance / Label',
                    'borlabs-cookie',
                ),
                'left' => _x(
                    'Left',
                    'Backend / Dialog Appearance / Label',
                    'borlabs-cookie',
                ),
                'linkColor' => _x(
                    'Link Color',
                    'Backend / Dialog Appearance / Label',
                    'borlabs-cookie',
                ),
                'opacity' => _x(
                    'Opacity',
                    'Backend / Dialog Appearance / Label',
                    'borlabs-cookie',
                ),
                'right' => _x(
                    'Right',
                    'Backend / Dialog Appearance / Label',
                    'borlabs-cookie',
                ),
                'screenSmall' => _x(
                    '<translation-key id="Small-Screen">Small Screen</translation-key>',
                    'Backend / Dialog Appearance / Label',
                    'borlabs-cookie',
                ),
                'screenMedium' => _x(
                    '<translation-key id="Medium-Screen">Medium Screen</translation-key>',
                    'Backend / Dialog Appearance / Label',
                    'borlabs-cookie',
                ),
                'table' => _x(
                    'Table',
                    'Backend / Dialog Appearance / Label',
                    'borlabs-cookie',
                ),
                'textColor' => _x(
                    'Text Color',
                    'Backend / Dialog Appearance / Label',
                    'borlabs-cookie',
                ),
                'top' => _x(
                    'Top',
                    'Backend / Dialog Appearance / Label',
                    'borlabs-cookie',
                ),
                'topLeft' => _x(
                    'Top Left',
                    'Backend / Dialog Appearance / Label',
                    'borlabs-cookie',
                ),
                'topRight' => _x(
                    'Top Right',
                    'Backend / Dialog Appearance / Label',
                    'borlabs-cookie',
                ),
            ],

            // Headlines
            'headline' => [
                'borderSettings' => _x(
                    'Border Settings',
                    'Backend / Dialog Appearance / Headline',
                    'borlabs-cookie',
                ),
                'buttonSettings' => _x(
                    'Button Settings',
                    'Backend / Dialog Appearance / Headline',
                    'borlabs-cookie',
                ),
                'cardSettings' => _x(
                    '<translation-key id="Card">Card</translation-key> Settings',
                    'Backend / Dialog Appearance / Headline',
                    'borlabs-cookie',
                ),
                'checkboxSettings' => _x(
                    'Checkbox Settings',
                    'Backend / Dialog Appearance / Headline',
                    'borlabs-cookie',
                ),
                /** @see SetupAssistantLocalizationStrings::class The identical string is used here. */
                'colorAssistant' => _x(
                    'Color Assistant',
                    'Backend / Dialog Appearance / Headline',
                    'borlabs-cookie',
                ),
                'customCss' => _x(
                    'Custom CSS',
                    'Backend / Dialog Appearance / Headline',
                    'borlabs-cookie',
                ),
                'dialogSettings' => _x(
                    'Dialog Settings',
                    'Backend / Dialog Appearance / Headline',
                    'borlabs-cookie',
                ),
                'linkSettings' => _x(
                    'Link Settings',
                    'Backend / Dialog Appearance / Headline',
                    'borlabs-cookie',
                ),
                'listSettings' => _x(
                    '<translation-key id="List">List</translation-key> Settings',
                    'Backend / Dialog Appearance / Headline',
                    'borlabs-cookie',
                ),
                'resetDialogAppearanceSettings' => _x(
                    'Reset <translation-key id="Dialog-Appearance-Settings">Dialog Appearance</translation-key> Settings',
                    'Backend / Dialog Appearance / Headline',
                    'borlabs-cookie',
                ),
                /** @see SetupAssistantLocalizationStrings::class The identical string is used here. */
                'schematicPreview' => _x(
                    'Schematic Preview',
                    'Backend / Dialog Appearance / Headline',
                    'borlabs-cookie',
                ),
                'searchBarSettings' => _x(
                    'Search Bar Settings',
                    'Backend / Dialog Appearance / Headline',
                    'borlabs-cookie',
                ),
                'switchButtonSettings' => _x(
                    'Switch Button Settings',
                    'Backend / Dialog Appearance / Headline',
                    'borlabs-cookie',
                ),
                'tabBarSettings' => _x(
                    'Tab Bar Settings',
                    'Backend / Dialog Appearance / Headline',
                    'borlabs-cookie',
                ),
                'tableSettings' => _x(
                    'Table Settings',
                    'Backend / Dialog Appearance / Headline',
                    'borlabs-cookie',
                ),
            ],

            // Hint
            'hint' => [
                /** @see SetupAssistantLocalizationStrings::class The identical string is used here. */
                'colorAssistantAccentColor' => _x(
                    'The <translation-key id="Accent-Color">Accent Color</translation-key> is primarily used as the background color for buttons, checkboxes, and tabs.',
                    'Backend / Dialog Appearance / Hint',
                    'borlabs-cookie',
                ),
                /** @see SetupAssistantLocalizationStrings::class The identical string is used here. */
                'colorAssistantContrastColor' => _x(
                    'The <translation-key id="Contrast-Color">Contrast Color</translation-key> is primarily used for text displayed on backgrounds with the <translation-key id="Accent-Color">Accent Color</translation-key>.',
                    'Backend / Dialog Appearance / Hint',
                    'borlabs-cookie',
                ),
                /** @see SetupAssistantLocalizationStrings::class The identical string is used here. */
                'colorAssistantDialogBackgroundColor' => _x(
                    'The <translation-key id="Dialog-Background-Color">Dialog Background Color</translation-key> sets the primary background for the dialog and is also applied in lighter or darker variations to distinguish different areas within the dialog.',
                    'Backend / Dialog Appearance / Hint',
                    'borlabs-cookie',
                ),
                /** @see SetupAssistantLocalizationStrings::class The identical string is used here. */
                'colorAssistantTextMode' => _x(
                    'When <translation-key id="Text-Mode">Text Mode</translation-key> is enabled, placeholder text is displayed in the scheme instead of simple bars. This helps evaluate the legibility of the <translation-key id="Accent-Color">Accent Color</translation-key> and <translation-key id="Contrast-Color">Contrast Color</translation-key>.',
                    'Backend / Dialog Appearance / Hint',
                    'borlabs-cookie',
                ),
                'customCss' => _x(
                    'Add your custom CSS to customize the <translation-key id="Dialog">Dialog</translation-key>.',
                    'Backend / Dialog Appearance / Hint',
                    'borlabs-cookie',
                ),
                'dialogButtonCloseColor' => _x(
                    'For content blocked by a <translation-key id="Content-Blocker">Content Blocker</translation-key>, there is always a button available to open the dialog with provider information. This setting influences the visual presentation of the button used to close this dialog.',
                    'Backend / Dialog Appearance / Hint',
                    'borlabs-cookie',
                ),
                'dialogButtonSelectionColor' => _x(
                    'When the <translation-key id="Iab-Tcf-Status">IAB TCF Status</translation-key> setting is enabled (found under <em><translation-key id="Navigation-Consent-Management">Consent Management</translation-key> &raquo; <translation-key id="Navigation-Iab-Tcf">IAB TCF</translation-key> &raquo; <translation-key id="Navigation-Iab-Tcf-Settings">Settings</translation-key></em>) this setting influences the visual presentation of the button responsible for showing all vendors.',
                    'Backend / Dialog Appearance / Hint',
                    'borlabs-cookie',
                ),
                'dialogFontFamily' => _x(
                    'Usually the theme font is used. To use a custom font family select the checkbox and enter custom font.',
                    'Backend / Dialog Appearance / Hint',
                    'borlabs-cookie',
                ),
                'dialogCardListPadding' => _x(
                    'The default padding is defined for <translation-key id="Small-Screen">Small Screen</translation-key>. However, once the viewport width surpasses 640px, the padding specified for <translation-key id="Medium-Screen">Medium Screen</translation-key> will be applied.',
                    'Backend / Dialog Appearance / Hint',
                    'borlabs-cookie',
                ),
                'dialogCardSeparatorColor' => _x(
                    'The separator is exclusively visible on smaller displays, such as smartphones.',
                    'Backend / Dialog Appearance / Hint',
                    'borlabs-cookie',
                ),
                'dialogFontSize' => _x(
                    'Based on the base font size, the font sizes of all elements are automatically adjusted proportionally.',
                    'Backend / Dialog Appearance / Hint',
                    'borlabs-cookie',
                ),
                'dialogListPadding' => _x(
                    'The default padding is defined for <translation-key id="Small-Screen">Small Screen</translation-key>. However, once the viewport width surpasses 640px, the padding specified for <translation-key id="Medium-Screen">Medium Screen</translation-key> will be applied.',
                    'Backend / Dialog Appearance / Hint',
                    'borlabs-cookie',
                ),
                'reset' => _x(
                    'Please confirm that you want to reset all <translation-key id="Dialog-Appearance-Settings">Dialog Appearance</translation-key> settings. They will be reset to their default settings.',
                    'Backend / Dialog Appearance / Hint',
                    'borlabs-cookie',
                ),
            ],

            // Options (select | checkbox | radio)
            'option' => [
            ],

            // Placeholder
            'placeholder' => [
                'dialogFontFamily' => _x(
                    'Enter custom font family',
                    'Backend / Dialog Appearance / Input Placeholder',
                    'borlabs-cookie',
                ),
            ],

            // Text
            'text' => [
            ],

            // Things to know
            'thingsToKnow' => [
                'cardExplained' => _x(
                    'A <translation-key id="Card">Card</translation-key> displays the name and description of a <translation-key id="Service-Group">Service Group</translation-key>.',
                    'Backend / Dialog Appearance / Things to know / Text',
                    'borlabs-cookie',
                ),
                'headlineOnWhichElementsDoTheseSettingsHaveAVisualImpact' => _x(
                    'On which elements do these settings have a visual impact?',
                    'Backend / Dialog Appearance / Things to know / Headline',
                    'borlabs-cookie',
                ),
                'listExplained' => _x(
                    'A <translation-key id="Card">Card</translation-key> provides a <translation-key id="List">List</translation-key> of <translation-key id="Services">Services</translation-key> associated with the respective <translation-key id="Service-Group">Service Group</translation-key>.',
                    'Backend / Dialog Appearance / Things to know / Text',
                    'borlabs-cookie',
                ),
                'onWhichElementsDoTheseSettingsHaveAVisualImpact' => _x(
                    'Click on the image to see what is affected by these settings.',
                    'Backend / Dialog Appearance / Things to know / Text',
                    'borlabs-cookie',
                ),
                'tableExplained' => _x(
                    'Tables are typically located in the details section of a <translation-key id="Service">Service</translation-key>, <translation-key id="Provder">Provider</translation-key>, or <translation-key id="Consent-History">Consent History</translation-key>.',
                    'Backend / Dialog Appearance / Things to know / Text',
                    'borlabs-cookie',
                ),
            ],
        ];
    }
}
