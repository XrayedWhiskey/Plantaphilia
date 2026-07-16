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
 * @see \Borlabs\Cookie\Localization\Dialog\DialogSettingsLocalizationStrings::get()
 */
final class DialogSettingsLocalizationStrings implements LocalizationInterface
{
    /**
     * @return array<array<string>>
     */
    public static function get(): array
    {
        return [
            // Alert messages
            'alert' => [
                'downloadGeoIpDatabaseSuccessfully' => _x(
                    'The <translation-key id="GeoIP">GeoIP</translation-key> database has been downloaded successfully.',
                    'Backend / Dialog Settings / Alert Message',
                    'borlabs-cookie',
                ),
                /** @see SetupAssistantLocalizationStrings::class The identical string is used here. */
                'noLanguageOptionConfigured' => _x(
                    'No <translation-key id="Language-Option">Language Option</translation-key> configured.',
                    'Backend / Dialog Settings / Alert Message',
                    'borlabs-cookie',
                ),
                'possibleLawViolationLayout' => _x(
                    'Depending on applicable law, this layout may not be allowed to be used.',
                    'Backend / Dialog Settings / Alert Message',
                    'borlabs-cookie',
                ),
                'possibleLawViolationSettingOff' => _x(
                    'Depending on applicable law, this option may not be allowed to be turned off.',
                    'Backend / Dialog Settings / Alert Message',
                    'borlabs-cookie',
                ),
                'possibleLawViolationSettingOn' => _x(
                    'Depending on applicable law, this option may not be allowed to be turned on.',
                    'Backend / Dialog Settings / Alert Message',
                    'borlabs-cookie',
                ),
                'settingNotAvailableBecauseIabTcfIsEnabled' => _x(
                    'The <translation-key id="IAB-TCF-Status">IAB TCF Status</translation-key> option is currently enabled, located under <em><translation-key id="Navigation-Consent-Management">Consent Management</translation-key> &raquo; <translation-key id="Navigation-Iab-Tcf">IAB TCF</translation-key> &raquo; <translation-key id="Navigation-Iab-Tcf-Settings">Settings</translation-key></em>. Therefore, modifications to this setting have no effect.',
                    'Backend / Dialog Settings / Alert Message',
                    'borlabs-cookie',
                ),
            ],

            // Breadcrumbs
            'breadcrumb' => [
                'module' => _x(
                    '<translation-key id="Dialog-Settings">Dialog - Settings</translation-key>',
                    'Backend / Dialog Settings / Breadcrumb',
                    'borlabs-cookie',
                ),
            ],

            // Buttons
            'button' => [
                /** @see SetupAssistantLocalizationStrings::class The identical string is used here. */
                'addLanguageOption' => _x(
                    'Add Language Option',
                    'Backend / Dialog Settings / Button Title',
                    'borlabs-cookie',
                ),
                'selectOrUploadLogo' => _x(
                    'Select or Upload Logo',
                    'Backend / Dialog Settings / Button Title',
                    'borlabs-cookie',
                ),
                'updateDatabase' => _x(
                    'Update database',
                    'Backend / Dialog Settings / Button Title',
                    'borlabs-cookie',
                ),
                'useThisMedia' => _x(
                    'Use this media',
                    'Backend / Dialog Settings / Button Title',
                    'borlabs-cookie',
                ),
            ],

            // Fields
            'field' => [
                'animation' => _x(
                    'Animation',
                    'Backend / Dialog Settings / Label',
                    'borlabs-cookie',
                ),
                'animationDelay' => _x(
                    'Animation Delay',
                    'Backend / Dialog Settings / Label',
                    'borlabs-cookie',
                ),
                'animationIn' => _x(
                    'Animation In',
                    'Backend / Dialog Settings / Label',
                    'borlabs-cookie',
                ),
                'animationOut' => _x(
                    'Animation Out',
                    'Backend / Dialog Settings / Label',
                    'borlabs-cookie',
                ),
                'animationPreview' => _x(
                    'Animation Preview',
                    'Backend / Dialog Settings / Label',
                    'borlabs-cookie',
                ),
                'buttonDetailsOrder' => _x(
                    'Button Details Order',
                    'Backend / Dialog Settings / Label',
                    'borlabs-cookie',
                ),
                'buttonEntranceOrder' => _x(
                    'Button Entrance Order',
                    'Backend / Dialog Settings / Label',
                    'borlabs-cookie',
                ),
                'buttonSwitchRound' => _x(
                    '<translation-key id="Switch-Button">Switch Button</translation-key> Round',
                    'Backend / Dialog Settings / Label',
                    'borlabs-cookie',
                ),
                'enableBackdrop' => _x(
                    'Enable Backdrop',
                    'Backend / Dialog Settings / Label',
                    'borlabs-cookie',
                ),
                'geoIpActive' => _x(
                    '<translation-key id="GeoIP-Active">GeoIP Active</translation-key>',
                    'Backend / Dialog Settings / Label',
                    'borlabs-cookie',
                ),
                'geoIpCachingMode' => _x(
                    '<translation-key id="GeoIP-Caching-Mode">GeoIP Caching Mode</translation-key> Active',
                    'Backend / Dialog Settings / Label',
                    'borlabs-cookie',
                ),
                'geoIpCountriesWithHiddenDialog' => _x(
                    'Show <translation-key id="Dialog">Dialog</translation-key> Countries',
                    'Backend / Dialog Settings / Label',
                    'borlabs-cookie',
                ),
                'geoIpDatabaseStatus' => _x(
                    'Database Status',
                    'Backend / Dialog Settings / Label',
                    'borlabs-cookie',
                ),
                'hideDialogOnPages' => _x(
                    '<translation-key id="Hide-Dialog-On-Pages">Hide Dialog on Pages</translation-key>',
                    'Backend / Dialog Settings / Label',
                    'borlabs-cookie',
                ),
                /** @see SetupAssistantLocalizationStrings::class The identical string is used here. */
                'imprintPageId' => _x(
                    '<translation-key id="Imprint-Page">Imprint Page</translation-key>',
                    'Backend / Dialog Settings / Label',
                    'borlabs-cookie',
                ),
                'layout' => _x(
                    '<translation-key id="Layout">Layout</translation-key>',
                    'Backend / Dialog Settings / Label',
                    'borlabs-cookie',
                ),
                'logo' => _x(
                    'Logo',
                    'Backend / Dialog Settings / Label',
                    'borlabs-cookie',
                ),
                'logoHd' => _x(
                    'Logo - HD',
                    'Backend / Dialog Settings / Label',
                    'borlabs-cookie',
                ),
                'position' => _x(
                    'Position',
                    'Backend / Dialog Settings / Label',
                    'borlabs-cookie',
                ),
                /** @see SetupAssistantLocalizationStrings::class The identical string is used here. */
                'privacyPageId' => _x(
                    '<translation-key id="Privacy-Page">Privacy Page</translation-key>',
                    'Backend / Dialog Settings / Label',
                    'borlabs-cookie',
                ),
                'serviceGroupJustification' => _x(
                    '<translation-key id="Service-Group-Justification">Service Group Justification</translation-key>',
                    'Backend / Dialog Settings / Label',
                    'borlabs-cookie',
                ),
                'showAcceptAllButton' => _x(
                    'Show "<translation-key id="Accept-All">Accept All</translation-key>" Button',
                    'Backend / Dialog Settings / Label',
                    'borlabs-cookie',
                ),
                'showAcceptOnlyEssentialButton' => _x(
                    'Show "<translation-key id="Accept-Only-Essential">Accept Only Essential</translation-key>" Button',
                    'Backend / Dialog Settings / Label',
                    'borlabs-cookie',
                ),
                'showBorlabsCookieBranding' => _x(
                    'Support Borlabs Cookie',
                    'Backend / Dialog Settings / Label',
                    'borlabs-cookie',
                ),
                'showCloseButton' => _x(
                    'Show "<translation-key id="Dialog-Close-Button">Close</translation-key>" Button',
                    'Backend / Dialog Settings / Label',
                    'borlabs-cookie',
                ),
                'showDialog' => _x(
                    'Show <translation-key id="Dialog">Dialog</translation-key>',
                    'Backend / Dialog Settings / Label',
                    'borlabs-cookie',
                ),
                'showDialogAfterUserInteraction' => _x(
                    'Show <translation-key id="Dialog">Dialog</translation-key> After User Interaction',
                    'Backend / Dialog Settings / Label',
                    'borlabs-cookie',
                ),
                'showDialogOnLoginPage' => _x(
                    'Show Dialog on Login Page',
                    'Backend / Dialog Settings / Label',
                    'borlabs-cookie',
                ),
                'showHeadlineSeparator' => _x(
                    'Show Headline Separator',
                    'Backend / Dialog Settings / Label',
                    'borlabs-cookie',
                ),
                'showLogo' => _x(
                    'Show Logo',
                    'Backend / Dialog Settings / Label',
                    'borlabs-cookie',
                ),
                'showSaveButton' => _x(
                    'Show "<translation-key id="Save-Selection">Save Selection</translation-key>" Button',
                    'Backend / Dialog Settings / Label',
                    'borlabs-cookie',
                ),
                'useBaseFontSizeForDescriptions' => _x(
                    '<translation-key id="Font-Size-Base">Font Size (Base)</translation-key> for Descriptions',
                    'Backend / Dialog Settings / Label',
                    'borlabs-cookie',
                ),
            ],

            // Headlines
            'headline' => [
                'a11ySettings' => _x(
                    'Accessibility Settings',
                    'Backend / Dialog Settings / Headline',
                    'borlabs-cookie',
                ),
                'animationSettings' => _x(
                    'Animation Settings',
                    'Backend / Dialog Settings / Headline',
                    'borlabs-cookie',
                ),
                'dialogSettings' => _x(
                    'Basic Dialog Settings',
                    'Backend / Dialog Settings / Headline',
                    'borlabs-cookie',
                ),
                'generalSettings' => _x(
                    'General Settings',
                    'Backend / Dialog Settings / Headline',
                    'borlabs-cookie',
                ),
                'geoIp' => _x(
                    '<translation-key id="GeoIP">GeoIP</translation-key>',
                    'Backend/ Dialog Settings / Headline',
                    'borlabs-cookie',
                ),
                /** @see SetupAssistantLocalizationStrings::class The identical string is used here. */
                'languageSwitcher' => _x(
                    'Language Switcher',
                    'Backend / Dialog Settings / Headline',
                    'borlabs-cookie',
                ),
                'logoSettings' => _x(
                    'Logo Settings',
                    'Backend / Dialog Settings / Headline',
                    'borlabs-cookie',
                ),
                'resetDialogSettings' => _x(
                    'Reset Dialog General Settings',
                    'Backend / Dialog Settings / Headline',
                    'borlabs-cookie',
                ),
            ],

            // Hint
            'hint' => [
                'animation' => _x(
                    'If enabled, the <translation-key id="Dialog">Dialog</translation-key> is animated.',
                    'Backend / Dialog Settings / Hint',
                    'borlabs-cookie',
                ),
                'animationDelay' => _x(
                    'If enabled, the appearance of the <translation-key id="Dialog">Dialog</translation-key> is delayed for two seconds.',
                    'Backend / Dialog Settings / Hint',
                    'borlabs-cookie',
                ),
                'animationIn' => _x(
                    'Choose the <translation-key id="Animation">Animation</translation-key> with which the <translation-key id="Dialog">Dialog</translation-key> appears.',
                    'Backend / Dialog Settings / Hint',
                    'borlabs-cookie',
                ),
                'animationOut' => _x(
                    'Choose the <translation-key id="Animation">Animation</translation-key> with which the <translation-key id="Dialog">Dialog</translation-key> disappears.',
                    'Backend / Dialog Settings / Hint',
                    'borlabs-cookie',
                ),
                'buttonSwitchRound' => _x(
                    'Choose if the <translation-key id="Switch-Button">Switch Button</translation-key> is round (Status: ON) or squared (Status: OFF). You see <translation-key id="Switch-Buttons">Switch Buttons</translation-key> in the dialog for example under the <translation-key id="Services">Services</translation-key> tab.',
                    'Backend / Dialog Settings / Hint',
                    'borlabs-cookie',
                ),
                /** @see SetupAssistantLocalizationStrings::class The identical string is used here. */
                'dialogIsHidden' => _x(
                    'The <translation-key id="Dialog">Dialog</translation-key> will not be shown on this page.',
                    'Backend / Dialog Settings / Hint',
                    'borlabs-cookie',
                ),
                'enableBackdrop' => _x(
                    'The content below the <translation-key id="Dialog">Dialog</translation-key> will be covered with a backdrop until the visitor has selected an option. The color of the backdrop can be customized under <translation-key id="Navigation-Dialog-Widget">Dialog &amp; Widget</translation-key> &raquo; <translation-key id="Navigation-Dialog-Widget-Dialog">Dialog</translation-key> &raquo; <translation-key id="Navigation-Dialog-Widget-Dialog-Appearance">Appearance</translation-key> &raquo; <translation-key id="Dialog-Settings">Dialog - Settings</translation-key> &raquo; <translation-key id="Backdrop">Backdrop</translation-key>.',
                    'Backend / Dialog Settings / Hint',
                    'borlabs-cookie',
                ),
                'geoIpActive' => _x(
                    'When this setting is enabled, only visitors from the specified countries will see the <translation-key id="Dialog">Dialog</translation-key>.',
                    'Backend / Dialog Settings / Hint',
                    'borlabs-cookie',
                ),
                'geoIpCachingMode' => _x(
                    'If you are using a caching plugin on your website, enabling this option is essential for enabling <translation-key id="GeoIP-Active">GeoIP Active</translation-key>.',
                    'Backend / Dialog Settings / Hint',
                    'borlabs-cookie',
                ),
                'hideDialogOnPages' => _x(
                    'Add one URL per line. The <translation-key id="Dialog">Dialog</translation-key> will not be shown on these pages.',
                    'Backend / Dialog Settings / Hint',
                    'borlabs-cookie',
                ),
                'imprintPageId' => _x(
                    'Choose your <translation-key id="Imprint-Page">Imprint Page</translation-key> or add a custom URL.',
                    'Backend / Dialog Settings / Hint',
                    'borlabs-cookie',
                ),
                'layout' => _x(
                    'Choose the <translation-key id="Layout">Layout</translation-key> of the <translation-key id="Dialog">Dialog</translation-key>.',
                    'Backend / Dialog Settings / Hint',
                    'borlabs-cookie',
                ),
                'logo' => _x(
                    'Choose a <translation-key id="Logo">Logo</translation-key> you want to appear in the <translation-key id="Dialog">Dialog</translation-key>.',
                    'Backend / Dialog Settings / Hint',
                    'borlabs-cookie',
                ),
                'logoHd' => _x(
                    'Choose the HD version of the <translation-key id="Logo">Logo</translation-key> you want to appear in the <translation-key id="Dialog">Dialog</translation-key>. It will be used for high resolution displays.',
                    'Backend / Dialog Settings / Hint',
                    'borlabs-cookie',
                ),
                'position' => _x(
                    'Choose the <translation-key id="Position">Position</translation-key> in which the <translation-key id="Dialog">Dialog</translation-key> appears.',
                    'Backend / Dialog Settings / Hint',
                    'borlabs-cookie',
                ),
                'privacyPageId' => _x(
                    'Choose your <translation-key id="Privacy-Page">Privacy Page</translation-key> or add a custom URL.',
                    'Backend / Dialog Settings / Hint',
                    'borlabs-cookie',
                ),
                'repeatAnimation' => _x(
                    'Repeat animation.',
                    'Backend / Dialog Settings / Hint',
                    'borlabs-cookie',
                ),
                'reset' => _x(
                    'Please confirm that you want to reset all <translation-key id="Dialog-General">Dialog General</translation-key> settings. They will be reset to their default settings.',
                    'Backend / Dialog Appearance / Hint',
                    'borlabs-cookie',
                ),
                'serviceGroupJustification' => _x(
                    'Choose the justification of the <translation-key id="Service-Group">Service Group</translation-key> items (list items or checkboxes) in the <translation-key id="Dialog">Dialog</translation-key>. Has no effect if the <translation-key id="Layout">Layout</translation-key> is set to <translation-key id="Bar-Slim">Bar - Slim</translation-key>, <translation-key id="Box-Slim">Box - Slim</translation-key> or <translation-key id="Box-Plus">Box - Plus</translation-key>.',
                    'Backend / Dialog Settings / Hint',
                    'borlabs-cookie',
                ),
                'showAcceptAllButton' => _x(
                    'If enabled, the <translation-key id="Accept-All">Accept All</translation-key> button will be shown to the visitor, with which the visitor can accept all <translation-key id="Services">Services</translation-key>.',
                    'Backend / Dialog Settings / Hint',
                    'borlabs-cookie',
                ),
                'showAcceptOnlyEssentialButton' => _x(
                    'If enabled, the <translation-key id="Accept-Only-Essential">Accept Only Essential</translation-key> button will be shown to the visitor.',
                    'Backend / Dialog Settings / Hint',
                    'borlabs-cookie',
                ),
                'showBorlabsCookieBranding' => _x(
                    'The <translation-key id="Dialog">Dialog</translation-key> contains a reference to Borlabs Cookie. Activate this option to support this plugin. Thank you very much!<br><br>Please note: if you have enabled the <translation-key id="IAB-TCF-Status">IAB TCF Status</translation-key>, an information about the <translation-key id="CMP-ID">CMP ID</translation-key> of <translation-key id="Borlabs-Cookie">Borlabs Cookie</translation-key> is displayed, which cannot be deactivated.',
                    'Backend / Dialog Settings / Hint',
                    'borlabs-cookie',
                ),
                'showCloseButton' => _x(
                    'If enabled, the <translation-key id="Dialog-Close-Button">Close</translation-key> button will be shown to the visitor. Clicking the button is equivalent to choosing <translation-key id="Accept-Only-Essential">Accept Only Essential</translation-key>. This button can be toggled on or off specifically for the <translation-key id="Dialog-Entrance">Dialog Entrance</translation-key>.',
                    'Backend / Dialog Settings / Hint',
                    'borlabs-cookie',
                ),
                'showDialog' => _x(
                    'If enabled, the <translation-key id="Dialog">Dialog</translation-key> is shown, in which the visitor can give their consent preferences.',
                    'Backend / Dialog Settings / Hint',
                    'borlabs-cookie',
                ),
                'showDialogAfterUserInteraction' => _x(
                    'If enabled, the <translation-key id="Dialog">Dialog</translation-key> is shown after the user interacts with the page (e.g., by clicking, scrolling, or pressing a key). The option <translation-key id="Show-Dialog">Show Dialog</translation-key> must be enabled. Enabling this option can enhance your website\'s <translation-key id="Web-Vitals">Web Vitals</translation-key> score, but it may also negatively impact the user experience.',
                    'Backend / Dialog Settings / Hint',
                    'borlabs-cookie',
                ),
                'showDialogOnLoginPage' => _x(
                    'If enabled, the <translation-key id="Dialog">Dialog</translation-key> is shown on the login page. The option <translation-key id="Show-Dialog">Show Dialog</translation-key> must be enabled.',
                    'Backend / Dialog Settings / Hint',
                    'borlabs-cookie',
                ),
                'showHeadlineSeparator' => _x(
                    'If enabled, a separator is displayed below the headline.',
                    'Backend / Dialog Settings / Hint',
                    'borlabs-cookie',
                ),
                'showLogo' => _x(
                    'If enabled, the selected <translation-key id="Logo">Logo</translation-key> is displayed in the <translation-key id="Dialog">Dialog</translation-key>.',
                    'Backend / Dialog Settings / Hint',
                    'borlabs-cookie',
                ),
                'showSaveButton' => _x(
                    'If enabled, the <translation-key id="Save-Selection">Save Selection</translation-key> button will be shown to the visitor, with which the visitor can save their consent preferences. This button can be toggled on or off specifically for the <translation-key id="Dialog-Entrance">Dialog Entrance</translation-key>, but it will always be visible for the <translation-key id="Dialog-Details">Dialog Details</translation-key>.',
                    'Backend / Dialog Settings / Hint',
                    'borlabs-cookie',
                ),
                'useBaseFontSizeForDescriptions' => _x(
                    'Instead of the calculated font size <strong><em>{{ calculatedFontSize }}</em></strong>, the font size <strong><em>{{ baseFontSize }}</em></strong> is used for most descriptions. To change the <translation-key id="Font-Size-Base">Font Size (Base)</translation-key> navigate to <translation-key id="Navigation-Dialog-Widget">Dialog &amp; Widget</translation-key> &raquo; <translation-key id="Navigation-Dialog-Widget-Dialog">Dialog</translation-key> &raquo; <translation-key id="Navigation-Dialog-Widget-Dialog-Appearance">Appearance</translation-key>.',
                    'Backend / Dialog Settings / Hint',
                    'borlabs-cookie',
                ),
            ],

            // Options (select | checkbox | radio)
            'option' => [
                'attentionSeekers' => _x(
                    'Attention Seekers',
                    'Backend / Dialog Settings / Option',
                    'borlabs-cookie',
                ),
                'bouncingEntrances' => _x(
                    'Bouncing Entrances',
                    'Backend / Dialog Settings / Option',
                    'borlabs-cookie',
                ),
                'bouncingExits' => _x(
                    'Bouncing Exits',
                    'Backend / Dialog Settings / Option',
                    'borlabs-cookie',
                ),
                'buttonDetailsOrderAll' => _x(
                    'Accept All',
                    'Backend / Dialog Settings / Option',
                    'borlabs-cookie',
                ),
                'buttonDetailsOrderEssential' => _x(
                    'Accept Only Essential',
                    'Backend / Dialog Settings / Option',
                    'borlabs-cookie',
                ),
                'buttonDetailsOrderSave' => _x(
                    'Save Selection',
                    'Backend / Dialog Settings / Option',
                    'borlabs-cookie',
                ),
                'buttonEntranceOrderAll' => _x(
                    'Accept All',
                    'Backend / Dialog Settings / Option',
                    'borlabs-cookie',
                ),
                'buttonEntranceOrderEssential' => _x(
                    'Accept Only Essential',
                    'Backend / Dialog Settings / Option',
                    'borlabs-cookie',
                ),
                'buttonEntranceOrderPreferences' => _x(
                    'Privacy Preferences',
                    'Backend / Dialog Settings / Option',
                    'borlabs-cookie',
                ),
                'buttonEntranceOrderSave' => _x(
                    'Save Selection',
                    'Backend / Dialog Settings / Option',
                    'borlabs-cookie',
                ),
                'fadingEntrances' => _x(
                    'Fading Entrances',
                    'Backend / Dialog Settings / Option',
                    'borlabs-cookie',
                ),
                'fadingExits' => _x(
                    'Fading Exits',
                    'Backend / Dialog Settings / Option',
                    'borlabs-cookie',
                ),
                'flippers' => _x(
                    'Flippers',
                    'Backend / Dialog Settings / Option',
                    'borlabs-cookie',
                ),
                'layoutBar' => _x(
                    'Bar',
                    'Backend / Dialog Settings / Option',
                    'borlabs-cookie',
                ),
                'layoutBarAdvanced' => _x(
                    'Bar - Advanced',
                    'Backend / Dialog Settings / Option',
                    'borlabs-cookie',
                ),
                'layoutBarSlim' => _x(
                    'Bar - Slim',
                    'Backend / Dialog Settings / Option',
                    'borlabs-cookie',
                ),
                'layoutBox' => _x(
                    'Box',
                    'Backend / Dialog Settings / Option',
                    'borlabs-cookie',
                ),
                'layoutBoxAdvanced' => _x(
                    'Box - Advanced',
                    'Backend / Dialog Settings / Option',
                    'borlabs-cookie',
                ),
                'layoutBoxCompact' => _x(
                    'Box - Compact',
                    'Backend / Dialog Settings / Option',
                    'borlabs-cookie',
                ),
                'layoutBoxPlus' => _x(
                    'Box - Plus',
                    'Backend / Dialog Settings / Option',
                    'borlabs-cookie',
                ),
                'layoutBoxSlim' => _x(
                    'Box - Slim',
                    'Backend / Dialog Settings / Option',
                    'borlabs-cookie',
                ),
                'lightspeed' => _x(
                    'Lightspeed',
                    'Backend / Dialog Settings / Option',
                    'borlabs-cookie',
                ),
                'positionBottomCenter' => _x(
                    'Bottom Center',
                    'Backend / Dialog Settings / Option',
                    'borlabs-cookie',
                ),
                'positionBottomLeft' => _x(
                    'Bottom Left',
                    'Backend / Dialog Settings / Option',
                    'borlabs-cookie',
                ),
                'positionBottomRight' => _x(
                    'Bottom Right',
                    'Backend / Dialog Settings / Option',
                    'borlabs-cookie',
                ),
                'positionMiddleCenter' => _x(
                    'Middle Center',
                    'Backend / Dialog Settings / Option',
                    'borlabs-cookie',
                ),
                'positionMiddleLeft' => _x(
                    'Middle Left',
                    'Backend / Dialog Settings / Option',
                    'borlabs-cookie',
                ),
                'positionMiddleRight' => _x(
                    'Middle Right',
                    'Backend / Dialog Settings / Option',
                    'borlabs-cookie',
                ),
                'positionTopCenter' => _x(
                    'Top Center',
                    'Backend / Dialog Settings / Option',
                    'borlabs-cookie',
                ),
                'positionTopLeft' => _x(
                    'Top Left',
                    'Backend / Dialog Settings / Option',
                    'borlabs-cookie',
                ),
                'positionTopRight' => _x(
                    'Top Right',
                    'Backend / Dialog Settings / Option',
                    'borlabs-cookie',
                ),
                'rotatingEntrances' => _x(
                    'Rotating Entrances',
                    'Backend / Dialog Settings / Option',
                    'borlabs-cookie',
                ),
                'rotatingExits' => _x(
                    'Rotating Exits',
                    'Backend / Dialog Settings / Option',
                    'borlabs-cookie',
                ),
                'serviceGroupJustificationAround' => _x(
                    '<translation-key id="Justification-Around">Around</translation-key>',
                    'Backend / Dialog Settings / Option',
                    'borlabs-cookie',
                ),
                'serviceGroupJustificationBetween' => _x(
                    '<translation-key id="Justification-Between">Between</translation-key>',
                    'Backend / Dialog Settings / Option',
                    'borlabs-cookie',
                ),
                'serviceGroupJustificationCenter' => _x(
                    '<translation-key id="Justification-Center">Center</translation-key>',
                    'Backend / Dialog Settings / Option',
                    'borlabs-cookie',
                ),
                'serviceGroupJustificationLeft' => _x(
                    '<translation-key id="Justification-Left">Left</translation-key>',
                    'Backend / Dialog Settings / Option',
                    'borlabs-cookie',
                ),
                'serviceGroupJustificationRight' => _x(
                    '<translation-key id="Justification-Right">Right</translation-key>',
                    'Backend / Dialog Settings / Option',
                    'borlabs-cookie',
                ),
                'slidingEntrances' => _x(
                    'Sliding Entrances',
                    'Backend / Dialog Settings / Option',
                    'borlabs-cookie',
                ),
                'slidingExits' => _x(
                    'Sliding Exits',
                    'Backend / Dialog Settings / Option',
                    'borlabs-cookie',
                ),
                'specials' => _x(
                    'Specials',
                    'Backend / Dialog Settings / Option',
                    'borlabs-cookie',
                ),
                'zoomEntrances' => _x(
                    'Zoom Entrances',
                    'Backend / Dialog Settings / Option',
                    'borlabs-cookie',
                ),
                'zoomExits' => _x(
                    'Zoom Exits',
                    'Backend / Dialog Settings / Option',
                    'borlabs-cookie',
                ),
            ],

            // Placeholder
            'placeholder' => [
            ],

            // Table
            'table' => [
                /** @see SetupAssistantLocalizationStrings::class The identical string is used here. */
                'language' => _x(
                    'Language',
                    'Backend / Dialog Settings / Table',
                    'borlabs-cookie',
                ),
                /** @see SetupAssistantLocalizationStrings::class The identical string is used here. */
                'languageCode' => _x(
                    'Language Code',
                    'Backend / Dialog Settings / Table',
                    'borlabs-cookie',
                ),
                /** @see SetupAssistantLocalizationStrings::class The identical string is used here. */
                'url' => _x(
                    'URL',
                    'Backend / Dialog Settings / Table',
                    'borlabs-cookie',
                ),
            ],

            // Text
            'text' => [
                'geoIpDatabaseDownloaded' => _x(
                    'Database is downloaded',
                    'Backend / Dialog Settings / Text',
                    'borlabs-cookie',
                ),
                'geoIpDatabaseNotDownloaded' => _x(
                    'Database was not downloaded',
                    'Backend / Dialog Settings / Text',
                    'borlabs-cookie',
                ),
                'geoIpHideList' => _x(
                    'Dialog hidden',
                    'Backend / Dialog Settings / Text',
                    'borlabs-cookie',
                ),
                'geoIpLastSyncAt' => _x(
                    'Last Check with API',
                    'Backend / Dialog Settings / Text',
                    'borlabs-cookie',
                ),
                'geoIpShowList' => _x(
                    'Dialog visible',
                    'Backend / Dialog Settings / Text',
                    'borlabs-cookie',
                ),
            ],

            // Things to know
            'thingsToKnow' => [
                'geoIpCachingDescription' => _x(
                    'Yes, this feature is compatible with caching. To ensure functionality, please enable the <translation-key id="GeoIP-Caching-Mode">GeoIP Caching Mode</translation-key> option.',
                    'Backend / Dialog Settings / Things to know / Text',
                    'borlabs-cookie',
                ),
                'geoIpGeneralDescriptionA' => _x(
                    '<translation-key id="GeoIP">GeoIP</translation-key> allows you show the <translation-key id="Dialog">Dialog</translation-key> only for visitors from some countries (e.g. for EU citizens).',
                    'Backend / Dialog Settings / Things to know / Text',
                    'borlabs-cookie',
                ),
                'geoIpGeneralDescriptionB' => _x(
                    'Visitors who don\'t see the <translation-key id="Dialog">Dialog</translation-key> are automatically treated as having consented to all <translation-key id="Services">Services</translation-key>.',
                    'Backend / Dialog Settings / Things to know / Text',
                    'borlabs-cookie',
                ),
                'headlineGeoIp' => _x(
                    'What is <translation-key id="GeoIP">GeoIP</translation-key>?',
                    'Backend / Dialog Settings / Things to know / Text',
                    'borlabs-cookie',
                ),
                'headlineGeoIpCaching' => _x(
                    'Is <translation-key id="GeoIP">GeoIP</translation-key> working if your website uses caching?',
                    'Backend / Dialog Settings / Things to know / Text',
                    'borlabs-cookie',
                ),
                'headlineHowToChangeConsent' => _x(
                    'How can the visitor change the consent?',
                    'Backend / Dialog Settings / Things to know / Headline',
                    'borlabs-cookie',
                ),
                'headlineHowToDisplayUserId' => _x(
                    'How to display User ID, Consent History and Service &amp; Service Groups Overview.',
                    'Backend / Dialog Settings / Things to know / Headline',
                    'borlabs-cookie',
                ),
                'headlineHowToOptOut' => _x(
                    'How can the visitor perform an opt-out?',
                    'Backend / Dialog Settings / Things to know / Headline',
                    'borlabs-cookie',
                ),
                /** @see SetupAssistantLocalizationStrings::class The identical string is used here. */
                'headlineLanguageSwitcherExplained' => _x(
                    'What is the purpose of the <translation-key id="Language-Switcher">Language Switcher</translation-key> section?',
                    'Backend / Dialog Settings / Things to know / Headline',
                    'borlabs-cookie',
                ),
                /** @see SetupAssistantLocalizationStrings::class The identical string is used here. */
                'headlineLanguageSwitcherInputFieldsExplained' => _x(
                    'Input fields explained',
                    'Backend / Dialog Settings / Things to know / Headline',
                    'borlabs-cookie',
                ),
                'headlineServiceGroupJustification' => _x(
                    'What is <translation-key id="Service-Group-Justification">Service Group Justification</translation-key>?',
                    'Backend / Dialog Settings / Things to know / Headline',
                    'borlabs-cookie',
                ),
                /** @see SetupAssistantLocalizationStrings::class The identical string is used here. */
                'headlineLogoSize' => _x(
                    'What\'s the best logo size?',
                    'Backend / Dialog Settings / Things to know / Headline',
                    'borlabs-cookie',
                ),
                'howToChangeConsentA' => _x(
                    'To offer this option, enter the following <translation-key id="Shortcode">Shortcode</translation-key> to your privacy page. This will create a button which reopens the <translation-key id="Dialog">Dialog</translation-key>.',
                    'Backend / Dialog Settings / Things to know / Text',
                    'borlabs-cookie',
                ),
                'howToChangeConsentB' => _x(
                    'Button',
                    'Backend / Dialog Settings / Things to know / Text',
                    'borlabs-cookie',
                ),
                'howToChangeConsentC' => _x(
                    'Link',
                    'Backend / Dialog Settings / Things to know / Text',
                    'borlabs-cookie',
                ),
                'howToChangeConsentD' => _x(
                    'CSS Class',
                    'Backend / Dialog Settings / Things to know / Text',
                    'borlabs-cookie',
                ),
                'howToDisplayUserIdA' => _x(
                    'Use the <translation-key id="Shortcode">Shortcode</translation-key> below to display the <translation-key id=User-ID">User ID</translation-key>: {{ shortcode }}',
                    'Backend / Dialog Settings / Things to know / Text',
                    'borlabs-cookie',
                ),
                'howToDisplayUserIdB' => _x(
                    'Use the <translation-key id="Shortcode">Shortcode</translation-key> below to display the <translation-key id="Consent-History">Consent History</translation-key>: {{ shortcode }}',
                    'Backend / Dialog Settings / Things to know / Text',
                    'borlabs-cookie',
                ),
                'howToDisplayUserIdC' => _x(
                    'Use the <translation-key id="Shortcode">Shortcode</translation-key> below to display an overview of all active <translation-key id="Services">Services</translation-key> and <translation-key id="Service-Groups">Service Groups</translation-key>: {{ shortcode }}',
                    'Backend / Dialog Settings / Things to know / Text',
                    'borlabs-cookie',
                ),
                'howToOptOutA' => _x(
                    'Insert the following <translation-key id="Shortcode">Shortcode</translation-key> to your privacy page. This will create an opt-out button that allows the visitor to opt-out of the specified <translation-key id="Service">Service</translation-key>: {{ shortcode }}',
                    'Backend / Dialog Settings / Things to know / Text',
                    'borlabs-cookie',
                ),
                'howToOptOutB' => _x(
                    'You find the opt-out <translation-key id="Shortcode">Shortcode</translation-key> for each <translation-key id="Service">Service</translation-key> under <translation-key id="Navigation-Consent-Management">Consent Management</translation-key> &raquo; <translation-key id="Navigation-Consent-Management-Services">Services</translation-key>. Click on a <translation-key id="Service">Service</translation-key>, and you\'ll find the <translation-key id="Opt-out-Shortcode">Opt-out Shortcode</translation-key>.',
                    'Backend / Dialog Settings / Things to know / Text',
                    'borlabs-cookie',
                ),
                /** @see SetupAssistantLocalizationStrings::class The identical string is used here. */
                'languageSwitcherExplained' => _x(
                    'The <translation-key id="Language-Switcher">Language Switcher</translation-key> allows visitors to change the website\'s language using the <translation-key id="Dialog">Dialog</translation-key>. Once they choose a language, they\'re automatically taken to the website\'s version in that language. This feature requires a multilingual plugin or a <translation-key id="Multisite-Network">Multisite Network</translation-key>.',
                    'Backend / Dialog Settings / Things to know / Text',
                    'borlabs-cookie',
                ),
                /** @see SetupAssistantLocalizationStrings::class The identical string is used here. */
                'languageSwitcherInputFieldsExplainedCode' => _x(
                    '<translation-key id="Language-Switcher-Code">Code</translation-key>: Enter the language code of the language you want to add, e.g. <strong><em>de</em></strong>. The language code must be in <translation-key id="ISO-639-1">ISO 639-1</translation-key> format. You can find a list of all language codes <a class="brlbs-cmpnt-link brlbs-cmpnt-link-with-icon" href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes" target="_blank"><strong><em>here</em></strong><span class="brlbs-cmpnt-external-link-icon"></span></a>.',
                    'Backend / Dialog Settings / Things to know / Text',
                    'borlabs-cookie',
                ),
                /** @see SetupAssistantLocalizationStrings::class The identical string is used here. */
                'languageSwitcherInputFieldsExplainedName' => _x(
                    '<translation-key id="Language-Switcher-Name">Name</translation-key>: Enter the name of the language, e.g. <strong><em>Deutsch</em></strong>.',
                    'Backend / Dialog Settings / Things to know / Text',
                    'borlabs-cookie',
                ),
                /** @see SetupAssistantLocalizationStrings::class The identical string is used here. */
                'languageSwitcherInputFieldsExplainedUrl' => _x(
                    '<translation-key id="Language-Switcher-URL">URL</translation-key>: Enter the URL of the page of the language.',
                    'Backend / Dialog Settings / Things to know / Text',
                    'borlabs-cookie',
                ),
                /** @see SetupAssistantLocalizationStrings::class The identical string is used here. */
                'logoSize' => _x(
                    'For best results, use a square (1:1) logo. Large logos may push the headline out of place or break the layout. A size around 128×128 px works well in most cases.',
                    'Backend / Dialog Settings / Things to know / Text',
                    'borlabs-cookie',
                ),
                'serviceGroupJustificationAround' => _x(
                    '<translation-key id="Justification-Around">Around</translation-key>: The spacing between each pair of adjacent items is the same. The empty space before the first and after the last item equals half of the space between each pair of adjacent items.',
                    'Backend / Dialog Settings / Things to know / Text',
                    'borlabs-cookie',
                ),
                'serviceGroupJustificationBetween' => _x(
                    '<translation-key id="Justification-Between">Between</translation-key>: The spacing between each pair of adjacent items is the same. The first item is flush with the main-start edge, and the last item is flush with the main-end edge.',
                    'Backend / Dialog Settings / Things to know / Text',
                    'borlabs-cookie',
                ),
            ],
        ];
    }
}
