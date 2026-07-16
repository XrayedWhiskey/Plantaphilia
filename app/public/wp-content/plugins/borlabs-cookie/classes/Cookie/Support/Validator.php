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

/**
 * Static class Validator.
 *
 * This class contains a collection of static methods that help validate data within programming.
 *
 * @see \Borlabs\Cookie\Support\Validator::isObjectEmpty() Checks if the object does not contain any properties.
 * @see \Borlabs\Cookie\Support\Validator::isHexColor() Checks if the string is a valid hex color code.
 * @see \Borlabs\Cookie\Support\Validator::isStringJSON() Checks if the string is in JSON format.
 * @see \Borlabs\Cookie\Validator\Validator Used to validate user input.
 */
final class Validator
{
    /**
     * Checks if the string is a valid hex color code.
     *
     * @param mixed $string
     */
    public static function isHexColor($string): bool
    {
        if (!is_string($string)) {
            return false;
        }

        return (bool) preg_match('/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/', $string);
    }

    /**
     * Checks if the object does not contain any properties.
     */
    public static function isObjectEmpty(object $obj): bool
    {
        foreach ($obj as $property) {
            return false;
        }

        return true;
    }

    /**
     * Checks if the string is in JSON format.
     */
    public static function isStringJSON(string $string): bool
    {
        json_decode($string);

        return json_last_error() === JSON_ERROR_NONE;
    }
}
