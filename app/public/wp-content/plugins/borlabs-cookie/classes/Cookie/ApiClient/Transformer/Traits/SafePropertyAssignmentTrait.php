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

namespace Borlabs\Cookie\ApiClient\Transformer\Traits;

use Borlabs\Cookie\Exception\IncompatibleTypeException;
use ReflectionNamedType;
use ReflectionProperty;

trait SafePropertyAssignmentTrait
{
    public function assignPropertySafely($targetObject, string $targetProperty, $sourceObject, string $sourceProperty, ?string $castTo = null)
    {
        $targetReflection = new ReflectionProperty(get_class($targetObject), $targetProperty);
        $targetType = $targetReflection->getType();

        if (!property_exists($sourceObject, $sourceProperty)) {
            throw new IncompatibleTypeException('Source property does not exist', ['sourceProperty' => $sourceProperty]);
        }

        $sourceData = $sourceObject->{$sourceProperty};

        if ($castTo === 'array') {
            $sourceData = (array) $sourceData;
        } elseif ($castTo === 'float') {
            $sourceData = (float) $sourceData;
        } elseif ($castTo === 'int') {
            $sourceData = (int) $sourceData;
        } elseif ($castTo === 'string') {
            $sourceData = (string) $sourceData;
        } elseif (!is_null($castTo)) {
            throw new IncompatibleTypeException('Unknown castTo type', ['castTo' => $castTo]);
        }

        if (!$targetType) {
            return $sourceData;
        }

        if (is_null($sourceData)) {
            if (!$targetType->allowsNull()) {
                throw new IncompatibleTypeException('Null not allowed', ['sourceProperty' => $sourceProperty, 'targetProperty' => $targetProperty,]);
            }

            return $sourceData;
        }

        if ($targetType instanceof ReflectionNamedType) {
            if ($targetType->getName() === $this->mapGettypeValue(gettype($sourceData))) {
                return $sourceData;
            }

            throw new IncompatibleTypeException('Types do not match', ['targetType' => $targetType->getName(), 'sourceType' => gettype($sourceData),]);
        }

        throw new IncompatibleTypeException('Type do not match', ['targetObject' => get_class($targetObject), '$targetProperty' => $targetProperty, 'targetType' => 'unknown', 'sourceType' => gettype($sourceData),]);
    }

    private function mapGettypeValue(string $gettypeValue): string
    {
        if ($gettypeValue === 'boolean') {
            return 'bool';
        }

        if ($gettypeValue === 'double') {
            return 'float';
        }

        if ($gettypeValue === 'integer') {
            return 'int';
        }

        if ($gettypeValue === 'NULL') {
            return 'null';
        }

        if ($gettypeValue === 'unknown type') {
            return 'mixed';
        }

        return $gettypeValue;
    }
}
