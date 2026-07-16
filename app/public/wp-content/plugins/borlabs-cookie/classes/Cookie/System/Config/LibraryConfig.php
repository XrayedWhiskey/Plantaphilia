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

namespace Borlabs\Cookie\System\Config;

use Borlabs\Cookie\Dto\Config\LibraryDto;
use Borlabs\Cookie\Enum\Library\AutoUpdateIntervalEnum;
use Borlabs\Cookie\System\Config\Traits\PackageAutoUpdateTimeHelperTrait;
use DateTime;

/**
 * @extends AbstractConfigManager<LibraryDto>
 *
 * @template TModel of LibraryDto
 */
final class LibraryConfig extends AbstractConfigManager
{
    use PackageAutoUpdateTimeHelperTrait;

    /**
     * Name of `config_name` where the configuration will be stored.
     */
    public const CONFIG_NAME = 'LibraryConfig';

    /**
     * The property is set by {@see \Borlabs\Cookie\System\Config\AbstractConfigManager}.
     */
    public static ?LibraryDto $baseConfigDto = null;

    /**
     * Returns an {@see \Borlabs\Cookie\Dto\Config\LibraryDto} object with all properties set to the default
     * values.
     */
    public function defaultConfig(): LibraryDto
    {
        $defaultConfig = new LibraryDto();
        $defaultConfig->packageAutoUpdateEmailAddresses = [
            $this->option->getThirdPartyOption('admin_email', '')->value,
        ];
        $defaultConfig->packageAutoUpdateInterval = AutoUpdateIntervalEnum::AFTER_24_HOURS();
        $randomTime = (new DateTime())->setTimestamp(
            rand(strtotime('04:00'), strtotime('17:59')),
        );
        $defaultConfig->packageAutoUpdateTimeSpan = $this->getTimeSpanFromTime($randomTime);
        $updateTime = $this->getRandomTimeWithinSpanIgnoringSeconds($defaultConfig->packageAutoUpdateTimeSpan);
        $defaultConfig->packageAutoUpdateTime = $updateTime->format('H:i');

        return $defaultConfig;
    }

    /**
     * This method returns the {@see \Borlabs\Cookie\Dto\Config\LibraryDto} object with all properties
     * when calling the {@see \Borlabs\Cookie\System\Config\LibraryDto::load()} method.
     */
    public function get(): LibraryDto
    {
        $this->ensureConfigWasInitialized();

        return self::$baseConfigDto;
    }

    /**
     * Returns the {@see \Borlabs\Cookie\Dto\Config\LibraryDto} object.
     * If no configuration is found, the default settings are used.
     */
    public function load(): LibraryDto
    {
        return $this->_load();
    }

    /**
     * Saves the configuration.
     */
    public function save(LibraryDto $config): bool
    {
        return $this->_save($config);
    }
}
