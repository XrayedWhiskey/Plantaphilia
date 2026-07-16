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
 * Static class HMAC.
 *
 * This class contains methods to validate data and generate a key hashed value using the HMAC method.
 *
 * @see https://en.wikipedia.org/wiki/HMAC
 * @see \Borlabs\Cookie\Support\Hmac::hash() Generate a key hashed value using the HMAC method.
 * @see \Borlabs\Cookie\Support\Hmac::isValid() Validates the data against the hash.
 */
final class Hmac
{
    /**
     * Generate a key hashed value using the HMAC method.
     */
    public static function hash(object $data, string $secretKey): string
    {
        $data = json_encode($data);

        return hash_hmac('sha256', $data, $secretKey);
    }

    /**
     * Validates the data against the hash.
     */
    public static function isValid(object $data, string $secretKey, string $hash): bool
    {
        $is_valid = false;
        $data = json_encode($data);
        $data_hash = hash_hmac('sha256', $data, $secretKey);

        if ($data_hash == $hash) {
            $is_valid = true;
        }

        return $is_valid;
    }
}
