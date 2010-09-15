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

  /**
   * Construct a new FrequencyTable from a word list and a font
   * @param string $text The text containing the words
   * @param string $font The TTF font file
   * @param integer $vertical_freq Frequency of vertical words (0 - 10, 0 = All horizontal, 10 = All vertical)
   */
  public function __construct($text, $font, $vertical_freq = FrequencyTable::WORDS_MAINLY_HORIZONTAL) {

    $this->font = $font;
    $words = split("[\n\r\t ]+", $text);
    $this->create_frequency_table($words);
    $this->process_frequency_table($vertical_freq);
  }

  /**
   * Return the current frequency table
   */
  public function get_table() {
    return $this->table;
  }

  /**
   * Creates the frequency table from a text.
   * @param string $words The text containing the words
   */
  private function create_frequency_table($words) {

    foreach($words as $key => $word) {
      // Reject unwanted words
      if ((strlen($word) < 3) || (in_array(strtolower($word), $this->rejected_words))) {
        unset($words[$key]);
      }
      else {
        $word = $this->cleanup_word($word);
        if (array_key_exists($word, $this->table)) {
          $this->table[$word]->count += 1;
        }
        else {
          $this->table[$word] = new StdClass();
          $this->table[$word]->count = 1;
          $this->table[$word]->word = $word;
        }
      }
    }
    arsort($this->table);
  }

  /**
   * Calculate word frequencies and set additionnal properties of the frequency table
   * @param integer $vertical_freq Frequency of vertical words (0 - 10, 0 = All horizontal, 10 = All vertical)
   */
  private function process_frequency_table($vertical_freq = FrequencyTable::WORDS_MAINLY_HORIZONTAL) {
    $count = count($this->table);
    foreach($this->table as $key => $val) {
      $f = $this->table[$key]->count / $count;
      $this->table[$key]->size = (integer)(500 * $f) + 7;
      $this->table[$key]->size += rand(-5, 3); // Add some noize to the font sizes
      $this->table[$key]->angle = 0;
      if (rand(1, 10) <= $vertical_freq) $this->table[$key]->angle = 90;
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
