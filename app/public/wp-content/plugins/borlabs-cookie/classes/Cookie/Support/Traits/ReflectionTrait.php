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

namespace Borlabs\Cookie\Support\Traits;

use ReflectionClass;
use ReflectionException;

trait ReflectionTrait
{
    /**
     * Caches the reflections of enum classes.
     *
     * @var array<ReflectionClass>
     */
    private static $reflectionCache = [];

    /**
     * Get list of all enum values.
     */
    private static function getConstants(): array
    {
        return self::getReflection()->getConstants();
    }

    /**
     * Returns a ReflectionClass instance of the parent class if a parent class exists, otherwise returns null.
     */
    private static function getParentClass(string $class): ?ReflectionClass
    {
        $parentClass = self::getReflection($class)->getParentClass();

        return $parentClass !== false ? $parentClass : null;
    }

    /**
     * Get list of properties.
     */
    private static function getProperties(): array
    {
        return self::getReflection()->getProperties();
    }

    /**
     * Returns the ReflectionClass for the late bound class.
     *
     * @throws ReflectionException
     */
    private static function getReflection(?string $class = null): ReflectionClass
    {
        $class = $class ?? static::class;

        return self::$reflectionCache[$class] ??= new ReflectionClass($class);
    }

    /**
     * Check if the constant exists.
     */
    private static function hasConstant(string $constantName): bool
    {
        return self::getReflection()->hasConstant($constantName);
    }

    /**
     * Check if the method exists.
     */
    private static function hasMethod(string $methodName): bool
    {
        return self::getReflection()->hasMethod($methodName);
    }

    private static function isDateTimeClass(string $typeName): bool
    {
        return in_array($typeName, ['DateTime', 'DateTimeImmutable', 'DateTimeInterface'], true);
    }

    /**
     * Check if the class is a Dto class.
     */
    private static function isDto(string $class): bool
    {
        while ($parentClass = self::getParentClass($class)) {
            if (strstr($parentClass->getName(), '\Dto\AbstractDto') !== false) {
                return true;
            }

            $class = $parentClass->getName();
        }

        return false;
    }

    /**
     * Check if the class is a AbstractDtoList class.
     */
    private static function isDtoList(string $class): bool
    {
        while ($parentClass = self::getParentClass($class)) {
            if (strstr($parentClass->getName(), '\Dto\AbstractDtoList') !== false) {
                return true;
            }

            $class = $parentClass->getName();
        }

        return false;
    }

    /**
     * Check if the class is a AbstractEnum class.
     */
    private static function isEnum(string $class): bool
    {
        $parentClass = self::getParentClass($class);

        if ($parentClass === null) {
            return false;
        }

        return strstr($parentClass->getName(), '\Enum\AbstractEnum') !== false;
    }

    private static function isScalar(string $typeName): bool
    {
        return in_array($typeName, ['bool', 'int', 'string', 'array', 'object'], true);
    }
}
