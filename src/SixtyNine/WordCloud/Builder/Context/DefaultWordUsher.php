<?php

namespace SixtyNine\WordCloud\Builder\Context;

use SixtyNine\WordCloud\Mask;
use SixtyNine\WordCloud\Box;

/**
 * Responsible to find a place for the word in the cloud
 */
class DefaultWordUsher extends WordUsher
{
    protected $mask;

    protected $imgWidth;

    protected $imgHeight;

    public function __construct($padding, $img_width, $img_height)
    {
        parent::__construct($padding);

        $this->mask = new Mask();
        $this->imgWidth = $img_width;
        $this->imgHeight = $img_height;
    }

    public function getMask()
    {
        return $this->mask;
    }

    public function getPlace($word, $angle, $box)
    {
        // Set the center so that vertical words are better distributed
        if ($angle == 0) {
            $cx = $this->imgWidth / 3;
            $cy = $this->imgHeight / 2;
        } else {
            $cx = $this->imgWidth / 3 - rand(0, $this->imgWidth / 10);
            $cy = $this->imgHeight / 2 - rand(-$this->imgHeight / 10, $this->imgHeight / 10);
        }

        $coord = $this->searchPlace($cx, $cy, $box);
        $this->mask->add(new Box($coord[0], $coord[1], $box));
        return $coord;
    }

    /**
     * Search a free place for a new box.
     *
     * @param float $ox The x coordinate of the starting search point
     * @param float $oy The y coordinate of the starting search point
     * @param array $box The 8 coordinates of the new box
     * @return array The x and y coordinates for the new box
     */
    protected function searchPlace($ox, $oy, $box)
    {
        $place_found = false;
        $i = 0;
        $x = $ox;
        $y = $oy;
        while (!$place_found) {
            $x = $x + ($i / 2 * cos($i));
            $y = $y + ($i / 2 * sin($i));
            $new_box = new Box($x, $y, $box);
            // TODO: Check if the new coord is in the clip area
            $place_found = !$this->mask->overlaps($new_box);
            $i += 1;
        }
        return array($x, $y);
    }
}
