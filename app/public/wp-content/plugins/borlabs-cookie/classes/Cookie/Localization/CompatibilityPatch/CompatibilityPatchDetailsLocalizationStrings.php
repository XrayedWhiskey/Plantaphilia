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

namespace Borlabs\Cookie\Localization\CompatibilityPatch;

use Borlabs\Cookie\Localization\LocalizationInterface;

use function Borlabs\Cookie\System\WordPressGlobalFunctions\_x;

final class CompatibilityPatchDetailsLocalizationStrings implements LocalizationInterface
{
    /**
     * @return array<array<string>>
     */
    public static function get(): array
    {
        return [
            // Alert messages
            'alert' => [
                'fileMissing' => _x(
                    'Error: The required file is missing. To resolve this issue, please reinstall the package.',
                    'Backend / Compatibility Patch Details / Alert Message',
                    'borlabs-cookie',
                ),
            ],

            // Description List
            'descriptionList' => [
                'calculatedHash' => _x(
                    'Calculated Hash',
                    'Backend  / Compatibility Patch Details /  Description List',
                    'borlabs-cookie',
                ),
                'fileName' => _x(
                    'File Name',
                    'Backend  / Compatibility Patch Details /  Description List',
                    'borlabs-cookie',
                ),
                'filePath' => _x(
                    'File Path',
                    'Backend  / Compatibility Patch Details /  Description List',
                    'borlabs-cookie',
                ),
                'id' => _x(
                    'ID',
                    'Backend  / Compatibility Patch Details /  Description List',
                    'borlabs-cookie',
                ),
                'installedAt' => _x(
                    'Installed at',
                    'Backend  / Compatibility Patch Details /  Description List',
                    'borlabs-cookie',
                ),
                'packageName' => _x(
                    'Package',
                    'Backend  / Compatibility Patch Details /  Description List',
                    'borlabs-cookie',
                ),
                'requiredHash' => _x(
                    'Required Hash',
                    'Backend  / Compatibility Patch Details /  Description List',
                    'borlabs-cookie',
                ),
                'updatedAt' => _x(
                    'Updated at',
                    'Backend  / Compatibility Patch Details /  Description List',
                    'borlabs-cookie',
                ),
                'validationStatus' => _x(
                    'Valid',
                    'Backend  / Compatibility Patch Details /  Description List',
                    'borlabs-cookie',
                ),
                'version' => _x(
                    'Version',
                    'Backend  / Compatibility Patch Details /  Description List',
                    'borlabs-cookie',
                ),
            ],
        ];
    }
}
