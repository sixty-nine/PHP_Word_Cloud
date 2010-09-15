<?php
/**
 * This file is part of the PHP_Word_Cloud project.
 * http://github.com/sixty-nine/PHP_Word_Cloud
 *
 * @author Daniel Barsotti / dan [at] dreamcraft [dot] ch
 * @license http://creativecommons.org/licenses/by-nc-sa/3.0/
 *          Creative Commons Attribution-NonCommercial-ShareAlike 3.0
 */

/**
 * Generate color palettes (arrays of allocated colors)
 */
class Palette {

  /**
   * Construct a random color palette
   * @param object $im The GD image
   * @param integer $count The number of colors in the palette
   */
  public static function get_random_palette($im, $count = 5) {
    $palette = array();
    for ($i = 0; $i < $count; $i++) {
      $palette[] = imagecolorallocate($im, rand(0, 255), rand(0, 255), rand(0, 255));
    }
    return $palette;
  }

  /**
   * Construct a color palette from a list of hexadecimal colors (RRGGBB)
   * @param object $im The GD image
   * @param array $hex_array An array of hexadecimal color strings
   */
  public static function get_palette_from_hex($im, $hex_array) {
    $palette = array();
    foreach($hex_array as $hex) {
    if (strlen($hex) != 6) throw new Exception("Invalid palette color '$hex'");
      $palette[] = imagecolorallocate($im,
        hexdec(substr($hex, 0, 2)),
        hexdec(substr($hex, 2, 2)),
        hexdec(substr($hex, 4, 2)));
    }
    return $palette;
  }
}
