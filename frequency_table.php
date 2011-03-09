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
 * Table of words and frequencies along with some additionnal properties.
 */
class FrequencyTable {

  const WORDS_HORIZONTAL = 0;
  const WORDS_MAINLY_HORIZONTAL = 1;
  const WORDS_MIXED = 6;
  const WORDS_MAINLY_VERTICAL = 9;
  const WORDS_VERTICAL = 10;

  private $table = array();
  private $rejected_words = array(
    'and', 'our', 'your', 'their', 'his', 'her', 'the', 'you', 'them', 'yours',
    'with', 'such', 'even');
  private $font;
  private $vertical_freq = FrequencyTable::WORDS_MAINLY_HORIZONTAL;
  private $total_occurences = 0;
  private $min_font_size = 10;
  private $max_font_size = 60;

  /**
   * Construct a new FrequencyTable from a word list and a font
   * @param string $text The text containing the words
   * @param string $font The TTF font file
   * @param integer $vertical_freq Frequency of vertical words (0 - 10, 0 = All horizontal, 10 = All vertical)
   */
  public function __construct($font, $text = '', $vertical_freq = FrequencyTable::WORDS_MAINLY_HORIZONTAL) {

    $this->font = $font;
    $this->vertical_freq = $vertical_freq;
    $words = preg_split("/[\n\r\t ]+/", $text);
    $this->create_frequency_table($words);
    $this->process_frequency_table();
  }

  public function setMinFontSize($val) {

      $this->min_font_size = $val;
  }

  public function setMaxFontSize($val) {

      $this->max_font_size = $val;
  }

  public function add_word($word, $nbr_occurence = 1) {
    $this->insert_word($word, $nbr_occurence);
    $this->process_frequency_table();
  }

  /**
   * Return the current frequency table
   */
  public function get_table() {
    return $this->table;
  }
  
   private function insert_word($word, $count = 1) {
      // Reject unwanted words
      if ((strlen($word) < 3) || (in_array(strtolower($word), $this->rejected_words))) {
        return;
      }
      else {
        $word = $this->cleanup_word($word);
        if (array_key_exists($word, $this->table)) {
          $this->table[$word]->count += $count;
        }
        else {
          $this->table[$word] = new StdClass();
          $this->table[$word]->count = $count;
          $this->table[$word]->word = $word;
        }
        $this->total_occurences += $count; 
      }
   }
  
  /**
   * Creates the frequency table from a text.
   * @param string $words The text containing the words
   */
  private function create_frequency_table($words) {

    foreach($words as $key => $word) {
      $this->insert_word($word);
    }
  }
  
  /**
   * Calculate word frequencies and set additionnal properties of the frequency table
   * @param integer $vertical_freq Frequency of vertical words (0 - 10, 0 = All horizontal, 10 = All vertical)
   */
  private function process_frequency_table() {
    arsort($this->table);
    $count = count($this->table);
    foreach($this->table as $key => $val) {
      $f = $this->table[$key]->count / $count;

      $font_size = (integer)(3 * $this->total_occurences * $f) + 10;
      $font_size += rand(-2, 2); // Add some noize to the font sizes

      // Set min/max val for font size
      if ($font_size < $this->min_font_size) {
          $font_size = $this->min_font_size;
      } elseif ($font_size > $this->max_font_size) {
          $font_size = $this->max_font_size;
      }
      $this->table[$key]->size = $font_size;

      $this->table[$key]->angle = 0;
      if (rand(1, 10) <= $this->vertical_freq) $this->table[$key]->angle = 90;
      $this->table[$key]->box = imagettfbbox ($this->table[$key]->size, $this->table[$key]->angle, $this->font, $key);
    }
  }

  /**
   * Remove unwanted characters from a word
   * @param string $word The word to clenup
   * @return string The cleaned up word
   */
  private function cleanup_word($word) {

    $tmp = $word;

    // Remove unwanted characters
    $punctuation = array('?', '!', '\'', '"');
    foreach($punctuation as $p) {
      $tmp = str_replace($p, '', $tmp);
    }

    // Remove trailing punctuation
    $punctuation[] = '.';
    $punctuation[] = ',';
    $punctuation[] = ':';
    foreach($punctuation as $p) {
      if(substr($tmp, -1) == $p) {
        $tmp = substr($tmp, 0, -1);
      }
    }
    return $tmp;
  }

}
