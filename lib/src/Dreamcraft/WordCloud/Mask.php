<?php

namespace Dreamcraft\WordCloud;

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

  public function getTable() { return $this->drawn_boxes; }

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
     * @param float $ox The x coordinate of the starting search point
     * @param float $oy The y coordinate of the starting search point
     * @param array $box The 8 coordinates of the new box
     * @return array The x and y coordinates for the new box
     */
  function searchPlace($ox, $oy, $box) {
    $place_found = false;
    $i = 0; $x = $ox; $y = $oy;
    while (! $place_found) {
      $x = $x + ($i / 2 * cos($i));
      $y = $y + ($i / 2 * sin($i));
      $new_box = new Box($x, $y, $box);
      // TODO: Check if the new coord is in the clip area
      $place_found = ! $this->overlaps($new_box);
      $i += 1;
    }
    return array($x, $y);
  }

  public function getBoundingBox($margin = 10) {
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

