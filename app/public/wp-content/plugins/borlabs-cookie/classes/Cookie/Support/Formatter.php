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

use DateTime;
use DateTimeZone;

/**
 * Static class Formatter.
 *
 * This class contains a collection of static methods that format data.
 *
 * @see \Borlabs\Cookie\Support\Formatter::timestamp() Formats a timestamp to the specified format.
 * @see \Borlabs\Cookie\Support\Formatter::interpolate() Interpolates context values into the message placeholders.
 */
final class Formatter
{
    /**
     * Interpolates context values into the message placeholders.
     *
     * @param array<string, string> $context
     */
    public static function interpolate(string $message, array $context = []): string
    {
        return preg_replace_callback('/{{\s*([a-zA-Z0-9_\.]+)\s*}}/', function ($matches) use ($context) {
            return self::getValueFromContext($matches[1], $context);
        }, $message);
    }

    public static function onlyFirstLetter(string $string, int $offset = 0, string $separator = '_', string $glue = '_'): string
    {
        $stringParts = explode($separator, $string);
        $shortenedString = '';

        foreach ($stringParts as $index => $word) {
            $shortenedString .= ($index < $offset ? $word : $word[0]) . (array_key_last($stringParts) !== $index ? $glue : '');
        }

        return $shortenedString;
    }

    public static function removeVowels(string $string, string $separator = '_', string $glue = '_'): string
    {
        $stringParts = explode($separator, $string);
        $shortenedString = '';

        foreach ($stringParts as $index => $word) {
            $shortenedString .= preg_replace('/[aeiou]/', '', $word) . (array_key_last($stringParts) !== $index ? $glue : '');
        }

        return $shortenedString;
    }

    /**
     * Formats a timestamp to the specified format.
     *
     * @param int         $timestamp  Unix timestamp
     * @param null|string $dateFormat Optional; Default: WordPress 'date_format' option; Example: Y-m-d
     * @param null|string $timeFormat Optional; Default: WordPress 'time_format' option; Example: H:i
     */
    public static function timestamp(int $timestamp, ?string $dateFormat = null, ?string $timeFormat = null, ?string $timeZone = null, ?string $gmtOffset = null): string
    {
        if (is_null($dateFormat)) {
            $dateFormat = get_option('date_format');
        }

        if (is_null($timeFormat)) {
            $timeFormat = get_option('time_format');
        }

        $dateTimeZone = new DateTimeZone('GMT');

        if (!is_null($gmtOffset)) {
            $sign = $gmtOffset < 0 ? '-' : '+';
            $absoluteOffset = abs($gmtOffset * 100);
            $timeZone = sprintf('%s%04d', $sign, $absoluteOffset);
        } elseif (is_null($timeZone) && is_null($gmtOffset)) {
            $wpTimeZone = get_option('timezone_string', '');
            $wpGmtOffset = get_option('gmt_offset', '');

            if ($wpTimeZone !== '') {
                $timeZone = $wpTimeZone;
            } elseif ($wpGmtOffset !== '') {
                $sign = $wpGmtOffset < 0 ? '-' : '+';
                $absoluteOffset = abs($wpGmtOffset * 100);
                $timeZone = sprintf('%s%04d', $sign, $absoluteOffset);
            }
        }

        if ($timeZone !== '') {
            $dateTimeZone = new DateTimeZone($timeZone);
        }

        $dateFormat .= (!empty($dateFormat) && !empty($timeFormat) ? ' ' : '');
        $dateFormat .= $timeFormat;

        $dateTime = (new DateTime())
            ->setTimestamp($timestamp)
            ->setTimezone($dateTimeZone);

        return $dateTime->format($dateFormat);
    }

    public static function toCamelCase(string $string): string
    {
        return lcfirst(self::toPascalCase($string));
    }

    public static function toKebabCase(string $string): string
    {
        $string = strtolower(trim(preg_replace('/(?<!\s)([A-Z])/', ' $1', $string)));
        $string = preg_replace('/[^a-z0-9]+/', ' ', $string);

        return preg_replace('/(\h)+/', '-', $string);
    }

    public static function toPascalCase(string $string): string
    {
        $string = ucwords(preg_replace('/[^a-z0-9]+/i', ' ', self::toKebabCase($string)));

        return str_replace(' ', '', $string);
    }

    public static function toSnakeCase(string $string): string
    {
        return str_replace('-', '_', self::toKebabCase($string));
    }

    private static function getValueFromContext(string $placeholder, array $context)
    {
        $keys = explode('.', $placeholder);
        $value = $context;

        foreach ($keys as $key) {
            if (is_array($value)) {
                $value = $value[$key] ?? null;
            } elseif (is_object($value)) {
                $value = $value->{$key} ?? null;
            } else {
                break;
            }
        }

        return $value;
    }
}
