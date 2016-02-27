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

    public function searchPlace($word, $angle, $box)
    {
        // Set the center so that vertical words are better distributed
        if ($angle == 0) {
            $cx = $this->imgWidth / 3;
            $cy = $this->imgHeight / 2;
        } else {
            $cx = $this->imgWidth / 3 - rand(0, $this->imgWidth / 10);
            $cy = $this->imgHeight / 2 - rand(-$this->imgHeight / 10, $this->imgHeight / 10);
        }

        $coord = $this->mask->searchPlace($cx, $cy, $box);
        $this->mask->add(new Box($coord[0], $coord[1], $box));
        return $coord;
    }
}
