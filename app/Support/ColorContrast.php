<?php

namespace App\Support;

class ColorContrast
{
    public static function textColorForBackground(string $hexColor): string
    {
        $hex = ltrim($hexColor, '#');

        if (strlen($hex) === 3) {
            $hex = "{$hex[0]}{$hex[0]}{$hex[1]}{$hex[1]}{$hex[2]}{$hex[2]}";
        }

        if (! preg_match('/^[0-9a-fA-F]{6}$/', $hex)) {
            return '#000000';
        }

        $r = hexdec(substr($hex, 0, 2)) / 255;
        $g = hexdec(substr($hex, 2, 2)) / 255;
        $b = hexdec(substr($hex, 4, 2)) / 255;

        $luminance = self::relativeLuminance($r, $g, $b);
        $contrastWithWhite = self::contrastRatio($luminance, 1.0);
        $contrastWithBlack = self::contrastRatio($luminance, 0.0);

        return $contrastWithWhite >= $contrastWithBlack ? '#FFFFFF' : '#000000';
    }

    private static function relativeLuminance(float $r, float $g, float $b): float
    {
        $r = $r <= 0.03928 ? $r / 12.92 : (($r + 0.055) / 1.055) ** 2.4;
        $g = $g <= 0.03928 ? $g / 12.92 : (($g + 0.055) / 1.055) ** 2.4;
        $b = $b <= 0.03928 ? $b / 12.92 : (($b + 0.055) / 1.055) ** 2.4;

        return (0.2126 * $r) + (0.7152 * $g) + (0.0722 * $b);
    }

    private static function contrastRatio(float $luminanceA, float $luminanceB): float
    {
        $lighter = max($luminanceA, $luminanceB);
        $darker = min($luminanceA, $luminanceB);

        return ($lighter + 0.05) / ($darker + 0.05);
    }
}
