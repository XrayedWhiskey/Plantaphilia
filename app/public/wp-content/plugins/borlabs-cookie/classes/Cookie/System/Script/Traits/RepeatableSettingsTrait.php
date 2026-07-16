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

namespace Borlabs\Cookie\System\Script\Traits;

use Borlabs\Cookie\DtoList\System\RepeatableSettingsFieldDtoList;

trait RepeatableSettingsTrait
{
    private function transformRepeatableSettingsToArray(RepeatableSettingsFieldDtoList $repeatableSettingsList): array
    {
        return array_column(
            array_map(
                fn ($repeatableSettings) => [
                    'repeatableSettingsKey' => $repeatableSettings->key,
                    'settings' => array_map(
                        fn ($settingsFieldsList) => array_column(
                            $settingsFieldsList->list,
                            'value',
                            'key',
                        ),
                        $repeatableSettings->settingsFieldsListList->list,
                    ),
                ],
                $repeatableSettingsList->list,
            ),
            'settings',
            'repeatableSettingsKey',
        );
    }
}
