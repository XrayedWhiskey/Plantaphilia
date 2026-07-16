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
use Borlabs\Cookie\System\Language\Language;
use Borlabs\Cookie\System\Option\Option;

/**
 * @template TModel of AbstractConfigDto
 *
 * @mixin AbstractConfigManagerTrait<TModel>
 */
abstract class AbstractConfigManagerWithLanguage
{
    use AbstractConfigManagerTrait;

    private Language $language;

    private Option $option;

    public function __construct(
        Language $language,
        Option $option
    ) {
        $this->language = $language;
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
     * Checks if a configuration exists for the specified language code.
     *
     * @param string $languageCode The language code to check the configuration for
     *
     * @return bool Returns true if a valid configuration exists, false otherwise
     */
    public function hasConfig(string $languageCode): bool
    {
        $customConfig = $this->option->get(static::CONFIG_NAME, false, $languageCode);

        return $customConfig->value !== false && is_object($customConfig->value) && $customConfig->value instanceof AbstractConfigDto;
    }

    /**
     * Loads and sets the configuration of the specified language.
     *
     * @param null|string $languageCode if null, the current language code is used
     */
    public function init(?string $languageCode = null): void
    {
        if (is_null($languageCode)) {
            $languageCode = $this->language->getSelectedLanguageCode();
        }

        $languageCode = strtolower($languageCode);
        static::$baseConfigDto = $this->_load($languageCode);
    }

    /**
     * Returns the configuration of the specified language.
     * If no configuration is found for the language, the default settings are used.
     *
     * @return TModel
     */
    protected function _load(string $languageCode): AbstractConfigDto
    {
        $customConfig = $this->option->get(static::CONFIG_NAME, 'does not exist', $languageCode);

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
     * Saves the configuration of the specified language.
     */
    protected function _save(AbstractConfigDto $config, string $languageCode): bool
    {
        $saveStatus = $this->option->set(static::CONFIG_NAME, $config, false, $languageCode);
        // Update current config object
        static::$baseConfigDto = $this->_load($languageCode);

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
