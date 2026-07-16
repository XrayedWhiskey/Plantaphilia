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

namespace Borlabs\Cookie\System\ThirdPartyImporter\BorlabsCookieLegacy\Traits;

use Borlabs\Cookie\Enum\System\SettingsFieldVisibilityEnum;
use Borlabs\Cookie\Model\Package\PackageModel;

trait ComponentSettingsTrait
{
    /**
     * @param array $languages ['en', 'de', 'fr', ...]
     */
    public function setDefaultComponentSettings(PackageModel $package, array $languages): array
    {
        // These settings are normally selected by the user in the backend in the details of the package.
        $componentSettings = [
            // ['en' => '1', 'de' => '1', 'fr' => '1', ...]
            'language' => array_map(
                fn () => '1',
                array_flip($languages),
            ),
            'settingsForLanguage' => [],
        ];

        foreach ($languages as $language) {
            $componentSettings['settingsForLanguage'][$language] = [
                'contentBlocker' => [],
                'service' => [],
            ];

            foreach (['contentBlocker' => $package->components->contentBlockers->list ?? [], 'service' => $package->components->services->list ?? []] as $type => $components) {
                foreach ($components as $component) {
                    $componentSettings['settingsForLanguage'][$language][$type][$component->key] = [
                        'overwrite-code' => '1',
                        'overwrite-translation' => '1',
                    ];

                    if (!isset($component->languageSpecificSetupSettingsFieldsList->list)) {
                        continue;
                    }

                    foreach ($component->languageSpecificSetupSettingsFieldsList->list as $languageSpecificSetupSettingsFields) {
                        $settingsFieldsLanguage = $languageSpecificSetupSettingsFields->language;

                        if (!isset($componentSettings['settingsForLanguage'][$settingsFieldsLanguage][$type][$component->key])) {
                            continue;
                        }

                        /**
                         * @var \Borlabs\Cookie\Dto\System\SettingsFieldDto $settingsField
                         */
                        foreach ($languageSpecificSetupSettingsFields->settingsFields->list as $settingsField) {
                            if ($settingsField->visibility->is(SettingsFieldVisibilityEnum::EDIT_ONLY())) {
                                continue;
                            }

                            $componentSettings['settingsForLanguage'][$settingsFieldsLanguage][$type][$component->key][$settingsField->key] = $settingsField->value;
                        }
                    }
                }
            }
        }

        return $componentSettings;
    }
}
