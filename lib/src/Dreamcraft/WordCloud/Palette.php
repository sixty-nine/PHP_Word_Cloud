<?php

namespace Dreamcraft\WordCloud;

/**
 * Generate color palettes (arrays of colors)
 */
class Palette
{

    private static $palettes = array(
        'aqua' => array('BED661', '89E894', '78D5E3', '7AF5F5', '34DDDD', '93E2D5'),
        'yellow/blue' => array('FFCC00', 'CCCCCC', '666699'),
        'grey' => array('87907D', 'AAB6A2', '555555', '666666'),
        'brown' => array('CC6600', 'FFFBD0', 'FF9900', 'C13100'),
        'army' => array('595F23', '829F53', 'A2B964', '5F1E02', 'E15417', 'FCF141'),
        'pastel' => array('EF597B', 'FF6D31', '73B66B', 'FFCB18', '29A2C6'),
        'red' => array('FFFF66', 'FFCC00', 'FF9900', 'FF0000'),
    );

    /**
     * Construct a random color palette
     * @param integer $count The number of colors in the palette
     * @return array
     */
    public static function getRandomPalette($count = 5)
    {
        $palette = array();
        for ($i = 0; $i < $count; $i++) {
            $palette[] = array(rand(0, 255), rand(0, 255), rand(0, 255));
        }
        return $palette;
    }

    /**
     * Construct a color palette from a list of hexadecimal colors (RRGGBB)
     * @param array $hex_array An array of hexadecimal color strings
     * @return array
     */
    public static function getPaletteFromHex($hex_array)
    {
        $palette = array();

        foreach ($hex_array as $hex) {
            if (strlen($hex) != 6) {
                throw new Exception("Invalid palette color '$hex'");
            }
            $palette[] = array(
                hexdec(substr($hex, 0, 2)),
                hexdec(substr($hex, 2, 2)),
                hexdec(substr($hex, 4, 2))
            );
        }
        return $palette;
    }

    public static function getNamedPalette($name)
    {
        if (array_key_exists($name, self::$palettes)) {
            return self::getPaletteFromHex(self::$palettes[$name]);
        }
        return self::getNamedPalette('grey');
    }

    public static function listNamedPalettes()
    {
        return array_keys(self::$palettes);
    }
}
