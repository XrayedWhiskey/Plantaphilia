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
 * Static class Sanitizer.
 *
 * This class contains a collection of static methods that sanitize data.
 *
 * @see \Borlabs\Cookie\Support\Sanitizer::hostList() Sanitizes and sorts a list of hosts or URLs. Duplicates are
 *     removed.
 * @see \Borlabs\Cookie\Support\Sanitizer::hostname() Sanitizes a host or URL.
 * @see \Borlabs\Cookie\Support\Sanitizer::requestData() Recursively removes slashes from data and eliminates spaces
 *     before and after strings.
 */
final class Sanitizer
{
    public static function booleanString(string $input): bool
    {
        if ($input === '0' || $input === '') {
            return false;
        }

        return $input === '1';
    }

    /**
     * Removes the entries from the given values array that are not in the array of allowed values.
     *
     * @param array $values        list of values
     * @param array $allowedValues List of allowed values
     */
    public static function convertToIntegerAndFilterAllowedValues(array $values, array $allowedValues): array
    {
        $sanitizedValues = [];

        foreach ($values as $key) {
            if (is_numeric($key) && in_array((int) $key, $allowedValues, true)) {
                $sanitizedValues[] = (int) $key;
            }
        }

        return $sanitizedValues;
    }

    /**
     * Removes the entries from the gives values array that are not keys of the given enum.
     *
     * @param array  $values    list of values
     * @param string $enumClass AbstractEnum class name (e.g. ServiceOptionEnum::class)
     *
     * @return array List of keys of the give enum. No other values.
     */
    public static function enumList(array $values, string $enumClass): array
    {
        $allowedValues = $enumClass::getKeys();

        if (isset($allowedValues[0]) && gettype($allowedValues[0]) === 'integer') {
            return self::convertToIntegerAndFilterAllowedValues($values, $allowedValues);
        }

        return self::filterAllowedValues($values, $allowedValues);
    }

    /**
     * Removes the entries from the given values array that are not in the array of allowed values.
     *
     * @param array $values        list of values
     * @param array $allowedValues List of allowed values
     */
    public static function filterAllowedValues(array $values, array $allowedValues): array
    {
        $sanitizedValues = [];

        foreach ($values as $key) {
            if (in_array($key, $allowedValues, true)) {
                $sanitizedValues[] = $key;
            }
        }

        return $sanitizedValues;
    }

    /**
     * Sanitizes and sorts an array of hosts or URLs. Duplicates are removed.
     *
     * @param array $uncleanedHosts list of host strings
     * @param bool  $allowUrl       optional; Default: `false`; `true`: URLs are allowed and are not reduced to their host
     */
    public static function hostArray(array $uncleanedHosts, bool $allowUrl = false): array
    {
        $cleanedHosts = [];

        foreach ($uncleanedHosts as $hostLine) {
            // Clean hosts by ,
            $hosts = explode(',', $hostLine);

            foreach ($hosts as $host) {
                $cleanedHost = self::hostname($host, $allowUrl);

                if ($cleanedHost !== '') {
                    $cleanedHosts[$cleanedHost] = $cleanedHost;
                }
            }
        }

        sort($cleanedHosts, SORT_NATURAL);

        return $cleanedHosts;
    }

    /**
     * Sanitizes and sorts a list of hosts or URLs. Duplicates are removed.
     *
     * @param string $hosts    multiple hosts MUST separated by newline character or comma character
     * @param bool   $allowUrl optional; Default: `false`; `true`: URLs are allowed and are not reduced to their host
     *
     * @return array<string>
     */
    public static function hostList(string $hosts, bool $allowUrl = false): array
    {
        // Clean hosts
        $uncleanedHosts = explode("\n", $hosts);

        return self::hostArray($uncleanedHosts, $allowUrl);
    }

    /**
     * Sanitizes a host or URL.
     *
     * @param bool $allowUrl optional; Default: `false`; `true`: URL is allowed and is not reduced to its host
     */
    public static function hostname(string $hostname, bool $allowUrl = false): string
    {
        $hostname = trim($hostname);

        if ($hostname === '') {
            return '';
        }

        if ($allowUrl === true && filter_var($hostname, FILTER_VALIDATE_URL) === false) {
            return '';
        }

        if ($allowUrl === false && filter_var($hostname, FILTER_VALIDATE_URL) !== false) {
            $urlData = parse_url($hostname);
            $hostname = $urlData['host'];
        }

        $hostname = stripslashes($hostname);

        return strtolower($hostname);
    }

    /**
     * If input string is empty (all whitespace removed) then the function returns null.
     * Otherwise it returns the input.
     *
     * @return null|string Sanitized input
     */
    public static function nullableString(string $input): ?string
    {
        if (strlen(trim($input)) > 0) {
            return $input;
        }

        return null;
    }

    /**
     * Recursively removes slashes from data and eliminates spaces before and after strings.
     *
     * @param array<string> $requestData
     *
     * @return array<string>
     */
    public static function requestData(array $requestData): array
    {
        array_walk_recursive($requestData, static function (&$val): void {
            $val = stripslashes((string) $val);
            $val = trim($val);
        });

        return $requestData;
    }

    public static function stripNonMatchingCharacters(string $input, string $pattern = '[^a-zA-Z]'): string
    {
        return preg_replace('/' . $pattern . '/', '', $input);
    }
}
