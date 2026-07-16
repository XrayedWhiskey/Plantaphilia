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

use Borlabs\Cookie\Dto\Config\BackwardsCompatibilityDto;

/**
 * @extends AbstractConfigManager<BackwardsCompatibilityDto>
 *
 * @template TModel of BackwardsCompatibilityDto
 */
final class BackwardsCompatibilityConfig extends AbstractConfigManager
{
    /**
     * Name of `config_name` where the configuration will be stored.
     */
    public const CONFIG_NAME = 'BackwardsCompatibilityConfig';

    /**
     * The property is set by {@see \Borlabs\Cookie\System\Config\AbstractConfigManager}.
     */
    public static ?BackwardsCompatibilityDto $baseConfigDto = null;

    /**
     * Returns an {@see \Borlabs\Cookie\Dto\Config\BackwardsCompatibilityDto} object with all properties set to the default
     * values.
     */
    public function defaultConfig(): BackwardsCompatibilityDto
    {
        return new BackwardsCompatibilityDto();
    }

    /**
     * This method returns the {@see \Borlabs\Cookie\Dto\Config\BackwardsCompatibilityDto} object with all properties
     * when calling the {@see \Borlabs\Cookie\System\Config\BackwardsCompatibilityDto::load()} method.
     */
    public function get(): BackwardsCompatibilityDto
    {
        $this->ensureConfigWasInitialized();

        return self::$baseConfigDto;
    }

    /**
     * Returns the {@see \Borlabs\Cookie\Dto\Config\BackwardsCompatibilityDto} object.
     * If no configuration is found, the default settings are used.
     */
    public function load(): BackwardsCompatibilityDto
    {
        return $this->_load();
    }

    /**
     * Saves the configuration.
     */
    public function save(BackwardsCompatibilityDto $config): bool
    {
        return $this->_save($config);
    }
}
