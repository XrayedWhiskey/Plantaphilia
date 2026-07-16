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

use Borlabs\Cookie\Dto\Config\AbstractConfigDto;
use Borlabs\Cookie\Dto\System\OptionDto;
use Borlabs\Cookie\System\Config\Traits\AbstractConfigManagerTrait;
use Borlabs\Cookie\System\Option\Option;

/**
 * @template TModel of AbstractConfigDto
 *
 * @mixin AbstractConfigManagerTrait<TModel>
 */
abstract class AbstractConfigManager
{
    use AbstractConfigManagerTrait;

    protected Option $option;

    public function __construct(
        Option $option
    ) {
        $this->option = $option;
    }

    /**
     * Returns all option names and their serialized data.
     *
     * @return array<OptionDto>
     */
    public function getAllConfigs(): array
    {
        return $this->option->getAll(static::CONFIG_NAME);
    }

    /**
     * Checks if a configuration exists.
     *
     * @return bool Returns true if a valid configuration exists, false otherwise
     */
    public function hasConfig(): bool
    {
        $customConfig = $this->option->get(static::CONFIG_NAME);

        return $customConfig->value !== false && is_object($customConfig->value) && $customConfig->value instanceof AbstractConfigDto;
    }

    /**
     * Loads and sets the configuration.
     */
    public function init(): void
    {
        static::$baseConfigDto = $this->_load();
    }

    /**
     * Returns the configuration. If no configuration is found, the default settings are used.
     *
     * @return TModel
     */
    protected function _load(): AbstractConfigDto
    {
        $customConfig = $this->option->get(static::CONFIG_NAME, 'does not exist');

        if (is_object($customConfig->value) && $customConfig->value instanceof AbstractConfigDto) {
            return $this->mapToProperties($customConfig->value, $this->defaultConfig());
        }

        // Fallback - set default values
        if (!is_array($customConfig->value) && !is_object($customConfig->value)) {
            $customConfig->value = $this->defaultConfig();
        }

        return $customConfig->value;
    }

    /**
     * Saves the configuration.
     *
     * @param TModel
     */
    protected function _save(AbstractConfigDto $config): bool
    {
        $saveStatus = $this->option->set(static::CONFIG_NAME, $config, false);
        // Update current config object
        static::$baseConfigDto = $this->_load();

        return $saveStatus;
    }

    /**
     * This method ensures that the config object is initialized.
     */
    protected function ensureConfigWasInitialized(): void
    {
        if (is_null(static::$baseConfigDto)) {
            $this->init();
        }
    }
}
