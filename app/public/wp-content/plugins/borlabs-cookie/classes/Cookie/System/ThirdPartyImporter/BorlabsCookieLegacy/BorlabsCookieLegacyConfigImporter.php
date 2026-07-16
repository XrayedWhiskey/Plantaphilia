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

namespace Borlabs\Cookie\System\ThirdPartyImporter\BorlabsCookieLegacy;

use Borlabs\Cookie\Adapter\WpDb;
use Borlabs\Cookie\Dto\System\OptionDto;
use Borlabs\Cookie\System\Log\Log;
use Borlabs\Cookie\System\ThirdPartyImporter\BorlabsCookieLegacy\ConfigImporterComponent\ContentBlockerSettingsConfigImporter;
use Borlabs\Cookie\System\ThirdPartyImporter\BorlabsCookieLegacy\ConfigImporterComponent\ContentBlockerStyleConfigImporter;
use Borlabs\Cookie\System\ThirdPartyImporter\BorlabsCookieLegacy\ConfigImporterComponent\DialogLocalizationConfigImporter;
use Borlabs\Cookie\System\ThirdPartyImporter\BorlabsCookieLegacy\ConfigImporterComponent\DialogSettingsConfigImporter;
use Borlabs\Cookie\System\ThirdPartyImporter\BorlabsCookieLegacy\ConfigImporterComponent\DialogStyleConfigImporter;
use Borlabs\Cookie\System\ThirdPartyImporter\BorlabsCookieLegacy\ConfigImporterComponent\GeneralConfigConfigImporter;
use Borlabs\Cookie\System\ThirdPartyImporter\BorlabsCookieLegacy\ConfigImporterComponent\WidgetConfigImporter;

final class BorlabsCookieLegacyConfigImporter
{
    private ContentBlockerSettingsConfigImporter $contentBlockerSettingsConfigImporter;

    private ContentBlockerStyleConfigImporter $contentBlockerStyleConfigImporter;

    private DialogLocalizationConfigImporter $dialogLocalizationConfigImporter;

    private DialogSettingsConfigImporter $dialogSettingsConfigImporter;

    private DialogStyleConfigImporter $dialogStyleConfigImporter;

    private GeneralConfigConfigImporter $generalConfigConfigImporter;

    private Log $log;

    private WidgetConfigImporter $widgetConfigImporter;

    private WpDb $wpdb;

    public function __construct(
        ContentBlockerSettingsConfigImporter $contentBlockerSettingsConfigImporter,
        ContentBlockerStyleConfigImporter $contentBlockerStyleConfigImporter,
        DialogLocalizationConfigImporter $dialogLocalizationConfigImporter,
        DialogSettingsConfigImporter $dialogSettingsConfigImporter,
        DialogStyleConfigImporter $dialogStyleConfigImporter,
        GeneralConfigConfigImporter $generalConfigConfigImporter,
        Log $log,
        WidgetConfigImporter $widgetConfigImporter,
        WpDb $wpdb
    ) {
        $this->contentBlockerSettingsConfigImporter = $contentBlockerSettingsConfigImporter;
        $this->contentBlockerStyleConfigImporter = $contentBlockerStyleConfigImporter;
        $this->dialogLocalizationConfigImporter = $dialogLocalizationConfigImporter;
        $this->dialogSettingsConfigImporter = $dialogSettingsConfigImporter;
        $this->dialogStyleConfigImporter = $dialogStyleConfigImporter;
        $this->generalConfigConfigImporter = $generalConfigConfigImporter;
        $this->log = $log;
        $this->widgetConfigImporter = $widgetConfigImporter;
        $this->wpdb = $wpdb;
    }

     public function import(): bool
     {
         $configs = $this->getAllConfigs();

         $this->log->info(
             '[Import] Found {{ count }} legacy configs.',
             [
                 'count' => count($configs),
             ],
         );

         foreach ($configs as $configPerLanguage) {
             if ($configPerLanguage->language === null) {
                 $this->log->error('[Import] Failed to import legacy config due to a missing language.', [
                     'config' => $configPerLanguage,
                 ]);

                 continue;
             }

             $this->contentBlockerSettingsConfigImporter->import($configPerLanguage->value, $configPerLanguage->language);
             $this->contentBlockerStyleConfigImporter->import($configPerLanguage->value, $configPerLanguage->language);
             $this->generalConfigConfigImporter->import($configPerLanguage->value, $configPerLanguage->language);
             $this->dialogSettingsConfigImporter->import($configPerLanguage->value, $configPerLanguage->language);
             $this->dialogStyleConfigImporter->import($configPerLanguage->value, $configPerLanguage->language);
             $this->dialogLocalizationConfigImporter->import($configPerLanguage->value, $configPerLanguage->language);
             $this->widgetConfigImporter->import($configPerLanguage->value, $configPerLanguage->language);
         }

         return true;
     }

     private function getAllConfigs(): array
     {
         $optionBaseName = 'BorlabsCookieLegacyConfig';
         $optionDtoList = [];
         $legacyOptions = $this->wpdb->get_results(
             '
                SELECT
                    `option_name`,
                    `option_value`
                FROM
                    `' . $this->wpdb->options . '`
                WHERE
                    `option_name` LIKE \'' . $optionBaseName . '_%\'
            ',
         );

         if (!is_array($legacyOptions)) {
             return $optionDtoList;
         }

         foreach ($legacyOptions as $option) {
             $language = null;
             $languageMatch = [];

             if (preg_match('/^(.*)\_(([a-z]{2,3})((-|_)[a-zA-Z]{2,})?)$/', $option->option_name, $languageMatch)) {
                 $language = $languageMatch[3];
             }

             $optionDtoList[] = new OptionDto(
                 $option->option_name,
                 unserialize($option->option_value),
                 false,
                 $language,
             );
         }

         return $optionDtoList;
     }
}
