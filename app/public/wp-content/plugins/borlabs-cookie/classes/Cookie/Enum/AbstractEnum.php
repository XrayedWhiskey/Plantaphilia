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

namespace Borlabs\Cookie\Enum;

use Borlabs\Cookie\DtoList\System\KeyValueDtoList;
use Borlabs\Cookie\Exception\InvalidEnumException;
use Borlabs\Cookie\Support\Traits\ReflectionTrait;
use Borlabs\Cookie\Support\Transformer;
use InvalidArgumentException;
use JsonSerializable;

/**
 * @property string $description
 * @property string $key
 * @property string $value
 */
abstract class AbstractEnum implements JsonSerializable
{
    use ReflectionTrait;

    /**
     * Get the enum from key.
     *
     * @return static
     */
    public static function fromKey(string $key): self
    {
        if (self::hasKey($key)) {
            return new static($key);
        }

        throw new InvalidEnumException('[ENUM][' . static::class . '] Invalid key "' . $key . '"');
    }

    /**
     * Get the enum from value.
     *
     * If the value is not found, the fallback value is used.
     * If the fallback value is not set, a logic exception is thrown.
     *
     * @return static
     */
    public static function fromValue(string $value): self
    {
        if (self::hasValue($value)) {
            return new static(self::getKey($value));
        }

        throw new InvalidEnumException('[ENUM][' . static::class . '] Invalid value "' . $value . '"');
    }

    /**
     * Get all enums.
     */
    public static function getAll(): array
    {
        $list = [];

        foreach (self::getConstants() as $key => $value) {
            $list[] = self::fromKey($key);
        }

        return $list;
    }

    public static function getKeys(): array
    {
        return array_keys(self::getConstants());
    }

    /**
     * The KeyValueDto->key contains the enum value and the KeyValueDto->value contains the localized description.
     */
    public static function getLocalizedKeyValueList(): KeyValueDtoList
    {
        return Transformer::toKeyValueDtoList(self::getAll(), 'value', 'description');
    }

    /**
     * @return static
     */
    public static function getRandom(): self
    {
        $keys = self::getKeys();

        return new static($keys[array_rand($keys)]);
    }

    /**
     * Check if the key exists.
     */
    public static function hasKey(string $key): bool
    {
        return isset(self::getConstants()[$key]);
    }

    /**
     * Check if the value exists.
     */
    public static function hasValue(string $value): bool
    {
        return in_array($value, self::getConstants(), true);
    }

    /**
     * Get the key for an enum value.
     */
    private static function getKey(string $value)
    {
        return array_search($value, self::getConstants(), true);
    }

    /**
     * Get the localized description for an enum value.
     */
    private static function getLocalizedDescription(string $value): string
    {
        return static::localized()[$value] ?? '';
    }

    /**
     * Get the value for an enum key.
     */
    private static function getValue(string $key)
    {
        return self::getConstants()[$key];
    }

    /**
     * Check if the localized method exists.
     */
    private static function isLocalized(): bool
    {
        return self::hasMethod('localized');
    }

    private string $description;

    private string $key;

    private string $value;

    /**
     * Construct a new enum instance.
     */
    public function __construct(string $key)
    {
        if (self::hasKey($key) === false) {
            throw new InvalidArgumentException('[ENUM][' . static::class . '] Invalid key "' . $key . '"');
        }

        $this->key = $key;
        $this->value = self::getValue($key);
        $this->description = $this->value;

        if (self::isLocalized() === true) {
            $this->description = self::getLocalizedDescription($this->value);
        }
    }

    public static function __callStatic($key, $arguments)
    {
        return new static($key);
    }

    public function __get($property)
    {
        return $this->{$property};
    }

    public function __isset($property): bool
    {
        return isset($this->{$property});
    }

    /**
     * Return a string representation of the enum.
     */
    public function __toString(): string
    {
        return (string) $this->value;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function is(self $enum): bool
    {
        return $this->key === $enum->key;
    }

    public function isNot(self $enum): bool
    {
        return !$this->is($enum);
    }

    public function isNotValue(string $key): bool
    {
        return !$this->isValue($key);
    }

    public function isValue(string $key): bool
    {
        return $this->key === $key;
    }

    public function jsonSerialize(): string
    {
        return (string) $this->value;
    }
}
