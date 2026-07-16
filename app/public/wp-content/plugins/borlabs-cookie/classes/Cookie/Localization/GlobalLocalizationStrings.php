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

namespace Borlabs\Cookie\Localization;

use function Borlabs\Cookie\System\WordPressGlobalFunctions\_x;

/**
 * The **GlobalLocalizationStrings** class contains various localized strings.
 *
 * @see \Borlabs\Cookie\Localization\GlobalLocalizationStrings::get()
 */
final class GlobalLocalizationStrings implements LocalizationInterface
{
    /**
     * @return array<array<string>>
     */
    public static function get(): array
    {
        return [
            // Alert Messages
            'alert' => [
                'actionFailed' => _x(
                    'Action failed. Please check the log files for more information.',
                    'Backend / Global / Alert Message',
                    'borlabs-cookie',
                ),
                'adBlockerDetected' => _x(
                    'Some <translation-key id="Borlabs-Cookie">Borlabs Cookie</translation-key> files have been blocked by a browser extension. Please deactivate your ad blocker or cookie notice blocker.',
                    'Backend / Global / Alert Message',
                    'borlabs-cookie',
                ),
                'apiError' => _x(
                    'An error occurred while communicating with the Borlabs Service API. Navigate to <translation-key id="Borlabs-Cookie">Borlabs Cookie</translation-key> &raquo; <translation-key id="Navigation-System">System</translation-key> &raquo; <translation-key id="Navigation-System-Logs">Logs</translation-key>, and check the logs for more information.',
                    'Backend / Global / Alert Message',
                    'borlabs-cookie',
                ),
                'changeStatusFailed' => _x(
                    'Change status failed.',
                    'Backend / Global / Alert Message',
                    'borlabs-cookie',
                ),
                'changedStatusSuccessfully' => _x(
                    'Changed status successfully.',
                    'Backend / Global / Alert Message',
                    'borlabs-cookie',
                ),
                'deleteFailed' => _x(
                    'Delete failed.',
                    'Backend / Global / Alert Message',
                    'borlabs-cookie',
                ),
                'deleteNotAllowed' => _x(
                    'You are not allowed to delete a resource that is undeletable.',
                    'Backend / Global / Alert Message',
                    'borlabs-cookie',
                ),
                'deletedSuccessfully' => _x(
                    'Deleted successfully.',
                    'Backend / Global / Alert Message',
                    'borlabs-cookie',
                ),
                'modelDeleteFailed' => _x(
                    'Deleting <strong>{{ modelName }}</strong> <strong><em>{{ modelLabel }}</em></strong> failed.',
                    'Backend / Global / Alert Message',
                    'borlabs-cookie',
                ),
                'modelStillInUse' => _x(
                    'The <strong>{{ modelName }}</strong> <strong><em>{{ modelLabel }}</em></strong> is still in use by the <strong>{{ blockingModelName }}</strong> <strong><em>{{ blockingModelLabel }}</em></strong>.',
                    'Backend / Global / Alert Message',
                    'borlabs-cookie',
                ),
                'modelStillInUseFallback' => _x(
                    'The model is still in use.',
                    'Backend / Global / Alert Message',
                    'borlabs-cookie',
                ),
                'noEntryConfigured' => _x(
                    'No entry configured.',
                    'Backend / Global / Alert Message',
                    'borlabs-cookie',
                ),
                'resetSuccessfully' => _x(
                    'Reset successfully.',
                    'Backend / Global / Alert Message',
                    'borlabs-cookie',
                ),
                'routeValidationFailed' => _x(
                    'The request could not be validated, please try again.',
                    'Backend / Global / Alert Message',
                    'borlabs-cookie',
                ),
                'savedSuccessfully' => _x(
                    'Saved successfully.',
                    'Backend / Global / Alert Message',
                    'borlabs-cookie',
                ),
                'savedUnsuccessfully' => _x(
                    'Save failed.',
                    'Backend / Global / Alert Message',
                    'borlabs-cookie',
                ),
                'unknown' => _x(
                    'An error occurred. Please contact the support. {{ message }}',
                    'Backend / Global / Alert Message',
                    'borlabs-cookie',
                ),
            ],

            // Buttons
            'button' => [
                'addNew' => _x(
                    '<translation-key id="Button-Add-New">Add New</translation-key>',
                    'Backend / Global / Button Title',
                    'borlabs-cookie',
                ),
                'close' => _x(
                    'Close',
                    'Backend / Global / Button Title',
                    'borlabs-cookie',
                ),
                'delete' => _x(
                    'Delete',
                    'Backend / Global / Button Title',
                    'borlabs-cookie',
                ),
                'deselectAll' => _x(
                    'Deselect all',
                    'Backend / Global / Button Title',
                    'borlabs-cookie',
                ),
                'goBackWithoutSaving' => _x(
                    'Go back without saving',
                    'Backend / Global / Button Title',
                    'borlabs-cookie',
                ),
                'reset' => _x(
                    'Reset',
                    'Backend / Global / Button Title',
                    'borlabs-cookie',
                ),
                'save' => _x(
                    'Save',
                    'Backend / Global / Button Title',
                    'borlabs-cookie',
                ),
                'saveAllSettings' => _x(
                    'Save all settings',
                    'Backend / Global / Button Title',
                    'borlabs-cookie',
                ),
                'search' => _x(
                    'Search',
                    'Backend / Global / Button Title',
                    'borlabs-cookie',
                ),
                'selectAll' => _x(
                    'Select all',
                    'Backend / Global / Button Title',
                    'borlabs-cookie',
                ),
                'send' => _x(
                    'Send',
                    'Backend / Global / Button Title',
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
                'confirmed' => _x(
                    'Confirmed',
                    'Backend / Global / Text',
                    'borlabs-cookie',
                ),
                'language' => _x(
                    'Language',
                    'Backend / Global / Label',
                    'borlabs-cookie',
                ),
                'languages' => _x(
                    'Additional Languages',
                    'Backend / Global / Label',
                    'borlabs-cookie',
                ),
                'synchronizeConfiguration' => _x(
                    'Synchronize Configuration',
                    'Backend / Global / Label',
                    'borlabs-cookie',
                ),
                'synchronizeTranslation' => _x(
                    'Synchronize Translation',
                    'Backend / Global / Label',
                    'borlabs-cookie',
                ),
            ],

            // Headlines
            'headline' => [
                'deleteSelection' => _x(
                    'Delete selection?',
                    'Backend / Modal / Headline',
                    'borlabs-cookie',
                ),
                'important' => _x(
                    'Important',
                    'Backend / Global / Info Panel / Headline',
                    'borlabs-cookie',
                ),
                'thingsToKnow' => _x(
                    '<translation-key id="Things-to-know">Things to know</translation-key>',
                    'Backend / Global / Info Panel / Headline',
                    'borlabs-cookie',
                ),
            ],

            // Hint
            'hint' => [
                'clipboard' => _x(
                    'Click to copy to clipboard.',
                    'Backend / Global / Hint',
                    'borlabs-cookie',
                ),
                'currentLanguage' => _x(
                    'You are seeing the settings for the language <strong>{{ currentLanguage }}</strong>.',
                    'Backend / Global / Hint',
                    'borlabs-cookie',
                ),
                'language' => _x(
                    'Your entry is stored for this language.',
                    'Backend / Global / Hint',
                    'borlabs-cookie',
                ),
                'languages' => _x(
                    'Create this entry also for other languages.',
                    'Backend / Global / Hint',
                    'borlabs-cookie',
                ),
                'synchronizeConfiguration' => _x(
                    'Sets all settings of this configuration as the configuration for the selected languages.',
                    'Backend / Global / Hint',
                    'borlabs-cookie',
                ),
                'synchronizeConfigurationRepositoryEntry' => _x(
                    'Set the configuration of this entry as the configuration for the selected languages.',
                    'Backend / Global / Hint',
                    'borlabs-cookie',
                ),
                'synchronizeTranslation' => _x(
                    'Sets all texts of this translation as the texts for the selected languages. The texts are translated automatically.',
                    'Backend / Global / Hint',
                    'borlabs-cookie',
                ),
                'synchronizeTranslationRepositoryEntry' => _x(
                    'Set the translation of this entry as the translation for the selected languages. The texts are translated automatically.',
                    'Backend / Global / Hint',
                    'borlabs-cookie',
                ),
            ],

            // Options (select | checkbox | radio)
            'option' => [
                'defaultSelectOption' => _x(
                    '-- Please select --',
                    'Backend / Global / Default Select Option',
                    'borlabs-cookie',
                ),
                'selected' => _x(
                    'Selected',
                    'Backend / Global / Radio Card Selected',
                    'borlabs-cookie',
                ),
            ],

            // Pagination
            'pagination' => [
                'firstPage' => _x(
                    'First Page',
                    'Backend / Global / Pagination',
                    'borlabs-cookie',
                ),
                'lastPage' => _x(
                    'Last Page',
                    'Backend / Global / Pagination',
                    'borlabs-cookie',
                ),
                'next' => _x(
                    '<translation-key id="Pagination-Next">Next</translation-key>',
                    'Backend / Global / Pagination',
                    'borlabs-cookie',
                ),
                'of' => _x(
                    'of',
                    'Backend / Global / Pagination',
                    'borlabs-cookie',
                ),
                'previous' => _x(
                    '<translation-key id="Pagination-Previous">Previous</translation-key>',
                    'Backend / Global / Pagination',
                    'borlabs-cookie',
                ),
                'results' => _x(
                    'results',
                    'Backend / Global / Pagination',
                    'borlabs-cookie',
                ),
                'showing' => _x(
                    'Showing',
                    'Backend / Global / Pagination',
                    'borlabs-cookie',
                ),
                'step' => _x(
                    'Step',
                    'Backend / Global / Pagination',
                    'borlabs-cookie',
                ),
                'to' => _x(
                    'to',
                    'Backend / Global / Pagination',
                    'borlabs-cookie',
                ),
            ],

            // Tables
            'table' => [
                'languageName' => _x(
                    'Language',
                    'Backend / Global / Table',
                    'borlabs-cookie',
                ),
            ],

            // ThingsToKnow
            'thingsToKnow' => [
                'headlineSynchronizeConfigurationExplained' => _x(
                    'Synchronize <translation-key id="Synchronize-Configuration">Configuration</translation-key> Explained',
                    'Backend / Global / Things to know / Headline',
                    'borlabs-cookie',
                ),
                'headlineSynchronizeTranslationExplained' => _x(
                    'Synchronize <translation-key id="Synchronize-Translation">Translation</translation-key> Explained',
                    'Backend / Global / Things to know / Headline',
                    'borlabs-cookie',
                ),
                'synchronizeConfigurationExplained' => _x(
                    'We use the term <translation-key id="Synchronize-Configuration">Configuration</translation-key> to denote all settings except the texts associated with this entry. Synchronizing the <translation-key id="Configuration">Configuration</translation-key> will set the settings in the selected languages to those of this entry.',
                    'Backend / Global / Things to know / Text',
                    'borlabs-cookie',
                ),
                'synchronizeTranslationExplained' => _x(
                    'We use the term <translation-key id="Synchronize-Translation">Translation</translation-key> to denote the texts associated with this entry. Synchronizing the <translation-key id="Translation">Translation</translation-key> will replace the texts in the selected languages with those from this entry.',
                    'Backend / Global / Things to know / Text',
                    'borlabs-cookie',
                ),
            ],

            // URL
            'url' => [
                'footer' => _x(
                    'https://borlabs.io/?utm_source=Borlabs+Cookie&amp;utm_medium=Footer+Logo&amp;utm_campaign=Analysis',
                    'Backend / Global / Footer / URL',
                    'borlabs-cookie',
                ),
            ],
        ];
    }
}
