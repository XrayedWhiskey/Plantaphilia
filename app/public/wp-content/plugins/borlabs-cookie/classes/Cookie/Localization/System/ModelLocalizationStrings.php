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

namespace Borlabs\Cookie\Localization\System;

use Borlabs\Cookie\Localization\LocalizationInterface;
use Borlabs\Cookie\Model\CompatibilityPatch\CompatibilityPatchModel;
use Borlabs\Cookie\Model\ContentBlocker\ContentBlockerLocationModel;
use Borlabs\Cookie\Model\ContentBlocker\ContentBlockerModel;
use Borlabs\Cookie\Model\Provider\ProviderModel;
use Borlabs\Cookie\Model\ScriptBlocker\ScriptBlockerModel;
use Borlabs\Cookie\Model\Service\ServiceCookieModel;
use Borlabs\Cookie\Model\Service\ServiceLocationModel;
use Borlabs\Cookie\Model\Service\ServiceModel;
use Borlabs\Cookie\Model\Service\ServiceOptionModel;
use Borlabs\Cookie\Model\ServiceGroup\ServiceGroupModel;
use Borlabs\Cookie\Model\StyleBlocker\StyleBlockerModel;

use function Borlabs\Cookie\System\WordPressGlobalFunctions\_x;

final class ModelLocalizationStrings implements LocalizationInterface
{
    /**
     * @return array<array<string>>
     */
    public static function get(): array
    {
        return [
            'models' => [
                CompatibilityPatchModel::class => _x(
                    'Compatibility Patch',
                    'Backend / Model Localization / Model',
                    'borlabs-cookie',
                ),
                ContentBlockerLocationModel::class => _x(
                    'Content Blocker Location',
                    'Backend / Model Localization / Model',
                    'borlabs-cookie',
                ),
                ContentBlockerModel::class => _x(
                    'Content Blocker',
                    'Backend / Model Localization / Model',
                    'borlabs-cookie',
                ),
                ProviderModel::class => _x(
                    'Provider',
                    'Backend / Model Localization / Model',
                    'borlabs-cookie',
                ),
                ScriptBlockerModel::class => _x(
                    'Script Blocker',
                    'Backend / Model Localization / Model',
                    'borlabs-cookie',
                ),
                ServiceCookieModel::class => _x(
                    'Service Cookie',
                    'Backend / Model Localization / Model',
                    'borlabs-cookie',
                ),
                ServiceGroupModel::class => _x(
                    'Service',
                    'Backend / Model Localization / Model',
                    'borlabs-cookie',
                ),
                ServiceLocationModel::class => _x(
                    'Service Location',
                    'Backend / Model Localization / Model',
                    'borlabs-cookie',
                ),
                ServiceModel::class => _x(
                    'Service',
                    'Backend / Model Localization / Model',
                    'borlabs-cookie',
                ),
                ServiceOptionModel::class => _x(
                    'Service Option',
                    'Backend / Model Localization / Model',
                    'borlabs-cookie',
                ),
                StyleBlockerModel::class => _x(
                    'Style Blocker',
                    'Backend / Model Localization / Model',
                    'borlabs-cookie',
                ),
            ],
        ];
    }
}
