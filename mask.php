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
 * List of already placed boxes used to search a free space for a new box.
 */
class Mask {

  private $drawn_boxes = array();

  /**
   * Add a new box to the mask.
   * @param Box $box The new box to add
   */
  public function add(Box $box) {
    $this->drawn_boxes[] = $box;
  }

  public function get_table() { return $this->drawn_boxes; }

  /**
   * Test whether a box overlaps with the already drawn boxes.
   * @param Box $test_box The box to test
   * @return boolean True if the box overlaps with the already drawn boxes and false otherwise
   */
  public function overlaps(Box $test_box) {
    foreach($this->drawn_boxes as $box) {
      if ($box->intersects($test_box)) {
        return true;
      }
    }
    return false;
  }

  /**
   * Search a free place for a new box.
   *
   * @param object $im The GD image
   * @param float $ox The x coordinate of the starting search point
   * @param float $oy The y coordinate of the starting search point
   * @param array $box The 8 coordinates of the new box
   * @param Mask $mask The mask containing the already drawn boxes
   * @return array The x and y coordinates for the new box
   */
  function search_place($im, $ox, $oy, $box) {
    $place_found = false;
    $i = 0; $x = $ox; $y = $oy;
    while (! $place_found) {
      $x = $x + ($i / 2 * cos($i));
      $y = $y + ($i / 2 * sin($i));
      $new_box = new Box($x, $y, $box);
      // TODO: Check if the new coord is in the clip area
      $place_found = ! $this->overlaps($new_box);
      // Uncomment the next line to see the spiral used to search a free place
      //imagesetpixel($im, $x, $y, imagecolorallocate($im, 255, 0, 0));
      $i += 1;
    }
    return array($x, $y);
  }

  public function get_bounding_box($margin = 10) {
    $left = null; $right = null;
    $top = null; $bottom = null;
    foreach($this->drawn_boxes as $box) {
      if (($left == NULL) || ($box->left < $left)) $left = $box->left;
      if (($right == NULL) || ($box->right > $right)) $right = $box->right;
      if (($top == NULL) || ($box->top > $top)) $top = $box->top;
      if (($bottom == NULL) || ($box->bottom < $bottom)) $bottom = $box->bottom;
    }
    return array($left - $margin, $bottom - $margin, $right + $margin, $top + $margin);
  }

  public function adjust($dx, $dy) {
    foreach($this->drawn_boxes as $box) {
      $box->left += $dx;
      $box->right += $dx;
      $box->top += $dy;
      $box->bottom += $dy;
    }
  }
}

