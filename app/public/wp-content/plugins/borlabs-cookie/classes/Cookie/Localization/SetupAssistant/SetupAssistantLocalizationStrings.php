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

namespace Borlabs\Cookie\Localization\SetupAssistant;

use Borlabs\Cookie\Localization\LocalizationInterface;

use function Borlabs\Cookie\System\WordPressGlobalFunctions\_x;

final class SetupAssistantLocalizationStrings implements LocalizationInterface
{
    /**
     * @return array<array<string>>
     */
    public static function get(): array
    {
        return [
            // Alert messages
            'alert' => [
                /** @see DialogSettingsLocalizationStrings::class The identical string is used here. */
                'noLanguageOptionConfigured' => _x(
                    'No <translation-key id="Language-Option">Language Option</translation-key> configured.',
                    'Backend / Setup Assistant / Alert Message',
                    'borlabs-cookie',
                ),
            ],

            // Breadcrumbs
            'breadcrumb' => [
                'customSetup' => _x(
                    '<translation-key id="Setup-Mode-Custom-Setup">Custom Setup</translation-key>',
                    'Backend / Setup Assistant / Breadcrumb',
                    'borlabs-cookie',
                ),
                'guidedSetup' => _x(
                    '<translation-key id="Setup-Mode-Guided-Setup">Guided Setup</translation-key>',
                    'Backend / Setup Assistant / Breadcrumb',
                    'borlabs-cookie',
                ),
                'module' => _x(
                    'Setup Assistant',
                    'Backend / Setup Assistant / Breadcrumb',
                    'borlabs-cookie',
                ),
                'quickSetup' => _x(
                    '<translation-key id="Setup-Mode-Quick-Setup">Quick Setup</translation-key>',
                    'Backend / Setup Assistant / Breadcrumb',
                    'borlabs-cookie',
                ),
            ],

            // Buttons
            'button' => [
                /** @see DialogSettingsLocalizationStrings::class The identical string is used here. */
                'addLanguageOption' => _x(
                    'Add Language Option',
                    'Backend / Setup Assistant / Button Title',
                    'borlabs-cookie',
                ),
                /** @see DialogAppearanceLocalizationStrings::class The identical string is used here. */
                'applyColors' => _x(
                    'Apply Colors',
                    'Backend / Setup Assistant / Button',
                    'borlabs-cookie',
                ),
                'button' => _x(
                    'Button',
                    'Backend / Setup Assistant / Button',
                    'borlabs-cookie',
                ),
                'gpToDashboard' => _x(
                    'Go to Dashboard',
                    'Backend / Setup Assistant / Button',
                    'borlabs-cookie',
                ),
                'selectOrUploadLogo' => _x(
                    'Select or Upload Logo',
                    'Backend / Setup Assistant / Button Title',
                    'borlabs-cookie',
                ),
                'startSetup' => _x(
                    '<translation-key id="Button-Start-Setup">Start Setup</translation-key>',
                    'Backend / Setup Assistant / Button Title',
                    'borlabs-cookie',
                ),
                /** @see DialogAppearanceLocalizationStrings::class The identical string is used here. */
                'updatePreview' => _x(
                    'Update Preview',
                    'Backend / Setup Assistant / Button',
                    'borlabs-cookie',
                ),
                'useThisMedia' => _x(
                    'Use this media',
                    'Backend / Setup Assistant / Button Title',
                    'borlabs-cookie',
                ),
            ],

            // Fields
            'field' => [
                /** @see DialogAppearanceLocalizationStrings::class The identical string is used here. */
                'colorAssistantAccentColor' => _x(
                    '<translation-key id="Accent-Color">Accent Color</translation-key>',
                    'Backend / Setup Assistant / Label',
                    'borlabs-cookie',
                ),
                /** @see DialogAppearanceLocalizationStrings::class The identical string is used here. */
                'colorAssistantContrastColor' => _x(
                    '<translation-key id="Contrast-Color">Contrast Color</translation-key>',
                    'Backend / Setup Assistant / Label',
                    'borlabs-cookie',
                ),
                /** @see DialogAppearanceLocalizationStrings::class The identical string is used here. */
                'colorAssistantDialogBackgroundColor' => _x(
                    '<translation-key id="Dialog-Background-Color">Dialog Background Color</translation-key>',
                    'Backend / Setup Assistant / Label',
                    'borlabs-cookie',
                ),
                /** @see DialogAppearanceLocalizationStrings::class The identical string is used here. */
                'colorAssistantTextMode' => _x(
                    '<translation-key id="Text-Mode">Text Mode</translation-key>',
                    'Backend / Setup Assistant / Label',
                    'borlabs-cookie',
                ),
                'guidedSetupExampleSettingA' => _x(
                    '<translation-key id="Example-Setting-A">Example Setting A</translation-key>',
                    'Backend / Setup Assistant / Label',
                    'borlabs-cookie',
                ),
                'guidedSetupExampleSettingB' => _x(
                    '<translation-key id="Example-Setting-B">Example Setting B</translation-key>',
                    'Backend / Setup Assistant / Label',
                    'borlabs-cookie',
                ),
                'guidedSetupExampleSettingC' => _x(
                    '<translation-key id="Example-Setting-C">Example Setting C</translation-key>',
                    'Backend / Setup Assistant / Label',
                    'borlabs-cookie',
                ),
                /** @see DialogSettingsLocalizationStrings::class The identical string is used here. */
                'imprintPageId' => _x(
                    '<translation-key id="Imprint-Page">Imprint Page</translation-key>',
                    'Backend / Setup Assistant / Label',
                    'borlabs-cookie',
                ),
                'logo' => _x(
                    'Logo',
                    'Backend / Setup Assistant / Label',
                    'borlabs-cookie',
                ),
                /** @see DialogSettingsLocalizationStrings::class The identical string is used here. */
                'privacyPageId' => _x(
                    '<translation-key id="Privacy-Page">Privacy Page</translation-key>',
                    'Backend / Setup Assistant / Label',
                    'borlabs-cookie',
                ),
                'showLogo' => _x(
                    'Show the <translation-key id="Logo">Logo</translation-key> in the <translation-key id="Dialog">Dialog</translation-key>',
                    'Backend / Setup Assistant / Label',
                    'borlabs-cookie',
                ),
                'startSetup' => _x(
                    'To start the setup, simply click the <translation-key id="Button-Start-Setup">Start Setup</translation-key> button.',
                    'Backend / Setup Assistant / Label',
                    'borlabs-cookie',
                ),
            ],

            // Headlines
            'headline' => [
                'adminPluginSettings' => _x(
                    '<translation-key id="Admin-Plugin-Settings">Admin &amp; Plugin Settings</translation-key>',
                    'Backend / Setup Assistant / Headline',
                    'borlabs-cookie',
                ),
                'basicConcept' => _x(
                    'Basic Concept',
                    'Backend / Setup Assistant / Headline',
                    'borlabs-cookie',
                ),
                'basicConceptBorlabsCookie' => _x(
                    'The Basic Concept of <translation-key id="Borlabs-Cookie">Borlabs Cookie</translation-key>',
                    'Backend / Setup Assistant / Headline',
                    'borlabs-cookie',
                ),
                'basics' => _x(
                    '<translation-key id="Borlabs-Cookie">Borlabs Cookie</translation-key> Basics',
                    'Backend / Setup Assistant / Headline',
                    'borlabs-cookie',
                ),
                'buttonColors' => _x(
                    'Button Colors',
                    'Backend / Setup Assistant / Headline',
                    'borlabs-cookie',
                ),
                'buttonColorsAndTheirActions' => _x(
                    'Button Colors and Their Actions',
                    'Backend / Setup Assistant / Headline',
                    'borlabs-cookie',
                ),
                'checkLanguageOptionsForAccuracy' => _x(
                    'Check the Language Switcher Options for Accuracy',
                    'Backend / Setup Assistant / Headline',
                    'borlabs-cookie',
                ),
                'checkLegalPagesForAccuracy' => _x(
                    'Check the <translation-key id="Legal-Pages">Legal Pages</translation-key> for Accuracy',
                    'Backend / Setup Assistant / Headline',
                    'borlabs-cookie',
                ),
                'chooseDialogLayout' => _x(
                    'Choose Your Preferred Layout for the Dialog',
                    'Backend / Setup Assistant / Headline',
                    'borlabs-cookie',
                ),
                'choosePreferredMode' => _x(
                    'Choose Your Preferred Mode',
                    'Backend / Setup Assistant / Headline',
                    'borlabs-cookie',
                ),
                /** @see DialogAppearanceLocalizationStrings::class The identical string is used here. */
                'colorAssistant' => _x(
                    'Color Assistant',
                    'Backend / Setup Assistant / Headline',
                    'borlabs-cookie',
                ),
                /** @see LibraryLocalizationStrings::class The identical string is used here. */
                'configureLanguage' => _x(
                    'Configure Language',
                    'Backend / Setup Assistant / Headline',
                    'borlabs-cookie',
                ),
                'dialogAppearance' => _x(
                    '<translation-key id="Dialog-Appearance">Dialog - Appearance</translation-key>',
                    'Backend / Setup Assistant / Headline',
                    'borlabs-cookie',
                ),
                'dialogColors' => _x(
                    'Dialog Colors',
                    'Backend / Setup Assistant / Headline',
                    'borlabs-cookie',
                ),
                'dialogSettings' => _x(
                    '<translation-key id="Dialog-Settings">Dialog - Settings</translation-key>',
                    'Backend / Setup Assistant / Headline',
                    'borlabs-cookie',
                ),
                'exampleSettings' => _x(
                    'Example Settings',
                    'Backend / Setup Assistant / Headline',
                    'borlabs-cookie',
                ),
                'fastestWayDetectAndSolveCommonIssues' => _x(
                    'The Fastest Way to Detect and Solve Common Issues',
                    'Backend / Setup Assistant / Headline',
                    'borlabs-cookie',
                ),
                'finalizingInstallation' => _x(
                    'Finalizing Installation',
                    'Backend / Setup Assistant / Headline',
                    'borlabs-cookie',
                ),
                'firstAidDebugConsole' => _x(
                    'First Aid: Debug Console',
                    'Backend / Setup Assistant / Headline',
                    'borlabs-cookie',
                ),
                'formSeparator' => _x(
                    'Form Separator',
                    'Backend / Setup Assistant / Headline',
                    'borlabs-cookie',
                ),
                /** @see DialogSettingsLocalizationStrings::class The identical string is used here. */
                'languageSwitcher' => _x(
                    '<translation-key id="Language-Switcher">Language Switcher</translation-key>',
                    'Backend / Setup Assistant / Headline',
                    'borlabs-cookie',
                ),
                'layout' => _x(
                    '<translation-key id="Layout">Layout</translation-key>',
                    'Backend / Setup Assistant / Headline',
                    'borlabs-cookie',
                ),
                'legalPages' => _x(
                    '<translation-key id="Legal-Pages">Legal Pages</translation-key>',
                    'Backend / Setup Assistant / Headline',
                    'borlabs-cookie',
                ),
                'logo' => _x(
                    '<translation-key id="Logo">Logo</translation-key>',
                    'Backend / Setup Assistant / Headline',
                    'borlabs-cookie',
                ),
                /** @see DialogAppearanceLocalizationStrings::class The identical string is used here. */
                'schematicPreview' => _x(
                    'Schematic Preview',
                    'Backend / Setup Assistant / Headline',
                    'borlabs-cookie',
                ),
                'selectDialogColors' => _x(
                    'Select Colors for the Dialog',
                    'Backend / Setup Assistant / Headline',
                    'borlabs-cookie',
                ),
                'selectDialogLogo' => _x(
                    'Select Your Logo for the Dialog',
                    'Backend / Setup Assistant / Headline',
                    'borlabs-cookie',
                ),
                'separatorLineBetweenForms' => _x(
                    'The Separator Line Between Forms',
                    'Backend / Setup Assistant / Headline',
                    'borlabs-cookie',
                ),
                'displayModeSettings' => _x(
                    '<translation-key id="Display-Mode-Settings">Display Mode of Settings</translation-key>',
                    'Backend / Setup Assistant / Headline',
                    'borlabs-cookie',
                ),
                'settingsInformation' => _x(
                    'Settings Information',
                    'Backend / Setup Assistant / Headline',
                    'borlabs-cookie',
                ),
                'setupAssistantWorkflow' => _x(
                    'Setup Assistant Workflow',
                    'Backend / Setup Assistant / Headline',
                    'borlabs-cookie',
                ),
                'setupComplete' => _x(
                    'Setup Completed',
                    'Backend / Setup Assistant / Headline',
                    'borlabs-cookie',
                ),
                'whatKindOfSetupDoYouPrefer' => _x(
                    'What Kind of Setup Do You Prefer?',
                    'Backend / Setup Assistant / Headline',
                    'borlabs-cookie',
                ),
                'whereToFindInformationAboutSettings' => _x(
                    'Where to Find Information About Settings',
                    'Backend / Setup Assistant / Headline',
                    'borlabs-cookie',
                ),
                'welcome' => _x(
                    'Welcome to the Setup Assistant',
                    'Backend / Setup Assistant / Headline',
                    'borlabs-cookie',
                ),
                'wordPressAdminSidebarMenuMode' => _x(
                    '<translation-key id="WordPress-Admin-Sidebar-Menu-Mode">WordPress Admin Sidebar Menu Mode</translation-key>',
                    'Backend / Setup Assistant / Headline',
                    'borlabs-cookie',
                ),
            ],

            // Hint
            'hint' => [
                /** @see DialogAppearanceLocalizationStrings::class The identical string is used here. */
                'colorAssistantAccentColor' => _x(
                    'The <translation-key id="Accent-Color">Accent Color</translation-key> is primarily used as the background color for buttons, checkboxes, and tabs.',
                    'Backend / Setup Assistant / Hint',
                    'borlabs-cookie',
                ),
                /** @see DialogAppearanceLocalizationStrings::class The identical string is used here. */
                'colorAssistantContrastColor' => _x(
                    'The <translation-key id="Contrast-Color">Contrast Color</translation-key> is primarily used for text displayed on backgrounds with the <translation-key id="Accent-Color">Accent Color</translation-key>.',
                    'Backend / Setup Assistant / Hint',
                    'borlabs-cookie',
                ),
                /** @see DialogAppearanceLocalizationStrings::class The identical string is used here. */
                'colorAssistantDialogBackgroundColor' => _x(
                    'The <translation-key id="Dialog-Background-Color">Dialog Background Color</translation-key> sets the primary background for the dialog and is also applied in lighter or darker variations to distinguish different areas within the dialog.',
                    'Backend / Setup Assistant / Hint',
                    'borlabs-cookie',
                ),
                /** @see DialogAppearanceLocalizationStrings::class The identical string is used here. */
                'colorAssistantTextMode' => _x(
                    'When <translation-key id="Text-Mode">Text Mode</translation-key> is enabled, placeholder text is displayed in the scheme instead of simple bars. This helps evaluate the legibility of the <translation-key id="Accent-Color">Accent Color</translation-key> and <translation-key id="Contrast-Color">Contrast Color</translation-key>.',
                    'Backend / Setup Assistant / Hint',
                    'borlabs-cookie',
                ),
                'demoText' => _x(
                    'This text demonstrates how additional information is shown.',
                    'Backend / Setup Assistant / Hint',
                    'borlabs-cookie',
                ),
                /** @see DialogSettingsLocalizationStrings::class The identical string is used here. */
                'dialogIsHidden' => _x(
                    'The <translation-key id="Dialog">Dialog</translation-key> will not be shown on this page.',
                    'Backend / Setup Assistant / Hint',
                    'borlabs-cookie',
                ),
                'guidedSetupExampleSettingA' => _x(
                    'More information about <translation-key id="Example-Setting-A">Example Setting A</translation-key>.',
                    'Backend / Setup Assistant / Hint',
                    'borlabs-cookie',
                ),
            ],

            // Options (select | checkbox | radio)
            'option' => [
                'layoutBarAdvanced' => _x(
                    'Bar - Advanced',
                    'Backend / Setup Assistant / Option',
                    'borlabs-cookie',
                ),
                'layoutBoxAdvanced' => _x(
                    'Box - Advanced',
                    'Backend / Setup Assistant / Option',
                    'borlabs-cookie',
                ),
                'layoutBoxCompact' => _x(
                    'Box - Compact',
                    'Backend / Setup Assistant / Option',
                    'borlabs-cookie',
                ),
                'layoutBoxPlus' => _x(
                    'Box - Plus',
                    'Backend / Setup Assistant / Option',
                    'borlabs-cookie',
                ),
            ],

            // Placeholder
            'placeholder' => [
            ],

            // Table
            'table' => [
                'checkCloudScanStatus' => _x(
                    'Running Cloud Scan',
                    'Backend / Setup Assistant / Table',
                    'borlabs-cookie',
                ),
                'configureLanguageSwitcher' => _x(
                    'Configuring <translation-key id="Language-Switcher">Language Switcher</translation-key>',
                    'Backend / Setup Assistant / Table',
                    'borlabs-cookie',
                ),
                'configureLegalPages' => _x(
                    'Detecting <translation-key id="Legal-Pages">Legal Pages</translation-key>',
                    'Backend / Setup Assistant / Table',
                    'borlabs-cookie',
                ),
                'downloadPageSelectionKeywordList' => _x(
                    'Preparing Page Detection',
                    'Backend / Setup Assistant / Table',
                    'borlabs-cookie',
                ),
                'getSuggestedPackages' => _x(
                    'Identifying Packages',
                    'Backend / Setup Assistant / Table',
                    'borlabs-cookie',
                ),
                'installPackages' => _x(
                    'Installing Packages',
                    'Backend / Setup Assistant / Table',
                    'borlabs-cookie',
                ),
                /** @see DialogSettingsLocalizationStrings::class The identical string is used here. */
                'language' => _x(
                    'Language',
                    'Backend / Setup Assistant / Table',
                    'borlabs-cookie',
                ),
                /** @see DialogSettingsLocalizationStrings::class The identical string is used here. */
                'languageCode' => _x(
                    'Language Code',
                    'Backend / Setup Assistant / Table',
                    'borlabs-cookie',
                ),
                'registerCloudScanAudit' => _x(
                    'Register Audit Scan',
                    'Backend / Setup Assistant / Table',
                    'borlabs-cookie',
                ),
                'registerCloudScanSetup' => _x(
                    'Starting Cloud Scan',
                    'Backend / Setup Assistant / Table',
                    'borlabs-cookie',
                ),
                'registerInstallationTasks' => _x(
                    'Preparing Installation',
                    'Backend / Setup Assistant / Table',
                    'borlabs-cookie',
                ),
                'status' => _x(
                    'Status',
                    'Backend / Setup Assistant / Table',
                    'borlabs-cookie',
                ),
                'task' => _x(
                    'Task',
                    'Backend / Setup Assistant / Table',
                    'borlabs-cookie',
                ),
                /** @see DialogSettingsLocalizationStrings::class The identical string is used here. */
                'url' => _x(
                    'URL',
                    'Backend / Setup Assistant / Table',
                    'borlabs-cookie',
                ),
            ],

            // Text
            'text' => [
                'basicConceptA' => _x(
                    '<translation-key id="Borlabs-Cookie">Borlabs Cookie</translation-key> helps make your website as privacy-compliant as possible automatically.',
                    'Backend / Setup Assistant / Text',
                    'borlabs-cookie',
                ),
                'basicConceptB' => _x(
                    'For this purpose, packages from the <translation-key id="Borlabs-Cookie">Borlabs Cookie</translation-key> <translation-key id="Navigation-Library">Library</translation-key> are installed based on your website\'s <translation-key id="Theme">Theme</translation-key>, <translation-key id="Plugins">Plugins</translation-key>, and content.',
                    'Backend / Setup Assistant / Text',
                    'borlabs-cookie',
                ),
                'basicConceptC' => _x(
                    'These packages ensure compatibility and provide a proper privacy-compliant integration.',
                    'Backend / Setup Assistant / Text',
                    'borlabs-cookie',
                ),
                'basicConceptD' => _x(
                    'The required packages are automatically determined using the <translation-key id="Navigation-Scanner">Scanner</translation-key>.',
                    'Backend / Setup Assistant / Text',
                    'borlabs-cookie',
                ),
                'blackButtonExplanation' => _x(
                    'Black buttons are used for actions that execute immediately without leaving or reloading the current page.',
                    'Backend / Setup Assistant / Text',
                    'borlabs-cookie',
                ),
                'blackButtonUseCases' => _x(
                    'They are typically used for background processes (such as package installation or downloading the GeoIP database), for actions that update the user interface dynamically (like marking all checkboxes or updating a preview in the color assistant), and for closing modals or dialogs.',
                    'Backend / Setup Assistant / Text',
                    'borlabs-cookie',
                ),
                'blueButtonExplanation' => _x(
                    'Blue buttons are used for actions that save changes and always reload or leave the current page.',
                    'Backend / Setup Assistant / Text',
                    'borlabs-cookie',
                ),
                'buttonColorsAndTheirActions' => _x(
                    'Button colors tell you what will happen before you click. They help you quickly understand actions, avoid unintended changes, and work with confidence.',
                    'Backend / Setup Assistant / Text',
                    'borlabs-cookie',
                ),
                'customSetup' => _x(
                    'The <translation-key id="Setup-Mode-Custom-Setup">Custom Setup</translation-key> installs all required packages and asks for basic settings like privacy page, logo, and dialog color.',
                    'Backend / Setup Assistant / Text',
                    'borlabs-cookie',
                ),
                'firstAidDebugConsoleA' => _x(
                    'The <translation-key id="Debug-Console">Debug Console</translation-key> is the fastest way to detect and resolve common errors.',
                    'Backend / Setup Assistant / Text',
                    'borlabs-cookie',
                ),
                'firstAidDebugConsoleB' => _x(
                    'It is enabled by default and only visible to logged-in users with the <strong>Administrator</strong> role.',
                    'Backend / Setup Assistant / Text',
                    'borlabs-cookie',
                ),
                'firstAidDebugConsoleC' => _x(
                    'The <translation-key id="Debug-Console">Debug Console</translation-key> opens automatically when an error is detected. If no issues are found, it remains collapsed at the bottom of your website.',
                    'Backend / Setup Assistant / Text',
                    'borlabs-cookie',
                ),
                'firstAidDebugConsoleD' => _x(
                    'Although the <translation-key id="Debug-Console">Debug Console</translation-key> can be disabled in the <translation-key id="Admin-Plugin-Settings">Admin &amp; Plugin Settings</translation-key> we recommend keeping it enabled.',
                    'Backend / Setup Assistant / Text',
                    'borlabs-cookie',
                ),
                'grayButtonExplanation' => _x(
                    'Gray buttons are used for navigation only, typically to go back to a previous view or overview.',
                    'Backend / Setup Assistant / Text',
                    'borlabs-cookie',
                ),
                'grayButtonUseCases' => _x(
                    'Clicking a grey button does not save any changes.',
                    'Backend / Setup Assistant / Text',
                    'borlabs-cookie',
                ),
                'guidedSetup' => _x(
                    'The <translation-key id="Setup-Mode-Guided-Setup">Guided Setup</translation-key> explains Borlabs Cookie basics, installs all required packages, and asks for basic settings like privacy page, logo, and dialog color.',
                    'Backend / Setup Assistant / Text',
                    'borlabs-cookie',
                ),
                'layoutBarAdvanced' => _x(
                    'A bar-style layout with content on the left and action buttons on the right. On mobile, it adapts to the &quot;<strong>Box - Advanced</strong>&quot; layout.',
                    'Backend / Setup Assistant / Text',
                    'borlabs-cookie',
                ),
                'layoutBoxAdvanced' => _x(
                    'Similar to the default layout, but displays all content at once. The user may need to scroll to reach the buttons. No <translation-key id="Service-Group">Service Group</translation-key> description.',
                    'Backend / Setup Assistant / Text',
                    'borlabs-cookie',
                ),
                'layoutBoxCompact' => _x(
                    'Default layout with scrollable content optimized for mobile. Displays the <translation-key id="Service-Group">Service Group</translation-key> description.',
                    'Backend / Setup Assistant / Text',
                    'borlabs-cookie',
                ),
                'layoutBoxPlus' => _x(
                    'Two-column layout with content on the left and the <translation-key id="Service-Group">Service Group</translation-key> description on the right. On mobile, it adapts to the &quot;<strong>Box - Advanced</strong>&quot; layout.',
                    'Backend / Setup Assistant / Text',
                    'borlabs-cookie',
                ),
                'pressNextStartCustomizing' => _x(
                    'Press <translation-key id="Pagination-Next">Next</translation-key> to continue. The next steps will let you customize <translation-key id="Borlabs-Cookie">Borlabs Cookie</translation-key>.',
                    'Backend / Setup Assistant / Text',
                    'borlabs-cookie',
                ),
                'pressNextToComplete' => _x(
                    'Press <translation-key id="Pagination-Next">Next</translation-key> to complete the setup. If it\'s not visible, scroll down slightly.',
                    'Backend / Setup Assistant / Text',
                    'borlabs-cookie',
                ),
                'pressNextToProceed' => _x(
                    'Press <translation-key id="Pagination-Next">Next</translation-key> to proceed to the next step.',
                    'Backend / Setup Assistant / Text',
                    'borlabs-cookie',
                ),
                'purpleButtonExplanation' => _x(
                    'Purple buttons are used exclusively in the library within the details of a package to trigger package updates.',
                    'Backend / Setup Assistant / Text',
                    'borlabs-cookie',
                ),
                'purpleButtonUseCases' => _x(
                    'Whenever you see the color purple for example as a purple circle with a number in the navigation it indicates that an update for a package is available.',
                    'Backend / Setup Assistant / Text',
                    'borlabs-cookie',
                ),
                'quickSetup' => _x(
                    'The <translation-key id="Setup-Mode-Quick-Setup">Quick Setup</translation-key> automatically configures settings and installs all required packages.',
                    'Backend / Setup Assistant / Text',
                    'borlabs-cookie',
                ),
                'redButtonExplanation' => _x(
                    'Red buttons are used for destructive actions, such as deleting or resetting data.',
                    'Backend / Setup Assistant / Text',
                    'borlabs-cookie',
                ),
                'redButtonUseCases' => _x(
                    'These actions reload the page or redirect away from the current page after execution.',
                    'Backend / Setup Assistant / Text',
                    'borlabs-cookie',
                ),
                'separatorLineBetweenFormsA' => _x(
                    'The separator line shows where one form ends and another one begins. It appears automatically when there is more than one form on a page.',
                    'Backend / Setup Assistant / Text',
                    'borlabs-cookie',
                ),
                'separatorLineBetweenFormsB' => _x(
                    'Each form can have one or more blue buttons. This allows you to apply settings without scrolling through a long page. When several forms are on the same page, the separator line helps you see which settings belong together.',
                    'Backend / Setup Assistant / Text',
                    'borlabs-cookie',
                ),
                'separatorLineBetweenFormsC' => _x(
                    'In the example, the first two buttons belong to the same form and apply <translation-key id="Example-Setting-A">Example Setting A</translation-key> and <translation-key id="Example-Setting-B">Example Setting B</translation-key>.',
                    'Backend / Setup Assistant / Text',
                    'borlabs-cookie',
                ),
                'separatorLineBetweenFormsD' => _x(
                    '<translation-key id="Example-Setting-C">Example Setting C</translation-key> belongs to a different form. This is indicated by the separator line between the forms.',
                    'Backend / Setup Assistant / Text',
                    'borlabs-cookie',
                ),
                'displayModeSettingsSimplified' => _x(
                    'Only the most essential settings are displayed. Ideal for non-technical users.',
                    'Backend / Setup Assistant / Text',
                    'borlabs-cookie',
                ),
                'displayModeSettingsStandard' => _x(
                    'All available settings are shown. Best for advanced or technical users.',
                    'Backend / Setup Assistant / Text',
                    'borlabs-cookie',
                ),
                'settingsInformationA' => _x(
                    'The info symbol next to a setting provides brief information about what the setting is for and what it does.',
                    'Backend / Setup Assistant / Text',
                    'borlabs-cookie',
                ),
                'settingsInformationB' => _x(
                    'You can find additional information in the purple section &quot;<translation-key id="Things-to-know">Things to know</translation-key>&quot;. Depending on the view, this section is displayed to the right of or below the settings.',
                    'Backend / Setup Assistant / Text',
                    'borlabs-cookie',
                ),
                'settingsInformationC' => _x(
                    'The content can refer to individual settings as well as general contexts and background information, helping you to better understand <translation-key id="Borlabs-Cookie">Borlabs Cookie</translation-key> and use it in a targeted manner.',
                    'Backend / Setup Assistant / Text',
                    'borlabs-cookie',
                ),
                'setupAssistantWorkflowA' => _x(
                    'The setup assistant automatically performs a scan and installs the required packages in the background.',
                    'Backend / Setup Assistant / Text',
                    'borlabs-cookie',
                ),
                'setupAssistantWorkflowB' => _x(
                    'The recommended and easiest settings are always preselected.',
                    'Backend / Setup Assistant / Text',
                    'borlabs-cookie',
                ),
                'setupAssistantWorkflowC' => _x(
                    'In the next step, you will learn more about using <translation-key id="Borlabs-Cookie">Borlabs Cookie</translation-key>.',
                    'Backend / Setup Assistant / Text',
                    'borlabs-cookie',
                ),
                'setupCompleteA' => _x(
                    'Setup is complete. The <translation-key id="Dialog">Dialog</translation-key> should now appear on your website.',
                    'Backend / Setup Assistant / Text',
                    'borlabs-cookie',
                ),
                'setupCompleteB' => _x(
                    'Open your website in incognito/private mode and confirm everything works as intended.',
                    'Backend / Setup Assistant / Text',
                    'borlabs-cookie',
                ),
                'setupCompleteC' => _x(
                    'Check your mailbox for any follow-up instructions required by a package. If a package included instructions, they were sent to <strong><em>{{ mailRecipient }}</em></strong>.',
                    'Backend / Setup Assistant / Text',
                    'borlabs-cookie',
                ),
                'setupCompleteD' => _x(
                    'Use the audit scan to confirm there are no unwanted external connections. Go to <translation-key id="Navigation-Library-Scanner">Library &amp; Scanner</translation-key> &raquo; <a class="brlbs-cmpnt-link" href="?page=borlabs-cookie-cloud-scan" target="_blank"><translation-key id="Navigation-Scanner">Scanner</translation-key></a>, open the most recent <translation-key id="Audit">Audit</translation-key> scan job, and check the results.',
                    'Backend / Setup Assistant / Text',
                    'borlabs-cookie',
                ),
                'setupCompleteE' => _x(
                    'During installation, additional <translation-key id="Services">Services</translation-key> may be added. Review them under <translation-key id="Navigation-Consent-Management">Consent Management</translation-key> &raquo; <a class="brlbs-cmpnt-link" href="?page=borlabs-cookie-services" target="_blank"><translation-key id="Navigation-Consent-Management-Services">Services</translation-key></a>, and disable any you don\'t use.',
                    'Backend / Setup Assistant / Text',
                    'borlabs-cookie',
                ),
                'setupCompleteF' => _x(
                    'If you have any questions, visit our <a class="brlbs-cmpnt-link brlbs-cmpnt-link-with-icon" href="%s" rel="nofollow noreferrer" target="_blank"><span>Knowledge Base</span><span class="brlbs-cmpnt-external-link-icon"></span></a>. If you can\'t find what you need, contact our support team.',
                    'Backend / Setup Assistant / Text',
                    'borlabs-cookie',
                ),
                'setupIsCompleting' => _x(
                    'Setup is completing, please wait...',
                    'Backend / Setup Assistant / Text',
                    'borlabs-cookie',
                ),
                'wordPressAdminSidebarMenuModeExpanded' => _x(
                    'Shows all Borlabs Cookie menu items. Best for users who want direct access to every settings page.',
                    'Backend / Setup Assistant / Text',
                    'borlabs-cookie',
                ),
                'wordPressAdminSidebarMenuModeSimplified' => _x(
                    'Shows only the most essential Borlabs Cookie menu items. Ideal for non-technical users.',
                    'Backend / Setup Assistant / Text',
                    'borlabs-cookie',
                ),
                'wordPressAdminSidebarMenuModeStandard' => _x(
                    'Shows the commonly used Borlabs Cookie menu items. Best for advanced or technical users.',
                    'Backend / Setup Assistant / Text',
                    'borlabs-cookie',
                ),
            ],

            // Things to know
            'thingsToKnow' => [
                'additionalBackgroundInformatioA' => _x(
                    'This section provides additional background information and helpful tips related to the current settings.',
                    'Backend / Setup Assistant / Things to know / Text',
                    'borlabs-cookie',
                ),
                'additionalBackgroundInformatioB' => _x(
                    'Here you\'ll find explanations, notes, and context that go beyond the short info texts directly next to a setting.',
                    'Backend / Setup Assistant / Things to know / Text',
                    'borlabs-cookie',
                ),
                'colorAssistantA' => _x(
                    'The Color Assistant helps you design the entire dialog using three basic colors.',
                    'Backend / Setup Assistant / Things to know / Text',
                    'borlabs-cookie',
                ),
                'colorAssistantB' => _x(
                    'For further customization, go to <translation-key id="Navigation-Dialog-Widget">Dialog &amp; Widget</translation-key> &raquo; <translation-key id="Navigation-Dialog-Widget-Dialog">Dialog</translation-key> &raquo; <translation-key id="Navigation-Dialog-Widget-Dialog-Appearance">Appearance</translation-key>, where you\'ll find more options for colors, spacing, and rounding.',
                    'Backend / Setup Assistant / Things to know / Text',
                    'borlabs-cookie',
                ),
                /** @see PluginSettingsLocalizationStrings::class The identical string is used here. */
                'displayModeSettingsA' => _x(
                    'The <translation-key id="Display-Mode-Settings">Display Mode of Settings</translation-key> determines how many configuration options are visible throughout the system.',
                    'Backend / Setup Assistant / Things to know / Text',
                    'borlabs-cookie',
                ),
                /** @see PluginSettingsLocalizationStrings::class The identical string is used here. */
                'displayModeSettingsB' => _x(
                    'You can switch between modes at any time. Changing the display mode only affects which settings are visible. It does not modify your existing configurations or saved values.',
                    'Backend / Setup Assistant / Things to know / Text',
                    'borlabs-cookie',
                ),
                'headlineAdditionalBackgroundInformation' => _x(
                    'Additional background information',
                    'Backend / Setup Assistant / Things to know / Headline',
                    'borlabs-cookie',
                ),
                'headlineColorAssistant' => _x(
                    'About the color assistant',
                    'Backend / Setup Assistant / Things to know / Headline',
                    'borlabs-cookie',
                ),
                /** @see PluginSettingsLocalizationStrings::class The identical string is used here. */
                'headlineDisplayModeSettings' => _x(
                    'About <translation-key id="Display-Mode-Settings">Display Mode of Settings</translation-key>',
                    'Backend / Setup Assistant / Things to know / Headline',
                    'borlabs-cookie',
                ),
                /** @see DialogSettingsLocalizationStrings::class The identical string is used here. */
                'headlineLanguageSwitcherExplained' => _x(
                    'What is the purpose of the <translation-key id="Language-Switcher">Language Switcher</translation-key> section?',
                    'Backend / Setup Assistant / Things to know / Headline',
                    'borlabs-cookie',
                ),
                /** @see DialogSettingsLocalizationStrings::class The identical string is used here. */
                'headlineLanguageSwitcherInputFieldsExplained' => _x(
                    'Input fields explained',
                    'Backend / Setup Assistant / Things to know / Headline',
                    'borlabs-cookie',
                ),
                'headlineLayout' => _x(
                    'About the layouts',
                    'Backend / Setup Assistant / Things to know / Headline',
                    'borlabs-cookie',
                ),
                'headlineLegalPages' => _x(
                    'About the <translation-key id="Legal-Pages">Legal Pages</translation-key>',
                    'Backend / Setup Assistant / Things to know / Headline',
                    'borlabs-cookie',
                ),
                /** @see DialogSettingsLocalizationStrings::class The identical string is used here. */
                'headlineLogoSize' => _x(
                    'What\'s the best logo size?',
                    'Backend / Setup Assistant / Things to know / Headline',
                    'borlabs-cookie',
                ),
                'headlineSidebarMenuMode' => _x(
                    'About <translation-key id="WordPress-Admin-Sidebar-Menu-Mode">WordPress Admin Sidebar Menu Mode</translation-key>',
                    'Backend / Setup Assistant / Things to know / Headline',
                    'borlabs-cookie',
                ),
                'headlineSymbolsExplained' => _x(
                    'Symbols explained',
                    'Backend / Setup Assistant / Things to know / Headline',
                    'borlabs-cookie',
                ),
                'layoutA' => _x(
                    'The layout illustrations show what the dialog looks like when all action buttons (blue) are visible.',
                    'Backend / Setup Assistant / Things to know / Text',
                    'borlabs-cookie',
                ),
                'layoutB' => _x(
                    'The layout and displayed buttons can be adjusted anytime in <translation-key id="Navigation-Dialog-Widget">Dialog &amp; Widget</translation-key> &raquo; <translation-key id="Navigation-Dialog-Widget-Dialog">Dialog</translation-key> &raquo; <translation-key id="Navigation-Dialog-Widget-Dialog-Settings">Settings</translation-key>.',
                    'Backend / Setup Assistant / Things to know / Text',
                    'borlabs-cookie',
                ),
                'legalPagesA' => _x(
                    'Ensure that the pages or URLs for the imprint and the privacy policy have been correctly recognized. If they are not, update the URLs accordingly.',
                    'Backend / Setup Assistant / Things to know / Text',
                    'borlabs-cookie',
                ),
                'legalPagesB' => _x(
                    'In some countries an imprint is not required. In that case, you may ignore this setting or choose a contact page instead.',
                    'Backend / Setup Assistant / Things to know / Text',
                    'borlabs-cookie',
                ),
                /** @see DialogSettingsLocalizationStrings::class The identical string is used here. */
                'languageSwitcherExplained' => _x(
                    'The <translation-key id="Language-Switcher">Language Switcher</translation-key> allows visitors to change the website\'s language using the <translation-key id="Dialog">Dialog</translation-key>. Once they choose a language, they\'re automatically taken to the website\'s version in that language. This feature requires a multilingual plugin or a <translation-key id="Multisite-Network">Multisite Network</translation-key>.',
                    'Backend / Setup Assistant / Things to know / Text',
                    'borlabs-cookie',
                ),
                /** @see DialogSettingsLocalizationStrings::class The identical string is used here. */
                'languageSwitcherInputFieldsExplainedCode' => _x(
                    '<translation-key id="Language-Switcher-Code">Code</translation-key>: Enter the language code of the language you want to add, e.g. <strong><em>de</em></strong>. The language code must be in <translation-key id="ISO-639-1">ISO 639-1</translation-key> format. You can find a list of all language codes <a class="brlbs-cmpnt-link brlbs-cmpnt-link-with-icon" href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes" target="_blank"><strong><em>here</em></strong><span class="brlbs-cmpnt-external-link-icon"></span></a>.',
                    'Backend / Setup Assistant / Things to know / Text',
                    'borlabs-cookie',
                ),
                /** @see DialogSettingsLocalizationStrings::class The identical string is used here. */
                'languageSwitcherInputFieldsExplainedName' => _x(
                    '<translation-key id="Language-Switcher-Name">Name</translation-key>: Enter the name of the language, e.g. <strong><em>Deutsch</em></strong>.',
                    'Backend / Setup Assistant / Things to know / Text',
                    'borlabs-cookie',
                ),
                /** @see DialogSettingsLocalizationStrings::class The identical string is used here. */
                'languageSwitcherInputFieldsExplainedUrl' => _x(
                    '<translation-key id="Language-Switcher-URL">URL</translation-key>: Enter the URL of the page of the language.',
                    'Backend / Setup Assistant / Things to know / Text',
                    'borlabs-cookie',
                ),
                /** @see DialogSettingsLocalizationStrings::class The identical string is used here. */
                'logoSize' => _x(
                    'For best results, use a square (1:1) logo. Large logos may push the headline out of place or break the layout. A size around 128×128 px works well in most cases.',
                    'Backend / Setup Assistant / Things to know / Text',
                    'borlabs-cookie',
                ),
                'sidebarMenuMode' => _x(
                    'The <translation-key id="WordPress-Admin-Sidebar-Menu-Mode">WordPress Admin Sidebar Menu Mode</translation-key> determines which menu items are visible in the WordPress admin sidebar.',
                    'Backend / Setup Assistant / Things to know / Text',
                    'borlabs-cookie',
                ),
                'symbolExplainedCompleted' => _x(
                    'Task was successfully completed.',
                    'Backend / Setup Assistant / Things to know / Text',
                    'borlabs-cookie',
                ),
                'symbolExplainedFailed' => _x(
                    'Task failed to complete.',
                    'Backend / Setup Assistant / Things to know / Text',
                    'borlabs-cookie',
                ),
                'symbolExplainedPending' => _x(
                    'Task is waiting to be processed.',
                    'Backend / Setup Assistant / Things to know / Text',
                    'borlabs-cookie',
                ),
                'symbolExplainedSkipped' => _x(
                    'Task was not required and skipped.',
                    'Backend / Setup Assistant / Things to know / Text',
                    'borlabs-cookie',
                ),
            ],

            // URL
            'url' => [
                'knowledgeBase' => _x(
                    'https://borlabs.io/support/?utm_source=Borlabs+Cookie&utm_medium=Setup-Assistant+Link&utm_campaign=Analysis',
                    'Backend / Setup Assistant / URL',
                    'borlabs-cookie',
                ),
            ],
        ];
    }
}
