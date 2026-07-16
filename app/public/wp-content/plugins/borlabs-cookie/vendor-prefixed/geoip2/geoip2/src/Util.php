<?php
/**
 * @license Apache-2.0
 *
 * Modified by borlabs on 06-February-2026 using {@see https://github.com/BrianHenryIE/strauss}.
 */

declare(strict_types=1);

namespace Borlabs\Cookie\Dependencies\GeoIp2;

class Util
{
    /**
     * This returns the network in CIDR notation for the given IP and prefix
     * length. This is for internal use only.
     *
     * @internal
     * @ignore
     */
    public static function cidr(string $ipAddress, int $prefixLen): string
    {
        $ipBytes = inet_pton($ipAddress);
        $networkBytes = str_repeat("\0", \strlen($ipBytes));

        $curPrefix = $prefixLen;
        for ($i = 0; $i < \strlen($ipBytes) && $curPrefix > 0; $i++) {
            $b = $ipBytes[$i];
            if ($curPrefix < 8) {
                $shiftN = 8 - $curPrefix;
                $b = \chr(0xFF & (\ord($b) >> $shiftN) << $shiftN);
            }
            $networkBytes[$i] = $b;
            $curPrefix -= 8;
        }

        $network = inet_ntop($networkBytes);

        return "$network/$prefixLen";
    }
}
