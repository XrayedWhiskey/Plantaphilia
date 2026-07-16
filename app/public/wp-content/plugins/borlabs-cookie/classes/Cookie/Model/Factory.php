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

namespace Borlabs\Cookie\Model;

use Borlabs\Cookie\Dto\Repository\PropertyMapDto;
use Borlabs\Cookie\Dto\Repository\PropertyMapItemDto;
use Borlabs\Cookie\Repository\RepositoryInterface;
use Borlabs\Cookie\Support\Traits\ReflectionTrait;
use DateTime;
use DateTimeImmutable;
use Exception;
use InvalidArgumentException;
use LogicException;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;
use stdClass;

class Factory
{
    use ReflectionTrait;

    /**
     * @param object $args Arguments passed to the constructor of the model
     *
     * @return AbstractModel The model that is an abstraction of the AbstractModel
     */
    public static function new(RepositoryInterface $repository, stdClass $args): AbstractModel
    {
        $repositoryReflection = new ReflectionClass($repository);

        if (!$repositoryReflection->isSubclassOf(RepositoryInterface::class)) {
            throw new InvalidArgumentException('[MODEL_FACTORY:NEW] ' . get_class($repository) . ' is not a RepositoryInterface');
        }

        $modelClassFqn = $repository::MODEL;

        try {
            $instance = new ReflectionClass($modelClassFqn);

            if (!$instance->isSubclassOf(AbstractModel::class)) {
                throw new InvalidArgumentException('[MODEL_FACTORY:NEW] ' . $modelClassFqn . ' is not a AbstractModel');
            }

            $propertyMap = $repository::propertyMap();
            $instance = $instance->newInstanceArgs();

            foreach ($args as $fieldName => $argsValue) {
                $propertyName = self::mapFieldNameToPropertyName($fieldName, $propertyMap);

                if ($propertyName === null) {
                    continue;
                }

                try {
                    $reflectionProperty = new ReflectionProperty($modelClassFqn, $propertyName);
                } catch (ReflectionException $_) {
                    continue;
                }

                $propertyTypeName = $reflectionProperty->getType()->getName();
                $propertyTypeAllowsNull = $reflectionProperty->getType()->allowsNull();
                $isPropertyTypeNameScalar = self::isScalar($propertyTypeName);
                $isPropertyTypeNameDateTimeClass = self::isDateTimeClass($propertyTypeName);

                /*
                 * If $propertyTypeName is a dto, we need to convert $argsValue to the dto by decoding the JSON string
                 * and passing the object to the fromJson method that creates the dto.
                 */
                if (!$isPropertyTypeNameScalar && !$isPropertyTypeNameDateTimeClass && class_exists($propertyTypeName) && (self::isDto($propertyTypeName) || self::isDtoList($propertyTypeName))) {
                    if ($argsValue === null) {
                        if ($propertyTypeAllowsNull) {
                            $instance->{$propertyName} = null;
                        } else {
                            throw new Exception('Received null value for a property which is non-nullable');
                        }
                    } else {
                        $instance->{$propertyName} = $propertyTypeName::fromJson(json_decode($argsValue));
                    }

                    continue;
                }

                /*
                 * If $propertyTypeName is an enum, we need to convert $argsValue to the enum by passing the value
                 * to the fromKey method to create the enum.
                 */
                if (!$isPropertyTypeNameScalar && !$isPropertyTypeNameDateTimeClass && class_exists($propertyTypeName) && self::isEnum($propertyTypeName)) {
                    $instance->{$propertyName} = $propertyTypeName::fromValue($argsValue);

                    continue;
                }

                // Convert to a scalar type or DateTime object.
                $instance->{$propertyName} = self::convertValToType($argsValue, $propertyTypeName, $propertyTypeAllowsNull);
            }

            return $instance;
        } catch (ReflectionException $e) {
            throw new LogicException('[MODEL_FACTORY:NEW] ' . $modelClassFqn . ' is not a class string');
        }
    }

    /**
     * @param mixed $val
     * @param mixed $typeName
     * @param mixed $allowsNull
     *
     * @return mixed
     */
    private static function convertValToType($val, $typeName, $allowsNull)
    {
        if ($allowsNull && $val === null) {
            return null;
        }

        if ($typeName === 'bool') {
            return (bool) $val;
        }

        if ($typeName === 'int') {
            return (int) $val;
        }

        if ($typeName === 'string') {
            return (string) $val;
        }

        if ($typeName === 'array') {
            if (is_string($val)) {
                return (array) json_decode($val, true);
            }

            if (is_object($val)) {
                return (array) $val;
            }

            if (is_array($val)) {
                return $val;
            }

            return [];
        }

        if ($typeName === 'object') {
            if (is_string($val)) {
                return (object) json_decode($val);
            }

            if (is_array($val)) {
                return (object) $val;
            }

            if (is_object($val)) {
                return $val;
            }

            return new stdClass();
        }

        if ($typeName === 'DateTime') {
            return !empty($val) ? new DateTime($val) : null;
        }

        if ($typeName === 'DateTimeImmutable') {
            return !empty($val) ? new DateTimeImmutable($val) : null;
        }

        if ($typeName === 'DateTimeInterface') {
            // TODO: This should better be `DateTimeImmutable` but it is unclear if this breaks existing code
            return !empty($val) ? new DateTime($val) : null;
        }

        return $val;
    }

    private static function mapFieldNameToPropertyName(string $fieldNameName, PropertyMapDto $propertyMap): ?string
    {
        foreach ($propertyMap->map as $property) {
            if (!$property instanceof PropertyMapItemDto) {
                continue;
            }

            if ($property->target === $fieldNameName) {
                return $property->reference;
            }
        }

        return null;
    }

    private function __construct()
    {
    }
}
