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
  private $min_font_size = 16;
  private $max_font_size = 72;
  
  private $max_count = 1;
  private $min_count = 1;
  private $padding_size = 1.05;
  private $padding_angle = 0;
  private $words_limit;

  /**
   * Construct a new FrequencyTable from a word list and a font
   * @param string $text The text containing the words
   * @param string $font The TTF font file
   * @param integer $vertical_freq Frequency of vertical words (0 - 10, 0 = All horizontal, 10 = All vertical)
   */
  public function __construct($font, $text = '', $vertical_freq = FrequencyTable::WORDS_MAINLY_HORIZONTAL,$words_limit=null) {
    $this->words_limit = $words_limit;
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

  public function add_word($word, $nbr_occurence = 1,$title=null) {
    $this->insert_word($word, $nbr_occurence,$title);
  }

  /**
   * Return the current frequency table
   */
  public function get_table() {
    $this->process_frequency_table();
    return $this->table;
  }
  
   private function insert_word($word, $count = 1,$title=null,$reject=false,$cleanup=false) {
      // Reject unwanted words
      $word = strtolower($word);
      if (($reject) && ( (strlen($word) < 3) || (in_array($word, $this->rejected_words))) )  {
        return;
      }
      else {
        if($cleanup) $word = $this->cleanup_word($word);
        if (array_key_exists($word, $this->table)) {
          $this->table[$word]->count += $count;
        }
        else {
          $this->table[$word] = new StdClass();
          $this->table[$word]->count = $count;
          $this->table[$word]->word = $word;
          $this->table[$word]->title = $title;
        }
        $this->total_occurences += $count; 
        if ($this->table[$word]->count > $this->max_count) {            
              $this->max_count = $this->table[$word]->count;
        }
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
      $diffcount = ($this->max_count - $this->min_count) != 0 ? ($this->max_count - $this->min_count) : 1;
      $diffsize = ($this->max_font_size - $this->min_font_size) != 0 ? ($this->max_font_size - $this->min_font_size) : 1;
      $slope = $diffsize / $diffcount;
      $yintercept = $this->max_font_size - ($slope * $this->max_count);    
      
      //cut the table so we have only $this->words_limit
      $this->table = array_slice($this->table, 0, $this->words_limit);
      
    foreach($this->table as $key => $val) {  	
      $font_size = (integer)($slope * $this->table[$key]->count + $yintercept);

      // Set min/max val for font size
      if ($font_size < $this->min_font_size) {
          $font_size = $this->min_font_size;
      } elseif ($font_size > $this->max_font_size) {
          $font_size = $this->max_font_size;
      }
      $this->table[$key]->size = $font_size;

      $this->table[$key]->angle = 0;
      if (rand(1, 10) <= $this->vertical_freq) $this->table[$key]->angle = 90;
      $this->table[$key]->box = imagettfbbox ($this->table[$key]->size * $this->padding_size, $this->table[$key]->angle - $this->padding_angle, $this->font, $key);
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
