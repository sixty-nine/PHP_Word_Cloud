<?php
/**
 * This file is part of the PHP_Word_Cloud project.
 * http://github.com/sixty-nine/PHP_Word_Cloud
 *
 * @author Daniel Barsotti / dan [at] dreamcraft [dot] ch
 * @license http://creativecommons.org/licenses/by-nc-sa/3.0/
 *          Creative Commons Attribution-NonCommercial-ShareAlike 3.0
 */

class WordCloud {

  private $width, $height;
  private $font;
  private $mask;
  private $table;
  private $image;

  public function __construct($width, $height, $font, $text, $vertical_freq = FrequencyTable::WORDS_MAINLY_HORIZONTAL) {
    $this->width = $width;
    $this->height = $height;
    $this->font = $font;

    $this->mask = new Mask();
    $this->table = new FrequencyTable($font, $text, $vertical_freq);
    $this->table->setMinFontSize(10);
    $this->table->setMaxFontSize(72);
    // $this->table = new FrequencyTable($font);//, $text);
    // $this->table->add_word('word1');
    // $this->table->add_word('word2', 2);
    // $this->table->add_word('word3');
    // $this->table->add_word('word4', 4);
    // $this->table->add_word('word5');
    // for($i = 6; $i <= 20; $i++) $this->table->add_word('word'.$i, $i % 5);
    $this->image = imagecreatetruecolor($width, $height);
  }

  public function get_image() {
    return $this->image;
  }

  public function render($palette) {
    $i = 0;
    $positions = array();
    
    foreach($this->table->get_table() as $key => $val) {

      // Set the center so that vertical words are better distributed
      if ($val->angle == 0) {
        $cx = $this->width /3;
        $cy = $this->height /2;
      }
      else {
        $cx = $this->width /3 + rand(0, $this->width / 10);
        $cy = $this->height /2 + rand(-$this->height/10, $this->height/10);
      }

      // Search the place for the next word
      list($cx, $cy) = $this->mask->search_place($this->image, $cx, $cy, $val->box);

      // Draw the word
      $res['words'][$key] = array(
        'x' => $cx,
        'y' => $cy,
        'angle' => $val->angle,
        'size' => $val->size,
        'color' => $palette[$i % count($palette)],
        'box' => $boxes[$key],
      );
      imagettftext($this->image, $val->size, $val->angle, $cx, $cy, $palette[$i % count($palette)], $this->font, $key);
      $this->mask->add(new Box($cx, $cy, $val->box));
      $i++;
    }

    // Crop the image
    list($x1, $y1, $x2, $y2) = $this->mask->get_bounding_box();
    $image2 = imagecreatetruecolor(abs($x2 - $x1), abs($y2 - $y1));
    imagecopy($image2 ,$this->image, 0, 0, $x1, $y1, abs($x2 - $x1), abs($y2 - $y1));
    imagedestroy($this->image);
    $this->image = $image2;

    // Adjust the map to the cropped image
    $this->mask->adjust(-$x1, -$y1);

    foreach($boxes = $this->get_image_map() as $map) {
      $res['words'][$map[0]]['box'] = $map[1];
    }

    $res['adjust'] = array('dx' => -$x1, 'dy' => -$y1);
    return $res;
  }

  public function get_image_map() {

    $words = $this->table->get_table();
    $boxes = $this->mask->get_table();
    if (count($boxes) != count($words)) {
      throw new Exception('Error: mask count <> word count');
    }

    $map = array();
    $i = 0;
    foreach($words as $key => $val) {
      $map[] = array($key, $boxes[$i]);
      $i += 1;
    }

    return $map;
  }
}

