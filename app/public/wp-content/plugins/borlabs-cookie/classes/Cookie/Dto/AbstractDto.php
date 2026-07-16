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

namespace Borlabs\Cookie\Dto;

use Borlabs\Cookie\Exception\CastUnknownTypeException;
use Borlabs\Cookie\Exception\TypeHintDoesNotExistException;
use Borlabs\Cookie\Support\Traits\ReflectionTrait;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use JsonSerializable;
use ReflectionProperty;
use stdClass;

abstract class AbstractDto implements JsonSerializable
{
    use ReflectionTrait;

    /**
     * @return null|static
     */
    public static function fromJson(?object $json = null)
    {
        if ($json === null) {
            return null;
        }

        /** @var static $dto */
        $dto = self::getReflection()->newInstanceWithoutConstructor();
        self::setProperties(
            $dto,
            $json,
        );

        return count(get_object_vars($dto)) > 0 ? $dto : null;
    }

    /**
     * Prepares the given value for JSON serialization.
     *
     * @throws CastUnknownTypeException
     */
    public static function prepareForJson(?AbstractDto $dto = null): ?object
    {
        if ($dto === null) {
            return null;
        }

        $untypedObject = new stdClass();
        // Loop through properties
        foreach (self::getProperties() as $property) {
            // Check if a mutator exists for the requested property.
            if (self::getReflection()->hasMethod('__' . $property->name . 'ToJson')) {
                $untypedObject->{$property->name} = static::{'__' . $property->name . 'ToJson'}($dto->{$property->name});

                continue;
            }

            if ($property->getType() === null) {
                if (self::hasDocCommentMixedTypeHint($property) === false) {
                    throw new TypeHintDoesNotExistException('Type hint does not exist for property "' . $property->getName() . '" of class "' . static::class . '".');
                }

                $untypedObject->{$property->name} = serialize($dto->{$property->name});

                continue;
            }

            $type = $property->getType()->getName();
            $isTypeNameScalar = self::isScalar($type);

            // Check if the requested type is another dto.
            if (!$isTypeNameScalar && class_exists($type) && (self::isDto($type) || self::isDtoList($type))) {
                $untypedObject->{$property->name} = $type::prepareForJson($dto->{$property->name});

                continue;
            }

            // Check if the requested type is an enum.
            if (!$isTypeNameScalar && class_exists($type) && self::isEnum($type)) {
                // TODO: Check if setting null is correct. Happened with `failureType` of PageDto
                $untypedObject->{$property->name} = isset($dto->{$property->name}) ? $dto->{$property->name}->value : null;

                continue;
            }

            if ($type === 'DateTime') {
                $untypedObject->{$property->name} = $dto->{$property->name}->format(DateTimeInterface::ATOM);

                continue;
            }

            if ($type === 'DateTimeImmutable') {
                $untypedObject->{$property->name} = $dto->{$property->name}->format(DateTimeInterface::ATOM);

                continue;
            }

            // Cast to scalar type
            $untypedObject->{$property->name} = self::castType($type, $dto->{$property->name});
        }

        return $untypedObject;
    }

    /**
     * Returns a json string of the dto. An alternative is to use json_encode($dto).
     *
     * @throws CastUnknownTypeException
     *
     * @return false|string
     */
    public static function toJson(AbstractDto $dto)
    {
        return json_encode(self::prepareForJson($dto));
    }

    /**
     * Casts a value to a specific type.
     *
     * @param mixed $data
     *
     * @throws CastUnknownTypeException
     *
     * @return array|bool|float|int|object|string
     */
    private static function castType(string $type, $data)
    {
        if ($type === 'array') {
            return (array) $data;
        }

        if ($type === 'bool') {
            return (bool) (is_string($data) && strtolower($data) === 'false' ? false : $data);
        }

        if ($type === 'float') {
            return (float) $data;
        }

        if ($type === 'int') {
            return (int) $data;
        }

        if ($type === 'object') {
            return (object) $data;
        }

        if ($type === 'string') {
            return (string) $data;
        }

        throw new CastUnknownTypeException('Unknown type "' . $type . '"');
    }

    private static function hasDocCommentMixedTypeHint(ReflectionProperty $property): bool
    {
        $docComment = $property->getDocComment();

        if ($docComment === false) {
            return false;
        }

        $matches = [];

        return (bool) (preg_match('/@var\s+([^\s]+)/', $docComment, $matches) && $matches[1] === 'mixed');
    }

    /**
     * Sets the properties for the specified dto.
     *
     * @throws CastUnknownTypeException
     */
    private static function setProperties(AbstractDto $dto, object $json): void
    {
        foreach (self::getProperties() as $property) {
            // Check if a mutator exists for the requested property.
            if (self::getReflection()->hasMethod('__' . $property->name . 'FromJson')) {
                if (isset($json->{$property->name})) {
                    $dto->{$property->name} = static::{'__' . $property->name . 'FromJson'}($json->{$property->name});
                }

                continue;
            }

            if ($property->getType() === null) {
                if (self::hasDocCommentMixedTypeHint($property) === false) {
                    throw new TypeHintDoesNotExistException('Type hint does not exist for property "' . $property->getName() . '" of class "' . static::class . '".');
                }

                $dto->{$property->name} = unserialize($json->{$property->name});

                continue;
            }

            $type = $property->getType()->getName();
            $isTypeNameScalar = self::isScalar($type);

            // If the property allows null values and the JSON does not have the property set, assign null to the DTO property.
            if (!$isTypeNameScalar && $property->getType()->allowsNull() && !isset($json->{$property->name})) {
                $dto->{$property->name} = null;

                continue;
            }

            // Check if the requested type is another dto class.
            if (!$isTypeNameScalar && class_exists($type) && (self::isDto($type) || self::isDtoList($type))) {
                $dto->{$property->name} = $type::fromJson($json->{$property->name});

                continue;
            }

            // Check if the requested type is another enum class.
            if (!$isTypeNameScalar && class_exists($type) && self::isEnum($type)) {
                $dto->{$property->name} = $type::fromValue($json->{$property->name});

                continue;
            }

            if ($type === 'DateTime') {
                $dto->{$property->name} = new DateTime($json->{$property->name});

                continue;
            }

            if ($type === 'DateTimeImmutable') {
                $dto->{$property->name} = new DateTimeImmutable($json->{$property->name});

                continue;
            }

            // Cast to scalar type
            $dto->{$property->name} = self::castType($type, $json->{$property->name});
        }
    }

    public function __clone()
    {
        foreach (self::getProperties() as $property) {
            if (is_object($this->{$property->name})) {
                $this->{$property->name} = clone $this->{$property->name};
            } elseif (is_array($this->{$property->name})) {
                $this->{$property->name} = $this->deepCloneArray($this->{$property->name});
            }
        }
    }

    public function jsonSerialize(): stdClass
    {
        return self::prepareForJson($this);
    }

    private function deepCloneArray(array $array): array
    {
        $clonedArray = [];

        foreach ($array as $key => $value) {
            if (is_object($value)) {
                $clonedArray[$key] = clone $value;
            } elseif (is_array($value)) {
                $clonedArray[$key] = $this->deepCloneArray($value);
            } else {
                $clonedArray[$key] = $value;
            }
        }

        return $clonedArray;
    }
}
