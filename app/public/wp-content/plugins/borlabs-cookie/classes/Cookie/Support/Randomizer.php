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

final class Randomizer
{
    public static function randomString(
        int $length = 32,
        string $charactersPool = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'
    ): string {
        $charactersLength = strlen($charactersPool);
        $randomString = '';

        for ($i = 0; $i < $length; ++$i) {
            if (function_exists('random_int')) {
                $index = random_int(0, $charactersLength - 1);
            } elseif (function_exists('mt_rand')) {
                $index = mt_rand(0, $charactersLength - 1);
            } else {
                $index = rand(0, $charactersLength - 1);
            }

            $randomString .= $charactersPool[$index];
        }

        return $randomString;
    }
}
