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

namespace Borlabs\Cookie\System\Config\Traits;

use Borlabs\Cookie\Dto\Config\AbstractConfigDto;

/**
 * @template TModel of AbstractConfigDto
 */
trait AbstractConfigManagerTrait
{
    /**
     * @param TModel $configDto
     *
     * @return TModel
     */
    public function mapPostDataToProperties(array $postData, AbstractConfigDto $configDto): AbstractConfigDto
    {
        foreach ($configDto as $property => $value) {
            // A missing property is skipped.
            if (!isset($postData[$property])) {
                continue;
            }

            $postDataValue = $postData[$property];

            // Cast post data value based on the config value.
            if (is_bool($value)) {
                $configDto->{$property} = (bool) $postDataValue;
            } elseif (is_int($value)) {
                $configDto->{$property} = (int) ($postDataValue);
            } elseif (is_float($value)) {
                $configDto->{$property} = (float) ($postDataValue);
            } elseif (is_string($value)) {
                $configDto->{$property} = $postDataValue;
            } elseif (is_array($value)) {
                $configDto->{$property} = is_array($postDataValue) ? $postDataValue : $value;
            } elseif (is_object($value)) {
                $configDto->{$property} = is_object($postDataValue) ? $postDataValue : $value;
            }
        }

        return $configDto;
    }

    /**
     * This method casts all values based on the default config values.
     * Missing properties are also added to the object.
     *
     * @param TModel $customConfig
     * @param TModel $defaultConfig
     *
     * @return TModel
     */
    protected function mapToProperties(AbstractConfigDto $customConfig, AbstractConfigDto $defaultConfig): AbstractConfigDto
    {
        foreach ($defaultConfig as $property => $value) {
            // A missing property is added to the config object.
            if (!property_exists($customConfig, $property)) {
                $customConfig->{$property} = $value;
            }

            // Cast config value based on the default value.
            if (is_bool($value)) {
                $customConfig->{$property} = (bool) ($customConfig->{$property});
            } elseif (is_int($value)) {
                $customConfig->{$property} = (int) ($customConfig->{$property});
            } elseif (is_float($value)) {
                $customConfig->{$property} = (float) ($customConfig->{$property});
            } elseif (is_string($value)) {
                $customConfig->{$property} = !empty($customConfig->{$property}) ? $customConfig->{$property} : $value;
            } elseif (is_array($value)) {
                $customConfig->{$property} = is_array($customConfig->{$property}) ? $customConfig->{$property} : $value;
            } elseif (is_object($value)) {
                $customConfig->{$property} = isset($customConfig->{$property}) && is_object($customConfig->{$property}) ? $customConfig->{$property}
                    : $value;
            }
        }

        return $customConfig;
    }
}
