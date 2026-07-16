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

namespace Borlabs\Cookie\Support;

final class Searcher
{
    /**
     * Returns the value of the first object in the list whose value was found in the string.
     */
    public static function findFirstMatchingObjectValue(string $string, array $objectList, string $propertyToMatch): ?string
    {
        foreach ($objectList as $object) {
            if (isset($object->{$propertyToMatch}) && strpos($string, $object->{$propertyToMatch}) !== false) {
                return $object->{$propertyToMatch};
            }
        }

        return null;
    }

    public static function findObject(array $objectList, string $key, string $value, bool $strict = true): ?object
    {
        foreach ($objectList as $object) {
            if (isset($object->{$key}) && $strict ? $object->{$key} === $value : $object->{$key} == $value) {
                return $object;
            }
        }

        return null;
    }
}
