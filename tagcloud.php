<?php

/**
 * Wordle-like words clouds generator.
 * This work is inspired by http://www.wordle.net/
 * 
 * @author Daniel Barsotti / dan [at] dreamcraft [dot] ch
 */


/**
 * An axis-aligned rectangle with collision detection 
 */
class Box {

  public $left, $right, $top, $bottom;
  
  /**
   * Construct a new rectangle from a point and a bounding box
   * @param integer $x The point x coordinate
   * @param integer $y The point x coordinate
   * @param array $bb The bounding box given in an array of 8 coordinates
   */
  public function __construct($x, $y, $bb) {
  
    $x1 = $x + $bb[0];
    $y1 = $y + $bb[1];
    $x2 = $x + $bb[2];
    $y2 = $y + $bb[3];
    $x3 = $x + $bb[4];
    $y3 = $y + $bb[5];
    $x4 = $x + $bb[6];
    $y4 = $y + $bb[7];
    
    $this->left = min($x1, $x2, $x3, $x4);
    $this->right = max($x1, $x2, $x3, $x4);
    $this->bottom = min($y1, $y2, $y3, $y4);
    $this->top = max($y1, $y2, $y3, $y4);
  }
  
  /**
   * Detect box collision 
   * This algorithm only works with Axis-Aligned boxes!
   * @param Box $box The other rectangle to test collision with
   * @return boolean True is the boxes collide, false otherwise
   */
  function intersects(Box $box) {
    if ($this->bottom > $box->top) return false;
    if ($this->top < $box->bottom) return false;
    if ($this->right < $box->left) return false;
    if ($this->left > $box->right) return false;
    
    return true;
  }
  
  /**
   * Return a string representing the HTML imagemap coords of the rect
   */
  public function get_map_coords() {
    return "{$this->left},{$this->top},{$this->right},{$this->bottom}";
  }
}

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

class WordCloud {

  private $width, $height;
  private $font;
  private $mask;
  private $table;
  private $image;

  public function __construct($width, $height, $font, $text) {
    $this->width = $width;
    $this->height = $height;
    $this->font = $font;
    
    $this->mask = new Mask();
    $this->table = new FrequencyTable($text, $font);
    $this->image = imagecreatetruecolor($width, $height);
  }
  
  public function get_image() {
    return $this->image;
  }
  
  public function render($palette) {
    $i = 0;
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
      list($cx, $cy) = $this->mask->search_place($im, $cx, $cy, $val->box);
      
      // Draw the word
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

$full_text = <<<EOT
dreamcraft.ch is a developement company based in Switzerland, aimed to create, integrate and mantain cutting-edge technology web applications.
We provide you our expertise in PHP/MySQL, Microsoft .NET and various Content Management Systems in order to develop new web applications or maintain and upgrade your current web sites.
Our philosophy:
Establish a durable and trustworthy win-win relation with our customers.
Use quality Open Source Software whenever possible.
Enforce good programming rules and standards to create better rich content web 2.0 applications.
A folk wisdom says "united we stand, divided we fall". As such we work closely with other Swiss companies to offer you an even larger range of skills.
Aicom are the creators of interactive web based tools such as FormFish, MettingPuzzle or MailJuggler.
oriented.net is a high-quality web hosting company based in Switzerland. Our partnership with them allows us to offer advanced hosting solutions to your web application.
EOT;

$font = dirname(__FILE__).'/Arial.ttf';
$width = 600;
$height = 600;
$cloud = new WordCloud($width, $height, $font, $full_text);
$palette = Palette::get_palette_from_hex($cloud->get_image(), array('FFA700', 'FFDF00', 'FF4F00', 'FFEE73'));
$cloud->render($palette);

// Render the cloud in a temporary file, and return its base64-encoded content
$file = tempnam(getcwd(), 'img');
imagepng($cloud->get_image(), $file);
$img64 = base64_encode(file_get_contents($file));
unlink($file);
imagedestroy($cloud->get_image());
?>

<img usemap="#mymap" src="data:image/png;base64,<?php echo $img64 ?>" border="0"/>
<map name="mymap">
<?php foreach($cloud->get_image_map() as $map): ?>
<area shape="rect" coords="<?php echo $map[1]->get_map_coords() ?>" onclick="alert('You clicked: <?php echo $map[0] ?>');" />
<?php endforeach ?>
</map>