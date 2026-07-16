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
 * Static class Converter.
 *
 * This class contains a collection of static methods that convert data to specific formats.
 *
 * @see \Borlabs\Cookie\Support\Converter::hexToHsl() Returns HSL values based on a hex string.
 * @see \Borlabs\Cookie\Support\Converter::hexToRgb() Returns RGB values based on a hex string.
 */
final class Converter
{
    /**
     * Returns HSL values based on a hex string.
     *
     * @return array<int, float|int> 0 = Hue, 1 = Saturation, 2 = Luminance
     */
    public static function hexToHsl(string $hex): array
    {
        $rgb = self::hexToRgb($hex);
        $rgb['r'] /= 255;
        $rgb['g'] /= 255;
        $rgb['b'] /= 255;

        $max = max($rgb);
        $min = min($rgb);
        $l = ($max + $min) / 2;
        $h = 0;

        if ($max === $min) {
            $s = 0;
        } else {
            $diff = $max - $min;
            $s = $l > 0.5 ? $diff / (2 - $max - $min) : $diff / ($max + $min);

            switch ($max) {
                case $rgb['r']:
                    $h = ($rgb['g'] - $rgb['b']) / $diff + ($rgb['g'] < $rgb['b'] ? 6 : 0);

                    break;

                case $rgb['g']:
                    $h = ($rgb['b'] - $rgb['r']) / $diff + 2;

                    break;

                case $rgb['b']:
                    $h = ($rgb['r'] - $rgb['g']) / $diff + 4;

                    break;
            }

            $h = round($h * 60);
        }

        return [
            $h,
            round($s * 100),
            round($l * 100),
        ];
    }

    /**
     * Returns RGB values based on a hex string.
     *
     * @return array<int> r = red value, g = green value, b = blue value
     */
    public static function hexToRgb(string $hex): array
    {
        $hex = str_replace('#', '', $hex);

        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }

        $hex = [
            $hex[0] . $hex[1],
            $hex[2] . $hex[3],
            $hex[4] . $hex[5],
        ];
        $rgb = array_map(
            static function ($part) {
                return hexdec($part);
            },
            $hex,
        );

        return [
            'r' => (int) ($rgb[0]),
            'g' => (int) ($rgb[1]),
            'b' => (int) ($rgb[2]),
        ];
    }

    public static function markdownToHtml(string $text): string
    {
        $text = htmlentities($text, ENT_QUOTES, 'UTF-8');
        $text = preg_replace('/\son([a-zA-z])*=/i', ' data-escaped-on$1=', $text);
        $text = str_replace('javascript:', '', $text);
        // Parse strong text: **strong**
        $text = preg_replace('/\*\*(.+?)\*\*/s', '<strong class="bc-font-bold">$1</strong>', $text);
        // Parse emphasized text: *em*
        $text = preg_replace('/\*(.+?)\*/s', '<em class="bc-italic">$1</em>', $text);
        // Parse images: ![AltText](ImageURL) - Images are not allowed
        $text = preg_replace('/!\[(.*?)\]\((.+?)\)/s', '', $text);
        // Parse links: [LinkText](URL)
        $text = preg_replace('/\[(.+?)\]\((.+?)\)/s', '<a class="brlbs-cmpnt-link brlbs-cmpnt-link-with-icon" href="$2" target="_blank" rel="nofollow noreferrer"><span>$1</span><span class="brlbs-cmpnt-external-link-icon"></span></a>', $text);
        // Replace \r\n with \n
        $text = preg_replace("/\r\n/", "\n", $text);
        // Parse single newlines: \n
        $text = preg_replace("/(?<!\n)\n(?!\n)/", '<br>', $text);
        // Parse paragraphs: \n\n
        $text = preg_replace("/\n{2,}/", '</p><p>', $text);
        $text = '<p>' . $text . '</p>';
        // Parse multiline code: ```multiline code```
        $text = preg_replace('/<p>```<br>(.+?)<br>```<\/p>/s', '<pre class="brlbs-cmpnt-code-multiline"><code>$1</code></pre>', $text);

        // Parse inline code: `code`
        return preg_replace('/`(.+?)`/', '<code class="brlbs-cmpnt-code-inline">$1</code>', $text);
    }
}
