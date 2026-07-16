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

use Borlabs\Cookie\Dto\System\KeyValueDto;
use Borlabs\Cookie\DtoList\System\KeyValueDtoList;
use Borlabs\Cookie\Enum\AbstractEnum;
use stdClass;

/**
 * Which transformer fell down the stairs? Stumblebee.
 */
final class Transformer
{
    public static function arrayValuesToString(array $array): array
    {
        $modifiedArray = [];

        foreach ($array as $key => $value) {
            if ($value instanceof AbstractEnum) {
                $modifiedArray[$key] = (string) $value;
            } elseif (is_array($value)) {
                $modifiedArray[$key] = self::arrayValuesToString($value);
            } elseif (is_object($value)) {
                $modifiedArray[$key] = self::arrayValuesToString((array) $value);
            } else {
                $modifiedArray[$key] = (string) $value;
            }
        }

        return $modifiedArray;
    }

    public static function buildNestedArray(array $input): array
    {
        $output = [];

        foreach ($input as $key => $value) {
            $parts = explode('_', $key);
            $current = &$output;

            foreach ($parts as $part) {
                // If the part is numeric, treat it as an integer index
                $part = is_numeric($part) ? (int) $part : $part;

                if (!isset($current[$part])) {
                    $current[$part] = [];
                }

                $current = &$current[$part];
            }

            $current = $value;
            unset($current);
        }

        return $output;
    }

    public static function flattenArray(array $array, array &$flattened = []): array
    {
        foreach ($array as $item) {
            if (is_array($item) || is_object($item)) {
                self::flattenArray((array) $item, $flattened);
            } else {
                $flattened[] = $item;
            }
        }

        return $flattened;
    }

    /**
     * TODO.
     */
    public static function naturalSortArrayByObjectProperty(array $objectList, string $propertyName): array
    {
        $sortedArray = $objectList;
        usort($sortedArray, function ($a, $b) use ($propertyName) {
            return strnatcmp($a->{$propertyName}, $b->{$propertyName});
        });

        return $sortedArray;
    }

    public static function objectToArray(object $object): array
    {
        return json_decode(
            json_encode($object),
            true,
        );
    }

    public static function objectToStdClass(object $object): stdClass
    {
        return json_decode(
            json_encode($object),
        );
    }

    public static function toKeyValueDtoList(array $array, ?string $key = null, ?string $value = null): KeyValueDtoList
    {
        return new KeyValueDtoList(array_map(function ($arrayValue, $arrayKey) use ($key, $value) {
            return new KeyValueDto(
                (string) ($key === null ? $arrayKey : (is_object($arrayValue) ? $arrayValue->{$key} : $arrayValue[$key])),
                (string) ($value === null ? $arrayValue : (is_object($arrayValue) ? $arrayValue->{$value} : $arrayValue[$value])),
            );
        }, $array, array_keys($array)));
    }
}
